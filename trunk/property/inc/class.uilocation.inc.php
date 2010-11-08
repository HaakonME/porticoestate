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
	* @subpackage location
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

	class property_uilocation
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;

		var $public_functions = array
		(
			'download'  			=> true,
			'index'  				=> true,
			'view'   				=> true,
			'edit'   				=> true,
			'delete' 				=> true,
			'update_cat'			=> true,
			'stop'					=> true,
			'summary'				=> true,
			'columns'				=> true,
			'update_location'		=> true,
			'responsiblility_role'	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location';
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->soadmin_location		= CreateObject('property.soadmin_location');
			$this->acl 					= & $GLOBALS['phpgw']->acl;

			$this->type_id				= $this->bo->type_id;

			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
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
				'part_of_town_id'	=> $this->part_of_town_id,
				'district_id'		=> $this->district_id,
				'status'		=> $this->status,
				'type_id'		=> $this->type_id,
			//	'allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$summary	= phpgw::get_var('summary', 'bool', 'GET');
			$type_id	= phpgw::get_var('type_id', 'int', 'GET');
			$lookup 	= phpgw::get_var('lookup', 'bool');
			//$lookup_name 	= phpgw::get_var('lookup_name');
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');

			if(!$summary)
			{
				$list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>true));
			}
			else
			{
				$list= $this->bo->read_summary();
			}

			$uicols	= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function columns()
		{
//			phpgwapi_yui::load_widget('tabview');
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'] && $this->type_id)
			{
				$GLOBALS['phpgw']->preferences->add('property','location_columns_' . $this->type_id . !!$this->lookup,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uilocation.columns',
				'type_id'		=> $this->type_id,
				'lookup'		=> $this->lookup
			);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'		=> $this->bo->column_list($selected , $this->type_id, $allrows=true),
				'function_msg'		=> $function_msg,
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'		=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}


		function index()
		{

			$type_id	= $this->type_id;
			// $lookup use for pop-up
			$lookup 	= $this->lookup;
			// $lookup_name use in pop-up option "project"
			$lookup_name 	= phpgw::get_var('lookup_name');
			// use in option menu TENANT
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');
			$block_query	= phpgw::get_var('block_query', 'bool');
			$dry_run=false;

			if(!$type_id)
			{
				$type_id = 1;
			}
			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();
			$values_combo_box = array();

    		$integrationurl = '';
    		$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$custom_config	= CreateObject('admin.soconfig',$location_id);
			$_integration_config = isset($custom_config->config_data['integration']) ? $custom_config->config_data['integration'] : array();

			if(isset($_integration_config['url']))
			{
				if(isset($_integration_config['auth_hash_name']) && $_integration_config['auth_hash_name'] && isset($_integration_config['auth_url']) && $_integration_config['auth_url'])
				{
					//get session key from remote system
				
					$arguments = array($_integration_config['auth_hash_name'] => $_integration_config['auth_hash_value']);
					$query = http_build_query($arguments);
					$auth_url = $_integration_config['auth_url'];
					$request = "{$auth_url}?{$query}";

					$aContext = array
					(
					   	'http' => array
						(
							'request_fulluri' => true,
						),
					);

					if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
					{
						$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
					}


					$cxContext = stream_context_create($aContext);
					$response = trim(file_get_contents($request, False, $cxContext));
				}


				$_integration_config['url']		= htmlspecialchars_decode($_integration_config['url']);
				$_integration_config['parametres']= htmlspecialchars_decode($_integration_config['parametres']);
				$integration_name = isset($_integration_config['name']) && $_integration_config['name'] ? $_integration_config['name'] : lang('integration');

				parse_str($_integration_config['parametres'], $output);
	
				foreach ($output as $_dummy => $_substitute)
				{
					$_keys[] = $_substitute;
					$__substitute = trim($_substitute, '_');
					$_values[] = $this->$__substitute;
				}
				unset($output);

				$_sep = '?';
				if (stripos($_integration_config['url'],'?'))
				{
					$_sep = '&';
				}
				$_param = str_replace($_keys, $_values, $_integration_config['parametres']);

				$integrationurl = "{$_integration_config['url']}{$_sep}{$_param}";
				$integrationurl .= "&{$_integration_config['auth_key_name']}={$response}";
				
				$_integration_config['location_data']= htmlspecialchars_decode($_integration_config['location_data']);
				
				$parameters_integration = array();
				if($_integration_config['location_data'])
				{
					parse_str($_integration_config['location_data'], $output);
	
					foreach ($output as $_name => $_substitute)
					{
						if($_substitute == '__loc1__') // This one is a link...
						{
							$_substitute = '__location_code__';
						}
						
						$parameters_integration['parameter'][] = array
						(
							'name'		=> $_name,
							'source'	=> trim($_substitute, '_'),
						);
					}
				}
	
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {
				if(!$lookup)
				{
					$datatable['menu']				= $this->bocommon->get_menu();
				}

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilocation.index',
	    					'type_id'        		=> $type_id,
							'query'            		=> $this->query,
 	                        'district_id'        	=> $this->district_id,
 	                        'part_of_town_id'    	=> $this->part_of_town_id,
 	                        'lookup'        		=> $lookup,
 	                        'lookup_tenant'        	=> $lookup_tenant,
 	                        'lookup_name'        	=> $lookup_name,
 	                        'cat_id'        		=> $this->cat_id,
 	                        'status'        		=> $this->status,
 	                        'location_code'			=> $this->location_code
	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilocation.index',"
	    											."type_id:'{$type_id}',"
	    											."query:'{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."part_of_town_id:'{$this->part_of_town_id}',"
						 	                        ."lookup:'{$lookup}',"
						 	                        ."second_display:1,"
 	                        						."lookup_tenant:'{$lookup_tenant}',"
						 	                        ."lookup_name:'{$lookup_name}',"
						 	                        ."cat_id:'{$this->cat_id}',"
 	                        						."status:'{$this->status}',"
 	                        						."location_code:'{$this->location_code}',"
 	                        						."block_query:'{$block_query}'";

				 // $values_combo_box  se usar� para escribir en el HTML, usando el XSLT
				$values_combo_box[0]  = $this->bocommon->select_category_list(array('format'=>'filter',
	                                                                        'selected' => $this->cat_id,
	                                                                        'type' =>'location',
	                                                                        'type_id' =>$type_id,
	                                                                        'order'=>'descr'));
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

		        $values_combo_box[2] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift ($values_combo_box[2],$default_value);

				if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
		        {
		             $values_combo_box[3] = $this->bo->get_owner_list('filter', $this->filter);
		        }
		        else
		        {
		            $values_combo_box[3] = $this->bo->get_owner_type_list('filter', $this->filter);
		        }
		        $default_value = array ('id'=>'','name'=>lang('show all'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilocation.index',
									'type_id' 			=> $type_id,
									'district_id'       => $this->district_id,
									'part_of_town_id'   => $this->part_of_town_id,
									'lookup'        	=> $lookup,
									'lookup_tenant'     => $lookup_tenant,
									'lookup_name'       => $lookup_name,
									'cat_id'        	=> $this->cat_id,
									)
						),
						'fields'	=> array(
                                    'field' => array(
			                                        array( //boton 	CATEGORY
														'id' => 'btn_cat_id',
														'name' => 'cat_id',
														'value'	=> lang('Category'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 1
			                                        ),
			                                        array( //boton 	DISTINT
														'id' => 'btn_district_id',
														'name' => 'district_id',
														'value'	=> lang('District'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 2
			                                        ),
			                                        array( //boton 	PART OF TOWN
														'id' => 'btn_part_of_town_id',
														'name' => 'part_of_town_id',
														'value'	=> lang('Part of Town'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 3
			                                        ),
			                                        array( //boton 	FILTER
														'id' => 'btn_owner_id',
														'name' => 'owner_id',
														'value'	=> lang('Filter'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 4
			                                        ),
						                            //for link "columns", next to Export button
										           array(
						                                'type' => 'link',
						                                'id' => 'btn_columns',
						                                'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
																				           array
																				              (
																				               'menuaction' => 'property.uilocation.columns',
																				               'type_id'  => $type_id,
																				               'lookup'  => $this->lookup
																				              ))."','','width=300,height=600,scrollbars=1')",
														'value' => lang('columns'),
														'tab_index' => 9
													),
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_export',
						                                'value'	=> lang('download'),
						                                'tab_index' => 8
						                            ),
													array( //hidden type_id
						                                'type'	=> 'hidden',
						                            	'id'	=> 'type_id',
						                                'value'	=> $type_id
						                            )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
																		'id' => 'values_combo_box_0',
																		'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
							                                      ),
							                                array( //div values  combo_box_1
																		'id' => 'values_combo_box_1',
																		'value'	=> $this->bocommon->select2String($values_combo_box[1])
							                                      ),
															 array( //div values  combo_box_2
																		'id' => 'values_combo_box_2',
																		'value'	=> $this->bocommon->select2String($values_combo_box[2])
							                                      ),
							                                array( //div values  combo_box_3
																		'id' => 'values_combo_box_3',
																		'value'	=> $this->bocommon->select2String($values_combo_box[3])
							                                      )
		                       								)
												)
										  )
				);

				if($integrationurl)
				{	
			        $datatable['actions']['form'][0]['fields']['field'][] =  array
			        								(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_integration',
						                                'value'	=> $integration_name,
						                                'tab_index' => 10
						                            );
				}


				if(!$block_query)
				{	
					$datatable['actions']['form'][0]['fields']['field'][] =  array
													(
														'id' => 'btn_search',
														'name' => 'search',
														'value'    => lang('search'),
														'type' => 'button',
														'tab_index' => 6
													);
			                                        
						$datatable['actions']['form'][0]['fields']['field'][] = array
													(
														'name'     => 'query',
														'id'     => 'txt_query',
														'value'    => $this->query,//'',//$query,
														'type' => 'text',
														'size'    => 28,
														'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 5
			                                        );
				}

				if(!$lookup)
				{
					$datatable['actions']['form'][0]['fields']['field'][] =  array
													(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_new',
						                                'value'	=> lang('add'),
						                                'tab_index' => 7
						                            );
				}

				$dry_run=true;

			}

			$location_list = array();
			//cramirez: $dry_run avoid to load all data the first time
			$location_list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));

			$uicols = $this->bo->uicols;

			$content = array();
			$j=0;
			if (isset($location_list) && is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($location['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $location['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $location[$uicols['name'][$i]];
								//$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $i;
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $location[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $location[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $location[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

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
							'name'		=> 'location_code',
							'source'	=> 'location_code'
						),
					)
				);

				$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'sibling',
							'source'	=> 'location_code'
						),
					)
				);

				$parameters3 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'search_for',
							'source'	=> 'location_code'
						),
					)
				);

				if($this->acl->check('run', PHPGW_ACL_READ, 'rental'))
				{
					$datatable['rowactions']['action'][] = array(
							'my_name'			=> 'view',
							'text' 			=> lang('contracts'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	  => 'rental.uicontract.index',
												'search_type'	  => 'location_id',
												'contract_status' => 'all',
												'populate_form'   => 'yes'
											)),
						'parameters'	=> $parameters3
					);
				}

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.view',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'view',
						'text' 			=> lang('open view in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.view',
											'lookup_tenant'	=> $lookup_tenant,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters2
					);
				}
				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);

				}
				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$datatable['rowactions']['action'][] = array(
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


				if($integrationurl)
				{	
					$datatable['rowactions']['action'][] = array(
							'my_name'		=> 'integration',
							'text'	 		=> $integration_name,
							'action'		=> $integrationurl.'&target=_blank',
							'parameters'	=> $parameters_integration
					);
				}

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.delete',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	=> 'property.uilocation.edit',
												'type_id'		=>	$type_id,
												'parent'		=>  $this->location_code
											))
					);
				}

				unset($parameters);
			}
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = !isset($uicols['formatter'][$i])  || !$uicols['formatter'][$i] ?  '""' : $uicols['formatter'][$i];

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;
					//$datatable['headers']['header'][$i]['formatter']		= (isset($uicols['formatter'][$i])? $uicols['formatter'][$i]:"");
					if($uicols['name'][$i]=='loc1'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'fm_location1.loc1';
					}
					elseif($uicols['name'][$i]=='street_name'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'street_name';
					}
					elseif(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH')):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
					endif;
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}
			// for POP-UPs
			if($lookup)
			{
				$input_name		= $GLOBALS['phpgw']->session->appsession('lookup_fields','property');

				$function_exchange_values = '';

				if(is_array($input_name))
				{
					for ($k=0;$k<count($input_name);$k++)
					{
						$function_exchange_values .= "opener.document.forms[0]." . $input_name[$k] . ".value = '';" ."\r\n";
					}
				}

	/*			for ($i=0;$i<count($uicols['name']);$i++)
				{
					if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
					{
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = "";' ."\r\n";
					}
				}
*/
				for ($i=0;$i<count($uicols['name']);$i++)
				{
					if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
					{
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = "";' ."\r\n";
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = valida(data.getData("'.$uicols['name'][$i].'"));' ."\r\n";
						//$function_exchange_values .= 'opener.document.forms[0].' . $uicols['name'][$i] .'.value = valida(data.getData("'.$uicols['name'][$i].'"));' ."\r\n";
					}
				}

				$function_exchange_values .='window.close()';

				$datatable['exchange_values'] = $function_exchange_values;

				$function_valida  = "var pos = data.indexOf('</a>');"."\r\n";
				$function_valida .= "if(pos==-1){"."\r\n";
				$function_valida .= "return data;"."\r\n";
				$function_valida .= "}else{"."\r\n";
				$function_valida .= "pos = data.indexOf('>');"."\r\n";
				$function_valida .= "var valor = data.slice(pos+1);"."\r\n";
				$function_valida .= "pos = valor.indexOf('<');"."\r\n";
				$function_valida .= "valor = valor.slice(0,pos);"."\r\n";
				$function_valida .= "return valor;"."\r\n";
				$function_valida .= "}"."\r\n";

				$datatable['valida'] = $function_valida;
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
					$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($location_list);

			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'loc1'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}





			$appname = lang('location');

			if($lookup)
			{
				$lookup_list	= $GLOBALS['phpgw']->session->appsession('lookup_name','property');
				$function_msg	= $lookup_list[$lookup_name];
			}
			else
			{
				if($lookup_tenant)
				{
					$function_msg	= lang('Tenant');
				}
				else
				{
					$function_msg					= $uicols['descr'][($type_id)];
				}
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
					'records'			=> array()
	    		);

				$json['integrationurl']	= $integrationurl;

				// values for datatable
	    		if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
	    			foreach( $datatable['rows']['row'] as $row )
	    			{
		    			$json_row = array();
		    			foreach( $row['column'] as $column)
		    			{
		    				if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
		    				}
		    				else
		    				{
		    				  $json_row[$column['name']] = $column['value'];
		    				}
		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}

				// values for control select
				//cr@ccfirst.com 10/09/08 values passed for update select in YUI
				$opt_cb_depend =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>'!no part of town');
				array_unshift ($opt_cb_depend,$default_value);

				$json['hidden']['dependent'][] = array ( 'id' => $this->part_of_town_id,
	                                                      'value' => $this->bocommon->select2String($opt_cb_depend)
														);

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

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

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
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.index', 'property' );

			//$this->save_sessiondata();
		}



		function responsiblility_role()
		{

			$type_id = phpgw::get_var('type_id', 'int');

			$dry_run=false;

			if(!$type_id)
			{
				$type_id = 1;
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$values = phpgw::get_var('values');

			$receipt = array();
			if($values && $this->acl_edit)
			{
_debug_array($values);
				$receipt = $this->bo->update_roles($values);
			}


			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilocation.responsiblility_role',
	    					'type_id'        		=> $type_id,
							'query'            		=> $this->query,
 	                        'district_id'        	=> $this->district_id,
 	                        'part_of_town_id'    	=> $this->part_of_town_id,
 	                        'lookup'        		=> $lookup,
 	                        'lookup_tenant'        	=> $lookup_tenant,
 	                        'lookup_name'        	=> $lookup_name,
 	                        'cat_id'        		=> $this->cat_id,
 	                        'status'        		=> $this->status,
 	                        'location_code'			=> $this->location_code
	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilocation.responsiblility_role',"
	    											."type_id:'{$type_id}',"
	    											."query:'{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."part_of_town_id:'{$this->part_of_town_id}',"
						 	                        ."lookup:'{$lookup}',"
						 	                        ."second_display:1,"
 	                        						."lookup_tenant:'{$lookup_tenant}',"
						 	                        ."lookup_name:'{$lookup_name}',"
						 	                        ."cat_id:'{$this->cat_id}',"
 	                        						."status:'{$this->status}',"
 	                        						."location_code:'{$this->location_code}',"
 	                        						."block_query:'{$block_query}'";


				$values_combo_box[0]  = execMethod('property.soadmin_location.read',array());

				$values_combo_box[1]  = $this->bocommon->select_category_list(array('format'=>'filter',
	                                                                        'selected' => $this->cat_id,
	                                                                        'type' =>'location',
	                                                                        'type_id' =>$type_id,
	                                                                        'order'=>'descr'));


				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[2],$default_value);

		        $values_combo_box[3] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift ($values_combo_box[3],$default_value);

				$values_combo_box[4]  = $this->bocommon->get_user_list_right2('filter',PHPGW_ACL_EDIT,$this->user_id,'.location');
				array_unshift ($values_combo_box[4],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine roles')));
				$default_value = array('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[4],$default_value);



		        $_role_criteria = array
		        (
		        	'allrows'	=> true,
		        	'order'		=> 'name'
		        );

		        $_role = CreateObject('property.socategory');

				$_role->get_location_info('responsibility_role','');
		        
				$roles = $_role->read($_role_criteria);
				foreach ($roles as $role)
				{
					if(ctype_digit(ltrim($role['location'],'.location')))
					{
						 $values_combo_box[5][] = array
						 (
						 	'id'		=> $role['id'],
						 	'name'		=> $role['name'],
						 	'type_id'	=> ltrim($role['location'],'.location')
						 );
					}
				}
		        
		 		$default_value = array ('id'=>'','name'=>lang('no role'));
				array_unshift ($values_combo_box[5],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilocation.responsiblility_role',
									'entity_id'			=> $this->entity_id,
									'cat_id'			=> $this->cat_id,
									'district_id'		=> $this->district_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
													array( //boton 	CATEGORY
														'id' => 'btn_type_id',
														'name' => 'type_id',
														'value'	=> lang('location type'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 1
													),
													array( //boton 	CATEGORY
														'id' => 'btn_cat_id',
														'name' => 'cat_id',
														'value'	=> lang('Category'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 2
													),
													array( //boton 	CATEGORY
														'id' => 'btn_district_id',
														'name' => 'district_id',
														'value'	=> lang('District'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 3
													),
			                                        array( //boton 	PART OF TOWN
														'id' => 'btn_part_of_town_id',
														'name' => 'part_of_town_id',
														'value'	=> lang('Part of Town'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 4
			                                        ),
			                                        array( //boton contact
														'id' => 'btn_contact_id',
														'name' => 'contact_id',
														'value'	=> lang('user'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 5
			                                        ),
			                                        array( //boton 	role
														'id' => 'btn_role_id',
														'name' => 'role_id',
														'value'	=> lang('role'),
														'type' => 'button',
														'style' => 'filter',
														'tab_index' => 6
			                                        ),
													array( // boton SAVE
														'id'	=> 'btn_save',
														//'name' => 'save',
														'value'	=> lang('save'),
														'tab_index' => 7,
														'type'	=> 'button'
		                    					        ),
													array( //boton  SEARCH
														'id' => 'btn_search',
														'name' => 'search',
														'value'    => lang('search'),
														'type' => 'button',
														'tab_index' => 8
													),
			   										array( // TEXT IMPUT
														'name'     => 'query',
														'id'     => 'txt_query',
														'value'    => '',//'',//$query,
														'type' => 'text',
														'size'    => 28,
														'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 7
													)
		                           				),
		                       		'hidden_value' => array(
															array( //div values  combo_box_0
																		'id' => 'values_combo_box_0',
																		'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
							                                      ),
															array( //div values  combo_box_1
																		'id' => 'values_combo_box_1',
																		'value'	=> $this->bocommon->select2String($values_combo_box[1]) //i.e.  id,value/id,vale/
							                                      ),
															array( //div values  combo_box_2
																		'id' => 'values_combo_box_2',
																		'value'	=> $this->bocommon->select2String($values_combo_box[2]) //i.e.  id,value/id,vale/
							                                      ),
															array( //div values  combo_box_3
																		'id' => 'values_combo_box_3',
																		'value'	=> $this->bocommon->select2String($values_combo_box[3]) //i.e.  id,value/id,vale/
							                                      ),
															array( //div values  combo_box_4
																		'id' => 'values_combo_box_4',
																		'value'	=> $this->bocommon->select2String($values_combo_box[4]) //i.e.  id,value/id,vale/
							                                      ),
															array( //div values  combo_box_5
																		'id' => 'values_combo_box_5',
																		'value'	=> $this->bocommon->select2String($values_combo_box[5]) //i.e.  id,value/id,vale/
							                                      )
		                       								)
												)
										  )
				);

				$dry_run=true;

			}

			$location_list = array();

			$location_list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));

			$uicols = $this->bo->uicols;

			$uicols['name'][]		= 'select';
			$uicols['descr'][]		= lang('select');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'myFormatterCheck';
			$uicols['input_type'][]	= '';

			$content = array();
			$j=0;
			if (isset($location_list) && is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($location['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $location['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $location[$uicols['name'][$i]];
								//$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $i;

								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $location[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $location[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $location[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

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
							'name'		=> 'location_code',
							'source'	=> 'location_code'
						),
					)
				);

				$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'sibling',
							'source'	=> 'location_code'
						),
					)
				);

				$parameters3 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'search_for',
							'source'	=> 'location_code'
						),
					)
				);

				if($this->acl->check('run', PHPGW_ACL_READ, 'rental'))
				{
					$datatable['rowactions']['action'][] = array(
							'my_name'			=> 'view',
							'text' 			=> lang('contracts'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	  => 'rental.uicontract.index',
												'search_type'	  => 'location_id',
												'contract_status' => 'all',
												'populate_form'   => 'yes'
											)),
						'parameters'	=> $parameters3
					);
				}

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.view',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'view',
						'text' 			=> lang('open view in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.view',
											'lookup_tenant'	=> $lookup_tenant,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters2
					);
				}
				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name'			=> 'edit',
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.edit',
											'lookup_tenant'	=> $lookup_tenant,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);

				}
				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$datatable['rowactions']['action'][] = array(
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


				if($integrationurl)
				{	
					$datatable['rowactions']['action'][] = array(
							'my_name'		=> 'integration',
							'text'	 		=> $integration_name,
							'action'		=> $integrationurl.'&target=_blank',
							'parameters'	=> $parameters_integration
					);
				}

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name'		=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uilocation.delete',
											'lookup_tenant'	=> $lookup_tenant
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	=> 'property.uilocation.edit',
												'type_id'		=>	$type_id,
												'parent'		=>  $this->location_code
											))
					);
				}

				unset($parameters);
			}
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = !isset($uicols['formatter'][$i])  || !$uicols['formatter'][$i] ?  '""' : $uicols['formatter'][$i];

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;
					//$datatable['headers']['header'][$i]['formatter']		= (isset($uicols['formatter'][$i])? $uicols['formatter'][$i]:"");
					if($uicols['name'][$i]=='loc1'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'fm_location1.loc1';
					}
					elseif($uicols['name'][$i]=='street_name'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'street_name';
					}
					elseif(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH')):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
					endif;
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
					$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($location_list);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'loc1'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname = lang('location');

			$function_msg					= lang('role');

//-- BEGIN----------------------------- JSON CODE ------------------------------

    		//values for Pagination
	    		$json = array
	    		(
	    			'recordsReturned' 	=> $datatable['pagination']['records_returned'],
    				'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
	    			'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
	    			'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'headers'			=> $uicols
	    		);

				$json['integrationurl']	= $integrationurl;

				// values for datatable
	    		if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
	    			foreach( $datatable['rows']['row'] as $row )
	    			{
		    			$json_row = array();
		    			foreach( $row['column'] as $column)
		    			{
		    				if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
		    				}
		    				else
		    				{
		    				  $json_row[$column['name']] = $column['value'];
		    				}
		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}

				// values for control select
				//cr@ccfirst.com 10/09/08 values passed for update select in YUI
				$opt_cb_depend =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>'!no part of town');
				array_unshift ($opt_cb_depend,$default_value);

				$json['hidden']['dependent'][] = array ( 'id' => $this->part_of_town_id,
	                                                      'value' => $this->bocommon->select2String($opt_cb_depend)
														);

		        $_role_criteria = array
		        (
		        	'type'		=> 'responsibility_role',
		        	'filter'	=> array('location' => ".location.{$type_id}"),
		        	'order'		=> 'name'
		        );

		        $_roles =   execMethod('property.socategory.get_list',$_role_criteria);
		 		$default_value = array ('id'=>'','name'=>lang('no role'));
				array_unshift ($_roles,$default_value);

				$json['hidden']['dependent'][] = array ( 'id' => $type_id,
	                                                      'value' => $this->bocommon->select2String($_roles)
														);

				// right in datatable
				if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
				{
					$json ['rights'] = $datatable['rowactions']['action'];
				}

				if(isset($receipt) && is_array($receipt) && count($receipt))
				{
					$json['message'][] = $receipt;
				}

				if( phpgw::get_var('phpgw_return_as') == 'json' )
				{
		    		return $json;
				}


			$datatable['json_data'] = json_encode($json);
//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . $appname . '::' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.responsiblility_role', 'property' );

			//$this->save_sessiondata();

		}


		function edit($view = '')
		{
			$get_history 		= phpgw::get_var('get_history', 'bool', 'POST');
			$change_type 		= phpgw::get_var('change_type', 'int', 'POST');
			$lookup_tenant 		= phpgw::get_var('lookup_tenant', 'bool');
			$location_code		= phpgw::get_var('location_code');
			$sibling			= phpgw::get_var('sibling');
			$parent				= phpgw::get_var('parent');
			$values_attribute	= phpgw::get_var('values_attribute');
			$location 			= explode('-',$location_code);

			if($sibling)
			{
				$parent = array();
				$sibling = explode('-',$sibling);
				$this->type_id = count($sibling);
				for ($i=0;$i<(count($sibling)-1);$i++)
				{
					$parent[] = $sibling[$i];
				}
				$parent = implode('-', $parent);
			}

			$type_id	 	= $this->type_id;

			if($location_code)
			{
				$type_id = count($location);
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if($view)
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

			$values = array();
			if(isset($_POST['save']) && !$view)
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$GLOBALS['phpgw']->session->appsession('insert_record','property','');

				if(isset($insert_record['location']) && is_array($insert_record['location']))
				{
					for ($i=0; $i<count($insert_record['location']); $i++)
					{
						$values[$insert_record['location'][$i]]= phpgw::get_var($insert_record['location'][$i], 'string', 'POST');
					}
				}

				$insert_record_attributes	= $GLOBALS['phpgw']->session->appsession('insert_record_values' . '.location.' . $this->type_id,'property');

				if(is_array($insert_record_attributes))
				{
					foreach ($insert_record_attributes as $attribute)
					{
						foreach ($values_attribute as &$attr)
						{
							if($attr['name'] ==  $attribute)
							{
								$attr['value'] = phpgw::get_var($attribute, 'string', 'POST');
							}
						}
					}
				}

				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					for ($i=0; $i<count($insert_record['extra']); $i++)
					{
						$values[$insert_record['extra'][$i]]= phpgw::get_var($insert_record['extra'][$i], 'string', 'POST');
					}
				}
			}


			$GLOBALS['phpgw']->xslttpl->add_file(array('location','attributes_form'));

			if ($values)
			{
				for ($i=1; $i<($type_id+1); $i++)
				{
					if((!$values['loc' . $i]  && (!isset($location[($i-1)])  || !$location[($i-1)])  ) || !$values['loc' . $i])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a location %1 ID !',$i));
						$error_id=true;
					}

					$values['location_code'][]= $values['loc' . $i];

					if($i<$type_id)
					{
						$location_parent[]= $values['loc' . ($i)];
					}
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category'));
				}

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['nullable'] != 1 && !$attribute['value'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if($attribute['datatype'] == 'I' && isset($attribute['value']) && $attribute['value'] && !ctype_digit($attribute['value']))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));
						}
					}
				}

				if (isset($insert_record['extra']) && array_search('street_id',$insert_record['extra']) && (!isset($values['street_id']) || !$values['street_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a street'));
				}
				if (isset($insert_record['extra']) && array_search('part_of_town_id',$insert_record['extra']) && (!isset($values['part_of_town_id']) || !$values['part_of_town_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a part of town'));
				}
				if (isset($insert_record['extra']) && array_search('owner_id',$insert_record['extra']) && (!isset($values['owner_id']) || !$values['owner_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select an owner'));
				}

				$values['location_code']=implode("-", $values['location_code']);

				if($values['location_code'] && !$location_code)
				{
					if($this->bo->check_location($values['location_code'],$type_id))


					{
						$receipt['error'][]=array('msg'=>lang('This location is already registered!') . '[ '.$values['location_code'].' ]');
						$error_location_id=true;
						$error_id=true;
					}
				}

				if($location_code)
				{
					$action='edit';
					$values['change_type'] = $change_type;


					if(!$values['change_type'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select change type'));
					}
				}
				elseif(!$location_code && !$error_id )
				{
					$location_code=$values['location_code'];
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values,$values_attribute,$action,$type_id,isset($location_parent)?$location_parent:'');
				}
				else
				{
					if(isset($location_parent) && $location_parent)
					{
						$location_code_parent=implode("-", $location_parent);
						$values = $this->bo->read_single($location_code_parent);
						/* restore date from posting */
						if(isset($insert_record['extra']) && is_array($insert_record['extra']))
						{
							for ($i=0; $i<count($insert_record['extra']); $i++)
							{
								$values[$insert_record['extra'][$i]]= phpgw::get_var($insert_record['extra'][$i], 'string', 'POST');
							}
						}
					}
				}
			}

			if(!isset($error_id) && $location_code)
			{
				$values = $this->bo->read_single($location_code,array('tenant_id'=>'lookup'));

				$check_history = $this->bo->check_history($location_code);
				if($get_history)
				{
					$history = $this->bo->get_history($location_code);
					$uicols = $this->bo->uicols;

					$j=0;
					if (isSet($history) && is_array($history))
					{
						foreach($history as $entry)
						{
							$k=0;
							for ($i=0;$i<count($uicols['name']);$i++)
							{
								if($uicols['input_type'][$i]!='hidden')
								{
									$content[$j]['row'][$k]['value'] 	= $entry[$uicols['name'][$i]];
									$content[$j]['row'][$k]['name'] 	= $uicols['name'][$i];
								}

								$content[$j]['hidden'][$k]['value'] 		= $entry[$uicols['name'][$i]];
								$content[$j]['hidden'][$k]['name'] 		= $uicols['name'][$i];
								$k++;
							}
							$j++;
						}
					}

					$uicols_count	= count($uicols['descr']);
					for ($i=0;$i<$uicols_count;$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$table_header[$i]['header'] 	= $uicols['descr'][$i];
							$table_header[$i]['width']	= '5%';
							$table_header[$i]['align']	= 'center';
						}
					}
				}
			}
			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
				unset($values['location_code']);
			}

			if(!$values)
			{
				$values['attributes']	= $this->bo->find_attribute(".location.{$this->type_id}");
				$values					= $this->bo->prepare_attribute($values, ".location.{$this->type_id}");
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$link_data = array
			(
				'menuaction'	=> $view ? 'property.uilocation.view' : 'property.uilocation.edit',
				'location_code'	=> $location_code,
				'type_id'	=> $type_id,
				'lookup_tenant'	=> $lookup_tenant
			);

			$lookup_type = $view ? 'view' : 'form';

			if(!$location_code && $parent)
			{
				$_values = $this->bo->read_single($parent,array('noattrib' => true));
				$_values['attributes'] = $values['attributes'];
			}
			else
			{
				$_values = $values;
			}
			
			$location_data=$this->bo->initiate_ui_location(array(
						'values'		=> $_values,
						'type_id'		=> ($type_id-1),
						'no_link'		=> ($type_id), // disable lookup links for location type less than type_id
						'tenant'		=> false,
						'lookup_type'	=> $lookup_type
						));

			$location_types	= $this->bo->location_types;
			$config			= $this->bo->config;

			if ($location_code)
			{
				$function_msg = lang('edit');
			}
			else
			{
				$function_msg = lang('add');
			}

			$function_msg .= ' ' .$location_types[($type_id-1)]['name'];

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

			if(!is_array($insert_record))
			{
				$insert_record = array();
			}

			$j=0;
			$additional_fields[$j]['input_text']	= $location_types[($type_id-1)]['name'];
			$additional_fields[$j]['statustext']	= $location_types[($type_id-1)]['descr'];
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id;
			$additional_fields[$j]['name']			= 'loc' . $type_id;
			$additional_fields[$j]['value']			= isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'';
			$additional_fields[$j]['class']			= 'th_text';
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];

			$j++;
			$additional_fields[$j]['input_text']	= lang('name');
			$additional_fields[$j]['statustext']	= lang('enter the name for this location');
			$additional_fields[$j]['datatype']		= 'varchar';
			$additional_fields[$j]['input_name']	= 'loc' . $type_id . '_name';
			$additional_fields[$j]['name']			= 'loc' . $type_id . '_name';
			$additional_fields[$j]['value']			= isset($values[$additional_fields[$j]['input_name']])?$values[$additional_fields[$j]['input_name']]:'';
			$additional_fields[$j]['size']			= $additional_fields[$j]['value'] ? strlen($additional_fields[$j]['value']) + 5 : 30;
			$insert_record['extra'][]				= $additional_fields[$j]['input_name'];
			$j++;



//_debug_array($attributes_values);

			$insert_record['extra'][]						= 'cat_id';

			$config_count=count($config);
			for ($j=0;$j<$config_count;$j++)
			{
				if($config[$j]['location_type'] == $type_id)
				{

					if($config[$j]['column_name']=='street_id')
					{
						$edit_street=true;
						$insert_record['extra'][]	= 'street_id';
						$insert_record['extra'][]	= 'street_number';
					}

					if($config[$j]['column_name']=='tenant_id')
					{
						$edit_tenant=true;
						$insert_record['extra'][]	= 'tenant_id';
					}

					if($config[$j]['column_name']=='part_of_town_id')
					{
						$edit_part_of_town		= true;
						$select_name_part_of_town	= 'part_of_town_id';
						$part_of_town_list		= $this->bocommon->select_part_of_town('select',$values['part_of_town_id']);
						$lang_town_statustext		= lang('Select the part of town the property belongs to. To do not use a part of town -  select NO PART OF TOWN');
						$insert_record['extra'][]	= 'part_of_town_id';
					}
					if($config[$j]['column_name']=='owner_id')
					{
						$edit_owner			= true;
						$lang_owner			= lang('Owner');
						$owner_list			= $this->bo->get_owner_list('',$values['owner_id']);
						$lang_select_owner		= lang('Select owner');
						$lang_owner_statustext		= lang('Select the owner');
						$insert_record['extra'][]	= 'owner_id';
					}
				}
			}

			$GLOBALS['phpgw']->session->appsession('insert_record','property',$insert_record);

			if(isset($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}


			if($location_code)
			{
				$change_type_list = $this->bo->select_change_type($values['change_type']);

				$location_types = $this->soadmin_location->read(array('order'=>'id','sort'=>'ASC'));
				foreach ($location_types as $location_type)
				{
					if($type_id != $location_type['id'])
					{
						if($type_id > $location_type['id'])
						{
							$entities_link[] = array
							(
								'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction'=> 'property.uilocation.edit',
															'location_code'=>implode('-',array_slice($location, 0, $location_type['id']))
															)
														),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> '<- '. $location_type['name'],
							);
						}
						else
						{
							$_location_code = implode('-',array_slice($location, 0, $location_type['id']));
							$marker = str_repeat('-', ($location_type['id'] - $type_id));
							$entities_link[] = array
							(
								'entity_link'			=> $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction'	=> 'property.uilocation.index',
															'type_id'		=> $location_type['id'],
															'query'			=> $_location_code,
															'location_code' => $_location_code
															)
														),
								'lang_entity_statustext'	=> $location_type['descr'],
								'text_entity'			=> "{$marker}> " . $location_type['name'],
							);
							unset($_location_code);
						}
					}
				}
			}

			phpgwapi_yui::tabview_setup('location_edit_tabview');
			$tabs = array();
			$tabs['general']	= array('label' => $location_types[($type_id-1)]['name'], 'link' => '#general');

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array // FIXME
						(
							'menuaction'	=> 'property.uilocation.attrib_history',
							'entity_id'	=> $this->entity_id,
							'cat_id'	=> $this->cat_id,
							'attrib_id'	=> $attribute['id'],
							'id'		=> $id,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				$location = ".location.{$type_id}";
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);
//_debug_array($attributes_groups);die();

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$tabs[str_replace(' ', '_', $group['name'])] = array('label' => $group['name'], 'link' => '#' . str_replace(' ', '_', $group['name']));
						$group['link'] = str_replace(' ', '_', $group['name']);
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			$documents = array();
			$file_tree = array();
			if($location_code)
			{
				$related = $this->bo->read_entity_to_link($location_code);
				$related_link = array();

				$document = CreateObject('property.sodocument');
				$documents = $document->get_files_at_location($location_code);

				if($documents)
				{
					$tabs['document']	= array('label' => lang('document'), 'link' => '#document');
					$documents = json_encode($documents);				
				}

				$_config		= CreateObject('phpgwapi.config','property');
				$_config->read();
				$_dirname = '';

				$_files_maxlevel = 0;
				if (isset($_config->config_data['external_files_maxlevel']) &&  $_config->config_data['external_files_maxlevel'])
				{
					$_files_maxlevel = $_config->config_data['external_files_maxlevel'];
				}
				$_files_filterlevel = 0;
				if (isset($_config->config_data['external_files_filterlevel']) &&  $_config->config_data['external_files_filterlevel'])
				{
					$_files_filterlevel = $_config->config_data['external_files_filterlevel'];
				}
				$_filter_info = explode('-',$location_code);

				if (isset($_config->config_data['external_files']) &&  $_config->config_data['external_files'])
				{
					$_dirname = $_config->config_data['external_files'];
					$file_tree = $document->read_file_tree($_dirname,$_files_maxlevel,$_files_filterlevel, $_filter_info[0]);
				}

				unset($_config);
				if($file_tree)
				{
					$tabs['file_tree']	= array('label' => lang('Files'), 'link' => '#file_tree');
					$file_tree = json_encode($file_tree);				
				}

				if(isset($related['related']))
				{
						$tabs['related']	= array('label' => lang('related'), 'link' => '#related');
				}

				foreach($related as $related_key => $related_data)
				{
					if( $related_key == 'gab')
					{
						foreach($related_data as $entry)
						{
							$entities_link[] = array
							(
								'entity_link'				=> $entry['entity_link'],
								'lang_entity_statustext'	=> $entry['descr'],
								'text_entity'				=> $entry['name'],
							);
						}
					}

					if( $related_key == 'related')
					{
						foreach($related_data as $entry)
						{
							$related_link[] = array
							(
								'entity_link'				=> $entry['entity_link'],
								'lang_entity_statustext'	=> $entry['descr'],
								'text_entity'				=> $entry['name'],
							);
						}
					}
				}
			}

			$data = array
			(
				'edit'							=> $view ? '' : true,
				'lang_change_type'				=> lang('Change type'),
				'lang_no_change_type'			=> lang('No Change type'),
				'lang_change_type_statustext'	=> lang('Type of changes'),
				'change_type_list'				=> (isset($change_type_list)?$change_type_list:''),
				'check_history'					=> (isset($check_history)?$check_history:''),
				'lang_history'					=> lang('History'),
				'lang_history_statustext'		=> lang('Fetch the history for this item'),
				'table_header'					=> (isset($table_header)?$table_header:''),
				'values'						=> (isset($content)?$content:''),

				'lang_related_info'				=> lang('related info'),
				'entities_link'					=> (isset($entities_link)?$entities_link:''),
				'related_link'					=> $related_link,
				'edit_street'					=> (isset($edit_street)?$edit_street:''),
				'edit_tenant'					=> (isset($edit_tenant)?$edit_tenant:''),
				'edit_part_of_town'				=> (isset($edit_part_of_town)?$edit_part_of_town:''),
				'edit_owner'					=> (isset($edit_owner)?$edit_owner:''),
				'select_name_part_of_town'		=> (isset($select_name_part_of_town)?$select_name_part_of_town:''),
				'part_of_town_list'				=> (isset($part_of_town_list)?$part_of_town_list:''),
				'lang_town_statustext'			=> (isset($lang_town_statustext)?$lang_town_statustext:''),
				'lang_part_of_town'				=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'lang_owner'					=> (isset($lang_owner)?$lang_owner:''),
				'owner_list'					=> (isset($owner_list)?$owner_list:''),
				'lang_select_owner'				=> (isset($lang_select_owner)?$lang_select_owner:''),
				'lang_owner_statustext'			=> (isset($lang_owner_statustext)?$lang_owner_statustext:''),
				'additional_fields'				=> $additional_fields,
				'attributes_group'				=> $attributes,
//				'attributes_values'				=> $values['attributes'],
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'lang_none'						=> lang('None'),

				'msgbox_data'					=> (isset($msgbox_data)?$GLOBALS['phpgw']->common->msgbox($msgbox_data):''),

				'street_link'					=> "menuaction:'" . 'property'.".uilookup.street'",
				'lang_street'					=> lang('Address'),
				'lang_select_street_help'		=> lang('Select the street name'),
				'lang_street_num_statustext'	=> lang('Enter the street number'),
				'value_street_id'				=> (isset($values['street_id'])?$values['street_id']:''),
				'value_street_name'				=> (isset($values['street_name'])?$values['street_name']:''),
				'value_street_number'			=> (isset($values['street_number'])?$values['street_number']:''),

				'tenant_link'					=> "menuaction:'" . 'property'.".uilookup.tenant'",
				'lang_tenant'					=> lang('tenant'),
				'value_tenant_id'				=> (isset($values['tenant_id'])?$values['tenant_id']:''),
				'value_last_name'				=> (isset($values['last_name'])?$values['last_name']:''),
				'value_first_name'				=> (isset($values['first_name'])?$values['first_name']:''),
				'lang_tenant_statustext'		=> lang('Select a tenant'),
				'size_last_name'				=> (isset($values['last_name'])?strlen($values['last_name']):''),
				'size_first_name'				=> (isset($values['first_name'])?strlen($values['first_name']):''),
				'lookup_type'					=> $lookup_type,
				'location_data'					=> $location_data,
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index','type_id'=> $type_id, 'lookup_tenant'=> $lookup_tenant)),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the location'),
				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'location','type_id' =>$type_id,'order'=>'descr')),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general'),
				'documents'						=> $documents,
				'file_tree'						=> $file_tree,
				'lang_expand_all'				=> lang('expand all'),
				'lang_collapse_all'				=> lang('collapse all')
			);

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/examples/treeview/assets/css/folders/tree.css');
			phpgwapi_yui::load_widget('treeview');
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.edit', 'property' );
			$appname	= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{

			$location_code	 	= phpgw::get_var('location_code', 'string', 'GET');
			$type_id	 	= $this->type_id;

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
	    		$this->bo->delete($location_code);
	    		return "location_code ".$location_code." ".lang("has been deleted");
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";

			if(!$this->acl_delete)
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uilocation.index',
				'type_id'	=>$type_id
			);

			if (phpgw::get_var('confirm', 'bool', 'GET'))
			{
				$this->bo->delete($location_code);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.delete', 'location_code'=> $location_code, 'type_id'=> $type_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname			= lang('location');
			$function_msg			= lang('delete location');

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
			$this->edit($view = true);
		}

		/**
		* Traverse the location hierarchy and set the parent to not active - where all children are not active.
		*
		* @return void
		*/

		function update_cat()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::inactive_cats';

			if(!$this->acl->check('.admin.location', PHPGW_ACL_EDIT, 'property'))
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uilocation.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$receipt= $this->bo->update_cat();
				$lang_confirm_msg = lang('Do you really want to update the categories again');
				$lang_yes			= lang('again');
			}
			else
			{
				$lang_confirm_msg 	= lang('Do you really want to update the categories');
				$lang_yes			= lang('yes');
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'update_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.update_cat')),
				'message'			=> $receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'			=> $lang_yes,
				'lang_yes_statustext'		=> lang('Update the category to not active based on if there is only nonactive apartments'),
				'lang_no_statustext'		=> lang('Back to Admin'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('location');
			$function_msg	= lang('Update the not active category for locations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('update_cat' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		/**
		* Perform an update on all location_codes on all levels to make sure they are consistent and unique
		*
		* @return void
		*/

		function update_location()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::location::update_location';

			if(!$this->acl->check('.admin.location', PHPGW_ACL_EDIT, 'property'))
			{
				$this->bocommon->no_access();
				return;
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$receipt= $this->bo->update_location();
				$lang_confirm_msg = lang('Do you really want to update the locations again');
				$lang_yes			= lang('again');
			}
			else
			{
				$lang_confirm_msg 	= lang('Do you really want to update the locations');
				$lang_yes			= lang('yes');
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'update_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.update_location')),
				'message'					=> $receipt['message'],
				'lang_confirm_msg'			=> $lang_confirm_msg,
				'lang_yes'					=> $lang_yes,
				'lang_yes_statustext'		=> lang('perform an update on all location_codes on all levels to make sure they are consistent and unique'),
				'lang_no_statustext'		=> lang('Back to Admin'),
				'lang_no'					=> lang('no')
			);

			$appname		= lang('location');
			$function_msg	= lang('Update the locations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('update_cat' => $data));
		}

		function stop()
		{
			$perm	 		= phpgw::get_var('perm', 'int');
			$location	 	= phpgw::get_var('acl_location');

			$right = array
			(
				PHPGW_ACL_READ		=> 'read',
				PHPGW_ACL_ADD		=> 'add',
				PHPGW_ACL_EDIT		=> 'edit',
				PHPGW_ACL_DELETE	=> 'delete',
				PHPGW_ACL_PRIVATE	=> 'manage'
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));

			$receipt['error'][] = array('msg' => lang('You need the right "%1" for this application at "%2" to access this function', lang($right[$perm]), $location));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
			);

			$appname		= lang('Access error');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' : ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('stop' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function summary()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::summary';

			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'		=> 'property.uilocation.summary',
					'district_id'		=>$this->district_id,
					'part_of_town_id'	=>$this->part_of_town_id,
					'filter'			=>$this->filter,
					'summary'			=>true
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uilocation.summary',"
	    											."district_id:'{$this->district_id}',"
	    											."part_of_town_id:'{$this->part_of_town_id}',"
 	                        						."filter: '{$this->filter}',"
 	                        						."summary: true";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'		=> 'property.uilocation.summary',
					'district_id'		=>$this->district_id,
					'part_of_town_id'	=>$this->part_of_town_id,
					'filter'			=>$this->filter,
					'summary'			=>true
				);

				$link_download = array
				(
					'menuaction'		=> 'property.uilocation.download',
					'district_id'		=>$this->district_id,
					'part_of_town_id'	=>$this->part_of_town_id,
					'filter'			=>$this->filter,
					'summary'			=>true
				);

				$values_combo_box[0] = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=> lang('no district'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift ($values_combo_box[1],$default_value);

				if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$owner_list = $this->bo->get_owner_list('filter', $this->filter);
				}
				else
				{
					$owner_list = $this->bo->get_owner_type_list('filter', $this->filter);
				}

				$values_combo_box[2]  = $owner_list;
				$default_value = array ('id'=>'','name'=>lang('show all'));
				array_unshift ($values_combo_box[2],$default_value);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'		=> 'property.uilocation.summary',
								'district_id'		=>$this->district_id,
								'part_of_town_id'	=>$this->part_of_town_id,
								'filter'			=>$this->filter,
								'summary'			=>true
							)
						),
					'fields'	=> array(
	                                    'field' => array(
				                                        array( //boton 	CATEGORY
															'id' => 'btn_district_id',
															'name' => 'district_id',
															'value'	=> lang('district'),
															'type' => 'button',
															'style' => 'filter',
															'tab_index' => 1
				                                        ),
				                                        array( //boton 	STATUS
															'id' => 'btn_part_of_town_id',
															'name' => 'part_of_town_id',
															'value'	=> lang('part of town'),
															'type' => 'button',
															'style' => 'filter',
															'tab_index' => 2
				                                        ),
				                                        array( //boton 	HOUR CATEGORY
															'id' => 'btn_owner_id',
															'name' => 'owner_id',
															'value'	=> lang('owner'),
															'type' => 'button',
															'style' => 'filter',
															'tab_index' => 3
				                                        ),
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_export',
							                                'value'	=> lang('download'),
							                                'tab_index' => 4
							                            )
			                           				),
			                       		'hidden_value' => array(
						                                        array( //div values  combo_box_0
																			'id' => 'values_combo_box_0',
																			'value'	=> $this->bocommon->select2String($values_combo_box[0])
								                                      ),
								                                array( //div values  combo_box_1
																			'id' => 'values_combo_box_1',
																			'value'	=> $this->bocommon->select2String($values_combo_box[1])
								                                      ),
																 array( //div values  combo_box_2
																			'id' => 'values_combo_box_2',
																			'value'	=> $this->bocommon->select2String($values_combo_box[2])
								                                      )
			                       								)
										)
					 )
				);

//				$dry_run = true;
			}

			$summary_list= $this->bo->read_summary();

			$uicols	= $this->bo->uicols;
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($summary_list) AND is_array($summary_list))
			{
				foreach($summary_list as $summary_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $summary_entry[$uicols['name'][$k]];
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($summary_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname		= lang('Summary');
			$function_msg		= lang('List') . ' ' . lang($this->role);

			//-- BEGIN----------------------------- JSON CODE ------------------------------
    		//values for Pagination
	    		$json = array
	    		(
	    			'recordsReturned' 	=> $datatable['pagination']['records_returned'],
    				'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
	    			'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
	    			'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
	    		);

				// values for datatable
	    		if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
	    			foreach( $datatable['rows']['row'] as $row )
	    			{
		    			$json_row = array();
		    			foreach( $row['column'] as $column)
		    			{
		    				if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
		    				}else
		    				{
		    				  $json_row[$column['name']] = $column['value'];
		    				}
		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}
	    		
	    		// 'part of town' depended on the selected 'district'
				$opt_cb_depend =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
		 		$default_value = array ('id'=>'','name'=>'!no part of town');
				array_unshift ($opt_cb_depend,$default_value);

				$json['hidden']['dependent'][] = array ( 'id' => $this->part_of_town_id,
	                                                      'value' => $this->bocommon->select2String($opt_cb_depend)
														);
	    		
	    		

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

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}

	      	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.summary', 'property' );


		}

 }
