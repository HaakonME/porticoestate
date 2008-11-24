<?php
	/**
	 * phpGroupWare custom fields
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @author Dave Hall dave.hall at skwashd.com
	 * @copyright Copyright (C) 2003-2006 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * Import the datetime class for date processing
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Custom Fields
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class phpgwapi_custom_fields
	{
		/**
		* @var array $datatype_text the translated end user field types
		*/
		protected $datatype_text = array();

		/**
		 * @var string $_appname the name of the current application
		 */
		protected $_appname;

		/**
		 * @var object $_db reference to the global database object
		 */
		protected $_db;

		/**
		 * @var string $_join SQL JOIN statement
		 */
		protected $_join;

		/**
		 * @var string $_like SQL LIKE statement
		 */
		protected $_like;

		/**
		* @var int $_total_records total number of records found
		*/
		protected $_total_records = 0;

		/**
		 * Constructor
		 *
		 * @param string $appname the name of the module using the custom fields
		 *
		 * @return void
		 */
		public function __construct($appname = null)
		{
			$this->_appname = $appname;
			if ( is_null($this->_appname) )
			{
				$this->_appname =& $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			$this->_db           	=& $GLOBALS['phpgw']->db;
			$this->_join			=& $this->_db->join;
			$this->_like			=& $this->_db->like;
			$this->_dateformat 		= phpgwapi_db::date_format();
			$this->_datetimeformat 	= phpgwapi_db::datetime_format();
			$this->_db->Halt_On_Error = 'yes';

			$this->datatype_text = array
			(
				'V'		=> lang('Varchar'),
				'I'		=> lang('Integer'),
				'C'		=> lang('char'),
				'N'		=> lang('Float'),
				'D'		=> lang('Date'),
				'T'		=> lang('Memo'),
				'R'		=> lang('Muliple radio'),
				'CH'	=> lang('Muliple checkbox'),
				'LB'	=> lang('Listbox'),
				'AB'	=> lang('Contact'),
				'VENDOR'=> lang('Vendor'),
				'email'	=> lang('Email'),
				'link'	=> lang('Link')
			);

			$this->_oProc			= createObject('phpgwapi.schema_proc', $GLOBALS['phpgw_info']['server']['db_type']);
			$this->_oProc->m_odb	=& $this->_db;
		}

		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($varname)
		{
			if ( $varname == 'total_records' )
			{
				return $this->_total_records;
			}

			return null;
		}

		/**
		 * Add a group for custom fields/attributes
		 * 
		 * @param array $attrib the field data
		 *
		 * @return int the the new custom field db pk
		 */
		public function add_group($group)
		{
			$receipt = array();

			$appname	= $group['appname'];
			$location	= $group['location'];

			// don't continue if the location is invalid
			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			if ( !$location_id )
			{
				return 0;
			}

			$values = array
			(
				'location_id'	=> $location_id,
				'id'			=> 0,
				'name'	=> $this->_db->db_addslashes(strtolower($group['group_name'])),
				'descr'	=> $this->_db->db_addslashes($group['descr']),
				'remark'	=> $this->_db->db_addslashes($group['remark']),
				'group_sort'	=> 0,
			);


			unset($group);

			$this->_db->transaction_begin();

			$sql = "SELECT id FROM phpgw_cust_attribute_group"
				. " WHERE location_id = {$values['location_id']}"
					. " AND name = '{$values['name']}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				return -1;
			}

			$sql = 'SELECT MAX(group_sort) AS max_sort, MAX(id) AS current_id'
				. ' FROM phpgw_cust_attribute_group '
				. " WHERE location_id ='{$values['location_id']}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$values['group_sort']	= $this->_db->f('max_sort') + 1;
			$values['id']	= $this->_db->f('current_id') + 1;		
			
			$cols = implode(', ', array_keys($values));
			$vals = $this->_db->validate_insert($values);

			$sql = "INSERT INTO phpgw_cust_attribute_group({$cols}) VALUES({$vals})";
			$this->_db->query($sql, __LINE__, __FILE__);

			unset($cols, $vals);

			$receipt['id'] = $values['id'];

			if ( $this->_db->transaction_commit() )
			{
				return $values['id'];
			}

			return 0;
		}
		/**
		 * Add a custom field/attribute
		 * 
		 * @param array $attrib the field data
		 * @param string $attrib_table where to append the attrib
		 * @param bool $doubled sometimes the attribute fits into a history-table as a double
		 *
		 * @return int the the new custom field db pk
		 */
		public function add($attrib, $attrib_table = null, $doubled = false)
		{
			$receipt = array();

			$appname	= $attrib['appname'];
			$location	= $attrib['location'];

			// don't continue if the location is invalid
			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			if ( !$location_id )
			{
				return 0;
			}

			$values = array
			(
				'location_id'	=> $location_id,
				'id'			=> 0,
				'column_name'	=> $this->_db->db_addslashes(strtolower($attrib['column_name'])),
				'input_text'	=> $this->_db->db_addslashes($attrib['input_text']),
				'statustext'	=> $this->_db->db_addslashes($attrib['statustext']),
				'search'		=> false,
				'list'			=> false,
				'history'		=> false,
				'lookup_form'	=> false,
				'disabled'		=> false,
				'helpmsg'		=> $this->_db->db_addslashes($attrib['helpmsg']),
				'attrib_sort'	=> 0,
				'datatype'		=> $this->_db->db_addslashes($attrib['column_info']['type']),
				'precision_'	=> (int) $attrib['column_info']['precision'],
				'scale'			=> (int) $attrib['column_info']['scale'],
				'default_value'	=> '',
				'nullable'		=> false
			);

			if ( isset($attrib['search']) )
			{
				$values['search'] = !!$attrib['search'];
			}

			if ( isset($attrib['list']) )
			{
				$values['list'] = !!$attrib['list'];
			}

			if ( isset($attrib['history']) )
			{
				$values['history'] = !!$attrib['history'];
			}

			if ( isset($attrib['lookup_form']) )
			{
				$values['lookup_form'] = !!$attrib['lookup_form'];
			}

			if ( isset($attrib['disabled']) )
			{
				$values['disabled'] = !!$attrib['disabled'];
			}

			if ( isset($attrib['default']) )
			{
				$values['default_value'] = $this->_db->db_addslashes($attrib['column_info']['default']);
			}

			switch ( $values['datatype'] )
			{
				case 'R':
				case 'CH':
				case 'LB':
				case 'AB':
				case 'VENDOR':
					if ( $attrib['history'] )
					{
						$receipt['error'][] = array('msg' => lang('History not allowed for this datatype'));
					}
					$values['history'] = false;
					break;

				default: // all is good
			}

			unset($attrib);

			$this->_db->transaction_begin();

			$sql = "SELECT id FROM phpgw_cust_attribute"
				. " WHERE location_id = {$values['location_id']}"
					. " AND column_name = '{$values['column_name']}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() && !$doubled)
			{
				return -1;
			}


			if ( !$doubled )
			{
				$sql = 'SELECT MAX(attrib_sort) AS max_sort, MAX(id) AS current_id'
					. ' FROM phpgw_cust_attribute '
					. " WHERE location_id ='{$values['location_id']}'";
				$this->_db->query($sql, __LINE__, __FILE__);
				$this->_db->next_record();
				$values['attrib_sort']	= $this->_db->f('max_sort') + 1;
				$values['id']	= $this->_db->f('current_id') + 1;		
			
				$cols = implode(', ', array_keys($values));
				$vals = $this->_db->validate_insert($values);

				$sql = "INSERT INTO phpgw_cust_attribute({$cols}) VALUES({$vals})";
				$this->_db->query($sql, __LINE__, __FILE__);
			}

			unset($cols, $vals);

			$receipt['id'] = $values['id'];

			if ( !$values['precision_'] )
			{
				$precision = $this->_translate_datatype_precision($values['datatype']);
				if ( $precision )
				{
					$values['precision_'] = $precision;
				}
			}

			$col_info = array
			(
				'type'		=> $this->_translate_datatype_insert($values['datatype']),
				'precision'	=> (int) $values['precision_'],
				'scale'		=> (int) $values['scale'],
				'default'	=> $values['default_value'],
				'nullable'	=> $values['nullable']
			);

			if ( !$col_info['default'] )
			{
				unset($col_info['default']);
			}

			if ( is_null($attrib_table) )
			{
				$attrib_table = $GLOBALS['phpgw']->locations->get_attrib_table($appname, $location);
			}

			$this->_oProc->AddColumn($attrib_table, $values['column_name'], $col_info);

			if ( $this->_db->transaction_commit() )
			{
				return $values['id'];
			}

			return 0;
		}

		/**
		 * Prepare an attribute value so it can be saved in the database
		 *
		 * @param array $values_attribute an attribute structure
		 *
		 * @return array the structure with the value prepared
		 *
		 * @internal the name of this method is misleading
		 */
		public function convert_attribute_save($values_attribute = null)
		{
			if ( !is_array($values_attribute) )
			{
				return '';
			}

			foreach ( $values_attribute as &$attrib )
			{
				if ( !$attrib['value'] )
				{
					continue;
				}

				switch ( $attrib['datatype'] )
				{
					case 'CH':
						$attrib['value'] = serialize($attrib['value']);
						break;

					case 'R':
						$attrib['value'] = $attrib['value'][0];
						break;

					case 'N':
						$attrib['value'] = str_replace(',', '.', $attrib['value']);
						break;

					case 'D':
						$ts = phpgwapi_datetime::date_to_timestamp($attrib['value']);
						$attrib['value'] = date($this->_dateformat, $ts);
						break;
				}
			}
			return $values_attribute;
		}

		/**
		 * Delete a custom field/attribute
		 * 
		 * @param string $location within an application
		 * @param string $appname where to delete the attrib
		 * @param integer $attrib_id id of attrib to delete
		 * @param bool $doubled sometimes the attribute fits into a history-table as a double
		 *
		 * @return boolean was the record deleted?
		 */
		public function delete($appname, $location, $attrib_id, $table = '',$doubled = false )
		{
			$loc_id		= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$attrib_id	= (int) $attrib_id;

			if(!$table)
			{
				$table	= $GLOBALS['phpgw']->locations->get_attrib_table($appname, $location);
			}

			$this->_db->transaction_begin();

			$sql = "SELECT column_name FROM phpgw_cust_attribute"
				. " WHERE location_id = {$loc_id} AND id = {$attrib_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( !$this->_db->next_record() )
			{
				$this->_db->transaction_abort();
				return false;
			}

			$column_name = $this->_db->f('column_name');

			$this->_oProc->DropColumn($table, false, $column_name);

			if(!$doubled) // else: wait for it - another one is coming
			{
				$sql = "SELECT group_id FROM phpgw_cust_attribute " 
					. " WHERE location_id = {$loc_id} AND id = {$attrib_id}";
				$this->_db->query($sql, __LINE__, __FILE__);
				$this->_db->next_record();
				$group_id	= $this->_db->f('group_id');

				$sql = "SELECT attrib_sort FROM phpgw_cust_attribute"
					. " WHERE location_id = {$loc_id} AND id = {$attrib_id} AND group_id = {$group_id}";
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->next_record();
				$attrib_sort	= $this->_db->f('attrib_sort');

				$sql = "SELECT MAX(attrib_sort) AS max_sort"
					. " FROM phpgw_cust_attribute WHERE location_id = {$loc_id} AND group_id = {$group_id}";
				$this->_db->query($sql, __LINE__, __FILE__);
				$this->_db->next_record();
				$max_sort	= $this->_db->f('max_sort');
			
				if ( $max_sort > $attrib_sort )
				{
					$sql = "UPDATE phpgw_cust_attribute SET attrib_sort = attrib_sort - 1"
						. " WHERE location_id = {$loc_id} AND attrib_sort > {$attrib_sort} AND group_id = {$group_id}";
					$this->_db->query($sql, __LINE__, __FILE__);
				}
				$sql = "DELETE FROM phpgw_cust_attribute"
						. " WHERE location_id = {$loc_id} AND id = {$attrib_id}";
				$this->_db->query($sql, __LINE__,__FILE__);
			}	
			return $this->_db->transaction_commit();
		}

		/**
		 * Edit a group for custom fields
		 * 
		 * @param array  $group  the field data
		 *
		 * @return integer the database id of the group
		 */
		function edit_group($group)
		{

			$location_id	= $GLOBALS['phpgw']->locations->get_id($group['appname'], $group['location']);
			$group_id		= (int) $group['id'];

			$this->_db->transaction_begin();

			$value_set = array
			(
				'name'		=> $this->_db->db_addslashes($group['group_name']),
				'descr'		=> $this->_db->db_addslashes($group['descr']),
				'remark'	=> $this->_db->db_addslashes($group['remark'])
			);

			$value_set	= $this->_db->validate_update($value_set);

			$this->_db->query("UPDATE phpgw_cust_attribute_group SET $value_set WHERE location_id = {$location_id} AND id=" . $group_id,__LINE__,__FILE__);

			if ( $this->_db->transaction_commit() )
			{
				return $group_id;
			}

			return false;
		}
		/**
		 * Edit a custom field
		 * 
		 * @param array  $attrib       the field data
		 * @param string $attrib_table which table the attribute is part of
		 * @param bool $doubled sometimes the attribute fits into a history-table as a double
		 *
		 * @return integer the database id of the attribute
		 */
		function edit($attrib, $attrib_table = '', $doubled = false)
		{
			// Checkboxes are only present if ticked, so we declare them here to stop errors
			$attrib['search'] = isset($attrib['search']) ? !!$attrib['search'] : false;
			$attrib['list'] = isset($attrib['list']) ? !!$attrib['list'] : false;
			$attrib['history'] = isset($attrib['history']) ? !!$attrib['history'] : false;
			$attrib['lookup_form'] = isset($attrib['lookup_form']) ? !!$attrib['lookup_form'] : false;

			if(!$attrib_table)
			{
				$attrib_table = $GLOBALS['phpgw']->locations->get_attrib_table($attrib['appname'],$attrib['location']);
			}

			$location_id	= $GLOBALS['phpgw']->locations->get_id($attrib['appname'], $attrib['location']);
			$attrib_id		= (int) $attrib['id'];

			$attrib['column_name'] = $this->_db->db_addslashes(strtolower($attrib['column_name']));
			$attrib['input_text'] = $this->_db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->_db->db_addslashes($attrib['statustext']);
			$attrib['helpmsg'] = $this->_db->db_addslashes($attrib['helpmsg']);
			$attrib['column_info']['default'] = $this->_db->db_addslashes($attrib['column_info']['default']);

			switch ($attrib['column_info']['type'] )
			{
				case 'R':
				case 'CH':
				case 'LB':
				case 'AB':
				case 'VENDOR':
					if ( $attrib['history'] )
					{
						$receipt['error'][] = array('msg'	=> lang('History not allowed for this datatype'));
					}
					$attrib['history'] = false;
					break;

				default: // all is good
			}

			$sql = "SELECT column_name, datatype, precision_, group_id FROM phpgw_cust_attribute " 
				. " WHERE location_id  = {$location_id} AND id = {$attrib_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( !$this->_db->next_record() )
			{
				// doesn't exist so we can't edit it
				return false;
			}

			$OldColumnName		= $this->_db->f('column_name');
			$OldDataType		= $this->_db->f('datatype');
			$OldPrecision		= $this->_db->f('precision_');
			$OldGroup			= $this->_db->f('group_id');			

			$table_def = $this->get_table_def($attrib_table);	

			$this->_db->transaction_begin();

			if( !$doubled )
			{
				$value_set = array
				(
					'input_text'	=> $attrib['input_text'],
					'statustext'	=> $attrib['statustext'],
					'search'		=> isset($attrib['search']) ? $attrib['search'] : '',
					'list'			=> isset($attrib['list']) ? $attrib['list'] : '',
					'history'		=> isset($attrib['history']) ? $attrib['history'] : '',
					'nullable'		=> $attrib['column_info']['nullable'] == 'False' ? 'False' : 'True',
					'disabled'		=> isset($attrib['disabled']) ? $attrib['disabled'] : '',
					'helpmsg'		=> $attrib['helpmsg'],
					'lookup_form'	=> isset($attrib['lookup_form']) ? $attrib['lookup_form'] : '',
					'group_id'		=> $attrib['group_id']
				);

				if($OldGroup != $attrib['group_id'])
				{
					$sql = "SELECT MAX(attrib_sort) AS max_sort FROM phpgw_cust_attribute " 
						. " WHERE location_id = {$location_id} AND group_id = {$attrib['group_id']}";
					$this->_db->query($sql,__LINE__,__FILE__);
					$this->_db->next_record();
					$max_sort	= $this->_db->f('max_sort');
					
					$value_set['attrib_sort'] = $max_sort + 1;


					$sql = "SELECT attrib_sort FROM phpgw_cust_attribute"
						. " WHERE location_id = {$location_id} AND id = {$attrib_id} AND group_id = {$OldGroup}";
					$this->_db->query($sql,__LINE__,__FILE__);
					$this->_db->next_record();
					$attrib_sort	= $this->_db->f('attrib_sort');

					$sql = "SELECT MAX(attrib_sort) AS max_sort"
						. " FROM phpgw_cust_attribute WHERE location_id = {$location_id} AND group_id = {$OldGroup}";
					$this->_db->query($sql, __LINE__, __FILE__);
					$this->_db->next_record();
					$max_sort	= $this->_db->f('max_sort');
			
					if ( $max_sort > $attrib_sort )
					{
						$sql = "UPDATE phpgw_cust_attribute SET attrib_sort = attrib_sort - 1"
							. " WHERE location_id = {$location_id} AND attrib_sort > {$attrib_sort} AND group_id = {$OldGroup}";
						$this->_db->query($sql, __LINE__, __FILE__);
					}
				}

				$value_set	= $this->_db->validate_update($value_set);

				$this->_db->query("UPDATE phpgw_cust_attribute set $value_set WHERE location_id = {$location_id} AND id=" . $attrib_id,__LINE__,__FILE__);

			}

			$this->_oProc->m_aTables = $table_def;

			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'	=> $attrib['column_name']);

				if( !$doubled )
				{
					$value_set	= $this->_db->validate_update($value_set);
					$this->_db->query("UPDATE phpgw_cust_attribute set $value_set WHERE location_id = {$location_id} AND id=" . $attrib_id,__LINE__,__FILE__);
				}

				$this->_oProc->RenameColumn($attrib_table, $OldColumnName, $attrib['column_name']);
			}

			if (($OldDataType != $attrib['column_info']['type'])
				|| ($OldPrecision != $attrib['column_info']['precision']) )
			{
				if( !$doubled )
				{
					switch ( $attrib['column_info']['type'] )
					{
						default:
							$sql = "DELETE FROM phpgw_cust_choice"
							. " WHERE location_id = {$location_id}"
							. " AND attrib_id = {$attrib_id}";
							$this->_db->query($sql, __LINE__, __FILE__);
							break;
						case 'R':
						case 'CH':
						case 'LB':
							//do nothing
					}
				}

				if(!$attrib['column_info']['precision'])
				{
					if($precision = $this->_translate_datatype_precision($attrib['column_info']['type']))
					{
						$attrib['column_info']['precision']=$precision;
					}
				}

				if(!isset($attrib['column_info']['default']))
				{
					unset($attrib['column_info']['default']);
				}

				$value_set=array(
					'column_name'	=> $attrib['column_name'],
					'datatype'		=> $attrib['column_info']['type'],
					'precision_'	=> $attrib['column_info']['precision'],
					'scale'			=> $attrib['column_info']['scale'],
					'default_value'	=> $attrib['column_info']['default'],
					'nullable'		=> $attrib['column_info']['nullable']
					);

				if( !$doubled )
				{
					$value_set	= $this->_db->validate_update($value_set);

					$sql = 'UPDATE phpgw_cust_attribute'
							. " SET {$value_set}"
							. " WHERE  location_id = {$location_id}"
								. " AND id = {$attrib_id}";
					$this->_db->query($sql ,__LINE__,__FILE__);
				}

				$attrib['column_info']['type']  = $this->_translate_datatype_insert($attrib['column_info']['type']);
				$this->_oProc->AlterColumn($attrib_table,$attrib['column_name'],$attrib['column_info']);			
			}
			
			if(isset($attrib['new_choice']) && $attrib['new_choice'] && !$doubled )
			{
				$choice_id = $this->_next_id('phpgw_cust_choice' ,array('location_id'=> $location_id, 'attrib_id'=>$attrib_id));

				$values= array(
					$location_id,
					$attrib_id,
					$choice_id,
					$attrib['new_choice']
					);

				$values	= $this->_db->validate_insert($values);

				$this->_db->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) "
				. "VALUES ($values)",__LINE__,__FILE__);
			}

			if ( count($attrib['delete_choice'])  && !$doubled )
			{
				foreach ($attrib['delete_choice'] as $choice_id)
				{
					$choice_id = (int) $choice_id;
					$sql = "DELETE FROM phpgw_cust_choice"
						. " WHERE location_id = {$location_id}"
							. " AND attrib_id = {$attrib_id}"
							. " AND id = {$choice_id}";
					$this->_db->query($sql, __LINE__, __FILE__);
				}
			}

			if ( $this->_db->transaction_commit() )
			{
				return true;
			}

			return false;
		}


		/**
		 * Get a list of attributes
		 * 
		 * @param string $appname      the name of the application
		 * @param string $location     the name of the location
		 * @param ?????? $start        ask sigurd
		 * @param ?????? $query        ask sigurd
		 * @param ?????? $sort         ask sigurd
		 * @param ?????? $order        ask sigurd
		 * @param ?????? $allrows      ask sigurd
		 * @param ?????? $inc_choices  ask sigurd
		 * @param ?????? $filtermethod THIS IS INSECURE - code that relies on this is broken by design
		 *
		 * @return ???? something
		 */
		public function find($appname, $location, $start = 0, $query = '', $sort = 'ASC', 
				$order = 'attrib_sort', $allrows = false, $inc_choices = false, $filtermethod = '')
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$start			= (int) $start;
			$query			= $this->_db->db_addslashes($query);
			$order			= $this->_db->db_addslashes($order);
			$allrows		= !!$allrows;
			// Drop raw SQL
			$filtermethod	= '';

			$ordermethod = 'ORDER BY attrib_sort ASC';
			if ( $order )
			{
				$sort = 'ASC';
				if ( $sort == 'DESC')
				{
					$sort = 'DESC';
				}

				$ordermethod = "ORDER BY {$order} {$sort}";
			}

			$querymethod = '';
			if ( $query )
			{
				$querymethod = "AND (phpgw_cust_attribute.column_name {$this->_like} '%{$query}%'"
					. " OR phpgw_cust_attribute.input_text {$this->_like} '%{$query}%')";
			}

			$sql = "FROM phpgw_cust_attribute "
				. " WHERE location_id = {$location_id}"
					. " AND custom = 1 $querymethod $filtermethod";

			$this->_total_records = 0;
			$this->_db->query("SELECT COUNT(*) AS cnt_rec {$sql}",__LINE__,__FILE__);
			if ( !$this->_db->next_record() )
			{
				return array();
			}

			$this->_total_records = $this->_db->f('cnt_rec');

			$sql = "SELECT * {$sql} {$ordermethod}";

			if ( $allrows )
			{
				$this->_db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->_db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$attribs = array();
			while ( $this->_db->next_record() )
			{
				$id = $this->_db->f('id');
				$attribs[$id] = array
				(
					'id'				=> $id,
					//'attrib_id'			=> $this->_db->f('id'), // FIXME
					'entity_type'		=> $this->_db->f('type_id'),
					'group_id'			=> $this->_db->f('group_id'),					
					'attrib_sort'		=> (int) $this->_db->f('attrib_sort'),
					'list'				=> $this->_db->f('list'),
					'lookup_form'		=> $this->_db->f('lookup_form'),
					'entity_form'		=> $this->_db->f('entity_form'),
					'column_name'		=> $this->_db->f('column_name'),
					'name'				=> $this->_db->f('column_name'),
					'size'				=> $this->_db->f('size'),
					'statustext'		=> $this->_db->f('statustext', true),
					'input_text'		=> $this->_db->f('input_text', true),
					'type_name'			=> $this->_db->f('type'),
					'datatype'			=> $this->_db->f('datatype'),
					'search'			=> $this->_db->f('search'),
					'trans_datatype'	=> $this->translate_datatype($this->_db->f('datatype')),
					'nullable'			=> ($this->_db->f('nullable') == 'True'),
					//'allow_null'		=> ($this->_db->f('nullable') == 'True'), // FIXME
					'history'			=> $this->_db->f('history'),
					'disabled'			=> $this->_db->f('disabled'),
					'helpmsg'			=> !!$this->_db->f('helpmsg')

				);
			}

			if ( $inc_choices )
			{
				foreach ( $attribs as &$attrib )
				{
					switch ( $attrib['datatype'] )
					{
						default:
							// bail out nothing to do
							break;
						case 'R':
						case 'CH':
						case 'LB':
							$attrib['choice'] = $this->_get_choices($location_id, $attrib['id']);
					}
				}
			}

			return $attribs;
		}
		/**
		 * Get a list of groups availlable for attributes within a location
		 * 
		 * @param string $appname      the name of the application
		 * @param string $location     the name of the location
		 * @param ?????? $start        ask sigurd
		 * @param ?????? $query        ask sigurd
		 * @param ?????? $sort         ask sigurd
		 * @param ?????? $order        ask sigurd
		 * @param ?????? $allrows      ask sigurd
		 *
		 * @return ???? something
		 */
		public function find_group($appname, $location, $start = 0, $query = '', $sort = 'ASC', 
				$order = 'group_sort', $allrows = false)
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$start			= (int) $start;
			$query			= $this->_db->db_addslashes($query);
			$order			= $this->_db->db_addslashes($order);
			$allrows		= !!$allrows;

			$ordermethod = 'ORDER BY group_sort ASC';
			if ( $order )
			{
				$sort = 'ASC';
				if ( $sort == 'DESC')
				{
					$sort = 'DESC';
				}

				$ordermethod = "ORDER BY {$order} {$sort}";
			}

			$querymethod = '';
			if ( $query )
			{
				$querymethod = "AND (phpgw_cust_attribute_group.name {$this->_like} '%{$query}%'";
			}

			$sql = "FROM phpgw_cust_attribute_group "
				. " WHERE location_id = {$location_id} {$querymethod}";

			$this->_total_records = 0;
			$this->_db->query("SELECT COUNT(*) AS cnt_rec {$sql}",__LINE__,__FILE__);
			if ( !$this->_db->next_record() )
			{
				return array();
			}

			$this->_total_records = $this->_db->f('cnt_rec');

			$sql = "SELECT * {$sql} {$ordermethod}";

			if ( $allrows )
			{
				$this->_db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->_db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$attrib_groups = array();
			while ( $this->_db->next_record() )
			{
				$attrib_groups[] = array
				(
					'id'				=> $this->_db->f('id'),
					'group_sort'		=> (int) $this->_db->f('group_sort'),
					'name'				=> $this->_db->f('name', true),
					'descr'				=> $this->_db->f('descr', true),
					'remark'			=> $this->_db->f('remark', true)
				);
			}

			return $attrib_groups;
		}

		/**
		* Read a single attribute group record
		*
		* @param string  $appname     the name of the module for the attribute
		* @param string  $location    the name of the location of the attribute
		* @param integer $id          the id of the attribute
		*
		* @return array the attribute record
		*/
		public function get_group($appname, $location, $id)
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$id = (int) $id;

			$sql = "SELECT * FROM phpgw_cust_attribute_group "
				. " WHERE location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			if ( !$this->_db->next_record() )
			{
				return null;
			}

			$group = array
			(
				'id'			=> $this->_db->f('id'),
				'group_name'	=> $this->_db->f('name', true),
				'descr'			=> $this->_db->f('descr', true),
				'remark'		=> $this->_db->f('remark', true),
				'group_sort'	=> $this->_db->f('group_sort')
			);

			return $group;
		}
		/**
		* Read a single attribute record
		*
		* @param string  $appname     the name of the module for the attribute
		* @param string  $location    the name of the location of the attribute
		* @param integer $id          the id of the attribute
		* @param boolean $inc_choices include choices if a lookup field
		*
		* @return array the attribute record
		*/
		public function get($appname, $location, $id, $inc_choices = true)
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$id = (int) $id;

			$sql = "SELECT phpgw_cust_attribute.* FROM phpgw_cust_attribute " 
				. " WHERE location_id = {$location_id}"
					. " AND phpgw_cust_attribute.id=$id";
			$this->_db->query($sql, __LINE__, __FILE__);

			if ( !$this->_db->next_record() )
			{
				return null;
			}

			$attrib = array
			(
				'id'			=> $this->_db->f('id'),
				'group_id'		=> $this->_db->f('group_id'),
				'column_name'	=> $this->_db->f('column_name', true),
				'input_text'	=> $this->_db->f('input_text', true),
				'statustext'	=> $this->_db->f('statustext', true),
				'type_id'		=> $this->_db->f('type_id'),
				'type_name'		=> $this->_db->f('type_name'),
				'lookup_form'	=> $this->_db->f('lookup_form'),
				'list'			=> !!$this->_db->f('list'),
				'search'		=> !!$this->_db->f('search'),
				'history'		=> !!$this->_db->f('history'),
				'location_id'	=> $this->_db->f('location_id'),
				// FIXME this is broken it should be a small int and used as a bool
				'nullable'		=> $this->_db->f('nullable') == 'True',
				// FIXME this isn't needed
				//'allow_null'	=> $this->_db->f('nullable') == 'True',
				'disabled'		=> !!$this->_db->f('disabled'),
				'helpmsg'		=> $this->_db->f('helpmsg', true),
				'column_info'	=> array
									(
										'precision'	=> $this->_db->f('precision_'),
										'scale'		=> $this->_db->f('scale'),
										'default'	=> $this->_db->f('default_value', true),
										// more duplicated values
										'nullable'	=> $this->_db->f('nullable'),
										'type'		=> $this->_db->f('datatype')
									)
			);

			if ( $inc_choices )
			{
				switch ( $this->_db->f('datatype') )
				{
					default:
						// bail out quickly
						break;
					case 'R':
					case 'CH':
					case 'LB':
						$attrib['choice'] = $this->_get_choices($location_id, $id);
						break;
				}
			}
			return $attrib;
		}

		/**
		 * Get the definition of a table
		 *
		 * @param string $table     the name of the table to look up
		 * @param array  $table_def ask sigurd
		 *
		 * @return array the table structure
		 */
		public function get_table_def($table = '', $table_def = array())
		{
			if( !$GLOBALS['phpgw_setup']->_oProc 
				|| !is_object($GLOBALS['phpgw_setup']->_oProc) )
			{
				$GLOBALS['phpgw_setup']->oProc =& $this->_oProc;
			}

			$setup = createobject('phpgwapi.setup_process');
			$tableinfo = $setup->sql_to_array($table);

			$fd = '$fd = array(' . str_replace("\t",'',$tableinfo[0]) .');';

			eval($fd);
			$table_def[$table]['fd'] = isset($table_def[$table]['fd']) && $table_def[$table]['fd'] ? $table_def[$table]['fd'] + $fd : $fd;
			$table_def[$table]['pk'] = isset($table_def[$table]['pk']) && $table_def[$table]['pk'] ? $table_def[$table]['pk'] : $tableinfo[1];
			$table_def[$table]['fk'] = isset($table_def[$table]['fk']) && $table_def[$table]['fk'] ? $table_def[$table]['fk'] : $tableinfo[2];		
			$table_def[$table]['ix'] = isset($table_def[$table]['ix']) && $table_def[$table]['ix'] ? $table_def[$table]['ix'] : $tableinfo[3];
			$table_def[$table]['uc'] = isset($table_def[$table]['uc']) && $table_def[$table]['uc'] ? $table_def[$table]['uc'] : $tableinfo[4];

			return $table_def;
		}

		/**
		 * Resort an attribute's position in relation to other attributes
		 * 
		 * @param int $id the attribute db pk
		 * @param string $resort the direction to move the field [up|down]
		 */
		public function resort_group($id, $resort, $appname, $location)
		{
			$id		= (int) $id;

			if ( $resort == 'down' )
			{
				$resort = 'down';
			}
			else
			{
				$resort	= 'up';
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$this->_db->transaction_begin();

			$sql = "SELECT group_sort FROM phpgw_cust_attribute_group " 
				. " WHERE location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$attrib_sort	= $this->_db->f('group_sort');

			$sql = "SELECT MAX(group_sort) AS max_sort FROM phpgw_cust_attribute_group " 
				. " WHERE location_id = {$location_id}";
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_db->next_record();
			$max_sort	= $this->_db->f('max_sort');

			$update = false;
			switch($resort)
			{
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$new_sort = $attrib_sort + 1;
						$update = true;
					}
					break;

				case 'up':
				default:
					if($attrib_sort>1)
					{
						$new_sort = $attrib_sort - 1;
						$update = true;
					}
					break;
			}

			if ( !$update )
			{
				// nothing to do
				return true;
			}

			$sql = "UPDATE phpgw_cust_attribute_group SET group_sort = {$attrib_sort}"
				. " WHERE location_id = {$location_id} AND group_sort = {$new_sort}";
			$this->_db->query($sql, __LINE__, __FILE__);

			$sql = "UPDATE phpgw_cust_attribute_group SET group_sort = {$new_sort}"
				. " WHERE location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			return $this->_db->transaction_commit();
		}

		/**
		 * Resort an attribute's position in relation to other attributes
		 * 
		 * @param int $id the attribute db pk
		 * @param string $resort the direction to move the field [up|down]
		 */
		public function resort($id, $resort, $appname, $location)
		{
			$id		= (int) $id;

			if ( $resort == 'down' )
			{
				$resort = 'down';
			}
			else
			{
				$resort	= 'up';
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$this->_db->transaction_begin();

			$sql = "SELECT group_id FROM phpgw_cust_attribute " 
				. " WHERE location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$group_id	= $this->_db->f('group_id');

			$sql = "SELECT attrib_sort FROM phpgw_cust_attribute " 
				. " WHERE location_id = {$location_id} AND id = {$id} AND group_id = {$group_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$attrib_sort	= $this->_db->f('attrib_sort');

			$sql = "SELECT MAX(attrib_sort) AS max_sort FROM phpgw_cust_attribute " 
				. " WHERE location_id = {$location_id} AND group_id = {$group_id}";
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_db->next_record();
			$max_sort	= $this->_db->f('max_sort');

			$update = false;
			switch($resort)
			{
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$new_sort = $attrib_sort + 1;
						$update = true;
					}
					break;

				case 'up':
				default:
					if($attrib_sort>1)
					{
						$new_sort = $attrib_sort - 1;
						$update = true;
					}
					break;
			}

			if ( !$update )
			{
				// nothing to do
				return true;
			}

			$sql = "UPDATE phpgw_cust_attribute SET attrib_sort = {$attrib_sort}"
				. " WHERE location_id = {$location_id} AND attrib_sort = {$new_sort} AND group_id = {$group_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			$sql = "UPDATE phpgw_cust_attribute SET attrib_sort = {$new_sort}"
				. " WHERE location_id = {$location_id} AND id = {$id} AND group_id = {$group_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			return $this->_db->transaction_commit();
		}

		/**
		 * Convert a datatype to a human readable label
		 *
		 * @param string $datatype the dataype to convert
		 *
		 * @return string the user readable string
		 */
		public function translate_datatype($datatype)
		{
			if ( isset($this->datatype_text[$datatype]) )
			{
				return $this->datatype_text[$datatype];
			}
			return '';
		}

		/**
		 * Get the list of available choices for a lookup field
		 *
		 * @param integer $location_id the location for the attribute
		 * @param integer $attrib_id   the field being looked up
		 */
		protected function _get_choices($location_id, $attrib_id)
		{
			$location_id	= (int) $location_id;
			$ttrib_id		= (int) $attrib_id;
			
			$sql = "SELECT * FROM phpgw_cust_choice " 
				. " WHERE location_id = {$location_id}"
					. " AND attrib_id = {$attrib_id}"
				. " ORDER BY value";
			$this->_db->query($sql,__LINE__,__FILE__);

			$choices = array();
			while ( $this->_db->next_record() )
			{
			//	$id = $this->_db->f('id');
			//	$choices[$id] = array
				$choices[] = array
				(
					'id'	=> $this->_db->f('id'),
					'value'	=> $this->_db->f('value', true)
				);
			}
			return $choices;
		}

		/**
		 * Finds the next ID for a record at a table
		 * 
		 * @param string $table tablename in question
		 * @param array  $key   conditions for finding the next id
		 *
		 * @return int the next id
		 */
		protected function _next_id($table = null, $key = null)
		{
			if ( !$table )
			{
				return 0;
			}

			$next_id = 0;

			$where = '';
			if ( is_array($key) )
			{
				foreach ( $key as $col => $val )
				{
					if ( $val )
					{
						$val = $this->_db->db_addslashes($val);
						$condition[] = "{$col} = '{$val}";
					}
				}

				$where='WHERE ' . implode("' AND ", $condition) . "'";
			}

			$sql = "SELECT max(id) as maximum FROM {$table} {$where}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				$next_id = $this->_db->f('maximum');
			}

			++$next_id;
			return $next_id;
		}

		/**
		 * Preapre a datatype for insert
		 *
		 * @param string $datatype the datatype being used
		 *
		 * @return string the converted datatype or empty string is invalid
		 */
		protected function _translate_datatype_insert($datatype)
		{
			$datatype_text = array
			(
				'V'			=> 'varchar',
				'I'			=> 'int',
				'C'			=> 'char',
				'N'			=> 'decimal',
				'D'			=> 'timestamp',
				'T'			=> 'text',
				'R'			=> 'int',
				'CH'		=> 'text',
				'LB'		=> 'int',
				'AB'		=> 'int',
				'VENDOR'	=> 'int',
				'email'		=> 'varchar',
				'link'		=> 'varchar'
			);

			if ( !isset($datatype_text[$datatype]) )
			{
				return '';
			}

			return $datatype_text[$datatype];
		}

		/**
		 * Get the precision for a datatype
		 *
		 * @param string $datatype the datatype to look up
		 *
		 * @return integer the precision - 0 for n/a or invalid
		 */
		protected function _translate_datatype_precision($datatype)
		{
			$datatype_precision = array
			(
				'I'			=> 4,
				'R'			=> 4,
				'LB'		=> 4,
				'AB'		=> 4,
				'VENDOR'	=> 4,
				'email'		=> 64,
				'link'		=> 255
			);

			if ( !isset($datatype_precision[$datatype]) )
			{
				return 0;
			}
			$ret = $datatype_precision[$datatype];
		}
	}
