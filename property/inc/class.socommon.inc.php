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
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_socommon
	{
		/**
		 * @var string $join the sql syntax to use for JOIN
		 */
		 var $join = ' INNER JOIN ';

		/**
		 * @var string $like the sql syntax to use for a case insensitive LIKE
		 */
		 var $like = 'LIKE';


		function property_socommon()
		{

			if(is_object($GLOBALS['phpgw']->db))
			{
				$this->db = CreateObject('phpgwapi.db');
			}
			else // for setup
			{
				$this->db = CreateObject('phpgwapi.db');

				if(isset($GLOBALS['phpgw_info']['server']['db_name']) && $GLOBALS['phpgw_info']['server']['db_name'])
				{
					$this->db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
					$this->db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
					$this->db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
					$this->db->User = $GLOBALS['phpgw_info']['server']['db_user'];
					$this->db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
				}
				else
				{
					$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'COOKIE');
					if(!$ConfigDomain)
					{
						$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'POST');
					}
					$GLOBALS['phpgw_info']['user']['domain'] = $ConfigDomain;
					$phpgw_domain = $GLOBALS['phpgw_domain'];
					$this->db->Host     = $phpgw_domain[$ConfigDomain]['db_host'];
					$this->db->Database = $phpgw_domain[$ConfigDomain]['db_name'];
					$this->db->User     = $phpgw_domain[$ConfigDomain]['db_user'];
					$this->db->Password = $phpgw_domain[$ConfigDomain]['db_pass'];
				}
			}

			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
			{
				case 'pgsql':
					$this->join = " JOIN ";
					$this->like = "ILIKE";
					break;
				case 'postgres':
					$this->join = " JOIN ";
					$this->like = "ILIKE";
					break;
				default:
					//do nothing for now
			}

			$this->left_join = " LEFT JOIN ";
		}

		function fm_cache($name='',$value='')
		{
			if($name && $value)
			{
				$value = serialize($value);
				$this->db->query("INSERT INTO fm_cache (name,value)VALUES ('$name','$value')",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query("SELECT value FROM fm_cache where name='$name'");
				if($this->db->next_record())
				{
					$value= unserialize($this->db->f('value'));
					return $value;
				}
			}
		}

		/**
		* Clear all content from cache
		*
		*/

		function reset_fm_cache()
		{
			$this->db->query("DELETE FROM fm_cache ",__LINE__,__FILE__);
		}

		/**
		* Clear computed userlist for location and rights from cache
		*
		* @return integer number of values was found and cleared
		*/

		function reset_fm_cache_userlist()
		{
			$this->db->query("DELETE FROM fm_cache WHERE name $this->like 'acl_userlist_%'",__LINE__,__FILE__);
			return $this->db->affected_rows();
		}

		function create_preferences($app='',$user_id='')
		{
				$this->db->query("SELECT preference_value FROM phpgw_preferences where preference_app = '$app' AND preference_owner=".(int)$user_id );
				$this->db->next_record();
				$value= unserialize($this->db->f('preference_value'));
				return $value;
		}

		function read_single_tenant($id)
		{
			$this->db->query("SELECT * FROM fm_tenant WHERE id =$id",__LINE__,__FILE__);
			$this->db->next_record();

				$tenant_data = array
				(
					'first_name'		=> $this->db->f('first_name'),
					'last_name'			=> $this->db->f('last_name'),
					'contact_phone'		=> $this->db->f('contact_phone')
				);

//_debug_array($tenant_data);

			return	$tenant_data;
		}

		function check_location($location_code='',$type_id='')
		{
			$this->db->query("SELECT count(*) FROM fm_location$type_id where location_code='$location_code'");
			$this->db->next_record();

			if ( $this->db->f(0))
			{
				return true;
			}
		}

		function select_part_of_town($district_id='')
		{
			$filter = '';
			$part_of_town = array();
			if($district_id)
			{
				$filter = "WHERE district_id = '$district_id'";
			}
			$this->db->query("SELECT name, part_of_town_id, district_id FROM fm_part_of_town $filter ORDER BY name ",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$part_of_town[] = array
				(
					'id'			=> $this->db->f('part_of_town_id'),
					'name'			=> $this->db->f('name',true),
					'district_id'	=> $this->db->f('district_id')
				);
			}

			return $part_of_town;
		}

		function select_district_list()
		{
			$this->db->query("SELECT id, descr FROM fm_district where id >'0' ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$district[$i]['id']				= $this->db->f('id');
				$district[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}

			return $district;
		}

		/**
		* Finds the next ID for a record at a table
		*
		* @param string $table tablename in question
		* @param array $key conditions
		* @return int the next id
		*/

		function next_id($table='',$key='')
		{
			$where = '';
			if(is_array($key))
			{
			//	while (is_array($key) && list($column,$value) = each($key))
				foreach ($key as $column => $value)
				{
					if($value)
					{
						$condition[] = $column . "='" . $value;
					}
				}

				$where='WHERE ' . implode("' AND ", $condition) . "'";
			}

			$this->db->query("SELECT max(id) as maximum FROM $table $where",__LINE__,__FILE__);
			$this->db->next_record();
			$next_id = $this->db->f('maximum')+1;
			return $next_id;
		}
		function get_lookup_entity($location)
		{
			$this->db->query("SELECT entity_id,name FROM fm_entity_lookup $this->join fm_entity on fm_entity_lookup.entity_id=fm_entity.id WHERE type='lookup' AND location='$location'  ");

			$i = 0;

			while ($this->db->next_record())
			{
				$entity[$i]['id']				= $this->db->f('entity_id');
				$entity[$i]['name']				= $this->db->f('name');
				$i++;
			}
			return $entity;
		}

		function get_start_entity($location)
		{
			$this->db->query("SELECT entity_id,name FROM fm_entity_lookup $this->join fm_entity on fm_entity_lookup.entity_id=fm_entity.id WHERE type='start' AND location='$location'  ");

			$i = 0;
			while ($this->db->next_record())
			{
				$entity[$i]['id']				= $this->db->f('entity_id');
				$entity[$i]['name']				= $this->db->f('name');
				$i++;
			}

			if (isset($entity))
			{
				return $entity;
			}
		}

		function increment_id($name)
		{
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name='$name'");
			$this->db->next_record();
			$next_id=$this->db->f('value') +1;

			$this->db->query("update fm_idgenerator set value = $next_id WHERE name = 'workorder'");
			return $next_id;
		}

		function new_db($db ='' )
		{
			if(is_object($db))
			{
				$db = clone($db);
			}
			else
			{
				$db = CreateObject('phpgwapi.db');
				if(isset($GLOBALS['phpgw_info']['server']['db_name']) && $GLOBALS['phpgw_info']['server']['db_name'])
				{
					$db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
					$db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
					$db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
					$db->User = $GLOBALS['phpgw_info']['server']['db_user'];
					$db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
				}
				else
				{
					$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'COOKIE');
					if(!$ConfigDomain)
					{
						$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'POST');
					}
					$phpgw_domain = $GLOBALS['phpgw_domain'];
					$GLOBALS['phpgw_info']['user']['domain'] = $ConfigDomain;
					$db->Host     = $phpgw_domain[$ConfigDomain]['db_host'];
					$db->Database = $phpgw_domain[$ConfigDomain]['db_name'];
					$db->User     = $phpgw_domain[$ConfigDomain]['db_user'];
					$db->Password = $phpgw_domain[$ConfigDomain]['db_pass'];
				}
			}

			return $db;
		}

		function get_max_location_level()
		{
			$this->db->query("SELECT count(*) as level FROM fm_location_type ");
			$this->db->next_record();
			return $this->db->f('level');
		}

		function active_group_members($group_id = '')
		{
			$this->db->query("SELECT phpgw_accounts.account_id, phpgw_accounts.account_lid FROM phpgw_acl $this->join phpgw_accounts on phpgw_acl.acl_account = phpgw_accounts.account_id"
				. " WHERE phpgw_acl.acl_location = $group_id AND phpgw_acl.acl_appname = 'phpgw_group' AND account_status = 'A'");

			while ($this->db->next_record())
			{
				$members[] = array (
				'account_id' => $this->db->f('account_id'),
				'account_name' => $this->db->f('account_lid')
				);
			}
			return $members;
		}

		/**
		* Get list of accessible physical locations for current user
		*
		* @param integer $required Right the user has to be granted at location
		*
		* @return array $access_location list of accessible physical locations
		*/

		public function get_location_list($required)
		{
			$access_list	= $GLOBALS['phpgw']->acl->get_location_list('property',$required);

			$needle = ".location.1.";
			$needle_len = strlen($needle);
			$access_location = array();
			foreach($access_list as $location)
			{
				if(strrpos($location,$needle ) === 0)
				{
					$target_len = strlen($location)- $needle_len;
					$access_location[] = substr($location,-$target_len);
				}
			}
			return $access_location;
		}
	}
