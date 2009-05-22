<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage catch
 	* @version $Id: catch_ppc.php 1993 2008-12-25 12:54:58Z sigurd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class catch_ppc
	{
		var	$function_name = 'catch_ppc';

		public function __construct()
		{
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function pre_run($data='')
		{
			phpgwapi_cache::session_set('catch', 'data', $data);

			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
				$cron = false;
			}

			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}


		function confirm($execute='')
		{
			$data = phpgwapi_cache::session_get('catch', 'data');
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'data'		=> urlencode(serialize($data)),
				'execute'	=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));


			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Export info as files'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= lang('Export info as files');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			try
			{
				$this->import_ppc();
			}
			catch(Exception $e)
			{
				$this->receipt['error'][]=array('msg'=>$e->getMessage());
			}

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$insert_values= array(
				$cron,
				date($this->db->datetime_format()),
				$this->function_name,
				implode(',',(array_keys($msgbox_data)))
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function import_ppc()
		{
			//do the actual import
			
			$valid_attachment = array
			(
				'jpg' => true
			);

 			$config = CreateObject('catch.soconfig');
 			$config->read_repository();
			$entity	= CreateObject('property.soentity');
			$entity->type = 'catch';
			$admin_entity = CreateObject('property.soadmin_entity');
			$admin_entity->type = 'catch';

			$bofiles	= CreateObject('property.bofiles');


 			foreach($config->config_data as $config_data)
 			{
 				$this->pickup_path = $config_data['pickup_path'];
 				$target = $config_data['target'];
 				$target_table = "fm_catch_{$target}";
				list($entity_id, $cat_id) = split('[_]', $target);
				$this->category_dir = "catch_{$entity_id}_{$cat_id}";
				$category			= $admin_entity->read_single_category($entity_id, $cat_id);
				$schema_text		= "{$target} {$category['name']}";

				$metadata = $this->db->metadata($target_table);
				if(!$metadata)
				{
					throw new Exception(lang('no valid target'));
				}
			
				$xmlparse = CreateObject('property.XmlToArray');
				$xmlparse->setEncoding('UTF-8');

				$file_list = $this->get_files();
 				$i = 0;
				foreach ($file_list as $file)
				{
					$var_result = $xmlparse->parseFile($file);
					$var_result = array_change_key_case($var_result, CASE_LOWER);
//_debug_array($var_result);
					//data
					$insert_values	= array();
					$cols			= array();
					foreach($metadata as $field => $field_info)
					{
						if(isset($var_result[$field]))
						{
							$insert_values[] = utf8_encode($var_result[$field]);
							$cols[]			 = $field;
						}
					}
					if($cols) // something to import
					{
						$cols[]	= 'entry_date';
						$insert_values[] = time();
						$id = $entity->generate_id(array('entity_id'=>$entity_id,'cat_id'=>$cat_id));
						$num = $entity->generate_num($entity_id, $cat_id, $id);
						$this->db->query("SELECT * FROM fm_catch_1_1 WHERE unitid ='{$var_result['unitid']}'",__LINE__,__FILE__);
						$this->db->next_record();
						$user_id = $this->db->f('user_');
						if(!$user_id)
						{
							throw new Exception(lang('no valid user for this UnitID: %1', $var_result['unitid']));
						}

						$insert_values	= $this->db->validate_insert($insert_values);
						$this->db->query("INSERT INTO $target_table (id, num, user_id, " . implode(',', $cols) . ')'
						. "VALUES ($id, $num, $user_id, $insert_values)",__LINE__,__FILE__);

						//attachment
						foreach($var_result as $field => $data)
						{
							$pathinfo = pathinfo($data);
							if(isset($pathinfo['extension']) && $valid_attachment[$pathinfo['extension']] && is_file("{$this->pickup_path}/{$data}"))
							{
								$to_file = "{$bofiles->fakebase}/{$this->category_dir}/dummy/{$id}/{$field}_{$data}"; // the dummy is for being consistant with the entity-code that relies on loc1
								$bofiles->create_document_dir("{$this->category_dir}/dummy/{$id}");
								$bofiles->vfs->override_acl = 1;

								if(!$bofiles->vfs->cp (array (
									'from'	=> "{$this->pickup_path}/{$data}",
									'to'	=> $to_file,
									'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
								{
									$this->receipt['error'][]=array('msg'=>lang('Failed to upload file %1 on id %2', $data, $num));
								}
								$bofiles->vfs->override_acl = 0;
								// move attachment
								rename("{$this->pickup_path}/{$data}", "{$this->pickup_path}/imported/{$data}");
							}
						}
						// move file
						$_file = basename($file);
						rename("{$this->pickup_path}/{$_file}", "{$this->pickup_path}/imported/{$_file}");
						$i++;
					}
				}
				$this->receipt['message'][]=array('msg'=>lang('%1 records imported to %2', $i, $schema_text));
			}
		}

		public function get_files()
		{
			$dirname = $this->pickup_path;
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| mime_content_type($file->getPathname()) != 'text/xml')
					{
						continue;
					}

					$file_list[] = (string) "{$dirname}/{$file}";
				}
			}

			return $file_list;
		}
	}
