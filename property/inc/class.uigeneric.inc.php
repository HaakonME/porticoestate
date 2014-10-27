<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009,2010,2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	//phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');
	/**
	 * Description
	 * @package property
	 */

	class property_uigeneric extends phpgwapi_uicommon_jquery
	{
		protected $appname = 'property';
		private $receipt = array();
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;
		var $location_info;

		var $public_functions = array
			(
				'query'				=> true,
				'index'  			=> true,
				'edit'   			=> true,
				'delete'			=> true,
				'download'			=> true,
				'columns'			=> true,
				'attrib_history'	=> true
			);

		function __construct()
		{
			parent::__construct();
			
			//$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogeneric',true);
			$this->bo->get_location_info();
			$this->bocommon				= & $this->bo->bocommon;
			$this->custom				= & $this->bo->custom;

			$this->location_info		= $this->bo->location_info;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->location_info['acl_location'];
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->location_info['acl_app']);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->location_info['acl_app']);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->location_info['acl_app']);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->location_info['acl_app']);
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, $this->location_info['acl_app']);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

			$this->type 		= $this->bo->type;
			$this->type_id 		= $this->bo->type_id;

			if($appname == $this->bo->appname)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = str_replace('property', $appname, $GLOBALS['phpgw_info']['flags']['menu_selection']);
				$this->appname = $appname;
			}
		}

		/*
		* Overrides with incoming data from POST
		*/
		private function _populate($data = array())
		{
			$insert_record = phpgwapi_cache::session_get('property', 'insert_record');

			$values	= phpgw::get_var('values');

			$_fields = array
			(
				array
				(
					'name' => 'title',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'descr',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'cat_id',
					'type'	=> 'integer',
					'required'	=> true
				),
				array
				(
					'name' => 'report_date',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'status_id',
					'type'	=> 'integer',
					'required'	=> true
				),
				array
				(
					'name' => 'vendor_id',
					'type'	=> 'integer',
					'required'	=> false
				),
				array
				(
					'name' => 'vendor_name',
					'type'	=> 'string',
					'required'	=> false
				),
				array
				(
					'name' => 'coordinator_id',
					'type'	=> 'integer',
					'required'	=> false
				),
				array
				(
					'name' => 'coordinator_name',
					'type'	=> 'string',
					'required'	=> false
				),
				array
				(
					'name' => 'multiplier',
					'type'	=> 'float',
					'required'	=> false
				),
			);


			foreach ($_fields as $_field)
			{
				if($data[$_field['name']] = $_POST['values'][$_field['name']])
				{
					$data[$_field['name']] =  phpgw::clean_value($data[$_field['name']], $_field['type']);
				}
				if($_field['required'] && !$data[$_field['name']])
				{
					$this->receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $_field['name']));
				}
			}

			$values = $this->bocommon->collect_locationdata($data,$insert_record);

			if(!isset($values['location_code']) || ! $values['location_code'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a location !'));
			}

			/*
			* Extra data from custom fields
			*/
			$values['attributes']	= phpgw::get_var('values_attribute');

			if(is_array($values['attributes']))
			{
				foreach ($values['attributes'] as $attribute )
				{
					if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if(!isset($values['cat_id']) || !$values['cat_id'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a category !'));
			}

			if(!isset($values['title']) || !$values['title'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please give a title !'));
			}

			if(!isset($values['report_date']) || !$values['report_date'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a date!'));
			}

			return $values;
		}
		
		private function _get_categories($selected = 0)
		{
			$values_combo_box = array();
			$combos = array();
			$i = 0;
			foreach ( $this->location_info['fields'] as $field )
			{
				if (!empty($field['filter']) && empty($field['hidden']))
				{
					if($field['values_def']['valueset'])
					{
						$values_combo_box[] = $field['values_def']['valueset'];
						// TODO find selected value
					}
					else if(isset($field['values_def']['method']))
					{
						foreach($field['values_def']['method_input'] as $_argument => $_argument_value)
						{
							if(preg_match('/^##/', $_argument_value))
							{
								$_argument_value_name = trim($_argument_value,'#');
								$_argument_value = $values[$_argument_value_name];
							}
							if(preg_match('/^\$this->/', $_argument_value))
							{
								$_argument_value_name = ltrim($_argument_value,'$this->');
								$_argument_value = $this->$_argument_value_name;
							}								
							$method_input[$_argument] = $_argument_value;
						}
						$values_combo_box[] = execMethod($field['values_def']['method'],$method_input);
					}
					$default_value = array ('id'=>'','name'=> lang('select') . ' ' . $field['descr']);
					array_unshift ($values_combo_box[$i],$default_value);
					
					$combos[$i] = array('type' => 'filter',
								'name' => $field['name'],
								'text' => lang($field['descr']) . ':',
								'list' => $values_combo_box[$i]
							);
					$i++;
				}
			}

			return $combos;
		}
		
		
		function save_sessiondata()
		{
			$data = array
				(
					'start'		=> $this->start,
					'query'		=> $this->query,
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'allrows'	=> $this->allrows,
					'type'		=> $this->type
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$values = $this->query();
			$uicols	= $this->bo->uicols;
			$this->bocommon->download($values,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function columns()
		{

			//cramirez: necesary for windows.open . Avoid error JS
			phpgwapi_yui::load_widget('tabview');

			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$values	= phpgw::get_var('values');

			if ($values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add($this->location_info['acl_app'],"generic_columns_{$this->type}_{$this->type_id}",$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uigeneric.columns',
					'type'			=> $this->type,
					'type_id'		=> $this->type_id

				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'	=> $this->bo->column_list($values['columns'],$allrows=true),
					'function_msg'	=> $function_msg,
					'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'	=> lang('columns'),
					'lang_none'		=> lang('None'),
					'lang_save'		=> lang('save'),
					'select_name'	=> 'period'
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			//$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$this->type}_{$this->type_id}");
			//$this->save_sessiondata();

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "general.index.{$this->type}";

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname			=  $this->location_info['name'];
			$function_msg		= lang('list %1', $appname);
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";
			
			$data = array(
				'datatable_name'	=> $appname,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('new'),
								'href' => self::link(array(
									'menuaction' => 'property.uigeneric.edit', 
									'appname'    => $this->appname,
									'type'       => $this->type,
									'type_id'    => $this->type_id								
									)),
								'class' => 'new_item'
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uigeneric.index', 
						'appname'		=> $this->appname,
						'type'			=> $this->type,
						'type_id'		=> $this->type_id,
						'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'property.uigeneric.download',
									'appname'    => $this->appname,
									'type'       => $this->type,
									'type_id'    => $this->type_id,
									'export'     => true,
									'allrows'    => true)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);
	
			$filters = $this->_get_categories();
			
			foreach ($filters as $filter) 
			{
				array_unshift ($data['form']['toolbar']['item'], $filter);
			}
			
			$this->bo->read();
			$uicols = $this->bo->uicols;

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
					$params = array(
									'key' => $uicols['name'][$k],
									'label' => $uicols['descr'][$k],
									'sortable' => ($uicols['sortable'][$k]) ? true : false,
									'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
								);
					if ($uicols['name'][$k] == 'id') {
						$params['formatter'] = 'JqueryPortico.formatLink';
					}
					array_push ($data['datatable']['field'], $params);
			}

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			if($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit',
						'statustext' 	=> lang('edit the actor'),
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
						)),
						'parameters'	=> json_encode($parameters)
					);
			
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit',
						'statustext' 	=> lang('edit the actor'),
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id,
							'target'		=> '_blank'
						)),
						'parameters'	=> json_encode($parameters)
					);
			}

			if($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
						'my_name' 		=> 'delete',
						'statustext' 	=> lang('delete the actor'),
						'text'			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uigeneric.delete',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
						)),
						'parameters'	=> json_encode($parameters)
					);
			}
			unset($parameters);
			
			if($this->acl_add)
			{
				$data['datatable']['actions'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('add'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
						))
					);
			}
			
			self::render_template_xsl('datatable_jquery', $data);

		}

		
		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1
			);

			foreach ( $this->location_info['fields'] as $field )
			{
				if (isset($field['filter']) && $field['filter'])
				{
					$params['filter'][$field['name']] = phpgw::get_var($field['name']);
				}
			}

			$result_objects = array();
			$result_count = 0;

			$values = $this->bo->read($params);
			if ( phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			
			$link_data = array
			(
				'menuaction' => 'property.uigeneric.edit',
				'appname'	 => $this->appname,
				'type'		 => $this->type,
				'type_id'	 => $this->type_id
			);
			
			array_walk(	$result_data['results'], array($this, '_add_links'), $link_data );

			return $this->jquery_results($result_data);
		}
		
		
		function edit($values = array(), $mode = 'edit')
		{
			if(!$this->acl_add)
			{
				$this->bocommon->no_access();
				return;
			}

			$id			= phpgw::get_var($this->location_info['id']['name']);
			//$values		= phpgw::get_var('values');

			$values_attribute  = phpgw::get_var('values_attribute');

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'general.edit.' . $this->type;

			/*if ($id)
			{
				if (!$values)
				{
					$values = $this->bo->read_single( array('id' => $id,  'view' => $mode == 'view') );
				}
			}*/
			
			if ($id)
			{
				if (!$values)
				{
					$values = $this->bo->read_single(array('id' => $id));
					$function_msg = $this->location_info['edit_msg'];
					$action='edit';
				}
			}
			else
			{
				if (!$values)
				{
					$values = $this->bo->read_single();
					$function_msg = $this->location_info['add_msg'];
					$action='add';
				}
			}

			/* Preserve attribute values from post */
			/*if(isset($receipt['error']))
			{
				foreach ( $this->location_info['fields'] as $field )
				{
					$values[$field['name']] = phpgw::clean_value($_POST['values'][$field['name']]);
				}

				if(isset( $values_attribute) && is_array( $values_attribute))
				{
					$values = $this->custom->preserve_attribute_values($values,$values_attribute);
				}
			}*/

			$link_data = array
				(
					'menuaction'	=> 'property.uigeneric.edit',
					'id'			=> $id,
					'appname'		=> $this->appname,
					'type'			=> $this->type,
					'type_id'		=> $this->type_id
				);

			$tabs = array();

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uigeneric.attrib_history',
								'appname'		=> $this->appname,
								'attrib_id'	=> $attribute['id'],
								'actor_id'	=> $actor_id,
								'role'		=> $this->role,
								'acl_location'	=> $this->acl_location,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				$attributes_groups = $this->custom->get_attribute_groups($this->location_info['acl_app'], $this->acl_location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					$attributes[] = $group;
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			foreach ($this->location_info['fields'] as & $field)
			{
				$field['value'] = 	isset($values[$field['name']]) ? $values[$field['name']] : '';
				if(isset($field['values_def']))
				{
					if($field['values_def']['valueset'] && is_array($field['values_def']['valueset']))
					{
						$field['valueset'] = $field['values_def']['valueset'];
						foreach($field['valueset'] as &$_entry)
						{
							$_entry['selected'] = $_entry['id'] == $field['value'] ? 1 : 0;
						}
					}
					else if(isset($field['values_def']['method']))
					{

						foreach($field['values_def']['method_input'] as $_argument => $_argument_value)
						{
							if(preg_match('/^##/', $_argument_value))
							{
								$_argument_value_name = trim($_argument_value,'#');
								$_argument_value = $values[$_argument_value_name];
							}
							if(preg_match('/^\$this->/', $_argument_value))
							{
								$_argument_value_name = ltrim($_argument_value,'$this->');
								$_argument_value = $this->$_argument_value_name;
							}

							$method_input[$_argument] = $_argument_value;
						}

						$field['valueset'] = execMethod($field['values_def']['method'],$method_input);
					}

					if(isset($values['id']) && $values['id'] && isset($field['role']) && $field['role'] == 'parent')
					{
						// can not select it self as parent.
						$exclude = array($values['id']);
						$children = $this->bo->get_children2($values['id'], 0,true);

						foreach($children as $child)
						{
							$exclude[] = $child['id']; 
						}

						$k = count($field['valueset']);
						for ($i=0; $i<$k; $i++)
						{
							if (in_array($field['valueset'][$i]['id'],$exclude))
							{
								unset($field['valueset'][$i]);
							}
						}
					}
				}
			}
	
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uigeneric.index', 
														'appname'		=> $this->appname,
														'type'			=> $this->type,
														'type_id' 		=> $this->type_id)),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigeneric.index', 'type'=> $this->type, 'type_id'=> $this->type_id)),
					'lang_descr'					=> lang('Descr'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_id'						=> isset($values['id']) ? $values['id'] : '',
					'value_descr'					=> $values['descr'],
					'attributes_group'				=> $attributes,
					'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 10,
					'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, 'general'),
					'id_name'						=> $this->location_info['id']['name'],
					'id_type'						=> $this->location_info['id']['type'],
					'fields'						=> $this->location_info['fields']
				);

			$appname	=  $this->location_info['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";
			//print_r($data); die;
			self::render_template_xsl(array('generic','attributes_form'), array('edit' => $data));
		}

		function attrib_history()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('attrib_history','nextmatchs'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$acl_location 	= phpgw::get_var('acl_location', 'string');
			$id				= phpgw::get_var('id', 'int');
			$attrib_id 		= phpgw::get_var('attrib_id', 'int');
			$detail_id 		= phpgw::get_var('detail_id', 'int');

			$data_lookup= array
			(
				'app'			=> 'property',
				'acl_location'	=> $acl_location,
				'id'			=> $id,
				'attrib_id' 	=> $attrib_id,
				'detail_id' 	=> $detail_id,
			);

			$delete = phpgw::get_var('delete', 'bool');
			$edit = phpgw::get_var('edit', 'bool');

			if ($delete)
			{
				$data_lookup['history_id'] = phpgw::get_var('history_id', 'int');
		//		$this->bo->delete_history_item($data_lookup);
			}

			$values = $this->bo->read_attrib_history($data_lookup);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while (is_array($values) && list(,$entry) = each($values))
			{
				$link_delete_history_data = array
					(
						'menuaction'	=> 'property.uientity.attrib_history',
						'acl_location'	=> $acl_location,
						'id'			=> $data_lookup['id'],
						'attrib_id'		=> $data_lookup['attrib_id'],
						'detail_id' 	=> $data_lookup['detail_id'],
						'history_id'	=> $entry['id'],
						'delete'		=> true,
						'edit'			=> true,
						'type'			=> $this->type
					);
				if($edit)
				{
					$text_delete	= lang('delete');
					$link_delete	= $GLOBALS['phpgw']->link('/index.php',$link_delete_history_data);
				}

				$content[] = array
					(
						'id'				=> $entry['id'],
						'value'				=> $entry['new_value'],
						'user'				=> $entry['owner'],
						'time_created'			=> $GLOBALS['phpgw']->common->show_date($entry['datetime'],$dateformat),
						'link_delete'			=> $link_delete,
						'lang_delete_statustext'	=> lang('delete the item'),
						'text_delete'			=> $text_delete,
					);
			}


			$table_header = array
				(
					'lang_value'		=> lang('value'),
					'lang_user'			=> lang('user'),
					'lang_time_created'	=> lang('time created'),
					'lang_delete'		=> lang('delete')
				);

			$link_data = array
				(
					'menuaction'	=> 'property.uientity.attrib_history',
					'acl_location'	=> $acl_location,
					'id'			=> $id,
					'detail_id' 	=> $data_lookup['detail_id'],
					'edit'			=> $edit,
					'type'			=> $this->type
				);


			//--- asynchronous response --------------------------------------------				

			if( phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}		
			//---datatable settings---------------------------------------------------				
			$parameters['delete'] = array
				(
					'parameter' => array
					(
						array
						(
							'name'  => 'acl_location',
							'source' => $data_lookup['acl_location'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'id',
							'source' => $data_lookup['id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'attrib_id',
							'source' => $data_lookup['attrib_id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'detail_id',
							'source' => $data_lookup['detail_id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'history_id',
							'source' => 'id',
						),
						array
						(
							'name'  => 'delete',
							'source' => true,
							'ready'  => 1
						),
						array
						(
							'name'  => 'edit',
							'source' => true,
							'ready'  => 1
						),
						array
						(
							'name'  => 'type',
							'source' => $this->type,
							'ready'  => 1
						)				
					)
				);

			if($edit && $this->acl->check($acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]))
			{
				$permissions['rowactions'][] = array
					(
						'text'    	=> lang('delete'),
						'action'  	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uigeneric.attrib_history' )),
						'confirm_msg'=> lang('do you really want to delete this entry'),
						'parameters'=> $parameters['delete']
					);
			}

			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($content),
					'total_records'	=> count($content),
					'permission'   	=> json_encode($permissions['rowactions']),
					'is_paginator'	=> 1,
					'footer'		=> 0
				);			   

			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array('key' => 'id',			'hidden'=>true),
													array('key' => 'value',			'label'=>lang('value'),		'sortable'=>true,'resizeable'=>true),
													array('key' => 'time_created',	'label'=>lang('time created'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'user',			'label'=>lang('user'),		'sortable'=>true,'resizeable'=>true)
				))
			);				

			//----------------------------------------------datatable settings--------			
			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
				'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'base_java_url'		=> json_encode(array(menuaction => "property.uigeneric.attrib_history")),
				'datatable'			=> $datavalues,
				'myColumnDefs'		=> $myColumnDefs,
				'allow_allrows'		=> false,
				'start_record'		=> $this->start,
				'record_limit'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'		=> count($values),
				'all_records'		=> $this->bo->total_records,
				'link_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'			=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'values' 			=> $content,
				'table_header'		=> $table_header,
			);
			//---datatable settings--------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'entity.attrib_history', 'property' );
			//-----------------------datatable settings---	

			//_debug_array($data);die();
			$custom			= createObject('phpgwapi.custom_fields');
			$attrib_data 	= $custom->get('property', $acl_location, $attrib_id);
			$appname		= $attrib_data['input_text'];
			$function_msg	= lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('attrib_history' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				return lang('no access');
			}

			$id	= phpgw::get_var($this->location_info['id']['name']);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return lang('id %1 has been deleted', $id);
			}
		}
		
		/**
		* Saves an entry to the database for new/edit - redirects to view
		*
		* @param int  $id  entity id - no id means 'new'
		*
		* @return void
		*/

		public function save()
		{
			$id = (int)phpgw::get_var('id');

			if ($id )
			{
				$values = $this->bo->read_single( array('id' => $id,  'view' => true) );
			}
			else
			{
				$values = array();
			}

			/*
			* Overrides with incoming data from POST
			*/
			$values = $this->_populate($values);

			if( $this->receipt['error'] )
			{
				$this->edit( $values );
			}
			else
			{

				try
				{
					$id = $this->bo->save($values);
				}

				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						$this->edit( $values );
						return;
					}
				}

				$this->_handle_files($id);
				if($_FILES['import_file']['tmp_name'])
				{
					$this->_handle_import($id);
				}
				else
				{
					phpgwapi_cache::message_set('ok!', 'message'); 
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.edit', 'id' => $id));
				}
			}
		}
	}
