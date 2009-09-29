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

	class property_uiproject
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
		var $district_id;
		var $criteria_id;

		var $public_functions = array
		(
			'download'  => true,
			'index'  => true,
			'view'   => true,
			'edit'   => true,
			'delete' => true,
			'date_search'=>true
		);

		function property_uiproject()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project';

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.boproject',true);
			$this->bocommon			= & $this->bo->bocommon;
			$this->cats				= & $this->bo->cats;
			$this->custom			= & $this->bo->custom;

			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.project';
			$this->acl_read 		= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 			= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 		= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 		= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->status_id		= $this->bo->status_id;
			$this->wo_hour_cat_id	= $this->bo->wo_hour_cat_id;
			$this->district_id		= $this->bo->district_id;
			$this->user_id			= $this->bo->user_id;
			$this->criteria_id		= $this->bo->criteria_id;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'status_id'	=> $this->status_id,
				'wo_hour_cat_id'=> $this->wo_hour_cat_id,
				'district_id'	=> $this->district_id,
				'user_id'		=> $this->user_id,
				'criteria_id'	=> $this->criteria_id
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date 	= urldecode(phpgw::get_var('end_date'));
			$list 		= $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' => true, 'skip_origin' => true));
			$uicols	= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project';
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiproject.stop', 'perm'=>1,'acl_location'=> $this->acl_location));
			}

			/*
			* FIXME:
			* Temporary fix to avoid doubled get of first page in table all the way from the database - saves about a second
			* Should be fixed in the js if possible.
			*/

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$json_get = phpgwapi_cache::session_get('property', 'project_index_json_get');
				if($json_get == 1)
				{
					$json = phpgwapi_cache::session_get('property', 'project_index_json');
					if($json && is_array($json))
					{
						phpgwapi_cache::session_clear('property', 'project_index_json');
						phpgwapi_cache::session_set('property', 'project_index_json_get', 2);
						return $json;
					}
				}
			}
			else
			{
				phpgwapi_cache::session_clear('property', 'project_index_json_get');
			}

			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$dry_run		= false;

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'			=> 'property.uiproject.index',
	    			'query'            		=> $this->query,
 	                'district_id'        	=> $this->district_id,
 	                'part_of_town_id'    	=> $this->part_of_town_id,
 	                'lookup'        		=> $lookup,
 	                'cat_id'        		=> $this->cat_id,
 	                'status_id'        		=> $this->status_id,
					'wo_hour_cat_id'		=> $this->wo_hour_cat_id,
					'user_id'				=> $this->user_id,
					'criteria_id'			=> $this->criteria_id
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiproject.index',"
	    											."query:'{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."part_of_town_id:'{$this->part_of_town_id}',"
						 	                        ."lookup:'{$lookup}',"						 	                        ."cat_id:'{$this->cat_id}',"
						 	                        ."user_id:'{$this->user_id}',"
						 	                        ."criteria_id:'{$this->criteria_id}',"
						 	                        ."wo_hour_cat_id:'{$this->wo_hour_cat_id}',"
						 	                        ."second_display:1,"
			                						."status_id:'{$this->status_id}'";

			    $datatable['config']['allow_allrows'] = false;

/*
				$link_data = array
				(
							'menuaction'	=> 'property.uiproject.index',
							'sort'			=>$this->sort,
							'order'			=>$this->order,
							'cat_id'		=>$this->cat_id,
							'district_id'	=>$this->district_id,
							'filter'		=>$this->filter,
							'status_id'		=>$this->status_id,
							'lookup'		=>$lookup,
							'from'			=>$from,
							'query'			=>$this->query,
							'start_date'	=>$start_date,
							'end_date'		=>$end_date,
							'wo_hour_cat_id'=>$this->wo_hour_cat_id,
							'second_display'=>true
				);
*/
				$values_combo_box[0]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[1]['cat_list'],$default_value);

				$values_combo_box[2]  = $this->bo->select_status_list('filter',$this->status_id);
				$default_value = array ('id'=>'','name'=>lang('no status'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
				$default_value = array ('id'=>'','name'=>lang('no hour category'));
				array_unshift ($values_combo_box[3],$default_value);

				$values_combo_box[4]  = $this->bocommon->get_user_list_right2('filter',2,$this->user_id,$this->acl_location);
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[4],$default_value);

				$values_combo_box[5]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[5],$default_value);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction' 		=> 'property.uiproject.index',
								'district_id'       => $this->district_id,
								'part_of_town_id'   => $this->part_of_town_id,
								'lookup'        	=> $lookup,
								'cat_id'        	=> $this->cat_id
							)
						),
					'fields'	=> array(
	                                    'field' => array(
				                                        array( //boton 	DISTRICT
				                                            'id' => 'btn_district_id',
				                                            'name' => 'district_id',
				                                            'value'	=> lang('district'),
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
				                                        array( //boton 	STATUS
				                                            'id' => 'btn_status_id',
				                                            'name' => 'status_id',
				                                            'value'	=> lang('Status'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 3
				                                        ),
				                                        array( //boton 	HOUR CATEGORY
				                                            'id' => 'btn_hour_category_id',
				                                            'name' => 'wo_hour_cat_id',
				                                            'value'	=> lang('Hour category'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 4
				                                        ),
				                                        array( //boton 	USER
				                                            'id' => 'btn_user_id',
				                                            'name' => 'user_id',
				                                            'value'	=> lang('User'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 5
				                                        ),
				                                        array( //boton 	search criteria
				                                            'id' => 'btn_criteria_id',
				                                            'name' => 'criteria_id',
				                                            'value'	=> lang('search criteria'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 6
				                                        ),
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_export',
							                                'value'	=> lang('download'),
							                                'tab_index' => 11
							                            ),
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_new',
							                                'value'	=> lang('add'),
							                                'tab_index' => 10
							                            ),
				                                        array( //hidden start_date
		                                                    'type' => 'hidden',
		                                                    'id' => 'start_date',
		                                                    'value' => $start_date
	                                                    ),
	                                                    array( //hidden end_date
		                                                    'type' => 'hidden',
		                                                    'id' => 'end_date',
		                                                    'value' => $end_date
	                                                    ),
	                                                    array(//for link "None",
	                                                  		'type'=> 'label_date'
	                                                    ),
				                                        array(//for link "Date search",
		                                                    'type'=> 'link',
		                                                    'id'  => 'btn_data_search',
		                                                    'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
		                                                           array(
		                                                               'menuaction' => 'property.uiproject.date_search'))."','','width=350,height=250')",
		                                                     'value' => lang('Date search'),
		                                                     'tab_index' => 9
	                                                    ),
				                                // FIXME test on lightbox for date search
				                                /*
				                                        array( //boton     Date SEARCH
				                                            'id' => 'btn_date_search',
				                                            'name' => 'date_search',
				                                            'value'    => lang('date search'),
				                                            'type' => 'button',
				                                            'tab_index' => 5
				                                        ),
												*/

				                                        array( //boton     SEARCH
				                                            'id' => 'btn_search',
				                                            'name' => 'search',
				                                            'value'    => lang('search'),
				                                            'type' => 'button',
				                                            'tab_index' => 8
				                                        ),
				   										array( // TEXT INPUT
				                                            'name'     => 'query',
				                                            'id'     => 'txt_query',
			                                                'value'    => $this->query,//'',//$query,
				                                            'type' => 'text',
				                                            'onkeypress' => 'return pulsar(event)',
				                                            'size'    => 28,
				                                            'tab_index' => 7
				                                        ),
			                           				),
			                       		'hidden_value' => array(
								                                array( //div values  combo_box_0
																		'id' => 'values_combo_box_0',
																		'value'	=> $this->bocommon->select2String($values_combo_box[0])
							                                      ),
						                                        array( //div values  combo_box_1
								                                            'id' => 'values_combo_box_1',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[1]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								                                      ),
								                                array( //div values  combo_box_2
								                                            'id' => 'values_combo_box_2',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[2])
								                                      ),
																 array( //div values  combo_box_3
								                                            'id' => 'values_combo_box_3',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[3])
								                                      ),
								                                array( //div values  combo_box_4
								                                            'id' => 'values_combo_box_4',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[4])
								                                      ),
								                                array( //div values  combo_box_5
								                                            'id' => 'values_combo_box_5',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[5])
								                                      )
			                       								)
										)
					 )
				);

				$dry_run = true;
			}

			$project_list = $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'dry_run' => $dry_run));
			$uicols	= $this->bo->uicols;
			$count_uicols_name=count($uicols['name']);

			$content = array();
			$j = 0;
			if (isset($project_list) AND is_array($project_list))
			{
				$lang_search = lang('search');
				foreach($project_list as $project_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]=='text')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']		= isset($project_entry[$uicols['name'][$k]])  ? $project_entry[$uicols['name'][$k]] : '';

							if(isset($project_entry['query_location'][$uicols['name'][$k]]) && $project_entry['query_location'][$uicols['name'][$k]])
							{
								$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
								$datatable['rows']['row'][$j]['column'][$k]['statustext']		= $lang_search;
								$datatable['rows']['row'][$j]['column'][$k]['value']			= $project_entry[$uicols['name'][$k]];
								$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$k]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$k]['link']				= $project_entry['query_location'][$uicols['name'][$k]];
								$uicols['formatter'][$k] = 'myCustom';
							}
							else if (isset($uicols['datatype']) && isset($uicols['datatype'][$k]) && $uicols['datatype'][$k]=='link' && isset($project_entry[$uicols['name'][$k]]) && $project_entry[$uicols['name'][$k]])
							{
									$datatable['rows']['row'][$j]['column'][$k]['value']		= $project_entry[$uicols['name'][$k]]['text'];
									$datatable['rows']['row'][$j]['column'][$k]['link']			= $project_entry[$uicols['name'][$k]]['url'];
									$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
									$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
									$datatable['rows']['row'][$j]['column'][$k]['statustext']	= $project_entry[$uicols['name'][$k]]['statustext'];
									
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
								$datatable['rows']['row'][$j]['column'][$k]['value']			= $project_entry[$uicols['name'][$k]];
						}

						if($lookup && $k==($count_uicols_name-1))
						{
							$content[$j]['row'][]= array(
							'lookup_action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.ui' . $from . '.edit', 'project_id'=> $project_entry['project_id']))
							);
						}
					}

					$j++;
				}

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
									'source'	=> 'project_id'
								),
							)
						);

						$parameters2 = array
						(
							'parameter' => array
							(
								array
								(
									'name'		=> 'project_id',
									'source'	=> 'project_id'
								),
							)
						);


						if (isset($project_entry) && $this->acl_read && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_READ))
						{
							$datatable['rowactions']['action'][] = array(
								'my_name' 			=> 'view',
								'statustext' 			=> lang('view the project'),
								'text'		=> lang('view'),
								'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiproject.view'

										)),
								'parameters'	=> $parameters
							);
							$datatable['rowactions']['action'][] = array(
								'my_name' 			=> 'view',
								'statustext' 		=> lang('view the project'),
								'text' 				=> lang('open view in new window'),
								'action'			=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiproject.view',
											'target'		=> '_blank'

										)),
								'parameters'	=> $parameters
							);
						}
						else
						{
				//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
						}

						if (isset($project_entry) && $this->acl_edit && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_EDIT))
						{
							$datatable['rowactions']['action'][] = array(
								'my_name' 			=> 'edit',
								'statustext' 		=> lang('edit the project'),
								'text'				=> lang('edit'),
								'action'			=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiproject.edit'

										)),
								'parameters'	=> $parameters
							);
							$datatable['rowactions']['action'][] = array(
								'my_name' 			=> 'edit',
								'statustext' 		=> lang('edit the project'),
								'text'	 			=> lang('open edit in new window'),
								'action'			=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiproject.edit',
											'target'		=> '_blank'
										)),
								'parameters'	=> $parameters
							);
						}
						else
						{
				//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
						}




						if(isset($project_entry) && $this->acl_delete && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_DELETE))
						{
							$datatable['rowactions']['action'][] = array(
								'my_name' 			=> 'delete',
								'text' 			=> lang('delete'),
								'confirm_msg'	=> lang('do you really want to delete this entry'),
								'action'		=> $GLOBALS['phpgw']->link('/index.php',array
												(
													'menuaction'	=> 'property.uiproject.delete'
												)),
								'parameters'	=> $parameters2
							);
						}
						else
						{
				//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
						}

						if (isset($project_entry) && $this->acl_add && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_ADD))
						{
							$datatable['rowactions']['action'][] = array(
												'my_name' 			=> 'add',
												'text' 			=> lang('add'),
												'action'		=> $GLOBALS['phpgw']->link('/index.php',array
																(
																	'menuaction'	=> 'property.uiproject.edit'
																))
										);
						}
					}

						unset($parameters);
			}
			$count_uicols_descr = count($uicols['descr']);


			for ($i=0;$i<$count_uicols_descr;$i++)
			{

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= isset($uicols['formatter'][$i]) && $uicols['formatter'][$i] ? $uicols['formatter'][$i] : '""';//($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					
					$datatable['headers']['header'][$i]['className']		= isset($uicols['classname'][$i]) && $uicols['classname'][$i] ? $uicols['classname'][$i] : '';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;

					if($uicols['name'][$i]=='project_id' || $uicols['name'][$i]=='address' || $uicols['name'][$i]=='project_group')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'location_code';
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['formatter'] 		= '""';
					$datatable['headers']['header'][$i]['className']		= '';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= '';
				}
			}

			$function_exchange_values = '';
			if($lookup)
			{
				$lookup_target = array
				(
					'menuaction'		=> 'property.ui'.$from.'.edit',
					'origin'			=> phpgw::get_var('origin'),
					'origin_id'			=> phpgw::get_var('origin_id')
				);

				for ($i=0;$i<$count_uicols_name;$i++)
				{
					if($uicols['name'][$i]=='project_id')
					{
						$function_exchange_values .= "var code_project = data.getData('".$uicols["name"][$i]."');"."\r\n";
						$function_exchange_values .= "valida('".$GLOBALS['phpgw']->link('/index.php',$lookup_target)."', code_project);";
						$function_detail .= "var url=data+'&project_id='+param;"."\r\n";
						$function_detail .= "window.open(url,'_self');";

					}
				}
				$datatable['exchange_values'] = $function_exchange_values;
				$datatable['valida'] = $function_detail;
			}

			$link_date_search = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
			(
				'menuaction'	=> 'property.uiproject.download',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'district_id'		=>$this->district_id,
						'filter'		=>$this->filter,
						'status_id'		=>$this->status_id,
						'lookup'		=>$lookup,
						'from'			=>$from,
						'query'			=>$this->query,
						'start_date'		=>$start_date,
						'end_date'		=>$end_date,
						'start'			=>$this->start,
						'wo_hour_cat_id'	=>$this->wo_hour_cat_id
			);

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($project_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('Project');
			$function_msg	= lang('list Project');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'project_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

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

		  	//-- BEGIN----------------------------- JSON CODE ------------------------------
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    				if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' title='{$column['statustext']}'>" .$column['value']."</a>";
		    				}else
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
		
				/*
				* FIXME:
				* Temporary fix to avoid doubled get of first page in table all the way from the database - saves about a second
				* Should be fixed in the js if possible.
				*/
				$json_get = phpgwapi_cache::session_get('property', 'project_index_json_get');
				if(!$json_get)
				{
						phpgwapi_cache::session_set('property', 'project_index_json',$json);
						phpgwapi_cache::session_set('property', 'project_index_json_get', 1);
				}

	    		return $json;
			}
//-------------------- JSON CODE ----------------------
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


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.index', 'property' );
			$this->save_sessiondata();
		}

		function date_search()
		{
			//cramirez: necesary for windows.open . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			$GLOBALS['phpgw']->xslttpl->add_file(array('date_search'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
		//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
		//	$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$values['start_date']	= phpgw::get_var('start_date', 'string', 'POST');
			$values['end_date']	= phpgw::get_var('end_date', 'string', 'POST');

			$function_msg	= lang('Date search');
			$appname	= lang('project');

			if(!$values['end_date'])
			{
				$values['end_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('start_date');
			$jscal->add_listener('end_date');

			$data = array
			(
				'lang_datetitle'		=> lang('Select date'),
				'img_cal'				=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),

				'lang_start_date_statustext'	=> lang('Select the estimated end date for the Project'),
				'lang_start_date'		=> lang('Start date'),
				'value_start_date'		=> $values['start_date'],

				'lang_end_date_statustext'	=> lang('Select the estimated end date for the Project'),
				'lang_end_date'			=> lang('End date'),
				'value_end_date'		=> $values['end_date'],

				'lang_submit_statustext'	=> lang('Select this dates'),
				'lang_submit'			=> lang('Submit')
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('date_search' => $data));
		}

		function edit()
		{
			$id = phpgw::get_var('id', 'int');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiproject.view', 'id'=> $id));
			}

			$values						= phpgw::get_var('values');
			$values_attribute			= phpgw::get_var('values_attribute');
			$add_request				= phpgw::get_var('add_request');
			$values['project_group']	= phpgw::get_var('project_group');
			$values['ecodimb']			= phpgw::get_var('ecodimb');
			$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');
			$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
			$auto_create 				= false;

			$datatable = array();

			/*$datatable['config']['base_java_url'] = "menuaction:'property.uiproject.edit',"
	    											."id:'{$id}'";*/

			$config				= CreateObject('phpgwapi.config','property');
			$bolocation			= CreateObject('property.bolocation');

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

			$insert_record_entity = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}",'property');

			if(isset($insert_record_entity) && is_array($insert_record_entity))
			{
				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}
			}

			
			$GLOBALS['phpgw']->xslttpl->add_file(array('project','attributes_form'));

			$bypass = phpgw::get_var('bypass', 'bool');

			if($add_request)
			{
				$receipt = $this->bo->add_request($add_request,$id);
			}

			if($_POST && !$bypass && isset($insert_record) && is_array($insert_record))
			{
					$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			else
			{
				$location_code 		= phpgw::get_var('location_code');
				$tenant_id 		= phpgw::get_var('tenant_id', 'int');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

				$origin				= phpgw::get_var('origin');
				$origin_id			= phpgw::get_var('origin_id', 'int');

				//23.jun 08: This will be handled by the interlink code - just doing a quick hack for now...
				if($origin == '.ticket' && $origin_id && !$values['descr'])
				{
					$boticket= CreateObject('property.botts');
					$ticket = $boticket->read_single($origin_id);
					$values['descr'] = $ticket['details'];
					$values['name'] = $ticket['subject'] ? $ticket['subject'] : $ticket['category_name'];
					$ticket_notes = $boticket->read_additional_notes($origin_id);
					$i = count($ticket_notes)-1;
					if(isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
					{
						$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
					}
					$values['contact_id'] = $ticket['contact_id'];
					$tts_status_create_project 	= isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project'] : '';
					if($tts_status_create_project)
					{
						$boticket->update_status(array('status' => $tts_status_create_project), $origin_id);
					}
					
					if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket'] == 'yes')
					{
						$auto_create = true;
					}
				}

				if($p_entity_id && $p_cat_id)
				{

					if(!is_object($boadmin_entity))
					{
						$boadmin_entity	= CreateObject('property.boadmin_entity');
					}

					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num, 'view' => true));
				}

			}

			if(isset($values['origin']) && $values['origin'])
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

			$config->read();

			$save='';
			if (isset($values['save']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				$save=true;

				if(!isset($values['location']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
					$error_id=true;
				}

				if(!isset($values['end_date']) || !$values['end_date'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please select an end date!'));
//					$error_id=true;
				}

				if(!$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a project NAME !'));
					$error_id=true;
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					$error_id=true;
				}

				if(!$values['coordinator'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a coordinator !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
					$error_id=true;
				}

				if(isset($values['budget']) && $values['budget'] && !ctype_digit($values['budget']))
				{
					$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
					$error_id=true;
				}

				if(isset($values['reserve']) && $values['reserve'] && !ctype_digit($values['reserve']))
				{
					$receipt['error'][]=array('msg'=>lang('reserve') . ': ' . lang('Please enter an integer !'));
					$error_id=true;
				}

				if(isset($values_attribute) && is_array($values_attribute))
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
					$values['id'] = $id;
					$action='edit';
				}

				if(!$receipt['error'])
				{

					if($values['copy_project'])
					{
						$action='add';
					}
	_debug_array($values);
	die();

					$receipt = $this->bo->save($values,$action,$values_attribute);

					if (! $receipt['error'])
					{
						$id = $receipt['id'];
					}

					if ( isset($GLOBALS['phpgw_info']['server']['smtp_server'])
						&& $GLOBALS['phpgw_info']['server']['smtp_server']
						&& $config->config_data['project_approval'] )
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}


						$action_params['responsible'] = $_account_id;
						$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
						$headers = "Return-Path: <". $from_email .">\r\n";
						$headers .= "From: " . $from_name . "<" . $from_email .">\r\n";
						$headers .= "Bcc: " . $from_name . "<" . $from_email .">\r\n";
						$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
						$headers .= "MIME-Version: 1.0\r\n";

						$subject = lang(Approval).": ". $id;
						$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit','id'=> $id)).'">' . lang('project %1 needs approval',$id) .'</a>';

						$bcc = '';//$from_email;

						$action_params = array
						(
							'appname'			=> 'property',
							'location'			=> '.project',
							'id'				=> $id,
							'responsible'		=> '',
							'responsible_type'  => 'user',
							'action'			=> 'approval',
							'remark'			=> '',
							'deadline'			=> ''
						);

						foreach ($values['mail_address'] as $_account_id => $_address)
						{
							if(isset($values['approval'][$_account_id]) && $values['approval'][$_account_id])
							{
								$rcpt = $GLOBALS['phpgw']->send->msg('email',$_address, $subject, stripslashes($message), '', $cc, $bcc, $from_email, $from_name, 'html');
								$action_params['responsible'] = $_account_id;
								execMethod('property.sopending_action.set_pending_action', $action_params);
								if(!$rcpt)
								{
									$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '" . $_address . "', subject='$subject' failed !!!");
									$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
									$bypass_error=true;
								}
								else
								{
									$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
								}
							}
						}

						if (isset($receipt['notice_owner']) && is_array($receipt['notice_owner']) 
						 && isset($GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner'] == 1)
						{
							if($this->account!=$values['coordinator'] && $config->config_data['mailnotification'])
							{
								$prefs_coordinator = $this->bocommon->create_preferences('property',$values['coordinator']);
								$to = $prefs_coordinator['email'];

								$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
								$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

								$body = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit', 'id'=> $id)).'">' . lang('project %1 has been edited',$id) .'</a>' . "\n";
								foreach($receipt['notice_owner'] as $notice)
								{
									$body .= $notice . "\n";
								}

								$body .= lang('Altered by') . ': ' . $from_name . "\n";
								$body .= lang('remark') . ': ' . $values['remark'] . "\n";

								$body = nl2br($body);

								$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject=lang('project %1 has been edited',$id),$body, false,false,false, $from_email, $from_name, 'html');

								if (!$returncode)	// not nice, but better than failing silently
								{
									$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '$to' subject='$subject' failed !!!");
									$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
									$bypass_error=true;
								}
								else
								{
									$receipt['message'][]=array('msg'=>lang('%1 is notified',$to));
								}
							}
						}
					}
				}

				if($receipt['error'] && !isset($bypass_error))
				{
					if(isset($values['location']) && is_array($values['location']))
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code,$values['extra']);
					}

					if(isset($values['extra']['p_num']))
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			//$record_history = '';
			$record_history = array();
			if(isset($bypass_error) || ((!isset($receipt['error']) || $add_request) && !$bypass) && $id)
			{
				$values	= $this->bo->read_single($id);

				if(!isset($values['origin']))
				{
					$values['origin'] = '';
				}

				if(!isset($values['workorder_budget']) && $save)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'project_id'=> $id));
				}

				if (!$this->bocommon->check_perms($values['grants'],PHPGW_ACL_EDIT))
				{
					$receipt['error'][]=array('msg'=>lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'property.uiproject.view', 'id'=> $id));
				}
				else
				{
					$record_history = $this->bo->read_record_history($id);
				}
			}

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_status'		=> lang('status')
			);

			if ($id)
			{
				$function_msg = lang('Edit Project');
			}
			else
			{
				$function_msg = lang('Add Project');
				$values	= $this->bo->read_single(0, $values);
			}

			$tabs = array();
			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uiproject.attrib_history',
							'attrib_id'	=> $attribute['id'],
							'id'		=> $id,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				$attributes_groups = $this->custom->get_attribute_groups('property', $this->acl_location, $values['attributes']);

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


			if (isset($values['cat_id']))
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type='form';

//_debug_array($values);
			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> true,
						'lookup_type'	=> $lookup_type,
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> (isset($values['p'])?$values['p']:'')
						));

			$b_account_data = array();
			$ecodimb_data = array();

			if(isset($config->config_data['budget_at_project']) && $config->config_data['budget_at_project'])
			{
				$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> $values['b_account_name']));



				$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
						'ecodimb'			=> $values['ecodimb'],
						'ecodimb_descr'		=> $values['ecodimb_descr']));
			}


			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array(
						'contact_id'		=> $values['contact_id'],
						'contact_name'		=> $values['contact_name'],
						'field'				=> 'contact',
						'type'				=> 'form'));


			if(isset($values['contact_phone']))
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
				'menuaction'	=> 'property.uiproject.edit',
				'id'		=> $id
			);

			$link_request_data = array
			(
				'menuaction'	=> 'property.uirequest.index',
				'query'		=> (isset($values['location_data']['loc1'])?$values['location_data']['loc1']:''),
				'project_id'	=> (isset($id)?$id:'')
			);

			$supervisor_id = 0;

			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'] )
			{
				$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
			}

			$need_approval = isset($config->config_data['project_approval'])?$config->config_data['project_approval']:'';
			$supervisor_email = array();
			if ($supervisor_id && ($need_approval=='yes'))
			{
				$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
				$supervisor_email[] = array
				(
					'id'	  => $supervisor_id,
					'address' => $prefs['email'],
				);
				if ( isset($prefs['approval_from']) )
				{
					$prefs2 = $this->bocommon->create_preferences('property', $prefs['approval_from']);

					if(isset($prefs2['email']))
					{
						$supervisor_email[] = array
						(
							'id'	  => $prefs['approval_from'],
							'address' => $prefs2['email'],
						);
						$supervisor_email = array_reverse($supervisor_email);
					}
					unset($prefs2);
				}
				unset($prefs);
			}

			$project_status=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['project_status']:'');
			$project_category=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['project_category']:'');
			if(!isset($values['status']))
			{
				$values['status']=$project_status;
			}

			if(!isset($values['cat_id']))
			{
				$values['cat_id']=$project_category;
			}

			if(!isset($values['coordinator']))
			{
				$values['coordinator']=$this->account;
			}

			if(!isset($values['start_date']) || !$values['start_date'])
			{
				$values['start_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			if(isset($receipt) && is_array($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}
			else
			{
				$msgbox_data ='';
			}

			$values['sum'] = isset($values['budget'])?$values['budget']:0;

			if(isset($values['reserve']) && $values['reserve']!=0)
			{
				$reserve_remainder=$values['reserve']-$values['deviation'];
				$remainder_percent= number_format(($reserve_remainder/$values['reserve'])*100, 2, ',', '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

			$value_remainder = $values['sum'];
			if(isset($values['sum_workorder_actual_cost']))
			{
				$value_remainder = $values['sum'] - $values['sum_workorder_actual_cost'];
			}
			$values['sum']  = number_format($values['sum'], 0, ',', ' ');
			$value_remainder = number_format($value_remainder, 0, ',', ' ');
			$values['planned_cost']  = number_format($values['planned_cost'], 0, ',', ' ');

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');

			$project_group_data=$this->bocommon->initiate_project_group_lookup(array(
						'project_group'			=> $values['project_group'],
						'project_group_descr'	=> $values['project_group_descr']));
			

			//---datatable settings---------------------------------------------------	
			
			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($values['workorder_budget']),
					'total_records'			=> count($values['workorder_budget']),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit'))),
					'is_paginator'			=> 1,
					'footer'				=> 0
			);


       		$myColumnDefs[0] = array
       		(
       			'name'		=> "0",
       			'values'	=>	json_encode(array(	array(key => workorder_id,label=>lang('Workorder'),sortable=>true,resizeable=>true,formatter=>'YAHOO.widget.DataTable.formatLink'),
									       			array(key => budget,label=>lang('Budget'),sortable=>true,resizeable=>true,formatter=>FormatterRight),
									       			array(key => calculation,label=>lang('Calculation'),sortable=>true,resizeable=>true,formatter=>FormatterRight),
									       			array(key => actual_cost,label=>lang('actual cost'),sortable=>true,resizeable=>true,formatter=>FormatterRight),
		       				       			//		array(key => charge_tenant,label=>lang('charge tenant'),sortable=>true,resizeable=>true),
		       				       					array(key => vendor_name,label=>lang('Vendor'),sortable=>true,resizeable=>true),
		       				       					array(key => status,label=>lang('Status'),sortable=>true,resizeable=>true)))
			);

			$datavalues[1] = array
			(
					'name'					=> "1",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
			);


       		$myColumnDefs[1] = array
       		(
       			'name'		=> "1",
       			'values'	=>	json_encode(array(	array(key => value_date,label=>lang('Date'),sortable=>true,resizeable=>true),
									       			array(key => value_user,label=>lang('User'),Action=>true,resizeable=>true),
									       			array(key => value_action,label=>lang('action'),sortable=>true,resizeable=>true),
									       			array(key => value_old_value,label=>lang('old value'),	sortable=>true,resizeable=>true),
		       				       					array(key => value_new_value,label=>lang('new value'),sortable=>true,resizeable=>true)))
			);

			//----------------------------------------------datatable settings--------



			$suppresscoordination			= isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';

			$data = array
			(
				'suppressmeter'						=> isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
				'suppresscoordination'				=> $suppresscoordination,
				'attributes_group'					=> $attributes,
				'lookup_functions'					=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'b_account_data'					=> $b_account_data,
				'ecodimb_data'						=> $ecodimb_data,
				'contact_data'						=> $contact_data,
				'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'							=> $datavalues,
				'myColumnDefs'						=> $myColumnDefs,
				'tabs'								=> self::_generate_tabs($tabs,array('coordination' => $suppresscoordination)),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_origin'						=> isset($values['origin']) ? $values['origin'] : '',
				'value_origin_type'					=> isset($origin)?$origin:'',
				'value_origin_id'					=> isset($origin_id)?$origin_id:'',
				'lang_select_request'				=> lang('Select request'),
				'lang_select_request_statustext'	=> lang('Add request for this project'),
				'lang_request_statustext'			=> lang('Link to the request for this project'),
				'lang_delete_request_statustext'	=> lang('Check to delete this request from this project'),
				'link_select_request'				=> $GLOBALS['phpgw']->link('/index.php',$link_request_data),
				'link_request'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.view')),
				'add_workorder_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'lang_add_workorder'				=> lang('Add workorder'),
				'lang_add_workorder_statustext'		=> lang('Add a workorder to this project'),
				'lang_no_workorders'				=> lang('No workorder budget'),
				'workorder_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'record_history'					=> $record_history,
				'table_header_history'				=> $table_header_history,
				'lang_history'						=> lang('History'),
				'lang_no_history'					=> lang('No history'),
				'img_cal'							=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'					=> lang('Select date'),
				'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_start_date'					=> lang('Project start date'),
				'value_start_date'					=> $values['start_date'],
				'lang_end_date_statustext'			=> lang('Select the estimated end date for the Project'),
				'lang_end_date'						=> lang('Project end date'),
				'value_end_date'					=> isset($values['end_date']) ? $values['end_date'] : '' ,
				'lang_copy_project'					=> lang('Copy project ?'),
				'lang_copy_project_statustext'		=> lang('Choose Copy Project to copy this project to a new project'),
				'lang_charge_tenant'				=> lang('Charge tenant'),
				'lang_charge_tenant_statustext'		=> lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant'						=> isset($values['charge_tenant'])?$values['charge_tenant']:'',
				'lang_power_meter'					=> lang('Power meter'),
				'lang_power_meter_statustext'		=> lang('Enter the power meter'),
				'value_power_meter'					=> isset($values['power_meter'])?$values['power_meter']:'',
				'lang_budget'						=> lang('Budget'),
				'value_budget'						=> isset($values['budget'])?$values['budget']:'',
				'lang_budget_statustext'			=> lang('Enter the budget'),
				'lang_reserve'						=> lang('reserve'),
				'value_reserve'						=> isset($values['reserve'])?$values['reserve']:'',
				'lang_reserve_statustext'			=> lang('Enter the reserve'),
				'value_sum'							=> isset($values['sum'])?$values['sum']:'',
				'lang_reserve_remainder'			=> lang('reserve remainder'),
				'value_reserve_remainder'			=> isset($reserve_remainder)?$reserve_remainder:'',
				'value_reserve_remainder_percent'	=> isset($remainder_percent)?$remainder_percent:'',
				'lang_planned_cost'					=> lang('planned cost'),
				'value_planned_cost'				=> $values['planned_cost'],
				'location_data'						=> $location_data,
				'location_type'						=> 'form',
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
				'lang_year'							=> lang('Year'),
				'lang_category'						=> lang('category'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'lang_name'							=> lang('Name'),
				'lang_project_id'					=> lang('Project ID'),
				'value_project_id'					=> isset($id)?$id:'',
				'project_group_data'				=> $project_group_data,
				'value_name'						=> isset($values['name'])?$values['name']:'',
				'lang_name_statustext'				=> lang('Enter Project Name'),
				'lang_other_branch'					=> lang('Other branch'),
				'lang_other_branch_statustext'		=> lang('Enter other branch if not found in the list'),
				'value_other_branch'				=> isset($values['other_branch'])?$values['other_branch']:'',
				'lang_descr_statustext'				=> lang('Enter a description of the project'),
				'lang_descr'						=> lang('Description'),
				'value_descr'						=> isset($values['descr'])?$values['descr']:'',
				'lang_remark_statustext'			=> lang('Enter a remark to add to the history of the project'),
				'lang_remark'						=> lang('remark'),
				'value_remark'						=> isset($values['remark'])?$values['remark']:'',
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the project'),
				'lang_no_cat'						=> lang('Select category'),
				'value_cat_id'						=> isset($values['cat_id'])?$values['cat_id']:'',
				'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
				'lang_workorder_id'					=> lang('Workorder ID'),
				//'sum_workorder_budget'				=> isset($values['sum_workorder_budget'])?$values['sum_workorder_budget']:'',
				//'sum_workorder_calculation'			=> isset($values['sum_workorder_calculation'])?$values['sum_workorder_calculation']:'',
				//'workorder_budget'					=> isset($values['workorder_budget'])?$values['workorder_budget']:'',
				//'sum_workorder_actual_cost'			=> isset($values['sum_workorder_actual_cost'])?$values['sum_workorder_actual_cost']:'',
				'lang_sum'							=> lang('Sum'),
				//'lang_actual_cost'					=> lang('Actual cost'),
				'value_remainder'					=> $value_remainder,
				'lang_remainder'					=> lang('remainder'),
				'lang_coordinator'					=> lang('Coordinator'),
				'lang_user_statustext'				=> lang('Select the coordinator the project belongs to. To do not use a category select NO USER'),
				'select_user_name'					=> 'values[coordinator]',
				'lang_no_user'						=> lang('Select coordinator'),
				'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),
				'status_list'						=> $this->bo->select_status_list('select',$values['status']),
				'status_name'						=> 'values[status]',
				'lang_no_status'					=> lang('Select status'),
				'lang_status'						=> lang('Status'),
				'lang_status_statustext'			=> lang('What is the current status of this project ?'),
				'lang_confirm_status'				=> lang('Confirm status'),
				'lang_confirm_statustext'			=> lang('Confirm status to the history'),
				'branch_list'						=> $this->bo->select_branch_p_list((isset($id)?$id:'')),
				'lang_branch'						=> lang('branch'),
				'lang_branch_statustext'			=> lang('Select the branches for this project'),
				'key_responsible_list'				=> $this->bo->select_branch_list((isset($values['key_responsible'])?$values['key_responsible']:'')),
				'lang_no_key_responsible'			=> lang('Select key responsible'),
				'lang_key_responsible'				=> lang('key responsible'),
				'lang_key_responsible_statustext'	=> lang('Select the key responsible for this project'),

				'key_fetch_list'					=> $this->bo->select_key_location_list((isset($values['key_fetch'])?$values['key_fetch']:'')),
				'lang_no_key_fetch'					=> lang('Where to fetch the key'),
				'lang_key_fetch'					=> lang('key fetch location'),
				'lang_key_fetch_statustext'			=> lang('Select where to fetch the key'),

				'key_deliver_list'					=> $this->bo->select_key_location_list((isset($values['key_deliver'])?$values['key_deliver']:'')),
				'lang_no_key_deliver'				=> lang('Where to deliver the key'),
				'lang_key_deliver'					=> lang('key deliver location'),
				'lang_key_deliver_statustext'		=> lang('Select where to deliver the key'),

				'need_approval'						=> isset($need_approval)?$need_approval:'',
				'lang_ask_approval'					=> lang('Ask for approval'),
				'lang_ask_approval_statustext'		=> lang('Check this to send a mail to your supervisor for approval'),
				'value_approval_mail_address'		=> $supervisor_email,

				'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
			);
			//_debug_array($data);die;

			if( $auto_create )
			{
				$location= explode('-', $values['location_data']['location_code']);

				$level = count($location);
				for ($i = 1; $i < $level+1; $i++)
				{
					$values['location']["loc$i"] = $location[($i-1)];
				}

				$values['street_name'] = $values['location_data']['street_name'];
 				$values['street_number'] = $values['location_data']['street_number'];
				$values['location_name'] = $values['location_data']["loc{$level}_name"];
				$values['extra'] = $values['p'][0];

				unset($values['location_data']);
				unset($values['p']);

				$receipt = $this->bo->save($values, 'add', array());

				if (! $receipt['error'])
				{
					$id = $receipt['id'];
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'project_id'=> $id));
				}
			}

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('project'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

			$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$appname		= lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.edit', 'property' );
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=>$this->acl_location));
			}

			$project_id = phpgw::get_var('project_id', 'int');
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($project_id);
				return "project_id ".$project_id." ".lang("has been deleted");
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
			$link_data = array
			(
				'menuaction' => 'property.uiproject.index',
				'project_id'	=> $project_id
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.delete', 'project_id'=> $project_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname			= lang('project');
			$function_msg			= lang('delete project');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');
			$bolocation			= CreateObject('property.bolocation');

			$id	= phpgw::get_var('id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('project'));

			$values	= $this->bo->read_single($id);

			$record_history = $this->bo->read_record_history($id);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor')
			);

			$function_msg = lang('View Project');

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> count(explode('-',$values['location_data']['location_code'])),
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> $values['location_data']['tenant_id'],
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> isset($values['p'])?$values['p']:''
						));

			if($values['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			$values['sum'] = isset($values['budget'])?$values['budget']:0;

			if(isset($values['reserve']) && $values['reserve']!=0)
			{
				$reserve_remainder=$values['reserve']-$values['deviation'];
				$remainder_percent= number_format(($reserve_remainder/$values['reserve'])*100, 2, ',', '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

//_debug_array($values);
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id));

			$project_group_data = $this->bocommon->initiate_project_group_lookup(array(
						'project_group'			=> $values['project_group'],
						'project_group_descr'	=> $values['project_group_descr'],
						'type'					=> 'view'));

			$config			= CreateObject('phpgwapi.config','property');
			$config->read();

			$suppresscoordination			= isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';
			$data = array
			(
				'suppressmeter'					=> isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
				'suppresscoordination'			=> $suppresscoordination,
				'tabs'							=> self::_generate_tabs(array(),array('coordination' => $suppresscoordination)),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'project_group_data'		=> $project_group_data,
				'value_origin'				=> $values['origin'],
			//	'value_origin_type'			=> $origin,
			//	'value_origin_id'			=> $origin_id,

				'table_header_workorder_budget'		=> $table_header_workorder_budget,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view')),
				'record_history'			=> $record_history,
				'table_header_history'			=> $table_header_history,
				'lang_history'				=> lang('History'),
				'lang_no_history'			=> lang('No history'),

				'lang_start_date'			=> lang('Project start date'),
				'value_start_date'			=> $values['start_date'],

				'lang_end_date'				=> lang('Project end date'),
				'value_end_date'			=> $values['end_date'],

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'charge_tenant'				=> isset($values['charge_tenant'])?$values['charge_tenant']:'',

				'lang_power_meter'			=> lang('Power meter'),
				'value_power_meter'			=> $values['power_meter'],

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> $values['budget'],

				'lang_reserve'				=> lang('reserve'),
				'value_reserve'				=> $values['reserve'],

				'value_sum'						=> (isset($values['sum'])?$values['sum']:''),

				'lang_reserve_remainder'		=> lang('reserve remainder'),
				'value_reserve_remainder'		=> isset($reserve_remainder)?$reserve_remainder:'',
				'value_reserve_remainder_percent'	=> isset($remainder_percent)?$remainder_percent:'',

				'vendor_data'				=> isset($vendor_data)?$vendor_data:'',
				'location_data'				=> $location_data,
				'location_type'				=> 'view',
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
				'lang_year'				=> lang('Year'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_name'				=> lang('Name'),

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $values['project_id'],
				'value_name'				=> $values['name'],

				'lang_other_branch'			=> lang('Other branch'),
				'value_other_branch'			=> $values['other_branch'],

				'lang_descr'				=> lang('Description'),
				'value_descr'				=> $values['descr'],
				'lang_done_statustext'			=> lang('Back to the list'),
				'select_name'				=> 'values[cat_id]',

				'cat_list'					=> $categories['cat_list'],
				'lang_workorder_id'			=> lang('Workorder ID'),
				'sum_workorder_budget'			=> $values['sum_workorder_budget'],
				'sum_workorder_calculation'		=> $values['sum_workorder_calculation'],
				'workorder_budget'			=> $values['workorder_budget'],
				'sum_workorder_actual_cost'		=> $values['sum_workorder_actual_cost'],
				'lang_actual_cost'			=> lang('Actual cost'),
				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'values[coordinator]',
				'lang_no_user'				=> lang('Select coordinator'),
				'user_list'				=> $this->bocommon->get_user_list('select',$values['coordinator'],$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'status_list'				=> $this->bo->select_status_list('select',$values['status']),
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),

				'branch_list'				=> $this->bo->select_branch_p_list($values['project_id']),
				'lang_branch'				=> lang('branch'),

				'key_responsible_list'			=> $this->bo->select_branch_list($values['key_responsible']),
				'lang_key_responsible'			=> lang('key responsible'),

				'key_fetch_list'			=> $this->bo->select_key_location_list($values['key_fetch']),
				'lang_key_fetch'			=> lang('key fetch location'),

				'key_deliver_list'			=> $this->bo->select_key_location_list($values['key_deliver']),
				'lang_key_deliver'			=> lang('key deliver location'),

				'edit_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit', 'id'=> $id)),
				'lang_edit_statustext'			=> lang('Edit this entry project'),
				'lang_edit'				=> lang('Edit'),
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $values['contact_phone'],
			);

			$appname		= lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));

		}

		protected function _generate_tabs($tabs_ = array(), $suppress = array())
		{
			$tabs = array
			(
				'general'		=> array('label' => lang('general'), 'link' => '#general'),
				'location'		=> array('label' => lang('location'), 'link' => '#location'),
				'budget'		=> array('label' => lang('Time and budget'), 'link' => '#budget'),
				'coordination'	=> array('label' => lang('coordination'), 'link' => '#coordination'),
				'history'		=> array('label' => lang('history'), 'link' => '#history')
			);
			$tabs = array_merge($tabs, $tabs_);
			foreach($suppress as $tab => $remove)
			{
				if($remove)
				{
					unset($tabs[$tab]);
				}
			}
			phpgwapi_yui::tabview_setup('project_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, 'general');
		}

	}
