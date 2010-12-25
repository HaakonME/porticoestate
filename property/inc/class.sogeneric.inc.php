<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sogeneric
	{
		var $type;
		var $type_id;
		var $location_info = array();

		function __construct()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->custom 	= createObject('property.custom_fields');
			$this->_db		= & $GLOBALS['phpgw']->db;
			$this->_like	= & $this->_db->like;
			$this->_join	= & $this->_db->join;
		}

		function read($data, $filter)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start']:0;
			$query		= isset($data['query'])?$data['query']:'';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order		= isset($data['order'])?$data['order']:'';
			$allrows	= isset($data['allrows'])?$data['allrows']:'';

			$values = array();
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

/*
			$valid_order = false;

			if($order)
			{
				if($this->location_info['id']['name'] != $order)
				{

					foreach ($this->location_info['fields'] as $field)
					{
						if($field['name'] == $order)
						{
							$valid_order = true;
							break;
						}
					}	
				}
				else
				{
					$valid_order = true;
				}

				if(!$valid_order)
				{
//					$order = '';
				}			
			}
 */
			$_filter_array = array();
			$get_single = array();
			foreach ( $this->location_info['fields'] as $field )
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if(isset($filter[$field['name']]) && $filter[$field['name']] && $field['type'] == 'multiple_select')
					{
						$_filter_array[] = "{$field['name']} {$this->_like} '%,{$filter[$field['name']]},%'";
					}
					else if(isset($filter[$field['name']]) && $filter[$field['name']])
					{
						$_filter_array[] = "{$field['name']} = '{$filter[$field['name']]}'";					
					}
				}
				if (isset($field['get_single']) && $field['get_single'])
				{
					$get_single[$field['name']] = $field['get_single'];
				}
			}

			$uicols = array();
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= $this->location_info['id']['name'];
			$uicols['descr'][]			= lang('id');
			$uicols['datatype'][]		= $this->location_info['id']['type'] == 'varchar' ? 'V' : 'I';
			$uicols['sortable'][]		= true;

			foreach($this->location_info['fields'] as $field)
			{
				$uicols['input_type'][]		= isset($field['hidden']) && $field['hidden'] ? 'hidden' : 'text';
				$uicols['name'][]			= $field['name'];
				$uicols['descr'][]			= $field['descr'];
				$uicols['datatype'][]		= 'V';
				$uicols['sortable'][]		= isset($field['sortable']) && $field['sortable'] ? true : false;
			}

			if($GLOBALS['phpgw']->locations->get_attrib_table('property', $this->location_info['acl_location']))
			{
				$choice_table = 'phpgw_cust_choice';
				$attribute_table = 'phpgw_cust_attribute';
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->location_info['acl_location']);
				$attribute_filter = " location_id = {$location_id}";

				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences']['property']["generic_columns_{$this->type}_{$this->type_id}"])?$GLOBALS['phpgw_info']['user']['preferences']['property']["generic_columns_{$this->type}_{$this->type_id}"]:'';

				$user_column_filter = '';
				if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
				{
					$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',',$user_columns) .'))';
				}

				$this->_db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");

				$i	= count($uicols['name']);
				while ($this->_db->next_record())
				{
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= $this->_db->f('column_name');
					$uicols['descr'][]			= $this->_db->f('input_text');
					$uicols['statustext'][]		= $this->_db->f('statustext');
					$uicols['datatype'][$i]		= $this->_db->f('datatype');
					$uicols['attib_id'][$i]		= $this->_db->f('id');
					$cols_return_extra[]= array(
						'name'	=> $this->_db->f('column_name'),
						'datatype'	=> $this->_db->f('datatype'),
						'attrib_id'	=> $this->_db->f('id')
					);

					$i++;
				}
			}

			$where = 'WHERE';
			$filtermethod = '';
			if(isset($this->location_info['check_grant']) && $this->location_info['check_grant'])
			{
				$filtermethod = "{$where} user_id = {$this->account} OR public = 1";
				$where = 'AND';
			}

			if($_filter_array)
			{
				$filtermethod .= " $where " . implode(' AND ', $_filter_array);
				$where = 'AND';
			}

			$this->uicols = $uicols;

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} {$sort}";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.{$this->location_info['id']['name']} ASC";
			}

			if($query)
			{
				if($this->location_info['id']['type']=='auto' || $this->location_info['id']['type']=='int')
				{
					$id_query = (int) $query;
				}
				else
				{
					$id_query = "'{$query}'";
				}

				$_query_start = '';
				$_query_end = '';

				if($filtermethod)
				{
					$_query_start = '(';
					$_query_end = ')';
				}
				$query = $this->_db->db_addslashes($query);
				$querymethod = " {$where } {$_query_start} ({$table}.{$this->location_info['id']['name']} = {$id_query}";
				//_debug_array($filtermethod);
				//_debug_array($where);die();

				foreach($this->location_info['fields'] as $field)
				{
					if($field['type'] == 'varchar')
					{
						$querymethod .= " OR {$table}.{$field['name']} $this->_like '%$query%'";
					}
					$where = 'OR';
				}
				$querymethod .= ')';

				$_querymethod = array();

				$this->_db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'",__LINE__,__FILE__);

				while ($this->_db->next_record())
				{
					if($this->_db->f('datatype')=='V' || $this->_db->f('datatype')=='email' || $this->_db->f('datatype')=='CH')
					{
						$_querymethod[]= "$table." . $this->_db->f('column_name') . " {$this->_like} '%{$query}%'";
					}
					else if($this->_db->f('datatype')=='I')
					{
						if(ctype_digit($query))
						{
							$_querymethod[]= "$table." . $this->_db->f('column_name') . '=' . (int)$query;
						}
					}
					else
					{
						$_querymethod[]= "$table." . $this->_db->f('column_name') . " = '$query'";
					}
				}

				if (isset($_querymethod) AND is_array($_querymethod))
				{
					$querymethod .= " $where (" . implode (' OR ',$_querymethod) . ')';
				}

				$querymethod .= $_query_end;
			}

			$sql = "SELECT * FROM $table {$filtermethod} {$querymethod}";

			$this->_db->query('SELECT count(*) as cnt ' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
			$this->_db->next_record();
			$this->total_records = $this->_db->f('cnt');

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$cols_return = $uicols['name'];
			$j=0;

			$dataset = array();
			while ($this->_db->next_record())
			{
				foreach($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
						(
							'value'		=> $this->_db->f($field),
							'datatype'	=> $uicols['datatype'][$key],
							'attrib_id'	=> $uicols['attib_id'][$key]
						);
				}
				$j++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			if($get_single)
			{
				foreach($values as $set => &$entry)
				{
					foreach ($entry as $field => &$value)
					{
						foreach ($get_single as $key => $method)
						{
							if($field == $key)
							{
								switch ($method)
								{
								case 'get_user':
									if($value)
									{
										$value = $GLOBALS['phpgw']->accounts->get($value)->__toString();
									}
									break;
								default:
									// nothing
								}
							}
						}
					}
				}
			}
			return $values;
		}


		function get_location_info($type,$type_id)
		{
			$type_id		= (int)$type_id;
			$this->type		= $type;
			$this->type_id	= $type_id;

			$info = array();
			switch($type)
			{
				//-------- ID type integer
			case 'part_of_town':
				$info = array
					(
						'table' 			=> 'fm_part_of_town',
						'id'				=> array('name' => 'part_of_town_id', 'type' => 'int', 'descr' => lang('id')),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable'	=> false,
								'size'		=> 20
							),
							array
							(
								'name'			=> 'district_id',
								'descr'			=> lang('district'),
								'type'			=> 'select',
								'nullable'		=> false,
								'filter'		=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bogeneric.get_list',
									'method_input'	=> array('type' => 'district',	'selected' => '##district_id##')
								)
							),
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('part of town'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::location::town',
/*
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						),
 */
						'check_grant'		=> false
					);

				break;

			case 'project_group':
				$info = array
					(
						'table' => 'fm_project_group',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::project_group'
					);
				break;
			case 'dimb':
				$info = array
					(
						'table' => 'fm_ecodimb',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('dimb'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimb'
					);
				break;
			case 'dimd':
				$info = array
					(
						'table' => 'fm_ecodimd',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('dimd'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimd'
					);
				break;
			case 'tax':
				$info = array
					(
						'table' => 'fm_ecomva',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_tax'
					);
				break;
			case 'voucher_cat':
				$info = array
					(
						'table' => 'fm_ecobilag_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_cats'
					);
				break;
			case 'voucher_type':
				$info = array
					(
						'table' => 'fm_ecoart',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_type'
					);
				break;
			case 'tender_chapter':
				$info = array
					(
						'table' => 'fm_chapter',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tender'
					);
				break;
			case 'location':

				$this->_db->query("SELECT id FROM fm_location_type WHERE id ={$type_id}",__LINE__,__FILE__);

				if($this->_db->next_record())
				{
					$info = array
						(
							'table' => "fm_location{$type_id}_category",
							'id'				=> array('name' => 'id', 'type' => 'varchar'),
							'fields'			=> array
							(
								array
								(
									'name' => 'descr',
									'descr' => lang('descr'),
									'type' => 'varchar'
								)
							),
							'edit_msg'	=> lang('edit'),
							'add_msg'	=> lang('add'),
							'name'		=> '',
							'acl_location' => '.admin',
							'menu_selection' => "admin::property::location::location::category_{$type_id}"
						);
				}
				else
				{
					throw new Exception(lang('ERROR: illegal type %1', $type_id));
				}
				break;
			case 'owner_cats':
				$info = array
					(
						'table' => 'fm_owner_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::owner::owner_cats'
					);
				break;
			case 'tenant_cats':
				$info = array
					(
						'table' => 'fm_tenant_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('tenant category'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::tenant_cats'
					);
				break;
			case 'vendor_cats':
				$info = array
					(
						'table' => 'fm_vendor_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('vendor category'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::vendor::vendor_cats'
					);
				break;
			case 'vendor':
				$info = array
					(
						'table' 			=> 'fm_vendor',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'contact_phone',
								'descr' => lang('contact phone'),
								'type' => 'varchar'
							),
							array
							(
								'name'			=> 'category',
								'descr'			=> lang('category'),
								'type'			=> 'select',
								'nullable'		=> false,
								'filter'		=> true,
								'sortable'	=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bogeneric.get_list',
									'method_input'	=> array('type' => 'vendor_cats',	'selected' => '##category##')
								)
							),
							array
							(
								//FIXME
								'name'			=> 'member_of',
								'descr'			=> lang('member'),
								'type'			=> 'multiple_select',
								'nullable'		=> false,
								'filter'		=> true,
								'sortable'		=> false,
								'hidden'		=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bocommon.get_categories',
									'method_input'	=> array('app' => 'property', 'acl_location' => '.vendor',	'selected' => '##member_of##')
								)
							),
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('vendor'),
						'acl_location' => '.vendor',
						'menu_selection' => 'property::invoice::vendor',
						'default'			=> array
						(
							'owner_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							//			'modified_date'	=> array('edit'	=> 'time()'),
						)

					);
				break;
			case 'owner':
				$info = array
					(
						'table' 			=> 'fm_owner',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'text'
							),
							array
							(
								'name'			=> 'category',
								'descr'			=> lang('category'),
								'type'			=> 'select',
								'nullable'		=> false,
								'filter'		=> true,
								'sortable'	=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bogeneric.get_list',
									'method_input'	=> array('type' => 'owner_cats',	'selected' => '##category##')
								)
							),
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('owner'),
						'acl_location' => '.owner',
						'menu_selection' => 'admin::property::owner',
						'default'			=> array
						(
							'owner_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							//			'modified_date'	=> array('edit'	=> 'time()'),
						)

					);
				break;
			case 'tenant':
				$info = array
					(
						'table' 			=> 'fm_tenant',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'contact_email',
								'descr' => lang('contact_email'),
								'type' => 'varchar',
								'sortable'	=> true,
							),
							array
							(
								'name'			=> 'category',
								'descr'			=> lang('category'),
								'type'			=> 'select',
								'nullable'		=> false,
								'filter'		=> true,
								'sortable'	=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bogeneric.get_list',
									'method_input'	=> array('type' => 'tenant_cats',	'selected' => '##category##')
								)
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('tenant'),
						'acl_location' => '.tenant',
						'menu_selection' => 'admin::property::tenant',
						'default'			=> array
						(
							'owner_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							//			'modified_date'	=> array('edit'	=> 'time()'),
						)

					);
				break;
			case 'district':
				$info = array
					(
						'table' => 'fm_district',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('district'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::district'
					);
				break;
			case 'street':
				$info = array
					(
						'table' => 'fm_streetaddress',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('streetaddress'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::street'
					);
				break;
			case 's_agreement':
				$info = array
					(
						'table' => 'fm_s_agreement_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::agreement::service_agree_cats'
					);
				break;
			case 'tenant_claim':
				$info = array
					(
						'table' => 'fm_tenant_claim_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::claims_cats'
					);
				break;
			case 'wo_hours':
				$info = array
					(
						'table' => 'fm_wo_hours_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::workorder_detail'
					);
				break;
			case 'r_condition_type':
				$info = array
					(
						'table' => 'fm_request_condition_type',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::request_condition'
					);
				break;
			case 'b_account':
				$info = array
					(
						'table' => 'fm_b_account_category',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('budget account'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_cats'
					);
				break;
				//-------- ID type varchar
			case 'project_status':
				$info = array
					(
						'table' 			=> 'fm_project_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'approved',
								'descr' => lang('approved'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'closed',
								'descr' => lang('closed'),
								'type' => 'checkbox'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('project status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::project_status'
					);
				break;
			case 'workorder_status':
				$info = array
					(
						'table' 			=> 'fm_workorder_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'approved',
								'descr' => lang('approved'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'in_progress',
								'descr' => lang('In progress'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'delivered',
								'descr' => lang('delivered'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'closed',
								'descr' => lang('closed'),
								'type' => 'checkbox'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('workorder status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::workorder_status'
					);
				break;
			case 'request_status':
				$info = array
					(
						'table' 			=> 'fm_request_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('request status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::request_status'
					);
				break;
			case 'agreement_status':
				$info = array
					(
						'table' 			=> 'fm_agreement_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('agreement status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::agreement::agreement_status'
					);
				break;
			case 'building_part':
				$info = array
					(
						'table' 			=> 'fm_building_part',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('building part'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::building_part'
					);
				break;
			case 'document_status':
				$info = array
					(
						'table' 			=> 'fm_document_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('document status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::document_status'
					);
				break;
			case 'unit':
				$info = array
					(
						'table' 			=> 'fm_standard_unit',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit unit'),
						'add_msg'			=> lang('add unit'),
						'name'				=> lang('unit'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::unit'
					);
				break;
			case 'budget_account':
				$info = array
					(
						'table' 			=> 'fm_b_account',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar',
								'nullable'	=> false,
								'size'		=> 60,
								'sortable'	=> true
							),
							array
							(
								'name'			=> 'category',
								'descr'			=> lang('category'),
								'type'			=> 'select',
								'nullable'		=> false,
								'filter'		=> true,
								'sortable'	=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bogeneric.get_list',
									'method_input'	=> array('type' => 'b_account',	'selected' => '##category##')//b_account_category
								)
							),
							array
							(
								'name'		=> 'mva',
								'descr'		=> lang('tax code'),
								'type'		=> 'int',
								'nullable'	=> true,
								'size'		=> 4,
								'sortable'	=> true
							),
							array
							(
								'name'			=> 'responsible',
								'descr'			=> lang('responsible'),
								'type'			=> 'select',
								'filter'		=> true,
								'get_single'	=> 'get_user',
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.bocommon.get_user_list_right2',
									'method_input'	=> array('selected' => '##responsible##', 'right' => 128, 'acl_location' => '.invoice')
								)
							),
							array
							(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked'
							),
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('budget account'),
						'acl_location' 		=> '.b_account',
						'menu_selection'	=> 'property::invoice::budget_account',
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						),
						'check_grant'		=> false
					);

				break;

				//-------- ID type auto
			case 'order_dim1':
				$info = array
					(
						'table' 			=> 'fm_order_dim1',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar',
								'nullable' => false
							),
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('order_dim1'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::order_dim1'
					);
				break;
			case 'branch':
				$info = array
					(
						'table' 			=> 'fm_branch',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('branch'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::branch'
					);

				break;
			case 'key_location':
				$info = array
					(
						'table' 			=> 'fm_key_loc',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('key location'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('branch'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::key_location'
					);

				break;

			case 'async':
				$info = array
					(
						'table' 			=> 'fm_async_method',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'data',
								'descr' => lang('data'),
								'type' => 'text'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('Async services'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::async'
					);
				break;

			case 'event_action':

				$info = array
					(
						'table' 			=> 'fm_event_action',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'action',
								'descr' => lang('action'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'data',
								'descr' => lang('data'),
								'type' => 'text'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('event action'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::event_action',
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						)
					);

				break;

			case 'ticket_status':

				$info = array
					(
						'table' 			=> 'fm_tts_status',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'sorting',
								'descr' => lang('sorting'),
								'type' => 'integer',
								'sortable'=> true
							),
							array
							(
								'name' => 'color',
								'descr' => lang('color'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'approved',
								'descr' => lang('approved'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'in_progress',
								'descr' => lang('In progress'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'delivered',
								'descr' => lang('delivered'),
								'type' => 'checkbox'
							),
							array
							(
								'name' => 'closed',
								'descr' => lang('closed'),
								'type' => 'checkbox'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('event action'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::ticket_status'
					);
				break;
			case 'pending_action_type':
				$info = array
					(
						'table' 			=> 'fm_action_pending_category',
						'id'				=> array('name' => 'num', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('Pending action type'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::action_type'
					);

				break;

			case 'order_template':

				$info = array
					(
						'table' 			=> 'fm_order_template',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'content',
								'descr' => lang('content'),
								'type' => 'text'
							),
							array
							(
								'name' => 'public',
								'descr' => lang('public'),
								'type' => 'checkbox'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('order template'),
						'acl_location' 		=> '.ticket.order',
						'menu_selection'	=> 'property::helpdesk::order_template',
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						),
						'check_grant'		=> true
					);

				break;

			case 'responsibility_role':

				$info = array
					(
						'table' 			=> 'fm_responsibility_role',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'text'
							),
							array
							(
								'name'			=> 'location',
								'descr'			=> lang('location'),
								'type'			=> 'select',
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'preferences.boadmin_acl.get_locations',
									'method_input'	=> array('acl_app' => 'property',	'selected' => '##location##')
								)
							),
							array
							(
								'name'			=> 'responsibility_id',
								'descr'			=> lang('responsibility'),
								'type'			=> 'select',
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'property.boresponsible.get_responsibilities',
									'method_input'	=> array('acl_app' => 'property',	'selected' => '##responsibility_id##')
								)
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('responsibility role'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::responsibility_role',
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						),
						'check_grant'		=> false
					);

				break;

			case 'custom_menu_items':

				$info = array
					(
						'table' 			=> 'fm_custom_menu_items',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'url',
								'descr' => lang('url'),
								'type' => 'text'
							),
							array
							(
								'name'			=> 'location',
								'descr'			=> lang('location'),
								'type'			=> 'select',
								'filter'		=> true,
								'values_def'	=> array
								(
									'valueset'		=> false,
									'method'		=> 'preferences.boadmin_acl.get_locations',
									'method_input'	=> array('acl_app' => 'property',	'selected' => '##location##')
								)
							),
							array
							(
								'name' => 'local_files',
								'descr' => lang('local files'),
								'type' => 'checkbox',
								'default' => ''
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('responsibility role'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::custom_menu_items',
						'default'			=> array
						(
							'user_id' 		=> array('add'	=> '$this->account'),
							'entry_date'	=> array('add'	=> 'time()'),
							'modified_date'	=> array('edit'	=> 'time()'),
						),
						'check_grant'		=> false
					);

				break;

			default:
				$receipt = array();
				$receipt['error'][]=array('msg'=>lang('ERROR: illegal type %1', $type));
				phpgwapi_cache::session_set('phpgwapi', 'phpgw_messages', $receipt);
				//	throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$this->location_info = $info;
			return $info;
		}

		function read_single($data,$values = array())
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			if($this->location_info['id']['type']=='auto' || $this->location_info['id']['type']=='int')
			{
				$id = (int) $data['id'];
			}
			else
			{
				$id = "'{$data['id']}'";
			}

			$sql = "SELECT * FROM $table WHERE {$this->location_info['id']['name']} = {$id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$values['id'] = $this->_db->f($this->location_info['id']['name']);

				// FIXME - add field to $values['attributes']
				foreach($this->location_info['fields'] as $field)
				{
					$values[$field['name']] = $this->_db->f($field['name'], true);
				}

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->_db->f($attr['column_name']);
					}
				}
			}
			return $values;
		}


		//deprecated
		function select_generic_list($data)
		{
			return $this->get_entity_list($data);
		}

		function get_list($data)
		{
			$values = array();

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			$filtermthod = '';
			if (isset($data['filter']) && is_array($data['filter']))
			{
				$_filter = array();
				foreach ($data['filter'] as $_field => $_value)
				{
					$_filter[] = "{$_field} = '{$_value}'";
				}
				if($_filter)
				{
					$filtermthod = 'WHERE ' . implode(' AND ', $_filter);
				}
			}
			$order		= isset($data['order']) && $data['order'] ? $data['order'] :'descr';

			foreach ($this->location_info['fields'] as $field)
			{
				$fields[] = $field['name'];
			}

			// Add extra info to name
			if(isset($data['id_in_name']) && $data['id_in_name'])
			{
				$id_in_name = 'id';	
				if (in_array($data['id_in_name'], $fields))
				{
					$id_in_name = $data['id_in_name'];
				}
			}

			$fields = implode(',', $fields);

			$this->_db->query("SELECT id, {$fields} FROM {$table} {$filtermthod} ORDER BY {$order}");

			while ($this->_db->next_record())
			{
				$_extra = $this->_db->f($id_in_name);
				$id		= $this->_db->f('id');
				if(!$name = $this->_db->f('name', true))
				{
					$name	= $this->_db->f('descr', true);
				}

				if($_extra)
				{
					$name = "{$_extra} - {$name}";
				}

				$values[] = array
					(
						'id'	=> $id,
						'name'	=> $name
					);
			}
			return $values;
		}

		function add($data,$values_attribute)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			if(isset($data['save']))
			{
				unset($data['save']);
			}
			if(isset($data['apply']))
			{
				unset($data['apply']);
			}

			foreach ( $this->location_info['fields'] as $field )
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if(isset($data[$field['name']]) && $data[$field['name']] && $field['type'] == 'multiple_select')
					{
						$data[$field['name']] = ',' . implode(',',$data[$field['name']]) . ',';
					}
				}
			}

			$cols = array();
			$vals = array();

			$data['descr'] = $this->_db->db_addslashes($data['descr']);

			if(isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}
			unset($data['extra']);

			foreach ($data as $input_name => $value)
			{
				if(isset($value) && $value)
				{
					$cols[] = $input_name;
					$vals[] = $this->_db->db_addslashes($value);
				}
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute);
			if(isset($data_attribute['value_set']))
			{
				foreach($data_attribute['value_set'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}


			if(isset($this->location_info['default']) && is_array($this->location_info['default']))
			{
				foreach ($this->location_info['default'] as $field => $default)
				{
					if(isset($default['add']))
					{
						$cols[] = $field;
						eval('$vals[] = ' . $default['add'] .';');
					}
				}
			}

			$this->_db->transaction_begin();

			if($this->location_info['id']['type']!='auto')
			{
				$this->_db->query("SELECT id FROM {$table} WHERE {$this->location_info['id']['name']} = '{$data['id']}'",__LINE__,__FILE__);
				if($this->_db->next_record())
				{
					$receipt['error'][]=array('msg'=>lang('duplicate key value'));
					$receipt['error'][]=array('msg'=>lang('record has not been saved'));
					return $receipt;
				}
				$id = $data['id'];
			}
			else
			{
				$id = $this->_db->next_id($table);
				$cols[] = 'id';
				$vals[] = $id;
			}

			$cols	= implode(",", $cols);
			$vals	= $this->_db->validate_insert($vals);

			$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$vals})",__LINE__,__FILE__);

/*			if($this->location_info['id']['type']=='auto')
			{
				if(!$data['id'] = $this->_db->get_last_insert_id($table, 'id'))
				{
					$this->_db->transaction_abort();
					$receipt['error'][]=array('msg'=>lang('record has not been saved'));
				}
			}
 */
			$this->_db->transaction_commit();
			$receipt['id'] = $id;
			$receipt['message'][]=array('msg'=>lang('record has been saved'));
			return $receipt;
		}

		function edit($data,$values_attribute)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$value_set = array();

			if(isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
				unset($data['extra']);
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute, $data['id']);

			if(isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}
			foreach($this->location_info['fields'] as $field)
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if(isset($data[$field['name']]) && $data[$field['name']] && $field['type'] == 'multiple_select')
					{
						$data[$field['name']] = ',' . implode(',',$data[$field['name']]) . ',';
					}
				}
				$value_set[$field['name']] = $this->_db->db_addslashes($data[$field['name']]);
			}

			if(isset($this->location_info['default']) && is_array($this->location_info['default']))
			{
				foreach ($this->location_info['default'] as $field => $default)
				{
					if(isset($default['edit']))
					{
						eval('$value_set[$field] = ' . $default['edit'] .';');
					}
				}
			}

			$value_set	= $this->_db->validate_update($value_set);
			$this->_db->transaction_begin();
			$this->_db->query("UPDATE $table SET {$value_set} WHERE {$this->location_info['id']['name']}='" . $data['id']. "'",__LINE__,__FILE__);

/*			//FIXME
			if (isset($data_attribute['history_set']) && is_array($data_attribute['history_set']))
			{
				$historylog	= CreateObject('phpgwapi.historylog','property', $this->location_info['acl_location']);
				foreach ($data_attribute['history_set'] as $attrib_id => $history)
				{
					$historylog->add('SO',$data['id'],$history['value'],false, $attrib_id,$history['date']);
				}
			}
 */
			$this->_db->transaction_commit();

			$receipt['id'] = $data['id'];

			$receipt['message'][]=array('msg'=>lang('record has been edited'));
			return $receipt;
		}

		function delete($id)
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return false;
			}
			$this->_db->query("DELETE FROM $table WHERE {$this->location_info['id']['name']}='{$id}'",__LINE__,__FILE__);
		}
	}

