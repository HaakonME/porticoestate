<?php
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

$GLOBALS['debug']["ged.ged_dm"] = false;
$GLOBALS['debug']["ged.ged_dm.list_versions"] = true;

class ged_dm
{
	var $db;
	var $ged_config;
	var $owner;
	var $admin;
	var $sqlaclread;
	var $sqlaclwrite;
	var $sqlaclchange_acl;
	var $datadir;

	var $tables=Array('comments'=>'ged_comments', 'elements'=>'ged_elements', 'history'=>'ged_history', 'mimetypes'=>'ged_mimes', 'acl'=>'ged_acl', 'versions'=>'ged_versions', 'relations' => 'ged_relations');

	function ged_dm()
	{
		// MEMO Le caract�re "administrateur est plut�t li� � l'activation du module Admin
		// MEMO=>existence de $GLOBALS['phpgw_info']['user']['apps']['admin']
		$this->admin=isset($GLOBALS['phpgw_info']['user']['apps']['admin']);

		// MEMO appartenance � des groupes
		// MEMO $GLOBALS['phpgw']->accounts->memberships[$i][account_id]
		
		if ( ! $this->admin )
		{
			$or="";
			$sqlaclbase="( ";
			foreach ( $GLOBALS['phpgw']->accounts->memberships as $membership )
			{
				$sqlaclbase.=$or.$this->tables['acl'].".account_id=".$membership['account_id']." ";
				$or="OR ";
			}
			$sqlaclbase.=$or.$this->tables['acl'].".account_id=".$GLOBALS['phpgw_info']['user']['account_id']." ";
			$this->sqlaclread=$sqlaclbase.") AND ( ".$this->tables['acl'].".aclread=1 )";
			$this->sqlaclwrite=$sqlaclbase.") AND ( ".$this->tables['acl'].".aclwrite=1 )";
			$this->sqlaclchangeacl=$sqlaclbase.") AND ( ".$this->tables['acl'].".aclchangeacl=1 )";
			
		}
		
		//$this->ged_config=$GLOBALS['ged_config'];
		$config=CreateObject('phpgwapi.config','ged');
		$config->read_repository();
		$this->ged_config=$config->config_data;
		//_debug_array($this->ged_config);
		unset($config);
		
		// TODO a g�rer via le (futur) hook d'admin
		$this->datadir=$GLOBALS['phpgw_info']['server']['files_dir']."/ged-data";
		
		// TODO : find a better way to know what doc type
		// TODO : should be used for comments ( cf reject and refuse actions )
		$this->internal_review_file_type='fiche-relecture-interne';
		$this->external_review_file_type='fiche-relecture-externe';
		
		if ( ! is_dir ( $this->datadir ))
			mkdir ( $this->datadir);

		$this->db=$GLOBALS['phpgw']->db;
		$this->owner=intval($GLOBALS['phpgw_info']['user']['account_id']);

	}

	function debug ($thefunction)
	{
		if ( ! array_key_exists('debug', $GLOBALS))
			$GLOBALS['debug']=Array();
			
		if ( !array_key_exists('all', $GLOBALS['debug']))
			$GLOBALS['debug']['all']=false;
		
		if ( !array_key_exists ("ged.".get_class($this), $GLOBALS['debug']) )
			$GLOBALS['debug']["ged.".get_class($this)]=false;
			
		if ( !array_key_exists ("ged.".get_class($this).".".$thefunction, $GLOBALS['debug']))
			$GLOBALS['debug']["ged.".get_class($this).".".$thefunction]=false;
		
		if ( $GLOBALS['debug']['all'] || $GLOBALS['debug']["ged.".get_class($this)] || $GLOBALS['debug']["ged.".get_class($this).".".$thefunction])
		{
			$GLOBALS['debug']['show']=true;
			return true;
		}
		else
			return false;
	}

	function cleanstr ( $the_str )
	{
		$the_str2=$this->db->db_addslashes($the_str);
		return ($the_str2);
	}
	
	function get_file_extention($filename)
	{
		$ext=strtolower(substr(strrchr($filename, "."), 1));
		return $ext;
	}

	// DONE fo gerer aussi les controles d'acces */
	function add_file($new_file)
	{

		// MEMO gestion du stored_name pour eviter les ecrasements.

		$basename=basename($new_file['file_name']);
		$candidate_name=$basename;

		$extension=$this->get_file_extention($basename);
		
		$parent=$this->get_element_info($new_file['parent_id']);

		$i=0;

		while (file_exists($this->datadir."/".$candidate_name))
		{
			$i ++;
			$candidate_name="v".$i."_".$basename;
		}

		$new_name=$this->datadir."/".$candidate_name;

		if (move_uploaded_file($new_file['file_tmp_name'], $new_name))
		{

			$download_result='OK';

		}
		else
		{

			$download_result='NOK';
			print ('NOOK FILE MOVE');

		}
		
		// TODO : If the document type requires a chrono
		// TODO : generate reference and override the reference
		// TODO : given by user if needed
		if ( !isset($new_file['reference']) || ((int)$parent['project_root'] != 0 && $this->is_chrono_type($new_file['doc_type'])))
		{
			$next_ref=$this->get_next_available_reference($new_file['doc_type'], $parent['project_root'] );
		}
		else
		{
			$next_ref=$new_file['reference'];
		}
						
		if ($download_result=='OK')
		{
			// DONE : ADD 'validity_period'
			$sql_model="INSERT INTO %s ( type, parent_id, name, reference, description, owner_id, creator_id, creation_date, doc_type, validity_period, project_name, project_root) VALUES ";
			$sql_model.=" ( '%s', %d, '%s', '%s', '%s', %d, %d, %d, '%s', %d, '%s', %d ) ";

			$sql=sprintf($sql_model, $this->tables['elements'], 'file', $new_file['parent_id'], $new_file['name'], $next_ref, $new_file['description'], $GLOBALS['phpgw_info']['user']['account_id'], $GLOBALS['phpgw_info']['user']['account_id'], time(), $new_file['doc_type'], $new_file['validity_period'],$parent['project_name'],$parent['project_root']);

			// DONE gestion des slashes !
			//print ($sql);

			$this->db->query($sql, __LINE__, __FILE__);
			//recuperer l'id
			$new_element_id=$this->db->get_last_insert_id($this->tables['elements'], 'element_id');

			$this->db->unlock();

			// DONE gestion des slashes

			$sql_model1="INSERT INTO %s ( size, file_name, stored_name, file_extension, creator_id, creation_date, status, element_id, description, major, minor ) VALUES ";
			$sql_model1.=" (  %d, '%s', '%s', '%s', %d, %d, '%s', %d, '%s', %d, %d ) ";
			
			$major=(int)$new_file['major'];
			$minor=(int)$new_file['minor'];

			$sql1=sprintf($sql_model1, $this->tables['versions'], $new_file['file_size'], $new_file['file_name'], $candidate_name, $extension, $GLOBALS['phpgw_info']['user']['account_id'], time(), 'working', $new_element_id, lang("First version"), $major, $minor);

			//print ("<br/>".$sql1);

			$this->db->query($sql1, __LINE__, __FILE__);
			$this->db->unlock();
			
			$this->set_default_acl($new_element_id);
			
			return ( $new_element_id );

		}

	}

	function update_file($new_file)
	{
		$myelement=$this->get_element_info($new_file['element_id']);
		$myparent=$this->get_element_info($myelement['parent_id']);

		if ( $this->debug('update_file') )
			print ( "update_file: entering.<br>\n");

		// DONE : add 'validity_period'
		// DONE gestion des slashes !
		$sql="UPDATE ".$this->tables['elements']." SET ";
		$sep="";
		
		if ( array_key_exists('name', $new_file) && $new_file['name'] != '')
		{
			$sql.=$sep."name='".$this->cleanstr($new_file['name'])."'";
			$sep=", ";
		}
			
		if ( array_key_exists('description', $new_file) && $new_file['description'] != '')
		{
			$sql.=$sep."description='".$this->cleanstr($new_file['description'])."'";
			$sep=", ";
		}

		if ( array_key_exists('doc_type', $new_file) && $new_file['doc_type'] != '' && $this->admin )
		{
			$sql.=$sep."doc_type='".$this->cleanstr($new_file['doc_type'])."'";
			$sep=", ";

			$newref="";
			$sql.=$sep."reference='".$this->cleanstr($new_file['reference'])."'";
		}

		if ((int)$myelement['project_root'] != 0 && $this->is_chrono_type($new_file['doc_type']) && $myelement['doc_type'] != $new_file['doc_type'] && $this->admin )
			$next_ref=$this->get_next_available_reference($new_file['doc_type'], $myelement['project_root'] );
		else
			$next_ref=$this->cleanstr($new_file['reference']);

		if ( $next_ref != "" && $this->admin )
		{
			$sql.=$sep."reference='".$next_ref."'";
			$sep=", ";
		}

		if ( array_key_exists('validity_period', $new_file) )
			$new_file2['validity_period']= (int) $new_file['validity_period'];
		else
			$new_file2['validity_period']=null;

		if ( array_key_exists('validity_period', $new_file) && $new_file['validity_period'] != '')
		{
			$sql.=$sep."validity_period='".$new_file2['validity_period']."'";
			$sep=", ";
		}

		$sql.="WHERE element_id=".$new_file['element_id'];
		
		//print ($sql);
		if ( $this->debug('update_file') )
			print ( "update_file: SQL =".$sql."<br>\n");
		
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();

		if ( isset($new_file['project_name']) && (int)$myparent['project_root'] == 0 )
		{
			$this->set_project($new_file['element_id'], $new_file['project_name']);
		}

		if ( $this->debug('update_file') )
			print ( "update_file: end.<br>\n");

	}

	function set_file_lock($element_id, $lock)
	{
		
		if ( $this->can_change_file_lock($element_id) )
		{
			if ( $lock==true )
			{
				$lock_value=1;
			}	
			else
			{
				$lock_value=0;
			}
			
			$sql="UPDATE ged_elements set lock_status=".$lock_value.", lock_user_id=".$GLOBALS['phpgw_info']['user']['account_id']." WHERE element_id=".$element_id;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();
			
			$version=$this->get_last_version($element_id);
			
			if ( $lock==true )
			{
				$this->store_history ('locked', 'locked', $version['version_id']);
			}
			else
			{
				$this->store_history ('unlocked', 'unlocked', $version['version_id']);				
			}

		}
	}
	
	function is_locked($element_id)
	{
		$out=false;
		$element=$this->get_element_info($element_id);
		
		if ( $element['lock_status'] == 1 && $element['lock_user_id'] != $GLOBALS['phpgw_info']['user']['account_id'])
		{
			$out=true;
		}
		
		return ( $out );
	}
	
	function set_project ($element_id, $project_name, $the_root_element_id=null)
	{		
		if ( $the_root_element_id == null )
			$root_element_id=$element_id;
		else
			$root_element_id=$the_root_element_id;
			
		$children_elements=$this->list_elements($element_id);
		
		if ( is_array($children_elements))
			foreach ( $children_elements as $child_element )
			{
					$this->set_project($child_element['element_id'], $project_name,$root_element_id);
			}
		
		if ( $project_name != '' )
		{
			$sql="UPDATE ".$this->tables['elements']." SET ";
			$sql.="project_name='".$project_name."', ";
			$sql.="project_root=".$root_element_id." ";
			$sql.="WHERE element_id=".$element_id;
		}
		else
		{
			$sql="UPDATE ".$this->tables['elements']." SET ";
			$sql.="project_name=null, ";
			$sql.="project_root=null ";
			$sql.="WHERE element_id=".$element_id;
			
		}
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();
		
	}
	
	function delete_element ( $element_id )
	{
		$element_info=$this->get_element_info($element_id);
		
		switch ( $element_info['type'] )
		{
			case 'file':
				//effacement des versions
				$versions=$this->list_versions($element_id);
				
				//_debug_array($versions);
				
				//effacement physique des fichiers
				foreach ( $versions as $version )
				{
					if ( is_file ( $version['file_full_path'] ))
					{
						unlink ( $version['file_full_path'] );
					}
					// TODO : Effacement des relations
					$sqlr="DELETE FROM ".$this->tables['relations']." WHERE linking_version_id=".$version['version_id']." ";
					$sqlr.="OR linked_version_id=".$version['version_id']." ";
					
					$this->db->query($sqlr, __LINE__, __FILE__);
				
				}
				// Effacement des versions
				$sqlv="DELETE FROM ".$this->tables['versions']." WHERE element_id=".$element_id;
				$this->db->query($sqlv, __LINE__, __FILE__);
				break;
			
			case 'folder':

				// Effacement des sous r�pertoires
				$children=$this->list_elements($element_id);
				
				//_debug_array($children);
				
				if ( is_array ( $children ) )
					foreach ( $children as $child )
					{
						$this->delete_element ( $child['element_id'] );
					}
			
		}
		
		// Effacement des ACLS
		$this->rm_all_acl ( $element_id );
		
		// Effacement des historiques
		$sqlh="DELETE FROM ".$this->tables['history']." WHERE element_id=".$element_id;
		$this->db->query($sqlh, __LINE__, __FILE__);
		

		// Effacement de l'element
		$sqle="DELETE FROM ".$this->tables['elements']." WHERE element_id=".$element_id;
		$this->db->query($sqle, __LINE__, __FILE__);
		$this->db->debug=false;
		
		return ($element_info['parent_id']);
	
	}

	function add_version($new_version)
	{
		if ( $this->debug('add_version') )
			print ( "add_version: entering.<br>\n");

		// MEMO gestion des numeros de versions

		$current_or_alert_or_refused_version=$this->get_current_or_alert_or_refused_version($new_version['element_id']);
		$major=$current_or_alert_or_refused_version['major'];
		$minor=$current_or_alert_or_refused_version['minor'];
		$last_version=$this->get_last_version($new_version['element_id']);
		
		if ($last_version['status'] == 'current' || $last_version['status'] == 'refused' || $last_version['status'] == 'alert' )
		{
			if ($new_version['type']=='major')
			{
				$major++;
				$minor=0;
			}
			elseif ($new_version['type']=='minor') $minor ++;
			else
				return "PB Version";
	
			// MEMO gestion du stored_name pour �viter les �crasements.
	
			$basename=basename($new_version['file_name']);
			$candidate_name=$basename;
	
			$extension=$this->get_file_extention($basename);
	
			$i=0;
	
			while (file_exists($this->datadir."/".$candidate_name))
			{
				$i ++;
				$candidate_name="v".$i."_".$basename;
			}
	
			$new_name=$this->datadir."/".$candidate_name;
	
			if (move_uploaded_file($new_version['file_tmp_name'], $new_name))
			{
	
				$download_result='OK';
	
			}
			else
				return "PB download";
	
			// MEMO attention que if $new_version['status'] est current il faut rendre obsol�te la "vieille"
	
			if ($download_result=='OK')
			{
	
				$sql_model1="INSERT INTO %s ( size, file_name, stored_name, file_extension, creator_id, creation_date, status, element_id, description, major, minor ) VALUES ";
				$sql_model1.=" (  %d, '%s', '%s', '%s', %d, %d, '%s', %d, '%s', %d, %d ) ";
	
				$sql1=sprintf($sql_model1, $this->tables['versions'], $new_version['file_size'], $new_version['file_name'], $candidate_name, $extension, $GLOBALS['phpgw_info']['user']['account_id'], time(), 'working', $new_version['element_id'], $this->cleanstr($new_version['description']), $major, $minor);
	
				//print ("<br/>".$sql1);
	
				$this->db->query($sql1, __LINE__, __FILE__);
				
				$my_new_version_id=$new_element_id=$this->db->get_last_insert_id($this->tables['versions'], 'version_id');
				
				$this->db->unlock();
				
				$this->store_history ('new version', $this->cleanstr($new_version['description']), $my_new_version_id);
	
			}
			
			// AJOUT des relations
			if ( is_array($new_version['relations']))
			{
				foreach ( $new_version['relations'] as $relation )
				{
					$sql3="INSERT INTO ged_relations ( linked_version_id, linking_version_id, relation_type) values ( ".$relation['linked_version_id'].",".$my_new_version_id.",'".$relation['relation_type']."' );";
					$this->db->query($sql3, __LINE__, __FILE__);
					$this->db->unlock();
				}
			}
	    	    
			if ( $this->debug('add_version') )
				print ( "add_version: end.<br>\n");
	
			return "OK";
		}

	}

	function update_version($amended_version)
	{

		// MEMO gestion des numeros de versions
		
		$cur_version=$this->get_current_or_alert_or_refused_version($amended_version['element_id']);
		$me_version=$this->get_version_info($amended_version['version_id']);
		
		if (  $me_version['status']=='working' )
		{
			if ( isset($cur_version))
			{
				$major=$cur_version['major'];
				$minor=$cur_version['minor'];
	
				if ($amended_version['type']=='major')
				{
					$major++;
					$minor=0;
				}
				elseif ($amended_version['type']=='minor') 
				{
					$minor ++;
				}
				else
				{
					return "PB Version";
				}
			}
			else
			{
				$major=$me_version['major'];
				$minor=$me_version['minor'];
			}

			// MEMO gestion du stored_name pour �viter les �crasements.
	
			if ($amended_version['file_name'] !="")
			{
	
				$old_name=$this->datadir."/".$me_version['stored_name'];
	
				if (!unlink($old_name))
					return ("cannot erase old file");
	
				$basename=basename($amended_version['file_name']);
				$candidate_name=$basename;
	
				$extension=$this->get_file_extention($basename);
	
				$i=0;
	
				while (file_exists($this->datadir."/".$candidate_name))
				{
					$i ++;
					$candidate_name="v".$i."_".$basename;
				}
	
				$new_name=$this->datadir."/".$candidate_name;
	
				if (move_uploaded_file($amended_version['file_tmp_name'], $new_name))
				{
					$download_result='OK';
				}
				else
				{
					$download_result='';
					return "PB download";
				}
			}
			else
				$download_result='';	
	
			// MEMO attention que if $new_version['status'] est current il faut rendre obsol�te la "vieille"
	
			if ($download_result=='OK')
			{
				$sql_model1="UPDATE %s  set size=%d, file_name='%s', stored_name='%s', file_extension='%s', ";
				$sql_model1.="status='%s', description='%s', major=%d, minor=%d ";
				$sql_model1.="WHERE version_id=%d";
	
				$sql1=sprintf($sql_model1, $this->tables['versions'], $amended_version['file_size'], $amended_version['file_name'], $this->cleanstr($candidate_name), $extension, 'working', $this->cleanstr($amended_version['description']), $major, $minor, $amended_version['version_id']);
			}
			else
			{
				$sql_model1="UPDATE %s  set  ";
				$sql_model1.="status='%s', description='%s', major=%d, minor=%d ";
				$sql_model1.="WHERE version_id=%d";
	
				$sql1=sprintf($sql_model1, $this->tables['versions'], 'working', $this->cleanstr($amended_version['description']), $major, $minor, $amended_version['version_id']);
			}
	
			$this->db->query($sql1, __LINE__, __FILE__);
			$this->db->unlock();
			
			$this->store_history ('updated', $amended_version['description'], $amended_version['version_id']);
			
			// Gestion des relations
			if ( is_array($amended_version['relations']))
			{
				$this->set_relations($amended_version['version_id'],$amended_version['relations']); 
			}
			else
			{
				$this->erase_relations($amended_version['version_id']); 
			}
		}

		return "OK";

	}

	function erase_relations($version_id)
	{
		$sql="DELETE FROM ".$this->tables['relations']." where linking_version_id=".$version_id; 
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();			
	}
	
	function set_relations($version_id,$relations)
	{
		// Enlever les relations en trop
		$sql="DELETE FROM ".$this->tables['relations']." where linking_version_id=".$version_id." "; 
		$sql.=" AND ( ";
		$_and='';
		foreach ( $relations as $relation ) 
		{
			$sql.=$_and."linked_version_id !=".$relation['linked_version_id']." ";
			$_and='AND ';
		}
		$sql.=")";
		
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();
		
		// Mettre à jour les existantes
		// Ou insérer les nouvelles
		foreach ( $relations as $relation ) 
		{
			$sql="SELECT * FROM ".$this->tables['relations']." ";
			$sql.="WHERE linking_version_id=".$version_id." ";
			$sql.="AND linked_version_id=".$relation['linked_version_id']." ";
			$this->db->query($sql, __LINE__, __FILE__);
			$n_found_rows=$this->db->num_rows();
			$this->db->unlock();
			
			// Si lenombre de lignes trouvées est zéro
			// Alors on insère
			if (  $n_found_rows == 0 )
			{
				$sql3="INSERT INTO ged_relations ( linked_version_id, linking_version_id, relation_type) values ( ".$relation['linked_version_id'].",".$version_id.",'".$relation['relation_type']."' );";
				$this->db->query($sql3, __LINE__, __FILE__);
				$this->db->unlock();
			}
			else
			{
				$sql2="UPDATE ".$this->tables['relations']." ";
				$sql2.="SET relation_type='".$relation['relation_type']."' ";
				$sql2.="WHERE linking_version_id=".$version_id." ";
				$sql2.="AND linked_version_id=".$relation['linked_version_id']." ";
				
				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->unlock();					
			}
		}	
	}
	
	function add_folder($new_folder)
	{
		$parent=$this->get_element_info($new_folder['parent_id']);
		
		if ( isset($parent['project_root']) &&  (int)$parent['project_root']!=0 )
		{
			$sql_model="INSERT INTO %s ( type, parent_id, name, reference, description, owner_id, creator_id, creation_date, project_name, project_root  ) VALUES ";
			$sql_model.=" ( '%s', %d, '%s', '%s', '%s', %d, %d, %d, '%s', %d) ";

			$sql=sprintf($sql_model, $this->tables['elements'], 'folder', $new_folder['parent_id'], $new_folder['name'], $parent['reference'], $new_folder['description'], $GLOBALS['phpgw_info']['user']['account_id'], $GLOBALS['phpgw_info']['user']['account_id'], time(),$parent['project_name'],$parent['project_root']);
			
			$set_project=false;
		}
		else
		{
			$sql_model="INSERT INTO %s ( type, parent_id, name, reference, description, owner_id, creator_id, creation_date, project_name ) VALUES ";
			$sql_model.=" ( '%s', %d, '%s', '%s', '%s', %d, %d, %d, '%s') ";

			$sql=sprintf($sql_model, $this->tables['elements'], 'folder', $new_folder['parent_id'], $new_folder['name'], $new_folder['referenceq'], $new_folder['description'], $GLOBALS['phpgw_info']['user']['account_id'], $GLOBALS['phpgw_info']['user']['account_id'], time(),$new_folder['project_name']);
			
			if ( $new_folder['project_name']!='')
			{
				$set_project=true;	
			}
		}

		//print ($sql);

		$this->db->query($sql, __LINE__, __FILE__);
		
		$new_element_id=$this->db->get_last_insert_id($this->tables['elements'], 'element_id');

		$this->db->unlock();
		
		if ( $set_project )
		{
			$this->set_project($new_element_id, $new_folder['project_name']);
		}
		
		//TODO positionnement des droits.
		$this->set_default_acl($new_element_id);

	}

	function addURL()
	{
	}

	function get_element_info($element_id)
	{
		if ( ! $this->can_read($element_id))
			$element_id=0;
			
		$sql="SELECT * FROM ".$this->tables['elements']." ";
		$sql.="WHERE element_id=".$element_id."";

		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			$out['element_id']=$this->db->f('element_id');
			$out['name']=$this->db->f('name');
			$out['parent_id']=$this->db->f('parent_id');
			$out['owner_id']=$this->db->f('owner_idR');
			$out['reference']=$this->db->f('reference');
			$out['type']=$this->db->f('type');
			$out['doc_type']=$this->db->f('doc_type');
			$out['creator_id']=$this->db->f('creator_id');
			$out['owner_id']=$this->db->f('owner_id');
			$out['creation_date']=$this->db->f('creation_date');
			$out['lock_status']=$this->db->f('lock_status');
			$out['lock_user_id']=$this->db->f('lock_user_id');
			$out['description']=$this->db->f('description');
			$out['validity_period']=$this->db->f('validity_period');
			$out['project_name']=$this->db->f('project_name');
			$out['project_root']=$this->db->f('project_root');
			// DONE : ADD 'validity_period' 
		}
		else
			$out="";

		$this->db->unlock();

		return $out;
	}

	function get_version_info($version_id)
	{
		$sql="SELECT ".$this->tables['versions'].".*, ".$this->tables['elements'].".name, ".$this->tables['elements'].".reference FROM ".$this->tables['versions']." ";
		$sql.="INNER JOIN ".$this->tables['elements']." ON ";
		$sql.=" ".$this->tables['elements'].".element_id = ".$this->tables['versions'].".element_id "; 
		$sql.="WHERE ".$this->tables['versions'].".version_id=".$version_id." ";

		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record());
		{

			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['name']=$this->db->f('name');
			$version['reference']=$this->db->f('reference');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
		}

		$this->db->unlock();
		
		$version['mime_type']=$this->get_mime_type($version['file_extension']);
		

		return $version;

	}

	function get_last_version($element_id)
	{
		if ( $this->debug('get_last_version') )
			print ( "get_last_version: entering with element_id=".$element_id."<br>\n");

		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_element_acl') )
			print ( "get_last_version: ".$sql."<br>\n");

		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record());
		{

			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			$version['validation_date']=$this->db->f('validation_date');
		}

		$this->db->unlock();
		
		$version['mime_type']=$this->get_mime_type($version['file_extension']);

		return $version;

	}

	function get_current_version($element_id)
	{
		$db2 = clone($this->db);

		if ( $this->debug('get_current_version') )
			print ( "get_current_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND status='current' ";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_current_version') )
			print ( "get_current_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$db2->query($sql, __LINE__, __FILE__);

		if ($db2->next_record())
		{
			
			$version['validation_date']=$db2->f('validation_date');
			$version['url']=$db2->f('url');
			$version['size']=$db2->f('size');
			$version['status']=$db2->f('status');
			$version['creator_id']=$db2->f('creator_id');
			$version['creation_date']=$db2->f('creation_date');
			$version['minor']=$db2->f('minor');
			$version['version_id']=$db2->f('version_id');
			$version['element_id']=$db2->f('element_id');
			$version['description']=$db2->f('description');
			$version['file_extension']=$db2->f('file_extension');
			$version['file_name']=$db2->f('file_name');
			$version['stored_name']=$db2->f('stored_name');
			$version['major']=$db2->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}
		else
		{
			$version=null;
		}

		$db2->unlock();
		$db2->free();		
		unset($db2);
		return $version;
	}
	
	function get_current_or_alert_version($element_id)
	{
		if ( $this->debug('get_current_or_alert_version') )
			print ( "get_current_or_alert_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='current' OR status='alert') ";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_current_or_alert_version') )
			print ( "get_current_or_alert_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_current_or_alert_or_refused_version($element_id)
	{
		$version=null;
		
		if ( $this->debug('get_current_or_alert_or_refused_version') )
			print ( "get_current_or_alert_or_refused_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='current' OR status='alert' OR status='refused' ) ";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_current_or_alert_or_refused_version') )
			print ( "get_current_or_alert_or_refused_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_current_or_pending_for_acceptation_version($element_id)
	{
		$version=null;

		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);

		if ( $this->debug('get_current_or_pending_for_acceptation_version') )
			print ( "get_current_or_pending_for_acceptation_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='current' OR status='alert' OR status='pending_for_acceptation' ) ";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_current_or_pending_for_acceptation_version') )
			print ( "get_current_or_pending_for_acceptation_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$db2->query($sql, __LINE__, __FILE__);

		if ($db2->next_record())
		{
			$version['validation_date']=$db2->f('validation_date');
			$version['url']=$db2->f('url');
			$version['size']=$db2->f('size');
			$version['status']=$db2->f('status');
			$version['creator_id']=$db2->f('creator_id');
			$version['creation_date']=$db2->f('creation_date');
			$version['minor']=$db2->f('minor');
			$version['version_id']=$db2->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$db2->f('description');
			$version['file_extension']=$db2->f('file_extension');
			$version['file_name']=$db2->f('file_name');
			$version['stored_name']=$db2->f('stored_name');
			$version['major']=$db2->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}
		
		$db2->unlock();		
		$db2->free(); 
		unset($db2);		
				
		return ($version);
	}
	
	function get_working_version($element_id)
	{
		if ( $this->debug('get_working_version') )
			print ( "get_working_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='working' )";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_working_version') )
			print ( "get_working_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_pending_for_internal_review ($element_id)
	{
		$version=null;
		
		if ( $this->debug('get_pending_for_internal_review') )
			print ( "get_pending_for_internal_review: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='pending_for_technical_review' or status='pending_for_quality_review')";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_pending_for_internal_review') )
			print ( "get_pending_for_internal_review: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_ready_for_delivery ($element_id)
	{
		if ( $this->debug('get_ready_for_delivery') )
			print ( "get_ready_for_delivery: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='ready_for_delivery' )";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_ready_for_delivery') )
			print ( "get_ready_for_delivery: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_pending_for_acceptation ($element_id)
	{
		if ( $this->debug('get_pending_for_acceptation') )
			print ( "get_pending_for_acceptation: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='pending_for_acceptation' )";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_pending_for_acceptation') )
			print ( "get_pending_for_acceptation: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}

	function get_working_or_pending_version($element_id)
	{
		if ( $this->debug('get_working_version') )
			print ( "get_working_version: entering with element_id=".$element_id."<br>\n");
		
		$sql="SELECT * FROM ".$this->tables['versions']." ";
		$sql.="WHERE element_id=".$element_id." ";
		$sql.="AND ( status='working' OR status='pending_for_technical_review' OR status='pending_for_quality_review' OR status='pending_for_acceptation' )";
		$sql.="ORDER BY version_id DESC LIMIT 1";

		if ( $this->debug('get_working_version') )
			print ( "get_working_version: ".$sql."<br>\n");

		//print ("<pre>");
		//print_r($this->db->metadata( $this->tables['versions'] ));
		//print ("</pre>");
		
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			
			$version['validation_date']=$this->db->f('validation_date');
			$version['url']=$this->db->f('url');
			$version['size']=$this->db->f('size');
			$version['status']=$this->db->f('status');
			$version['creator_id']=$this->db->f('creator_id');
			$version['creation_date']=$this->db->f('creation_date');
			$version['minor']=$this->db->f('minor');
			$version['version_id']=$this->db->f('version_id');
			$version['element_id']=$this->db->f('element_id');
			$version['description']=$this->db->f('description');
			$version['file_extension']=$this->db->f('file_extension');
			$version['file_name']=$this->db->f('file_name');
			$version['stored_name']=$this->db->f('stored_name');
			$version['major']=$this->db->f('major');
			$version['file_full_path']=$this->datadir.'/'.$version['stored_name'];
			
			$version['mime_type']=$this->get_mime_type($version['file_extension']);
			
		}

		$this->db->unlock();
				
		return $version;
	}


	function get_parent_id($item_id)
	{
		$sql="SELECT parent_id FROM ".$this->tables['elements']." ";
		$sql.="WHERE element_id=".$item_id."";

		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
			$out=$this->db->f('parent_id');
		else
			$out=0;

		$this->db->unlock();

		return $out;
	}


	function get_path($focused_id)
	{
		$current_id=$focused_id;
		$path[]=$current_id;

		while ($current_id !=0)
		{
			$current_id=$this->get_parent_id($current_id);
			$path[]=$current_id;

		}
		return $path;
	}

	function is_on_path($element, $path)
	{
		foreach ($path as $node_id)
		{
			if ($element['element_id']==$node_id)
				return true;
		}
		return false;
	}
	

	function can_read($element_id)
	{
		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);
		
		if ( $this->admin )
		{
			$result=true;
		}
		else
		{
			$sql0="SELECT ".$this->tables['elements'].".* ";
			$sql0.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql0.="WHERE ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql0.="AND ".$this->tables['elements'].".element_id=".$element_id." ";
			$sql0.="AND (".$this->sqlaclread.") ";
			
			$db2->query($sql0, __LINE__, __FILE__);
			
			$result=($db2->next_record() ||  $element_id==0);
			
			$db2->unlock();
		}
		
		$db2->free(); 
		unset($db2);
		return ($result );	
	}

	function can_write($element_id)
	{
		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);

		if ( $this->admin )
		{
			$result=true;
		}
		else
		{
			$sql0="SELECT ".$this->tables['elements'].".* ";
			$sql0.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql0.="WHERE ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql0.="AND ".$this->tables['elements'].".element_id=".$element_id." ";
			$sql0.="AND (".$this->sqlaclwrite.") ";
			
			$db2->query($sql0, __LINE__, __FILE__);
			
			$result=($db2->next_record());
			
			$db2->unlock();
		}
				
		$db2->free(); 
		unset($db2);		
		return ($result );	
	}
	
	function can_change_file_lock($element_id)
	{
		$out=false;
		$element=$this->get_element_info($element_id);
		
		if ( $element['lock_status'] == 0 && $this->can_write($element_id) )
		{
			$out=true;
		}
		elseif ( $element['lock_status'] == 1 && ( $this->admin || ($element['lock_user_id'] == $GLOBALS['phpgw_info']['user']['account_id'] && $this->can_write($element_id))) )
		{
			$out=true;
		}
		
		return( $out );
	}
	
	function can_change_acl($element_id)
	{
		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);

		if ( $this->admin )
		{
			$result=true;
		}
		else
		{
			$sql0="SELECT ".$this->tables['elements'].".* ";
			$sql0.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql0.="WHERE ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql0.="AND ".$this->tables['elements'].".element_id=".$element_id." ";
			$sql0.="AND (".$this->sqlaclchangeacl.") ";
			
			$db2->query($sql0, __LINE__, __FILE__);
			
			$result=($db2->next_record());
			
			$db2->unlock();
		}

		$db2->free(); 
		unset($db2);		
		return ($result );	
	}
	
	function can_delete($element_id)
	{
		if ( $this->admin )
		{
			$result=true;
		}
		else
		{
			$result=false;
		}
		
		return ( $result );
	}
	

	// DONE acl management 
	function list_elements($parent_id=0, $type='', $order='name')
	{

		if ( $this->admin )
		{
			$sql="SELECT * FROM ".$this->tables['elements']." ";
			$sql.="WHERE parent_id=".$parent_id." ";
			$sql.="AND element_id !=parent_id ";
			if ($type !="" )
				$sql.="AND type='".$type."'";
			$sql.="ORDER by type desc, name asc";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
		}
		elseif ( $this->can_read($parent_id) )
		{
			$sql="SELECT DISTINCT ".$this->tables['elements'].".* ";
			$sql.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql.="WHERE ".$this->tables['elements'].".parent_id=".$parent_id." ";
			$sql.="AND ".$this->tables['elements'].".element_id !=".$this->tables['elements'].".parent_id ";
			if ($type !="" )
				$sql.="AND ".$this->tables['elements'].".type='".$type."' ";
			$sql.="AND ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql.="AND (".$this->sqlaclread.") ";
			
			$sql.="ORDER by type desc, name asc";
			
			$this->db->query($sql, __LINE__, __FILE__);
		}
		//print ( $sql );

		$elements=Array();

		$i=0;
		while ($this->db->next_record())
		{
			$the_element_id=$this->db->f('element_id');
			$the_element_type=$this->db->f('type');
			
			if ( $the_element_type == "file" )
			{
				if ( $this->admin || $this->can_write($the_element_id) )
				{
					$go=true;
				}
				else
				{
					$the_current_version=null;
					$the_current_version=$this->get_current_or_pending_for_acceptation_version($the_element_id);
					
					if ( is_array($the_current_version))
					{
						//if ( $the_element_id == 496 )
						//_debug_array($the_current_version);
						$go=true;
					}
					else
					{
						$go=false;
					}
				}
			}
			elseif ( $the_element_type == "folder" )
			{
				$go=true;
			}
				
			if ( $go == true )
			{
				$elements[$i]['element_id']=$the_element_id;
				$elements[$i]['name']=$this->db->f('name');
				$elements[$i]['parent_id']=$this->db->f('parent_id');
				$elements[$i]['reference']=$this->db->f('reference');
				$elements[$i]['type']=$the_element_type;
				$elements[$i]['creator_id']=$this->db->f('creator_id');
				$elements[$i]['owner_id']=$this->db->f('owner_id');
				$elements[$i]['creation_date']=$this->db->f('creation_date');
				$elements[$i]['lock_status']=$this->db->f('lock_status');
				$elements[$i]['lock_user_id']=$this->db->f('lock_user_id');
				$elements[$i]['description']=$this->db->f('description');
				$elements[$i]['validity_period']=$this->db->f('validity_period');
				$elements[$i]['project_root']=$this->db->f('project_root');
	
				$i ++;
			}
		}

		$this->db->unlock();

		return $elements;

	}
	
	function list_sub_folders ( $element_id )
	{
		return( $this->list_elements($element_id, 'folder'));
	}

	function list_versions($element_id)
	{
		$versions=null;

		if ( $this->debug('list_version') )
			echo "list_versions: entering with element_id=".$element_id."<br/>\n";

		if ( $this->admin || $this->can_write($element_id))
		{
			if ( $this->debug('list_version') )
				echo "list_versions: can write<br/>\n";

			$sql1="SELECT ALL ";
			$sql1.="version_id, element_id, description, creation_date, ";
			$sql1.="status, major, minor, size, ";
			$sql1.="creator_id, validation_date, file_extension, ";
			$sql1.="file_name, stored_name ";
	
			$sql1.="FROM ".$this->tables['versions']." ";
			$sql1.="WHERE element_id=".$element_id." ";
			$sql1.="ORDER BY version_id ";
		}
		else
		{
			if ( $this->debug('list_version') )
				echo "list_versions: can read<br/>\n";

			$sql1="SELECT ALL ";
			$sql1.="version_id, element_id, description, creation_date, ";
			$sql1.="status, major, minor, size, ";
			$sql1.="creator_id, validation_date, file_extension, ";
			$sql1.="file_name, stored_name ";
	
			$sql1.="FROM ".$this->tables['versions']." ";
			$sql1.="WHERE element_id=".$element_id." ";
			$sql1.="AND ( status='current' OR status='pending_for_acceptation') ";
			$sql1.="ORDER BY version_id ";		
		}

		if ( $this->debug('list_version') )
			print ("list_versions: SQL ".$sql1."<br/>\n");

		$this->db->query($sql1, __LINE__, __FILE__);
		$nn=$this->db->num_rows();

		if ( $this->debug('list_version') )
			print ("list_versions: SQL done ( ".$nn." lines )<br/>\n");

		$ii=0;
		while ($this->db->next_record())
		{
			if ( $this->debug('list_version') )
				print ("list_versions: parsing iteration ii= ".$ii."<br/>\n");

			$versions[$ii]['version_id']=$this->db->f('version_id');
			$versions[$ii]['element_id']=$this->db->f('element_id');
			$versions[$ii]['description']=$this->db->f('description');
			$versions[$ii]['status']=$this->db->f('status');
			$versions[$ii]['major']=$this->db->f('major');
			$versions[$ii]['minor']=$this->db->f('minor');
			$versions[$ii]['creator_id']=$this->db->f('creator_id');
			$versions[$ii]['creation_date']=$this->db->f('creation_date');
			$versions[$ii]['size']=$this->db->f('size');
			$versions[$ii]['file_extension']=$this->db->f('file_extension');
			$versions[$ii]['file_name']=$this->db->f('file_name');
			$versions[$ii]['stored_name']=$this->db->f('stored_name');
			$versions[$ii]['file_full_path']=$this->datadir.'/'.$versions[$ii]['stored_name'];
			$versions[$ii]['validation_date']=$this->db->f('validation_date');

			$ii++;
		}
		
		for ( $jj=0;$jj< $ii; $jj++)
			$versions[$jj]['mime_type']=$this->get_mime_type($versions[$jj]['file_extension']);

		$this->db->unlock();

	if ( $this->debug('list_version') )
		print ("list_versions: fin<br/>\n");
					
		return $versions;
	}

	function get_mime_type($file_extension)
	{
		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);

		if ( $this->debug('get_mime_type') )
			print ("get_mime_type: debut<br/>\n");

		$sql_model="SELECT mime_type FROM %s WHERE file_extension='%s';";

		$sql=sprintf($sql_model, $this->tables['mimetypes'], $file_extension);

		if ( $this->debug('get_mime_type') )
			print ("get_mime_type: ".$sql."<br/>\n");
						
		$db2->query($sql, __LINE__, __FILE__);

		if ($db2->next_record())
			$out=$db2->f('mime_type');
		else
			$out="default";
		
		if ( $this->debug('get_mime_type') )
			print ("get_mime_type: ".$out."<br/>\n");		

		$db2->free(); 
		unset($db2);		
		return $out;

	}
	
	function get_chrono_order ( $BaseRef )
	{
		$sql="select reference from ged_elements where reference like '".$BaseRef."%'";
		
		$this->db->query($sql, __LINE__, __FILE__);
		
		while ($this->db->next_record())
		{
			$list[]=$this->db->f('reference');
		}
		
		$i=1;
		
		if ( is_array($list))
			while ( in_array($BaseRef."-".$i, $list) )
				$i++;
		
		return ($i);
	}
	
	function list_elements_of_type ( $product_type )
	{
		// db2 neededbecause can_read can be called during a $this->db loop;		
		$db2 = clone($this->db);

		//TODO : limiter en fonction des droits
		$sql="select * from ged_elements where doc_type='".$product_type."'";
		$db2->query($sql, __LINE__, __FILE__);

		$ii=0;

		while ($db2->next_record())
		{

			$elements[$ii]['element_id']=$db2->f('element_id');
			$elements[$ii]['name']=$db2->f('name');
			$elements[$ii]['reference']=$db2->f('reference');
			$elements[$ii]['description']=$db2->f('description');
			$elements[$ii]['validity_period']=$db2->f('validity_period');
			// TODO : ADD 'validity_period' DONE
			$ii ++;
		}

		$db2->unlock();
		$db2->free(); 
		unset($db2);		

		return $elements;
	
	}
	
	/**
	* Generic functions for ACL management
	*/
	
	/**
	* Get acl for a ged element
	*
	* @param integer $element_id
	* @return array of acl
	*/
	function get_element_acl ( $element_id )
	{
		if ( $this->debug('get_element_acl') )
			print ( "get_element_acl: ".$element_id."<br/>\n" );

		$acl=null;
		
		//TODO : limiter en fonction des droits
		$sql="select * from ged_acl where element_id=".$element_id;
		$this->db->query($sql, __LINE__, __FILE__);

		while ($this->db->next_record())
		{
			$account_id=$this->db->f('account_id');

			if ( $this->debug('get_element_acl') )
				print ( "get_element_acl: account_id=".$account_id."<br/>\n" );
			
			$acl[$account_id]['acl_id']=$this->db->f('acl_id');
			$acl[$account_id]['element_id']=$this->db->f('element_id');
			$acl[$account_id]['account_id']=$account_id;
			$acl[$account_id]['read']=$this->db->f('aclread');
			$acl[$account_id]['write']=$this->db->f('aclwrite');
			$acl[$account_id]['changeacl']=$this->db->f('aclchangeacl');

		}
		
		//print ( "<br/>\n" );
		
		$this->db->unlock();
		
		return ($acl);
	}
	
	function get_acl_info ( $acl_id )
	{
		//TODO : limiter en fonction des droits ?
		$sql="SELECT ged_acl.* FROM  ged_acl WHERE ged_acl.acl_id=".$acl_id;
		
		$this->db->query($sql, __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			$acl_info['acl_id']=$this->db->f('acl_id');
			$acl_info['element_id']=$this->db->f('element_id');
			$acl_info['account_id']=$this->db->f('account_id');
			$acl_info['read']=$this->db->f('aclread');
			$acl_info['write']=$this->db->f('aclwrite');
			$acl_info['changeacl']=$this->db->f('aclchangeacl');
		}

		$this->db->unlock();

		return $acl_info;
	} 

	
	function get_element_acl_candidates ( $element_id )
	{
		//TODO : limiter en fonction des droits
		$sql="SELECT phpgw_accounts.* FROM  phpgw_accounts LEFT JOIN ged_acl ON ( ged_acl.account_id=phpgw_accounts.account_id and ged_acl.element_id=".$element_id.") WHERE ged_acl.element_id is null;";
		$this->db->query($sql, __LINE__, __FILE__);

		$ii=0;

		while ($this->db->next_record())
		{

			$acl[$ii]['account_id']=$this->db->f('account_id');
			$acl[$ii]['account_type']=$this->db->f('account_type');
			$ii ++;
		}

		$this->db->unlock();

		return $acl;
	}
	
	function ensure_read_on_path($element_id, $account_id )
	{
			//print ( "ensure_read_on_path($element_id, $account_id )" );
			//print ( "<br/>");

			$element_acl=$this->get_element_acl ( $element_id );
			$element_acl_for_account=$element_acl[$account_id];
			
			if ( is_array($element_acl_for_account) )
			{
				//print ( "<pre>");
				//print_r($element_acl_for_account);
				//print ( "</pre>");
				
				$aclread=1;
				$aclwrite=$element_acl_for_account['write'];
				$aclchangeacl=$element_acl_for_account['changeacl'];
				
				//print ( "set_acl(".$element_acl_for_account['acl_id'].", $aclread, $aclwrite, $aclchangeacl, false, false)" );
				//print ( "<br/>");
				$this->set_acl($element_acl_for_account['acl_id'], $aclread, $aclwrite, $aclchangeacl, false, false);
			}
			else
			{
				//print ( "new_acl($element_id, $account_id, 1, 'null', 'null', false, false)" );
				//print ( "<br/>");
				$this->new_acl($element_id, $account_id, 1, 'null', 'null', false, false);
			}
		
		if ( $element_id !="0" )
		{
			$parent_id=$this->get_parent_id($element_id);
			$this->ensure_read_on_path($parent_id, $account_id );
		}
		
	}
	
	function new_acl($element_id, $account_id, $aclread, $aclwrite, $aclchangeacl, $recursive=false, $check_read_on_path=true)
	{
		//print ( "new_acl $element_id, $account_id, $aclread, $aclwrite, $aclchangeacl" );
		//print ( "<br/>");
		
		if ($aclread=="" || $aclread=="null" )
			$aclread="null";
		else
			$aclread=1;
			
		if ($aclwrite=="" || $aclwrite=="null")
			$aclwrite="null";
		else
		{
			$aclread=1;
			$aclwrite=1;
		}
		
		if ($aclchangeacl=="" || $aclchangeacl=="null")
			$aclchangeacl="null";
		else
		{
			$aclchangeacl=1;
			$aclread=1;
			$aclwrite=1;
		}

		if ( $recursive )
		{
			$children_elements=$this->list_elements($element_id);
			
			if ( ! empty($children_elements))
				foreach ( $children_elements as $child_element )
				{
					$child_element_acl=$this->get_element_acl ( $child_element['element_id']);
					@$child_element_acl_for_account=$child_element_acl[$account_id];
					
					if ( is_array($child_element_acl_for_account) )
					{
						$this->set_acl($child_element_acl_for_account['acl_id'], $aclread, $aclwrite, $aclchangeacl, true, false);
											}
					else
					{
						$this->new_acl($child_element['element_id'], $account_id, $aclread, $aclwrite, $aclchangeacl, true, false);
					}
			}
		}
		
		if ( $aclread !='null' )
		{
			$sql="INSERT INTO ged_acl ( element_id, account_id, aclread, aclwrite, aclchangeacl ) VALUES ( ".$element_id.", ".$account_id.", ".$aclread.", ".$aclwrite.", ".$aclchangeacl.")";
			
			//print ( $sql );
			//print ( "<br/>");
			
			$this->db->query($sql, __LINE__, __FILE__);

			$this->db->unlock();
		}
		
		if ( $check_read_on_path && $aclread !='null'  )
		{
			$parent_id=$this->get_parent_id($element_id);
			$this->ensure_read_on_path($parent_id, $account_id);
		}
		

	
	}
	
	
	// Positionnement des droits par d�faut
	// Full droits au groupe Admin (huhu)
	// Full droits au createur (presque huhu)
	// Heritage des droits du parent
	function set_default_acl($element_id)
	{
		$owner_id=$GLOBALS['phpgw_info']['user']['account_id'];
		$this->new_acl($element_id, $owner_id, 1, 1, 1);
		$admin_id=$GLOBALS['phpgw']->accounts->name2id('Admins');
		$this->new_acl($element_id, $admin_id, 1, 1, 1);
		
		//h�ritage des droits du parent
		$parent_id=$this->get_parent_id($element_id);
		
		$parent_acl=$this->get_element_acl ( $parent_id );
		if ( is_array($parent_acl)) 
		foreach ( $parent_acl as $ac )
		{
			if ( $ac['account_id'] !=$owner_id && $ac['account_id'] !=$admin_id )
				$this->new_acl($element_id, $ac['account_id'], $ac['read'],$ac['write'], $ac['change_acl']);
		
		}
		
		
	}
	
	function set_acl($acl_id, $aclread, $aclwrite, $aclchangeacl, $recursive=false, $check_read_on_path=true)
	{
		//print ( "set_acl $acl_id, $aclread, $aclwrite, $aclchangeacl $recursive");
		//print ( "<br/>");
		//$this->db->debug=true;
		
		$acl_info=$this->get_acl_info ( $acl_id );
		
		$element_id=$acl_info['element_id'];
		$account_id=$acl_info['account_id'];
		
		if ($aclread=="" || $aclread=="null" )
			$aclread="null";
		else
			$aclread=1;
			
		if ($aclwrite=="" || $aclwrite=="null")
			$aclwrite="null";
		else
		{
			$aclread=1;
			$aclwrite=1;
		}
		
		if ($aclchangeacl=="" || $aclchangeacl=="null")
			$aclchangeacl="null";
		else
		{
			$aclchangeacl=1;
			$aclread=1;
			$aclwrite=1;
		}
		
				
		if ( $recursive )
		{
			$children_elements=$this->list_elements($element_id);
			
			if ( is_array($children_elements))
				foreach ( $children_elements as $child_element )
				{
					$child_element_acl=null;
					$child_element_acl=$this->get_element_acl ( $child_element['element_id'] );
					
					$child_element_acl_for_account=null;
					@$child_element_acl_for_account=$child_element_acl[$account_id];
					
					if ( ! empty($child_element_acl_for_account) )
					{
						$this->set_acl($child_element_acl_for_account['acl_id'], $aclread, $aclwrite, $aclchangeacl, true, false);
					}
					else
					{
						$this->new_acl($child_element['element_id'], $account_id, $aclread, $aclwrite, $aclchangeacl, true, false);
					}
			}
		}
		
		if ( $aclread=='null' )
			$this->rm_acl($acl_id);
		else
		{
			$sql="UPDATE ged_acl set aclread=".$aclread.", aclwrite=".$aclwrite.", aclchangeacl=".$aclchangeacl." WHERE acl_id=".$acl_id;
			
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();
		}
			
		//Control remontant
		if ( $check_read_on_path && $aclread !='null'  )
		{
			$parent_id=$this->get_parent_id($element_id);
			$this->ensure_read_on_path($parent_id, $acl_info['account_id']);
		}
		
	}

	function rm_acl($acl_id)
	{
		$sql="DELETE FROM ged_acl WHERE acl_id=".$acl_id;
		
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();
	}
	
	function rm_all_acl ( $element_id )
	{
		$sql="DELETE FROM ged_acl WHERE element_id=".$element_id;
		
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->unlock();
	}


	function select_periods()
	{
		$sql="SELECT distinct * from ged_periods ORDER BY period";

		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$periods[$i]['period']=$this->db->f('period');
			$periods[$i]['description']=$this->db->f('description');
			$i ++;
		}
		return $periods;
	}
	
	// Say file is OK
	// User must have acceptation rights on this document

	function accept_file ( $element_id, $comment='accepted', $comment_file=null )
	{
		$current_or_alert_version=$this->get_current_or_alert_version($element_id);
		$working_or_pending_version=$this->get_working_or_pending_version($element_id);
		$element=$this->get_element_info($element_id);
		
		$result=false;
		
		// Need to check if there is a working version
		if (is_array($working_or_pending_version) )
		{
			if ($working_or_pending_version['version_id'] )
			{
				// If there is a previous "current" make it obsolete
				if (is_array($current_or_alert_version) )
				{
					if ($current_or_alert_version['version_id'] )
					{
						$sql="UPDATE ged_versions set status='obsolete' WHERE version_id=".$current_or_alert_version['version_id']." ";
						$sql.="AND status != 'refused'";		
						$this->db->query($sql, __LINE__, __FILE__);
						$this->db->unlock();
						
						// TODO : Set status of depending documents to alert
						$versions_referring_to=$this->list_versions_referring_to($current_or_alert_version['version_id']);
						
						if ( is_array($versions_referring_to))
						{
							foreach ( $versions_referring_to as $version_referring_to)
							{
								$this->alert_version ($version_referring_to['version_id']);
							}
						}
					}
				}
				
				$sql="UPDATE ged_versions set status='current', validation_date=".time()." WHERE version_id=".$working_or_pending_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();

				if (isset($comment_file) && $comment_file['file_name'] != "")
				{
					$new_file=$comment_file;
					$new_file['doc_type']=$this->external_review_file_type;
					$new_file['name']=$this->get_type_desc($new_file['doc_type'])." / ".$element['name'];
					$new_file['description']=$comment;
					$new_file['reference']=$this->get_next_available_reference($new_file['doc_type'], $element['project_root'] );
					$new_file['major']=1;
					$new_file['minor']=0;
					$new_file['validity_period']=0;
					
					$new_place=null;
					$new_place=$this->get_type_place($new_file['doc_type'],$element['project_root']);
					if ( !isset($new_place))
					{
						$new_place=$element['parent_id'];
					}
					$new_file['parent_id']=$new_place;
					
					$new_id=$this->add_file($new_file);

					$new_version=$this->get_last_version($new_id);
					$new_relations[0]['linked_version_id']=$working_or_pending_version['version_id'];
					$new_relations[0]['relation_type']='comment';
					
					$this->set_relations($new_version['version_id'],$new_relations);
					
				}
				
				$this->store_history ('accepted', $comment, $working_or_pending_version['version_id']);
				$result=true;
			}
		}
		else
		{
			$result=false;
		}
		// TODO : check if all is ok
		return ( $result );
	}

	function alert_version ( $version_id )
	{
		$sql="UPDATE ged_versions set status='alert' WHERE version_id=".$version_id." AND status='current'";		
		$this->db->query($sql, __LINE__, __FILE__);
		
		// TODO : recursivite
						
		$this->db->unlock();
		
		$this->store_history ('alerted', 'alerted', $version_id['version_id']);
	}

	// Say file is not valid

	function reject_file ( $element_id, $comment='rejected', $comment_file=null )
	{
		$pending_version=$this->get_pending_for_internal_review($element_id);
		$element=$this->get_element_info($element_id);
		
		$result=false;
		
		// Need to check if there is a working version
		if (is_array($pending_version) )
		{
			if ($pending_version['version_id'] )
			{
				$next_status='working';
				
				$sql="UPDATE ged_versions set status='".$next_status."' WHERE version_id=".$pending_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();
				
				if (isset($comment_file) && $comment_file['file_name'] != "")
				{
					$new_file=$comment_file;
					
					$new_file=$comment_file;
					$new_file['doc_type']=$this->internal_review_file_type;
					$new_file['name']=$this->get_type_desc($new_file['doc_type'])." / ".$element['name'];
					$new_file['description']=$comment;
					$new_file['reference']=$this->get_next_available_reference($new_file['doc_type'], $element['project_root'] );
					$new_file['major']=1;
					$new_file['minor']=0;
					$new_file['validity_period']=0;
					
					$new_place=null;
					$new_place=$this->get_type_place($new_file['doc_type'],$element['project_root']);
					if ( !isset($new_place))
					{
						$new_place=$element['parent_id'];
					}
					$new_file['parent_id']=$new_place;
					
					$new_id=$this->add_file($new_file);
					
					$new_version=$this->get_last_version($new_id);
					$new_relations[0]['linked_version_id']=$pending_version['version_id'];
					$new_relations[0]['relation_type']='comment';
					
					$this->set_relations($new_version['version_id'],$new_relations);
					
				}
				
				$this->store_history ('rejected', $comment, $pending_version['version_id']);
								
				$result=true;
			}
		}
		else
		{
			$result=false;
		}
		// TODO : check if all is ok
		return ( $result );
	}
	
	// Say file is not valid
	// A new version must be worked on and delivered
	// User must have acceptation rights on this document

	function refuse_file ( $element_id, $comment='rejected', $comment_file=null )
	{
		$pending_version=$this->get_pending_for_acceptation($element_id);
		$element=$this->get_element_info($element_id);
		
		$result=false;
		
		// Need to check if there is a working version
		if (is_array($pending_version) )
		{
			if ($pending_version['version_id'] )
			{
				// Set "pending_for_internal_review" status
				// TODO : Add submission date
				$sql="UPDATE ged_versions set status='refused' WHERE version_id=".$pending_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();
				
				if (isset($comment_file) && $comment_file['file_name'] != "")
				{
					
					$new_file=$comment_file;
					$new_file['doc_type']=$this->external_review_file_type;
					$new_file['name']=$this->get_type_desc($new_file['doc_type'])." / ".$element['name'];
					$new_file['description']=$comment;
					$new_file['reference']=$this->get_next_available_reference($new_file['doc_type'], $element['project_root'] );
					$new_file['major']=1;
					$new_file['minor']=0;
					$new_file['validity_period']=0;
					
					$new_place=null;
					$new_place=$this->get_type_place($new_file['doc_type'],$element['project_root']);
					if ( !isset($new_place))
					{
						$new_place=$element['parent_id'];
					}
					$new_file['parent_id']=$new_place;
					
					$new_id=$this->add_file($new_file);

					$new_version=$this->get_last_version($new_id);
					$new_relations[0]['linked_version_id']=$pending_version['version_id'];
					$new_relations[0]['relation_type']='comment';
					
					$this->set_relations($new_version['version_id'],$new_relations);
					
				}
				
				$this->store_history ('refused', $comment, $pending_version['version_id']);
								
				$result=true;
			}
		}
		else
		{
			$result=false;
		}
		// TODO : check if all is ok
		return ( $result );
	}
	
	// Submit file to customer for acceptation

	// After a contractual timeout the file is
	// considered accepted
	function deliver_file ( $element_id )
	{
		$pending_version=$this->get_ready_for_delivery($element_id);
		
		// Need to check if there is a working version
		if (is_array($pending_version) )
		{
			if ($pending_version['version_id'] )
			{
				// Set "pending_for_internal_review" status
				// TODO : Add submission date
				$sql="UPDATE ged_versions set status='pending_for_acceptation' WHERE version_id=".$pending_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();
				
				$this->store_history ('delivered', 'delivered', $pending_version['version_id']);
			}
		}
	}

	// submit file for internal acceptation then delivery
	// the file must be working
	// and the performer of this action must have editor role
	function submit_file ( $element_id )
	{
		$working_version=$this->get_working_version($element_id);
		
		// Need to check if there is a working version
		if (is_array($working_version) )
		{
			if ($working_version['version_id'] )
			{
				// Set "pending_for_internal_review" status
				// TODO : Add submission date
				$sql="UPDATE ged_versions set status='pending_for_technical_review' WHERE version_id=".$working_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();
				
				$this->store_history ('submitted', 'submitted', $working_version['version_id']);
			}
		}		
	}

	function approve_file ( $element_id, $comment='approved', $comment_file=null )
	{
		//_debug_array($comment_file);
		
		$pending_version=$this->get_pending_for_internal_review($element_id);
		$element=$this->get_element_info($element_id);
		
		$result=false;
		
		// Need to check if there is a working version
		if (is_array($pending_version) )
		{
			if ($pending_version['version_id'] )
			{
				// Set "pending_for_internal_review" status
				// TODO : Add submission date
				if ( $pending_version['status']=='pending_for_technical_review' )
					$next_status='pending_for_quality_review';
				elseif ($pending_version['status']=='pending_for_quality_review' )
					$next_status='ready_for_delivery';
				else
					$next_status=$pending_version['status'];
				
				$sql="UPDATE ged_versions set status='".$next_status."' WHERE version_id=".$pending_version['version_id'];			
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->unlock();
				
				if (isset($comment_file) && $comment_file['file_name'] != "")
				{
					$new_file=$comment_file;
					$new_file['doc_type']=$this->internal_review_file_type;
					$new_file['name']=$this->get_type_desc($new_file['doc_type'])." / ".$element['name'];
					$new_file['description']=$comment;
					$new_file['reference']=$this->get_next_available_reference($new_file['doc_type'], $element['project_root'] );
					$new_file['major']=1;
					$new_file['minor']=0;
					$new_file['validity_period']=0;
					
					$new_place=null;
					$new_place=$this->get_type_place($new_file['doc_type'],$element['project_root']);
					if ( !isset($new_place))
					{
						$new_place=$element['parent_id'];
					}
					$new_file['parent_id']=$new_place;
					
					$new_id=$this->add_file($new_file);

					$new_version=$this->get_last_version($new_id);
					$new_relations[0]['linked_version_id']=$pending_version['version_id'];
					$new_relations[0]['relation_type']='comment';
					
					$this->set_relations($new_version['version_id'],$new_relations);
					
				}

				$this->store_history ('approved', $comment, $pending_version['version_id']);
								
				$result=true;
			}
		}
		else
		{
			$result=false;
		}
		// TODO : check if all is ok
		return ( $result );
	}

	// relations management
	
	function list_version_relations_out ( $version_id )
	{
		if ( is_numeric($version_id) )
		{
			$sql="SELECT ged_relations.*, ged_versions.*, ged_elements.* ";
			$sql.="FROM (ged_relations INNER JOIN ged_versions on ged_relations.linked_version_id=ged_versions.version_id) ";
			$sql.="INNER JOIN ged_elements ON ged_versions.element_id = ged_elements.element_id WHERE linking_version_id=".$version_id;
			
			$this->db->query($sql);
	
			$i=0;
			while ($this->db->next_record())
			{
				$element_id=$this->db->f('element_id');
				
				if ( $this->can_read($element_id) )
				{
					$relations[$i]['element_id']=$element_id;
					$relations[$i]['version_id']=$this->db->f('version_id');
					$relations[$i]['name']=$this->db->f('name');
					$relations[$i]['status']=$this->db->f('status');
					$relations[$i]['reference']=$this->db->f('reference');
					$relations[$i]['major']=$this->db->f('major');
					$relations[$i]['minor']=$this->db->f('minor');
					$i ++;
				}
			}
				
			$this->db->unlock();
		}
		
		if ( isset($relations))
			return ($relations);
		else
			return null;			
	}

	function list_version_relations_in ( $version_id )
	{
		if ( is_numeric($version_id) )
		{
			$sql="SELECT ged_relations.*, ged_versions.*, ged_elements.* ";
			$sql.="FROM (ged_relations INNER JOIN ged_versions on ged_relations.linking_version_id=ged_versions.version_id) ";
			$sql.="INNER JOIN ged_elements ON ged_versions.element_id = ged_elements.element_id WHERE linked_version_id=".$version_id;
			
			$this->db->query($sql);
	
			$i=0;
			while ($this->db->next_record())
			{
				$element_id=$this->db->f('element_id');
				
				if ( $this->can_read($element_id) )
				{
					$relations[$i]['element_id']=$element_id;
					$relations[$i]['version_id']=$this->db->f('version_id');
					$relations[$i]['name']=$this->db->f('name');
					$relations[$i]['status']=$this->db->f('status');
					$relations[$i]['reference']=$this->db->f('reference');
					$relations[$i]['major']=$this->db->f('major');
					$relations[$i]['minor']=$this->db->f('minor');
					$i ++;
				}
			}
				
			$this->db->unlock();
		}
		
		if ( isset($relations))
			return ($relations);
		else
			return null;			
	}

	function list_versions_referring_to ( $version_id )
	{
		if ( is_numeric($version_id) )
		{
			$sql="SELECT ged_relations.*, ged_versions.*, ged_elements.* ";
			$sql.="FROM (ged_relations INNER JOIN ged_versions on ged_relations.linking_version_id=ged_versions.version_id) ";
			$sql.="INNER JOIN ged_elements ON ged_versions.element_id = ged_elements.element_id WHERE linked_version_id=".$version_id;
			
			$this->db->query($sql);
	
			$i=0;
			while ($this->db->next_record())
			{
				$element_id=$this->db->f('element_id');
				
				if ( $this->can_read($element_id) )
				{
					$relations[$i]['element_id']=$element_id;
					$relations[$i]['version_id']=$this->db->f('version_id');
					$relations[$i]['name']=$this->db->f('name');
					$relations[$i]['status']=$this->db->f('status');
					$relations[$i]['reference']=$this->db->f('reference');
					$relations[$i]['major']=$this->db->f('major');
					$relations[$i]['minor']=$this->db->f('minor');
					$i ++;
				}
			}
				
			$this->db->unlock();
		}
		
		if ( isset($relations))
			return ($relations);
		else
			return null;			
	}
	
	// Home board functions

	// Project filtering
	function list_available_projects()
	{

		if ( $this->admin )
		{
			$sql="SELECT * FROM ".$this->tables['elements']." ";
			$sql.="WHERE project_root=element_id ";
			
			$this->db->query($sql, __LINE__, __FILE__);
		}
		else
		{
			$sql="SELECT DISTINCT ".$this->tables['elements'].".* ";
			$sql.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql.="WHERE ".$this->tables['elements'].".project_root=".$this->tables['elements'].".element_id ";
			$sql.="AND ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql.="AND (".$this->sqlaclread.") ";
			
			$sql.="ORDER by project_name asc";
			
			$this->db->query($sql, __LINE__, __FILE__);
		}
		//print ( $sql );

		$projects=Array();

		while ($this->db->next_record())
		{
			$i=$this->db->f('element_id');
			
			$projects[$i]=$this->db->f('name');
		}

		$this->db->unlock();

		return $projects;

	}

	function list_wanted_projects()
	{
		if ( $this->admin )
		{
			$sql="SELECT * FROM ".$this->tables['elements']." ";
			$sql.="WHERE ".$this->tables['elements'].".project_root=".$this->tables['elements'].".element_id ";
		}
		else
		{
			$sql="SELECT DISTINCT ".$this->tables['elements'].".* ";
			$sql.="FROM ".$this->tables['elements'].", ".$this->tables['acl']." ";
			$sql.="WHERE ".$this->tables['elements'].".project_root=".$this->tables['elements'].".element_id ";
			$sql.="AND ".$this->tables['elements'].".element_id=".$this->tables['acl'].".element_id ";
			$sql.="AND (".$this->sqlaclread.") ";

		}

		if ( @is_array($GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] ))
		{
			$sql.="AND ( ";
			$vor="";

			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}

			$sql.=") ";
		}

		$sql.="ORDER by project_name asc";

		$this->db->query($sql, __LINE__, __FILE__);

		$projects=Array();

		while ($this->db->next_record())
		{
			$i=$this->db->f('element_id');

			$projects[$i]=$this->db->f('name');
		}

		$this->db->unlock();

		return $projects;
	}
	
	function list_new_documents ($element_id=null)
	{
		// Get previous login time
		$session_id=$GLOBALS['phpgw_info']['user']['sessionid'];
		$account_id=$GLOBALS['phpgw_info']['user']['account_id'];
		
		$sql0="select max(li) as llt from phpgw_access_log where account_id=".$account_id." and sessionid !=\"".$session_id."\"";
		
		$this->db->query($sql0);
		
		if ( $this->db->next_record() )
			$then=0 + $this->db->f('llt');
		
		$this->db->unlock();
		
		// List new suff
		
		$sql="SELECT ged_elements.*, ged_versions.* from ged_elements INNER JOIN ged_versions ON ged_elements.element_id=ged_versions.element_id ";
		$sql.="WHERE ( ged_versions.status='current' AND ( ged_versions.validation_date >=$then ";
		$sql.="OR ( ( ged_elements.validity_period > 0 OR ged_elements.validity_period IS NOT NULL) AND ged_versions.creation_date >=$then ))) ";

		$sql.="AND ( ";
		$vor="";
			
		if (is_null($element_id))
			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}
		else
			$sql.="ged_elements.project_root =".$element_id." ";
			
		$sql.=") ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			
			if ( $this->can_read($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$this->db->f('status');
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['description']=$this->db->f('description');
				$i ++;
			}
		}
			
		$this->db->unlock();

		if ( isset($docs))
			return ($docs);
		else
			return null;
	}

	function list_documents_to_expire ($period=0)
	{
		if ( $period==0 )
			$period=$GLOBALS['phpgw_info']['user']['preferences']['ged']['warn_acceptation_within'];
			
		$now=time();
		$then=$now+$period*24*3600;
		
		$sql="SELECT ged_elements.*, ged_current_version.* ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ged_current_version.status='current' ) ";
		$sql.="GROUP BY ged_elements.element_id ";
		$sql.="HAVING ged_elements.validity_period IS NOT NULL ";
		$sql.="AND ged_elements.validity_period > 0 ";
		$sql.="AND ( ged_elements.validity_period+ged_current_version.validation_date < $then ";
		$sql.="OR  ged_current_version.validation_date IS NULL ) ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			
			if ( $this->can_read($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['status']=$this->db->f('status');
				$docs[$i]['description']=$this->db->f('description');
				$docs[$i]['validity_period']=$this->db->f('validity_period');
				$docs[$i]['validation_date']=$this->db->f('validation_date');
				$docs[$i]['expiration_date']=$docs[$i]['validity_period']+$docs[$i]['validation_date'];
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;
	
	}

	function list_pending_documents ($element_id=null )
	{
		$sql="SELECT ged_elements.*, ged_current_version.* ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ( ged_current_version.status='pending_for_technical_review' or ged_current_version.status='pending_for_quality_review' or ged_current_version.status='pending_for_acceptation' or ged_current_version.status='ready_for_delivery' )) ";
		$sql.="WHERE ( ";
		$vor="";
			
		if (is_null($element_id))
			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}
		else
			$sql.="ged_elements.project_root =".$element_id." ";

		$sql.=") ";

		$sql.="GROUP BY ged_elements.element_id ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			$version_status=$this->db->f('status');
			
			if ( $this->can_write($element_id) || $version_status == "pending_for_acceptation" && $this->can_read($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$version_status;
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['description']=$this->db->f('description');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;
	
	}

	function list_working_documents ($element_id=null )
	{
					
		$sql="SELECT ged_elements.*, ged_current_version.* ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ( ged_current_version.status='working' )) ";

		$sql.="WHERE ( ";
		$vor="";
			
		if (is_null($element_id))
			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}
		else
			$sql.="ged_elements.project_root =".$element_id." ";

		$sql.=") ";

		$sql.="GROUP BY ged_elements.element_id ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			
			if ( $this->can_write($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$this->db->f('status');
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['description']=$this->db->f('description');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;
	
	}

	function list_alert_documents ($element_id=null )
	{
					
		$sql="SELECT ged_elements.*, ged_current_version.* ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ( ged_current_version.status='alert' )) ";

		$sql.="WHERE ( ";
		$vor="";
			
		if (is_null($element_id))
			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}
		else
			$sql.="ged_elements.project_root =".$element_id." ";

		$sql.=") ";

		$sql.="GROUP BY ged_elements.element_id ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			
			if ( $this->can_write($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$this->db->f('status');
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['description']=$this->db->f('description');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;
	
	}

	function list_refused_documents ($element_id=null)
	{
					
		$sql="SELECT ged_elements.*, ged_current_version.*, max(ged_last_version.version_id) as last_version_id ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ( ged_current_version.status='refused' )) ";
		$sql.="INNER JOIN ged_versions as ged_last_version ";
		$sql.="ON ged_elements.element_id=ged_last_version.element_id ";

		$sql.="WHERE ( ";
		$vor="";
		
		if (is_null($element_id))
			foreach ( $GLOBALS['phpgw_info']['user']['preferences']['ged']['show_projects'] as $id => $project )
			{
				$sql.=$vor."ged_elements.project_root =".$id." ";
				$vor="OR ";
			}
		else
			$sql.="ged_elements.project_root =".$element_id." ";

		$sql.=") ";

		$sql.="GROUP BY ged_elements.element_id ";
		$sql.="HAVING last_version_id=ged_current_version.version_id ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			
			if ( $this->can_write($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$this->db->f('status');
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['description']=$this->db->f('description');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;
	
	}
	
	// History
	function get_history ( $element_id)
	{
		if ( $this->admin || $this->can_write($element_id))
		{	
			$sql="SELECT ".$this->tables['history'].".*, ".$this->tables['versions'].".major, ".$this->tables['versions'].".minor FROM ".$this->tables['history']." INNER JOIN ".$this->tables['versions']." ";
			$sql.="ON ".$this->tables['history'].".version_id = ".$this->tables['versions'].".version_id ";
			$sql.="WHERE ".$this->tables['history'].".element_id=".$element_id." ";
			$sql.="ORDER BY ".$this->tables['history'].".logdate ASC";			
		}
		else
		{
			$sql="SELECT ".$this->tables['history'].".*, ".$this->tables['versions'].".major, ".$this->tables['versions'].".minor FROM ".$this->tables['history']." INNER JOIN ".$this->tables['versions']." ";
			$sql.="ON ".$this->tables['history'].".version_id = ".$this->tables['versions'].".version_id ";
			$sql.="WHERE ".$this->tables['history'].".element_id=".$element_id." ";
			$sql.="AND (".$this->tables['history'].".status='current' OR ".$this->tables['history'].".status='pending_for_acceptation') ";
			$sql.="ORDER BY ".$this->tables['history'].".logdate ASC";
		}
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$history[$i]['element_id']=$element_id;
			$history[$i]['version_id']=$this->db->f('version_id');
			$history[$i]['status']=$this->db->f('status');
			$history[$i]['logdate']=$this->db->f('logdate');
			$history[$i]['action']=$this->db->f('action');
			$history[$i]['account_id']=$this->db->f('account_id');
			$history[$i]['comment']=$this->db->f('comment');
			$history[$i]['ip']=$this->db->f('ip');
			$history[$i]['agent']=$this->db->f('agent');
			$history[$i]['major']=$this->db->f('major');
			$history[$i]['minor']=$this->db->f('minor');
			
			$i++;

		}
				
		$this->db->unlock();
		
		if ( isset($history))
			return ($history);
		else
			return null;  	
	}
	
	function store_history ($action, $comment, $version_id)
	{
		$version_info=$this->get_version_info($version_id);
		
		$sql="INSERT INTO ".$this->tables['history']. "(element_id, version_id, status, logdate, action, account_id, comment, ip, agent) ";
		$sql.="VALUES (".$version_info['element_id'].", ".$version_info['version_id'].", '".$version_info['status']."', '".time()."', '".$action."', ".intval($GLOBALS['phpgw_info']['user']['account_id']).", '".$comment."', 'ip', 'agent')";
		
		$this->db->query($sql);
		$this->db->unlock();
	}
	
  // Searching  
  function search($query, $start_date=null, $end_date=null, $status=null )
  {
		$sql="SELECT ged_elements.*, ged_current_version.*, ged_elements.description as descriptione, ged_current_version.description as descriptionv ";
		$sql.="FROM ( ged_elements ";
		$sql.="INNER JOIN ged_versions as ged_current_version ";
		$sql.="ON ged_elements.element_id=ged_current_version.element_id ";
		$sql.="AND ( ged_current_version.status='current' OR ged_current_version.status='working' OR ged_current_version.status='pending_for_technical_review' ";
		$sql.="OR ged_current_version.status='pending_for_quality_review' OR ged_current_version.status='ready_for_delivery' OR ged_current_version.status='pending_for_acceptation' )) ";
		$sql.="WHERE ged_elements.name like '%".$query."%' OR ged_elements.description like '%".$query."%' OR ged_elements.reference like '%".$query."%' ";
		$sql.="OR  ged_current_version.description like '%".$query."%' ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			$version_status=$this->db->f('status');
			
			if ( $this->can_write($element_id) || ( $version_status == "pending_for_acceptation" || $version_status == "current" ) && $this->can_read($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['version_id']=$this->db->f('version_id');
				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['status']=$version_status;
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['reference']=$this->db->f('reference');
				
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['description']=$this->db->f('descriptione');
				$docs[$i]['descriptionv']=$this->db->f('descriptionv');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;  	
  }
  
  function get_stats ($start_date=null, $end_date=null, $status=null, $project_root_id=null)
  {
		$sql="SELECT ged_history.*, ged_elements.name, ged_elements.reference, ged_elements.description descriptione, ";
		$sql.="ged_versions.major, ged_versions.minor, ged_versions.description descriptionv ";
		$sql.="FROM (ged_history JOIN ged_elements on ged_history.element_id = ged_elements.element_id) ";
		$sql.="JOIN ged_versions on ged_history.version_id = ged_versions.version_id ";
		$sql.="WHERE ged_history.logdate >= ".$start_date." AND ged_history.logdate <= ".$end_date." ";
		
		if ( isset($status))
			$sql .="AND ged_history.status='".$status."' ";
			
		if ( isset($project_root_id))
			$sql.="AND ged_elements.project_root =".$project_root_id." ";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$element_id=$this->db->f('element_id');
			$version_id=$this->db->f('version_id');
			$version_status=$this->db->f('status');
			
			if ( $this->can_write($element_id) || ( $version_status == "pending_for_acceptation" || $version_status == "current" || $version_status == "refused") && $this->can_read($element_id) )
			{
				$docs[$i]['element_id']=$element_id;
				$docs[$i]['version_id']=$version_id;
				$docs[$i]['status']=$version_status;

				$docs[$i]['name']=$this->db->f('name');
				$docs[$i]['major']=$this->db->f('major');
				$docs[$i]['minor']=$this->db->f('minor');
				$docs[$i]['reference']=$this->db->f('reference');
				
				$docs[$i]['reference']=$this->db->f('reference');
				$docs[$i]['description']=$this->db->f('descriptione');
				$docs[$i]['descriptionv']=$this->db->f('descriptionv');
				$i ++;
			}
		}
			
		$this->db->unlock();
		
		if ( isset($docs))
			return ($docs);
		else
			return null;  	
  }

	// Type management
	function list_doc_types ($show_notype=true)
	{		
		
		$i=0;
		
		if ( $show_notype == true )
		{
			
			$list[$i]['type_id'] = "";
			$list[$i]['type_desc'] = lang('No type');
			$list[$i]['type_ref'] = "";
			$list[$i]['type_chrono'] = 0;
			$i++;
		}

		$sql="SELECT * from ged_doc_types order by type_desc asc";
		$this -> db -> query($sql, __LINE__, __FILE__);
		
		while ($this -> db -> next_record())
		{
			$list[$i]['type_id'] = $this -> db -> f('type_id');
			$list[$i]['type_desc'] = $this -> db -> f('type_desc');
			$list[$i]['type_ref'] = $this -> db -> f('type_ref');
			$list[$i]['type_chrono'] = $this -> db -> f('type_chrono');

			$i ++;
		}
		return $list;	
	}

	function is_chrono_type($doc_type)
	{
		$db2 = clone($this->db);
		$out=false;
		$sql="SELECT * FROM ged_doc_types WHERE type_id='".$doc_type."' and type_chrono=1";
		
		$db2->query($sql, __LINE__, __FILE__);
			
		$out=($db2->next_record());
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
	}

	function get_type_desc($doc_type)
	{
		$db2 = clone($this->db);
		$out=null;
		$sql="SELECT * FROM ged_doc_types WHERE type_id='".$doc_type."'";
		
		$db2->query($sql, __LINE__, __FILE__);
			
		if($db2->next_record())
		{
			$out=$db2->f('type_desc');
		}
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
	}
	
	function get_type_std_ref($doc_type)
	{
		$db2 = clone($this->db);
		$out=null;
		$sql="SELECT * FROM ged_doc_types WHERE type_id='".$doc_type."'";
		
		$db2->query($sql, __LINE__, __FILE__);
			
		if($db2->next_record())
		{
			$out=$db2->f('type_ref');
		}
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
	}

	function add_doc_types($doc_types)
	{
		foreach ( $doc_types as $old_type_id => $doc_type )
		{
			if ( isset($doc_type['type_chrono']) && $doc_type['type_chrono'] == "on" )
			{
				$chrono_flag=1;
			}
			else
			{
				$chrono_flag=0;
			}
			 
			$sql="select * FROM ged_doc_types ";
			$sql.="WHERE type_id='".$doc_type['type_id']."'";
			
			$this -> db -> query($sql, __LINE__, __FILE__);
			
			$nogo=($this->db->next_record());
			$this->db->unlock();
			
			$sql2="INSERT INTO ged_doc_types (type_id, type_desc, type_ref, type_chrono) values ( '".$doc_type['type_id']."', '".addslashes($doc_type['type_desc'])."', '".$doc_type['type_ref']."', ".$chrono_flag.") ";

			$this -> db->query($sql2, __LINE__, __FILE__);
			$this->db->unlock();		
		}
	}

	function update_doc_types($doc_types)
	{
		foreach ( $doc_types as $old_type_id => $doc_type )
		{
			if ( isset($doc_type['type_chrono']) && $doc_type['type_chrono'] == "on" )
			{
				$chrono_flag=1;
			}
			else
			{
				$chrono_flag=0;
			}
			 
			$sql="UPDATE ged_doc_types set type_id='".$doc_type['type_id']."', type_desc='".addslashes($doc_type['type_desc'])."', type_ref='".$doc_type['type_ref']."', type_chrono=".$chrono_flag." ";
			$sql.="WHERE type_id='".$old_type_id."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();
			
			$sql2="UPDATE ged_elements set doc_type='".$doc_type['type_id']."' ";
			$sql2.="WHERE doc_type='".$old_type_id."'";

			$this->db-> query($sql2, __LINE__, __FILE__);
			$this->db->unlock();
			
			$sql3="UPDATE ged_types_places set type_id='".$doc_type['type_id']."' ";
			$sql3.="WHERE type_id='".$old_type_id."'";

			$this->db-> query($sql3, __LINE__, __FILE__);
			$this->db->unlock();
			
					
		}
	}

	function delete_doc_types($doc_types)
	{
		foreach ( $doc_types as $old_type_id => $doc_type )
		{			 
			$sql="DELETE FROM ged_doc_types ";
			$sql.="WHERE type_id='".$old_type_id."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();
			
			$sql2="UPDATE ged_elements set doc_type='' ";
			$sql2.="WHERE doc_type='".$old_type_id."'";

			$this->db-> query($sql2, __LINE__, __FILE__);
			$this->db->unlock();		
		}
	}

	function get_project_base_ref($project_root_id)
	{
		$db2 = clone($this->db);
		$out=null;
		$sql="SELECT reference FROM ged_elements WHERE element_id=".$project_root_id;
		
		$db2->query($sql, __LINE__, __FILE__);
			
		if($db2->next_record())
		{
			$out=$db2->f('reference');
		}
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
	}
	
	function list_projects()
	{
		
	}

	function get_project_name($project_root_id)
	{
		$db2 = clone($this->db);
		$out=null;
		$sql="SELECT project_name FROM ged_elements WHERE element_id=".$project_root_id;
		
		$db2->query($sql, __LINE__, __FILE__);
			
		if($db2->next_record())
		{
			$out=$db2->f('project_name');
		}
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
	}

	function get_next_available_reference($doc_type, $project_root_id )
	{
		$project_base_ref=$this->get_project_base_ref((int)$project_root_id);
		$type_std_ref=$this->get_type_std_ref($doc_type);
		$type_base_ref=$project_base_ref."/".$type_std_ref;
		
		$db2 = clone($this->db);
		$sql="SELECT reference FROM ged_elements WHERE reference like '".$type_base_ref."%' order by element_id desc limit 1";
		$db2->query($sql, __LINE__, __FILE__);
		
		if($db2->next_record())
		{
			$last=$db2->f('reference');
			preg_match("@^(".$type_base_ref."[-\/]*)?([0-9]+)@i", $last, $splittage);
			$next=(int)$splittage[2]+1;
			$out=$type_base_ref."-".$next;
		}
		else
			$out=$type_base_ref."-1";
			
		$db2->unlock();		
		$db2->free(); 
		unset($db2);
		
		return ($out );	
		
	}

	function get_type_place($doc_type, $project_root_id=null)
	{
		if ( isset($project_root_id))
		{
			$db2 = clone($this->db);
			$sql="SELECT element_id FROM ged_types_places WHERE project_root=".$project_root_id." AND type_id='".$doc_type."'";
			$db2->query($sql, __LINE__, __FILE__);
			
			if($db2->next_record())
			{
				$result=$db2->f('element_id');
			}
			else
			{
				$result=null;
			}
				
			$db2->unlock();		
			$db2->free(); 
			unset($db2);			
		}
		else
		{
			$result=null;
		}
		return($result);
	}
	
	function list_types_places($project_root_id)
	{
		$places=null;
		
		// if is_project($project_root_id) ?
		$sql="SELECT * from ged_types_places WHERE project_root=".$project_root_id;
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$places[$i]['type_id']=$this->db->f('type_id');
			$places[$i]['project_root']=$this->db->f('project_root');
			$places[$i]['element_id']=$this->db->f('element_id');
			$i ++;
		}
			
		$this->db->unlock();
		
		return ($places);
	}
	
	function list_unplaced_types ($project_root_id)
	{
		$types=null;
		
		// if is_project($project_root_id) ?
		$sql="SELECT ged_doc_types.* from (ged_doc_types LEFT JOIN ged_types_places ";
		$sql.="ON ged_types_places.type_id=ged_doc_types.type_id ";		
		$sql.="AND ged_types_places.project_root=".$project_root_id.") ";
		$sql.="WHERE ged_types_places.element_id is null";
		
		$this->db->query($sql);

		$i=0;
		while ($this->db->next_record())
		{
			$types[$i]['type_id']=$this->db->f('type_id');
			$types[$i]['type_desc']=$this->db->f('type_desc');
			$types[$i]['type_ref']=$this->db->f('type_ref');
			$types[$i]['type_chrono']=$this->db->f('type_chrono');			
			$i ++;
		}
			
		$this->db->unlock();
		
		return ($types);
		
	}

	function add_places($places)
	{
		foreach ( $places as $place )
		{			 
			
			$sql="INSERT INTO ged_types_places (type_id, project_root, element_id) values ( '".addslashes($place['type_id'])."', ".(int)$place['project_root'].", ".(int)$place['element_id'].") ";

			$this -> db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();		
		}
		
	}

	function delete_places($places)
	{
		foreach ( $places as $place )
		{			 
			
			$sql="DELETE FROM ged_types_places WHERE type_id='".addslashes($place['type_id'])."' AND project_root=".(int)$place['project_root'];

			$this -> db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();		
		}
		
	}

	function update_places($places)
	{
		foreach ( $places as $place )
		{			 
			
			$sql="UPDATE ged_types_places SET element_id=".(int)$place['element_id']." ";
			$sql.="WHERE type_id='".addslashes($place['type_id'])."' AND project_root=".(int)$place['project_root'];

			$this -> db->query($sql, __LINE__, __FILE__);
			$this->db->unlock();		
		}
		
	}
	
	function list_chronos($project_root_id)
	{
		$out=null;
		
		$sql="SELECT *, ged_elements.description edescription, ged_versions.description vdescription, ";
		$sql.="ged_versions.creation_date vcreation_date, ged_elements.creator_id ecreator_id ";
		$sql.="FROM ( ged_elements ";
		$sql.="JOIN ged_doc_types ON ged_elements.project_root=".(int)$project_root_id." ";
		$sql.="AND ged_elements.doc_type = ged_doc_types.type_id AND ged_doc_types.type_chrono=1) ";
		$sql.="JOIN ged_versions On ( ged_elements.element_id = ged_versions.element_id) ";
		$sql.="ORDER BY ged_versions.creation_date";
		
		$this->db->query($sql, __LINE__, __FILE__);
				
		while($this->db->next_record())
		{
			$doc_type=$this->db->f('type_desc');
			
			if ( !isset($out[$doc_type]) )
			{
				$out[$doc_type]=Array();
			}

			$i=count($out[$doc_type])+1;

			$out[$doc_type][$i]['no']=$i;
			
			$out[$doc_type][$i]['name']=$this->db->f('name');
			$out[$doc_type][$i]['element_id']=$this->db->f('element_id');
			$out[$doc_type][$i]['date']=$this->db->f('vcreation_date');
			$out[$doc_type][$i]['creator_id']=$this->db->f('ecreator_id');

			$out[$doc_type][$i]['description']=$this->db->f('edescription')." / ".$this->db->f('vdescription');
			$out[$doc_type][$i]['version_id']=$this->db->f('version_id');

			$out[$doc_type][$i]['status']=$this->db->f('status');
			$major=$this->db->f('major');
			$minor=$this->db->f('minor');
			$out[$doc_type][$i]['version_label']=$major.".".$minor;

			$out[$doc_type][$i]['reference']=$this->db->f('reference');
			
			$i++;
		}
		
		return ($out);
	}
	
	function get_project_status($project_root)
	{
		
	}
}
?>
