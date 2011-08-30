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
	* @subpackage project
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uirequest
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;

		var $public_functions = array
			(
				'index' 		=> true,
				'view'  		=> true,
				'edit'  		=> true,
				'delete'		=> true,
				'priority_key'	=> true,
				'view_file'		=> true,
				'download'		=> true,
				'columns'		=> true
			);

		function property_uirequest()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::request';
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.borequest',true);
			$this->boproject			= CreateObject('property.boproject');
			$this->bocommon				= & $this->bo->bocommon;
			$this->cats					= & $this->bo->cats;
			$this->bolocation			= CreateObject('property.bolocation');
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->config->read();
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.project.request';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->district_id			= $this->bo->district_id;

			$this->allrows				= $this->bo->allrows;
			$this->p_num				= $this->bo->p_num;
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'filter'		=> $this->filter,
					'cat_id'		=> $this->cat_id,
					'status_id'		=> $this->status_id,
					'district_id'	=> $this->district_id,
					'allrows'		=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property','request_columns', $values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uirequest.columns',
			);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'		=> $this->bo->column_list($selected , $this->type_id, $allrows=true),
					'function_msg'		=> $function_msg,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'		=> lang('columns'),
					'lang_none'			=> lang('None'),
					'lang_save'			=> lang('save'),
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}


		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$location_code 	= phpgw::get_var('location_code');

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('request');
		}

		function download()
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);
			$list 			= $this->bo->read(array('start_date' =>$start_date, 'end_date' =>$end_date,'allrows'=>true,'list_descr' => true));
			$uicols			= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$datatable = array();
			$values_combo_box = array();

			$project_id = phpgw::get_var('project_id', 'int'); // lookup for maintenance planning

			if($project_id)
			{
				$lookup	= true;
			}

			$this->save_sessiondata();

			$dry_run = false;

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				if(!$lookup)
				{
					$datatable['menu']	= $this->bocommon->get_menu();
				}

				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uirequest.index',
						'lookup'    => $lookup,
		//				'cat_id'	=> $this->cat_id,
		//				'filter'	=> $this->filter,
		//				'status_id'	=> $this->status_id,
						'project_id'	=> $project_id,
		//				'query'		=> $this->query,
						'p_num'		=> $this->p_num,
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uirequest.index',"
					."p_num: '{$this->p_num}',"
					."query:'{$this->query}',"
					."lookup:'{$lookup}',"
					."project_id:'{$project_id}',"
					."filter:'{$this->filter}',"
					."status_id:'{$this->status_id}',"
					."district_id: '{$this->district_id}',"
					."cat_id:'{$this->cat_id}'";

				$values_combo_box[0]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1] = $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[1]['cat_list'],$default_value);

				$values_combo_box[2]  = $this->bo->select_status_list('filter',$this->status_id);
				$default_value = array ('id'=>'','name'=> lang('no status'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bocommon->get_user_list('filter',$this->filter,$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1);
				array_unshift ($values_combo_box[3],array('user_id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>$GLOBALS['phpgw_info']['user']['fullname']));
				$default_value = array ('user_id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uirequest.index',
								'lookup'        	=> $lookup,
								'cat_id'			=> $this->cat_id,
								'filter'			=> $this->filter,
								'status_id'			=> $this->status_id,
								'project_id'		=> $project_id,
								'district_id'       => $this->district_id,
								'query'				=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	DISTRICT
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('district'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',

									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	STATUS
									'id' => 'btn_status_id',
									'name' => 'status_id',
									'value'	=> lang('Status'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	FILTER
									'id' => 'btn_user_id',
									'name' => 'filter',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 4
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 9
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_update',
									'value'	=> lang('Update project'),
									'tab_index' => 8
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 7
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'onkeypress' => 'return pulsar(event)',
									'type' => 'button',
									'tab_index' => 6
								),
								array
								( //hidden request
									'type'	=> 'hidden',
									'id'	=> 'myValuesForUpdatePHP',
									'name'	=> 'myValuesForUpdatePHP',
									'value'	=> ''
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 5
								),
								array
								(
									'type'=> 'link',
									'id'  => 'btn_priority_key',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uirequest.priority_key'))."','','left=50,top=100,width=350,height=350,scrollbars=1')",
										'value' => lang('Priority key'),
										'tab_index' => 4
								),
								array
								(
									'type' => 'link',
									'id' => 'btn_columns',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uirequest.columns'))."','','width=300,height=600,scrollbars=1')",
										'value' => lang('columns'),
										'tab_index' => 10
								),
							),
								'hidden_value' => array
								(
									array
									( //div values  combo_box_0
										'id' => 'values_combo_box_0',
										'value'	=> $this->bocommon->select2String($values_combo_box[0])
									),
									array
									( //div values  combo_box_1
										'id' => 'values_combo_box_1',
										'value'	=> $this->bocommon->select2String($values_combo_box[1]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
									),
									array
									( //div values  combo_box_2
										'id' => 'values_combo_box_2',
										'value'	=> $this->bocommon->select2String($values_combo_box[2])
									),
									array
									( //div values  combo_box_3
										'id' => 'values_combo_box_3',
										'value'	=> $this->bocommon->select2String($values_combo_box[3], 'user_id')
									)
								)
							)
						)
					);

				if(!$this->acl_manage)
				{
					unset($datatable['actions']['form'][0]['fields']['field'][9]);
				}

				if(!$this->acl_add)
				{
					unset($datatable['actions']['form'][0]['fields']['field'][5]);
				}

				if(!$project_id)
				{
					unset($datatable['actions']['form'][0]['fields']['field'][4]);
				}
				$dry_run = true;
			}

			$request_list = array();
			$request_list = $this->bo->read(array('project_id' => $project_id,'allrows'=>$this->allrows, 'dry_run' => $dry_run));
			$uicols	= $this->bo->uicols;

			$j=0;
			if (isset($request_list) && is_array($request_list))
			{
				foreach($request_list as $request)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($request['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $request[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $request['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $request[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $request[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $request[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']			= $request[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $request[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

					if($lookup)
					{
						$datatable['rows']['row'][$j]['column'][$i + 1]['name'] 			= 'select';
						$datatable['rows']['row'][$j]['column'][$i + 1]['statustext']		= lang('select');
						$datatable['rows']['row'][$j]['column'][$i + 1]['align'] 			= 'center';
						$datatable['rows']['row'][$j]['column'][$i + 1]['value']			= '<input name="add_request[request_id][]" id="add_request[request_id][]"  class="myValuesForPHP close_order" type="hidden" value=""/> <input type="checkbox" name="add_request[request_id_tmp][]" id="add_request[request_id_tmp][]" value="'.$request['request_id'].'" class="close_order_tmp">';					}
						$j++;
				}
			}

			// NO pop-up
			$datatable['rowactions']['action'] = array();
			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'request_id'
							),
						)
					);

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uirequest.view'
							)),
							'parameters'	=> $parameters
						);
					$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

					foreach ($jasper as $report)
					{
						$datatable['rowactions']['action'][] = array
							(
								'my_name'		=> 'edit',
								'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
								'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uijasper.view',
									'jasper_id'			=> $report['id'],
									'target'		=> '_blank'
								)),
								'parameters'			=> $parameters
							);
					}

				}
				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uirequest.edit'
							)),
							'parameters'	=> $parameters
						);
				}
				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uirequest.delete'
							)),
							'parameters'	=> $parameters
						);
				}

				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uirequest.edit'
							))
						);
				}
				unset($parameters);
			}
			else
			{

				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'request_id'
							),
						)
					);

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'view',
							'text' 				=> lang('view'),
							'target' 			=> '_blank',
							'action'			=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uirequest.view'
							)),
							'parameters'	=> $parameters
						);
				}
			}

			$uicols_count	= count($uicols['descr']);

			$show_dates	= isset($this->config->config_data['request_show_dates']) && $this->config->config_data['request_show_dates'] ? 1 : '';

			for ($i=0;$i<$uicols_count;$i++)
			{
				if(!$show_dates && $uicols['name'][$i] == 'start_date')
				{
					$uicols['input_type'][$i] = 'hidden';
				}

				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
				
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
					$datatable['headers']['header'][$i]['sort_field']		= $uicols['name'][$i];

					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= "location_code";
					}

				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			if($lookup)
			{
				$i++;
				$datatable['headers']['header'][$i]['name'] 			= 'select';
				$datatable['headers']['header'][$i]['text'] 			= lang('select');
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= false;
				$datatable['headers']['header'][$i]['format'] 			= '';
				$datatable['headers']['header'][$i]['sortable']			= false;
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['formatter']		= '""';
			}

			// path for property.js
			$datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($request_list);
			}

			$appname					= lang('request');
			$function_msg				= lang('list request');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['currentPage']	= 1;
				$datatable['sorting']['order']	= 'request_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort']	= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['currentPage']	= phpgw::get_var('currentPage');
				$datatable['sorting']['order']  = phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort']	= phpgw::get_var('sort', 'string'); // ASC / DESC
			}


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'currentPage'		=> $datatable['sorting']['currentPage'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{

						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'request.index', 'property' );
		}


		function priority_key()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			//cramirez: necesary for windows.open . Avoid error JS
			phpgwapi_yui::load_widget('tabview');

			$GLOBALS['phpgw']->xslttpl->add_file(array('request'));
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$values 	= phpgw::get_var('values');

			$values['authorities_demands'] = $values['authorities_demands'] ? $values['authorities_demands'] : $this->config->config_data['authorities_demands'];

			if($values['update'])
			{
				$receipt = $this->bo->update_priority_key($values);
				$this->config->config_data['authorities_demands'] = (int) $values['authorities_demands'];
				$this->config->save_repository();
			}

			$function_msg	= lang('Edit priority key');
			$link_data = array('menuaction' => 'property.uirequest.priority_key');

			$priority_key = $this->bo->read_priority_key();

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$function_exchange_values = '';
			if ($receipt != '')
			{
				$function_exchange_values = "window.opener.myexecuteTEMP();";
			}

			$data = array
				(
					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'function_msg'						=> $function_msg,
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_priority_key_statustext'		=> lang('Weight for prioritising'),
					'lang_save'							=> lang('save'),
					'priority_key'						=> $priority_key,
					'exchange_values'  					=> $function_exchange_values,
					'value_authorities_demands'			=> $values['authorities_demands']
				);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('priority_form' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function edit($mode = 'edit')
		{
			$id 	= phpgw::get_var('id', 'int');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uirequest.view', 'id'=> $id));
			}

			if($mode == 'view')
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$values				= phpgw::get_var('values');
			$values_attribute	= phpgw::get_var('values_attribute');

			$bypass 			= phpgw::get_var('bypass', 'bool');

			if($_POST && !$bypass)
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}",'property');

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}
				$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			elseif ($mode == 'edit')
			{
				$location_code 	= phpgw::get_var('location_code');
				$tenant_id 		= phpgw::get_var('tenant_id', 'int');

				if(phpgw::get_var('p_num'))
				{
					$p_entity_id	= phpgw::get_var('p_entity_id', 'int');
					$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
					$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
					$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
					$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');
				}

				$origin		= phpgw::get_var('origin');
				$origin_id	= phpgw::get_var('origin_id', 'int');

				//23.jun 08: This will be handled by the interlink code - just doing a quick hack for now...
				if($origin == '.ticket' && $origin_id && !$values['descr'])
				{
					$boticket= CreateObject('property.botts');
					$ticket = $boticket->read_single($origin_id);
					$values['descr'] = $ticket['details'];
					$values['title'] = $ticket['subject'];
					$ticket_notes = $boticket->read_additional_notes($origin_id);
					$i = count($ticket_notes)-1;
					if(isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
					{
						$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
					}
				}

				if($p_entity_id && $p_cat_id)
				{
					$boadmin_entity	= CreateObject('property.boadmin_entity');
					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}


				if($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num, 'view' => true));
				}

			}

			if($values['origin'])
			{
				$origin		= $values['origin'];
				$origin_id	= $values['origin_id'];
			}

			$interlink 	= CreateObject('property.interlink');

			if(isset($origin) && $origin)
			{
				unset($values['origin']);
				unset($values['origin_id']);
				$values['origin'][0]['location']= $origin;
				$values['origin'][0]['descr']= $interlink->get_location_name($origin);
				$values['origin'][0]['data'][]= array(
					'id'	=> $origin_id,
					'link'	=> $interlink->get_relation_link(array('location' => $origin), $origin_id),
				);
			}



			//_debug_array($values);

			if ($values['save'] && $mode == 'edit')
			{
				if(!$values['location'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
					$error_id=true;
				}

				if(!$values['title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a request TITLE !'));
					$error_id=true;
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}

				if($values['consume_value'] && !$values['consume_date'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a date !'));
				}
				if($values['planning_value'] && !$values['planning_date'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a date !'));
				}


				if(isset($values['budget']) && $values['budget'])
				{
					$values['budget'] = str_replace(' ', '', $values['budget']);
					if( !ctype_digit($values['budget']))
					{
						$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
						$error_id=true;
					}
				}

				if(is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}
					}
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if(!$receipt['error'])
				{
					if($values['copy_request'])
					{
						$action='add';
					}
					$receipt = $this->bo->save($values,$action,$values_attribute);
					if (! $receipt['error'])
					{
						$id = $receipt['id'];
					}

					//----------files
					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/request/{$id}/", $values);
					}

					$values['file_name']=str_replace(" ","_",$_FILES['file']['name']);
					$to_file = "{$bofiles->fakebase}/request/{$id}/{$values['file_name']}";

					if(!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}

					if($values['file_name'])
					{
						$bofiles->create_document_dir("request/{$id}");
						$bofiles->vfs->override_acl = 1;

						if(!$bofiles->vfs->cp (array (
							'from'	=> $_FILES['file']['tmp_name'],
							'to'	=> $to_file,
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
					//---------end files

					$function_msg = lang('Edit request');

					if ($values['notify'])
					{
						$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
						$headers = "Return-Path: <". $coordinator_email .">\r\n";
						$headers .= "From: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Bcc: " . $coordinator_name . "<" . $coordinator_email .">\r\n";
						$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";

						$subject = lang(notify).": ". $id;
						$message = lang(request) . " " . $id ." ". lang('is registered');

						if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
						{
							$bcc = $coordinator_email;
							if (!is_object($GLOBALS['phpgw']->send))
							{
								$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
							}

							$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['mail_address'], $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'plain');
						}
						else
						{
							$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
						}
					}

					if($rcpt)
					{
						$receipt['message'][]=array('msg'=>lang('%1 is notified',$values['mail_address']));
					}
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
					}

					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			if(!$receipt['error'] && !$bypass && $id)
			{
				$values	= $this->bo->read_single($id);
				$record_history = $this->bo->read_record_history($id);
			}

			$table_header_history[] = array
				(
					'lang_date'		=> lang('Date'),
					'lang_user'		=> lang('User'),
					'lang_action'		=> lang('Action'),
					'lang_new_value'	=> lang('New value')
				);

			if ($id)
			{
				$function_msg = lang("{$mode} request");
			}
			else
			{
				$function_msg = lang('Add request');
				$values	= $this->bo->read_single(0, $values);
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type = $mode == 'edit' ? 'form' : 'view';

			$location_data=$this->bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> -1, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> true,
					'lookup_type'	=> $lookup_type,
					'lookup_entity'	=> $this->bocommon->get_lookup_entity('request'),
					'entity_data'	=> $values['p']
				)
			);


			if($values['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						$location_data['location'][$i]['value'] = $values['contact_phone'];
					}
				}
			}

			$link_data = array
				(
					'menuaction'	=> "property.uirequest.{$mode}",
					'id'			=> $id
				);

			if(!$values['coordinator'])
			{
				$values['coordinator']=$this->account;
			}

			$supervisor_id=$GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];

			$notify = $this->config->config_data['workorder_approval'];

			if ($supervisor_id && ($notify=='yes'))
			{
				$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
				$supervisor_email = $prefs['email'];
			}


			if($values['project_id'])
			{
				$project_lookup_data = array
					(
						'menuaction'	=> 'property.uiproject.view'
					);
			}

			$show_dates = isset($this->config->config_data['request_show_dates']) && $this->config->config_data['request_show_dates'] ? 1 : '';
			$jscal = CreateObject('phpgwapi.jscalendar');
			if($show_dates)
			{
				$jscal->add_listener('values_start_date');
				$jscal->add_listener('values_end_date');
			}

			$jscal->add_listener('values_consume_date');
			$jscal->add_listener('values_planning_date');
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
				(
					'menuaction'	=> 'property.uirequest.view_file',
					'location_code'	=>$values['location_data']['location_code'],
					'id'			=>$id
				);

			$link_to_files = $this->config->config_data['files_url'];

			$j	= count($values['files']);
			for ($i=0;$i<$j;$i++)
			{
				$values['files'][$i]['file_name']=urlencode($values['files'][$i]['name']);
			}

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'value_date','label'=>lang('Date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_user','label'=>lang('User'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_action','label'=>lang('Action'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_old_value','label' => lang('old value'), 'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_new_value','label'=>lang('New Value'),'sortable'=>true,'resizeable'=>true)))
				);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			for($z=0; $z<count($values['files']); $z++)
			{
				if ($link_to_files != '')
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_to_files.'/'.$values['files'][$z]['directory'].'/'.$values['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$values['files'][$z]['name'].'</a>';
				}
				else
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$values['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'" style="cursor:help">'.$values['files'][$z]['name'].'</a>';
				}				
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$values['files'][$z]['name'].'" title="'.lang('Check to delete file').'" style="cursor:help">';
			}									

			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')))
				);



			$_predisposed = 0;
			if($this->acl_edit)
			{
				$_lang_delete = lang('Check to delete');
				foreach($values['consume'] as & $consume)
				{
					$_predisposed = $_predisposed + $consume['amount'];
					$consume['delete'] = "<input type='checkbox' name='values[delete_consume][]' value='{$consume['id']}' title='{$_lang_delete}'>";
				}
				foreach($values['planning'] as & $planning)
				{
					$_predisposed = $_predisposed + $planning['amount'];
					$planning['delete'] = "<input type='checkbox' name='values[delete_planning][]' value='{$planning['id']}' title='{$_lang_delete}'>";
				}

			}
			
			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($values['planning']),
					'total_records'			=> count($values['planning']),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);



			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array('key' => 'amount','label'=>lang('amount'),'sortable'=>true,'resizeable'=>true, 'formatter' => FormatterRight),
														array('key' => 'date','label'=>lang('date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'delete','label'=>lang('delete'),'sortable'=>false,'resizeable'=>false)))
				);

			$datavalues[3] = array
				(
					'name'					=> "3",
					'values' 				=> json_encode($values['consume']),
					'total_records'			=> count($values['consume']),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);



			$myColumnDefs[3] = array
				(
					'name'		=> "3",
					'values'	=>	json_encode(array(	array('key' => 'amount','label'=>lang('amount'),'sortable'=>true,'resizeable'=>true, 'formatter' => FormatterRight),
														array('key' => 'date','label'=>lang('date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'delete','label'=>lang('delete'),'sortable'=>false,'resizeable'=>false)))
				);


			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uirequest.attrib_history',
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
			}

			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->config->config_data['filter_buildingpart']) ? $this->config->config_data['filter_buildingpart'] : array();
			
			if($filter_key = array_search('.project.request', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}


			$data = array
				(
					'mode'								=> $mode,
					'value_authorities_demands' 		=> isset($this->config->config_data['authorities_demands']) &&  $this->config->config_data['authorities_demands'] ? $this->config->config_data['authorities_demands'] : 0,
					'suppressmeter'						=> isset($this->config->config_data['project_suppressmeter']) && $this->config->config_data['project_suppressmeter'] ? 1 : '',
					'show_dates'						=> $show_dates,
					'custom_attributes'					=> array('attributes' => $values['attributes']),
					'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'datatable'							=> $datavalues,
					'myColumnDefs'						=> $myColumnDefs,
					'tabs'								=> self::_generate_tabs(),
					'fileupload'						=> true,
					'link_view_file'					=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
					'link_to_files'						=> $link_to_files,
					'files'								=> $values['files'],
					'lang_files'						=> lang('files'),
					'lang_filename'						=> lang('Filename'),
					'lang_file_action'					=> lang('Delete file'),
					'lang_view_file_statustext'			=> lang('click to view file'),
					'lang_file_action_statustext'		=> lang('Check to delete file'),
					'lang_upload_file'					=> lang('Upload file'),
					'lang_file_statustext'				=> lang('Select file to upload'),

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

					'value_acl_location'				=> $this->acl_location,
					'value_target'						=> $values['target'],
					'value_origin'						=> $values['origin'],
					'value_origin_type'					=> $origin,
					'value_origin_id'					=> $origin_id,
					'lang_origin_statustext'			=> lang('Link to the origin for this request'),

					'generate_project_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit')),
					'edit_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.edit', 'id'=> $id)),
					'acl_add_project'					=> $mode == 'view' ? 0 : $this->acl->check('.project', PHPGW_ACL_ADD, 'property'),
					'lang_generate_project'				=> lang('Generate project'),
					'lang_generate_project_statustext'	=> lang('Generate a project from this request'),
					'location_code'						=> $values['location_code'],
					'p_num'								=> $values['p_num'],
					'p_entity_id'						=> $values['p_entity_id'],
					'p_cat_id'							=> $values['p_cat_id'],
					'tenant_id'							=> $values['tenant_id'],

					'lang_importance'					=> lang('Importance'),

					'importance_weight'					=> $importance_weight,

					'lang_no_workorders'				=> lang('No workorder budget'),
					'workorder_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
					'record_history'					=> $record_history,
					'table_header_history'				=> $table_header_history,
					'lang_history'						=> lang('History'),
					'lang_no_history'					=> lang('No history'),

					'img_cal'							=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
					'lang_datetitle'					=> lang('Select date'),

					'value_entry_date'					=> $values['entry_date'],
					'value_closed_date'					=> $values['closed_date'],
					'value_in_progress_date'			=> $values['in_progress_date'],
					'value_delivered_date'				=> $values['delivered_date'],

					'lang_start_date_statustext'		=> lang('Select the estimated end date for the request'),
					'lang_start_date'					=> lang('request start date'),
					'value_start_date'					=> $values['start_date'],

					'lang_end_date_statustext'			=> lang('Select the estimated end date for the request'),
					'lang_end_date'						=> lang('request end date'),
					'value_end_date'					=> $values['end_date'],

					'lang_copy_request'					=> lang('Copy request ?'),
					'lang_copy_request_statustext'		=> lang('Choose Copy request to copy this request to a new request'),

					'lang_power_meter'					=> lang('Power meter'),
					'lang_power_meter_statustext'		=> lang('Enter the power meter'),
					'value_power_meter'					=> $values['power_meter'],

					'lang_budget'						=> lang('Budget'),
					'value_budget'						=> number_format($values['budget'], 0, ',', ' '),
					'lang_budget_statustext'			=> lang('Enter the budget'),
					'value_diff'						=> number_format(((int)$values['budget'] - $_predisposed), 0, ',', ' '),

					'location_data'						=> $location_data,
					'location_type'						=> 'form',
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.index')),
					'lang_category'						=> lang('category'),
					'lang_save'							=> lang('save'),
					'lang_done'							=> lang('done'),

					'lang_request_id'					=> lang('request ID'),
					'value_request_id'					=> $id,

					'value_title'						=> $values['title'],

					'value_descr'						=> $values['descr'],
					'lang_score'						=> lang('Score'),
					'value_score'						=> $values['score'],
					'lang_done_statustext'				=> lang('Back to the list'),
					'lang_save_statustext'				=> lang('Save the request'),
					'lang_no_cat'						=> lang('Select category'),
					'lang_cat_statustext'				=> lang('Select the category the request belongs to. To do not use a category select NO CATEGORY'),
					'value_cat_id'						=> $values['cat_id'],

					'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),

					'lang_coordinator'					=> lang('Coordinator'),
					'lang_user_statustext'				=> lang('Select the coordinator the request belongs to. To do not use a category select NO USER'),
					'select_user_name'					=> 'values[coordinator]',
					'lang_no_user'						=> lang('Select coordinator'),
					'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),

					'status_list'						=> array('options' => $this->bo->select_status_list('select',$values['status'])),
					'lang_no_status'					=> lang('Select status'),
					'lang_status'						=> lang('Status'),
					'lang_status_statustext'			=> lang('What is the current status of this request ?'),

					'branch_list'						=> array('options' => $this->boproject->select_branch_list($values['branch_id'])),
					'lang_branch'						=> lang('branch'),
					'lang_no_branch'					=> lang('Select branch'),
					'lang_branch_statustext'			=> lang('Select the branches for this request'),

					'notify'							=> $notify,
					'lang_notify'						=> lang('Notify'),
					'lang_notify_statustext'			=> lang('Check this to notify your supervisor by email'),
					'value_notify_mail_address'			=> $supervisor_email,

					'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

					'authorities_demands'				=> array('options' => execMethod('property.bogeneric.get_list',array('type' => 'authorities_demands', 'selected' => $values['authorities_demands']))),
					'regulations'						=> execMethod('property.bogeneric.get_list',array('type' => 'regulations', 'selected' => $values['regulations'], 'fields' => array('descr', 'external_ref'))),

					'condition_list'					=> $this->bo->select_conditions($id),
					'building_part_list'				=> array('options' => $this->bocommon->select_category_list(array('type'=> 'building_part','selected' =>$values['building_part'], 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),
					'value_consume'						=> isset($receipt['error']) ? $values['consume_value'] : ''
				);
//_debug_array($data);die();
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$appname	= lang('request');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->add_file(array('request', 'files','attributes_form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'request.edit', 'property' );
		}

		function delete()
		{
			$id = phpgw::get_var('id', 'int');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
			}

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}


			//$id = phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uirequest.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.delete', 'id'=> $id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname	= lang('request');
			$function_msg	= lang('delete request');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit($mode = 'view');
		}

		protected function _generate_tabs()
		{
			$tabs = array
				(
					'general'		=> array('label' => lang('general'), 'link' => '#general'),
					'condition'		=> array('label' => lang('condition'), 'link' => '#condition'),
					'budget'		=> array('label' => lang('documents'), 'link' => '#documents'),
					'history'		=> array('label' => lang('history'), 'link' => '#history')
				);

			phpgwapi_yui::tabview_setup('project_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, 'general');
		}
	}

