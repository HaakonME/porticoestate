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
	* @subpackage helpdesk
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	phpgw::import_class('phpgwapi.yui');

	class property_uitts
	{
		var $public_functions = array
		(
			'index'		=> true,
			'index2'	=> true,
			'view'		=> true,
			'view2'		=> true,
			'add'		=> true,
			'add2'		=> true,
			'delete'	=> true,
			'download'		=> true,
			'download2'	=> true,
			'view_file'	=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::helpdesk';
			if($this->tenant_id	= $GLOBALS['phpgw']->session->appsession('tenant_id','property'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo					= CreateObject('property.botts',true);
			$this->bocommon 			= & $this->bo->bocommon;
			$this->cats					= & $this->bo->cats;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.ticket';
			$this->acl_read 			= $this->acl->check('.ticket', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.ticket', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.ticket', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.ticket', PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check('.ticket', PHPGW_ACL_PRIVATE, 'property'); // manage
			$this->bo->acl_location		= $this->acl_location;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->status_id				= $this->bo->status_id;
			$this->user_id			= $this->bo->user_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->district_id			= $this->bo->district_id;
			$this->allrows				= $this->bo->allrows;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->status_id,
				'user_id'	=> $this->user_id,
				'cat_id'	=> $this->cat_id,
				'district_id'	=> $this->district_id,
				'allrows'	=> $this->allrows,
				'start_date'	=> $this->start_date,
				'end_date'	=> $this->end_date
			);
			$this->bo->save_sessiondata($data);
		}

		function download2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$this->download($external = true);
		}

		function download($external='')
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$this->bo->allrows = true;
			$list = $this->bo->read($start_date,$end_date,$external);

			if (isset($list) AND is_array($list))
			{
				$i=0;
				foreach($list as &$entry)
				{
					if($entry['subject'])
					{
						$entry['category'] = $entry['subject'];
					}

					if (isset($entry['child_date']) AND is_array($entry['child_date']))
					{
						$j=0;
						foreach($entry['child_date'] as $date)
						{
							if($date['date_info'][0]['descr'])
							{
							 	$entry['date_' . $j]=$date['date_info'][0]['entry_date'];
							 	$name_temp['date_' . $j]=true;
							 	$descr_temp[$date['date_info'][0]['descr']]=true;
							 }
							 $j++;
						}
						unset($entry['child_date']);
					}
					$i++;
				}
			}
//_debug_array($descr_temp);

			$name	= array('id',
						'category',
						'location_code',
						'address',
						'user',
						'assignedto',
						'timestampopened'
						);

			while (is_array($name_temp) && list($name_entry,) = each($name_temp))
			{
				array_push($name,$name_entry);
			}

			array_push($name,'finnish_date','delay');

			$descr	= array(lang('ID'),
					lang('category'),
					lang('location'),
					lang('address'),
					lang('user'),
					lang('Assigned to'),
					lang('Started')
					);

			while (is_array($descr_temp) && list($descr_entry,) = each($descr_temp))
			{
				array_push($descr,$descr_entry);
			}

			array_push($descr,lang('finnish date'),lang('delay'));

			$this->bocommon->download($list,$name,$descr);
		}

		function index()
		{
			if($this->tenant_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index2'));
			}

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$dry_run=false;

			if(phpgw::get_var('edit_status', 'bool', 'GET'))
			{
				if(!$this->acl_edit)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=> 4, 'acl_location'=> $this->acl_location));
				}

				$new_status = phpgw::get_var('new_status', 'string', 'GET');
				$id = phpgw::get_var('id', 'int');
				$so2	= CreateObject('property.sotts2');
				$receipt = $so2->update_status(array('status'=>$new_status),$id);
				$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
			}

			$second_display = phpgw::get_var('second_display', 'bool');

			$default_category = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');
			$default_status = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status']:'');
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			if ($default_category && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_category;
				$this->district_id		= $default_category;
			}

			if ($default_status && !$second_display)
			{
				$this->bo->status_id	= $default_status;
				$this->status_id	= $default_status;
			}

			$bgcolor_array[1]	= '#dadada';
			$bgcolor_array[2]	= '#dad0d0';
			$bgcolor_array[3]	= '#dacaca';
			$bgcolor_array[4]	= '#dac0c0';
			$bgcolor_array[5]	= '#dababa';
			$bgcolor_array[6]	= '#dab0b0';
			$bgcolor_array[7]	= '#daaaaa';
			$bgcolor_array[8]	= '#da9090';
			$bgcolor_array[9]	= '#da8a8a';
			$bgcolor_array[10]	= '#da7a7a';



			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 		= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$allrows  = phpgw::get_var('allrows', 'bool');



			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

			$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'			=> 'property.uitts.index',
	    			'query'            		=> $this->query,
 	                'district_id'        	=> $this->district_id,
 	                'part_of_town_id'    	=> $this->part_of_town_id,
 	                'cat_id'        		=> $this->cat_id,
 	                'status'        		=> $this->status
   				));

   				/*$datatable['config']['base_java_url'] = "menuaction:'property.uitts.index',"
	    											."query:'{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."part_of_town_id:'{$this->part_of_town_id}',"
 	                        						."cat_id:'{$this->cat_id}',"
			                						."status:'{$this->status}'";*/

				$datatable['config']['base_java_url'] = "menuaction:'property.uitts.index',"
	    											."second_display:1,"
 	                        						."sort: '{$this->sort}',"
 	                        						."order: '{$this->order}',"
 	                        						."cat_id:'{$this->cat_id}',"
			                						."status_id: '{$this->status_id}',"
 	                        						."user_id: '{$this->user_id}',"
 	                        						."query: '{$this->query}',"
 	                        						."district_id: '{$this->district_id}',"
 	                        						."start_date: '{$start_date}',"
 	                        						."end_date: '{$end_date}',"
 	                        						."allrows:'{$this->allrows}'";


			$link_data = array
			(
				'menuaction'	=> 'property.uitts.index',
				'second_display'=> true,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'status_id'	=> $this->status_id,
				'user_id'	=> $this->user_id,
				'query'		=> $this->query,
				'district_id'	=> $this->district_id,
				'start_date'	=> $start_date,
				'end_date'	=> $end_date,
				'allrows'	=> $this->allrows
			);


			$group_filters = 'select';

			$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
			$default_value = array ('cat_id'=>'','name'=> lang('no category'));
			array_unshift ($values_combo_box[0]['cat_list'],$default_value);

			$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
			$default_value = array ('id'=>'','name'=>lang('no district'));
			array_unshift ($values_combo_box[1],$default_value);

			$values_combo_box[2]  = $this->bo->filter(array('format' => $group_filters, 'filter'=> $this->status_id,'default' => 'O'));
			$default_value = array ('id'=>'','name'=>lang('Open'));
			array_unshift ($values_combo_box[2],$default_value);

			$values_combo_box[3]  = $this->bocommon->get_user_list_right2('filter',2,$this->status_id,$this->acl_location);
			$default_value = array ('id'=>'','name'=>lang('no user'));
			array_unshift ($values_combo_box[3],$default_value);

			$datatable['actions']['form'] = array(
			array(
				'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
							'menuaction' 		=> 'property.uitts.index',
							'second_display'       => $second_display,
							'district_id'       => $this->district_id,
							'part_of_town_id'   => $this->part_of_town_id,
							'cat_id'        	=> $this->cat_id,
							'status'			=> $this->status

						)
					),
				'fields'	=> array(
                                    'field' => array(
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter'
			                                        ),
			                                        array( //boton 	STATUS
			                                            'id' => 'btn_district_id',
			                                            'name' => 'district_id',
			                                            'value'	=> lang('District'),
			                                            'type' => 'button',
			                                            'style' => 'filter'
			                                        ),
			                                        array( //boton 	HOUR CATEGORY
			                                            'id' => 'btn_status_id',
			                                            'name' => 'status_id',
			                                            'value'	=> lang('Status'),
			                                            'type' => 'button',
			                                            'style' => 'filter'
			                                        ),
			                                        array( //boton 	USER
			                                            'id' => 'btn_user_id',
			                                            'name' => 'user_id',
			                                            'value'	=> lang('User'),
			                                            'type' => 'button',
			                                            'style' => 'filter'
			                                        ),
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_export',
						                                'value'	=> lang('download')
						                            ),
													array(
						                                'type'	=> 'submit',
						                            	'id'	=> 'btn_new',
						                                'value'	=> lang('add')
						                            ),
			                                        array( //boton     SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                        ),						                            
			   										array( // TEXT INPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28
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
		                                                 'value' => lang('Date search')
	                                                )   
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
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

				$dry_run = true;
				}
				$ticket_list = $this->bo->read($start_date,$end_date,false,$dry_run);
				//$uicols = $this->bo->uicols;
				$uicols = array();
				//$uicols['name'][0] = 'color';
				$uicols['name'][0] = 'priority';
				$uicols['name'][1] = 'new_ticket';
				$uicols['name'][2] = 'id';
				$uicols['name'][3] = 'bgcolor';
				$uicols['name'][4] = 'subject';
				$uicols['name'][5] = 'location_code';
				$uicols['name'][6] = 'address';
				$uicols['name'][7] = 'timestampopened';
				$uicols['name'][8] = 'finnish_date';
				$uicols['name'][9] = 'delay';
				$uicols['name'][10] = 'user';
				$uicols['name'][11] = 'assignedto';
				$uicols['name'][12] = 'child_date';
				$uicols['name'][13] = 'link_view';
				$uicols['name'][14] = 'lang_view_statustext';
				$uicols['name'][15] = 'text_view';
				$uicols['name'][16] = 'status';

				$count_uicols_name = count($uicols['name']);

			$j = 0;
			$k = 0;
			if(is_array($ticket_list))
			{
				$status['X'] = array
				(
					'bgcolor'			=> '#5EFB6E',
					'status'			=> lang('closed'),
					'text_edit_status'	=> lang('Open'),
					'new_status' 		=> 'O'
				);

				$custom_status	= $this->bo->get_custom_status();

				foreach($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = array
					(
						'bgcolor'			=> $custom['color'] ? $custom['color'] : '',
						'status'			=> $custom['name'],
						'text_edit_status'	=> lang('close'),
						'new_status'		=> 'X'
					);
				}

				foreach($ticket_list as $ticket)
				{
					/*switch ($ticket['status'])
					{
						case 'O':
							$bgcolor = $bgcolor_array[$ticket['priority']];
							$status_text = lang('Open');
							$text_edit_status = lang('Close');
							$new_status = 'X';
						break;
						default :
							$bgcolor	 		= $status[$ticket['status']]['bgcolor'];
							$status_text		= $status[$ticket['status']]['status'];
							$text_edit_status	= $status[$ticket['status']]['text_edit_status'];
							$new_status			= $status[$ticket['status']]['new_status'];
						break;
					}

					$link_status_data = array
					(
						'menuaction'		=> 'property.uitts.index',
						'id'				=> $ticket['id'],
						'edit_status'		=> true,
						'new_status'		=> $new_status,
						'second_display'	=> true,
						'sort'				=> $this->sort,
						'order'				=> $this->order,
						'cat_id'			=> $this->cat_id,
						'filter'			=> $this->status_id,
						'user_id'		=> $this->user_id,
						'query'				=> $this->query,
						'district_id'		=> $this->district_id,
						'allrows'			=> $this->allrows
					);*/



					for ($k=0;$k<$count_uicols_name;$k++)
					{

								/*$datatable['rows']['row'][$j]['column'][0]['id'] 					= $ticket['id'];
								$datatable['rows']['row'][$j]['column'][1]['bgcolor']				= $bgcolor;
								$datatable['rows']['row'][$j]['column'][2]['new_ticket']			= (isset($ticket['new_ticket'])?$ticket['new_ticket']:'');
								$datatable['rows']['row'][$j]['column'][3]['priostr'] 				= $ticket['priority'];
								$datatable['rows']['row'][$j]['column'][4]['subject']				= $ticket['subject'];
								$datatable['rows']['row'][$j]['column'][5]['location_code']			= $ticket['location_code'];
								$datatable['rows']['row'][$j]['column'][6]['address']				= $ticket['address'];
								$datatable['rows']['row'][$j]['column'][7]['date']					= $ticket['timestampopened'];
								$datatable['rows']['row'][$j]['column'][8]['finnish_date']			= $ticket['finnish_date'];
								$datatable['rows']['row'][$j]['column'][9]['delay']					= (isset($ticket['delay'])?$ticket['delay']:'');
								$datatable['rows']['row'][$j]['column'][10]['user']					= $ticket['user'];
								$datatable['rows']['row'][$j]['column'][11]['assignedto']			= $ticket['assignedto'];
								$datatable['rows']['row'][$j]['column'][12]['child_date']			= $ticket['child_date'];
								$datatable['rows']['row'][$j]['column'][13]['link_view']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $ticket['id']));
								$datatable['rows']['row'][$j]['column'][14]['lang_view_statustext']	= lang('view the ticket');
								$datatable['rows']['row'][$j]['column'][15]['text_view']				= lang('view');
								$datatable['rows']['row'][$j]['column'][16]['status']				= $status_text;
								$datatable['rows']['row'][$j]['column'][17]['link_edit_status']		= $GLOBALS['phpgw']->link('/index.php',$link_status_data);
								$datatable['rows']['row'][$j]['column'][18]['lang_edit_status']		= lang('Edit status');
								$datatable['rows']['row'][$j]['column'][19]['text_edit_status']		= $text_edit_status;*/


								if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='O')
								{
									$datatable['rows']['row'][$j]['column'][$k]['name']				= $uicols['name'][$k];
									$datatable['rows']['row'][$j]['column'][$k]['value'] 			= lang('Open');
								}
								elseif($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='C')
								{
									$datatable['rows']['row'][$j]['column'][$k]['name']				= $uicols['name'][$k];
									$datatable['rows']['row'][$j]['column'][$k]['value'] 			= lang('Closed');
								}
								else
								{
									$datatable['rows']['row'][$j]['column'][$k]['name']				= $uicols['name'][$k];
									$datatable['rows']['row'][$j]['column'][$k]['value']			= $ticket[$uicols['name'][$k]];
								}
								if($uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'timestampopened')
								{
									$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
									$datatable['rows']['row'][$j]['column'][$k]['link']		=	$GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uitts.view',
											'id'			=> $ticket['id']
										));
									$datatable['rows']['row'][$j]['column'][$k]['value']		= $ticket[$uicols['name'][$k]];
									$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
								}

					}

					/*$content[] = array
					(
						'id'					=> $ticket['id'],
						'bgcolor'				=> $bgcolor,
						'new_ticket'			=> (isset($ticket['new_ticket'])?$ticket['new_ticket']:''),
						'priostr'				=> $ticket['priority'],
						'subject'				=> $ticket['subject'],
						'location_code'			=> $ticket['location_code'],
						'address'				=> $ticket['address'],
						'date'					=> $ticket['timestampopened'],
						'finnish_date'			=> $ticket['finnish_date'],
						'delay'					=> (isset($ticket['delay'])?$ticket['delay']:''),
						'user'					=> $ticket['user'],
						'assignedto'			=> $ticket['assignedto'],
						'child_date'			=> $ticket['child_date'],
						'link_view'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $ticket['id'])),
						'lang_view_statustext'	=> lang('view the ticket'),
						'text_view'				=> lang('view'),
						'status'				=> $status_text,
						'link_edit_status'		=> $GLOBALS['phpgw']->link('/index.php',$link_status_data),
						'lang_edit_status'		=> lang('Edit status'),
						'text_edit_status'		=> $text_edit_status,
					);*/

					$j++;
				}
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

						if($this->acl_read)
						{
							$datatable['rowactions']['action'][] = array(
								'statustext' 			=> lang('view the project'),
								'text'		=> lang('view'),
								'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uitts.view'
										)),
							'parameters'	=> $parameters
							);

						}


							$datatable['rowactions']['action'][] = array(
												'text' 			=> lang('add'),
												'action'		=> $GLOBALS['phpgw']->link('/index.php',array
																(
																	'menuaction'	=> 'property.uitts.add'
																))
										);


						if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'])
						{
							$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'];
														$datatable['rowactions']['action'][] = array(
																'text' 			=> lang('status'),
																'action'		=> $GLOBALS['phpgw']->link('/index.php',array
																(
																	'menuaction'		=> 'property.uitts.index',
																	'edit_status'		=> true,
																	'new_status'		=> $new_status,
																	'second_display'	=> true,
																	'sort'				=> $this->sort,
																	'order'				=> $this->order,
																	'cat_id'			=> $this->cat_id,
																	'filter'			=> $this->filter,
																	'user_filter'		=> $this->user_filter,
																	'query'				=> $this->query,
																	'district_id'		=> $this->district_id,
																	'allrows'			=> $this->allrows
																)),
																'parameters'	=> $parameters
														);
						}
			unset($parameters);

			/*$table_header[] = array
			(
				'sort_priority'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'priority',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_priority'		=> lang('Priority'),
				'lang_priority_statustext'		=> lang('Sort the tickets by their priority'),

				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_id'		=> lang('ID'),
				'lang_id_statustext'	=> lang('Sort the tickets by their ID'),

				'lang_subject'		=> lang('Subject'),
				'lang_time_created'	=> lang('Started'),
				'lang_view'		=> lang('view'),
				'lang_location_code'	=> lang('Location'),
				'lang_address'		=> lang('Address'),
				'lang_user'		=> lang('user'),
				'sort_assigned_to'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'assignedto',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'lang_assigned_to'	=> lang('Assigned to'),
				'sort_opened_by'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'user_lid',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.entry_date',
											'order'	=> $this->order,
											'extra' => array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_finnish_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'finnish_date',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=> $this->cat_id,
																	'filter'	=> $this->status_id,
																	'user_id'	=> $this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'second_display'=> true,
																	'allrows'	=> $this->allrows,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date
																	)
										)),
				'lang_finnish_date'	=> lang('finnish date'),
				'lang_delay'		=> lang('delay'),
				'lang_finnish_statustext'=> lang('presumed finnish date'),
				'lang_opened_by'	=> lang('Opened by'),
				'lang_status'		=> lang('Status')
			);*/

			for ($i=0;$i<$count_uicols_name;$i++)
			{

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='priority' || $uicols['name'][$i]=='id' || $uicols['name'][$i]=='assignedto' || $uicols['name'][$i]=='finnish_date'|| $uicols['name'][$i]=='user')
					{
						$datatable['headers']['header'][$i]['sortable']			= true;
						$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
					}

					if($uicols['name'][$i]=='text_view' || $uicols['name'][$i]=='bgcolor' || $uicols['name'][$i]=='child_date' || $uicols['name'][$i]== 'link_view' || $uicols['name'][$i]=='lang_view_statustext')
					{
						$datatable['headers']['header'][$i]['visible'] 			= false;
						$datatable['headers']['header'][$i]['format'] 			= 'hidden';
					}
				}
			}

			/*for ($i=0;$i<count($uicols);$i++)
			{
				//$table_header[0]['extra'][$i]['header'] = $uicols[$i];
				$datatable['headers']['header'][$i]['extra'] = $uicols[$i];
				//_debug_array($datatable['headers']);die;
			}*/

			/*$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a ticket'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.add'))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uitts.index',
				'second_display'=> true,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->status_id,
				'user_id'	=> $this->user_id,
				'query'		=> $this->query,
				'district_id'	=> $this->district_id,
				'start_date'	=> $start_date,
				'end_date'	=> $end_date,
				'allrows'	=> $this->allrows
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');

			$GLOBALS['phpgw']->preferences->read_repository();
			$autorefresh ='';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval']))
			{
				$autorefresh = $GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval'].'; URL='.$GLOBALS['phpgw']->link('/index.php',$link_data);
			}


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_date_search	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
			(
				'menuaction' 	=> 'property.uitts.download',
				'second_display'=> true,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->status_id,
				'user_id'	=> $this->user_id,
				'query'		=> $this->query,
				'district_id'	=> $this->district_id,
				'allrows'	=> $this->allrows,
				'start_date'	=> $start_date,
				'end_date'	=> $end_date,
				'start'		=> $this->start
			);*/

			/*$cat_select = '';
			$cat_filter = '';
			$pref_group_filters = '';
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters']))
			{
				$pref_group_filters = true;
				$group_filters = 'select';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field_grouped'));
				$cat_select	= $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id));
			}
			else
			{
				$group_filters = 'filter';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));
				$cat_filter = $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data));
			}*/

			//$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			/*$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'group_filters'					=> $pref_group_filters,
				'lang_download'					=> 'download',
				'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'				=> lang('Download table to your browser'),

				'start_date'					=> $start_date,
				'end_date'						=> $end_date,
				'lang_none'						=> lang('None'),
				'lang_date_search'				=> lang('Date search'),
				'lang_date_search_help'			=> lang('Narrow the search by dates'),
				'link_date_search'				=> $link_date_search,

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'autorefresh'					=> $autorefresh,
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($ticket_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'cat_select'					=> $cat_select,
				'cat_filter'					=> $cat_filter,

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_name'					=> 'filter',
				'filter_list'					=> $this->bo->filter(array('format' => $group_filters, 'filter'=> $this->status_id,'default' => 'O')),
				'lang_show_all'					=> lang('Open'),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'						=> (isset($content)?$content:''),
				'table_add'						=> $table_add,

				'district_list'					=> $this->bocommon->select_district_list($group_filters,$this->district_id),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'		=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'			=> 'district_id',

				'lang_user_statustext'			=> lang('Assigned to'),
				'select_user_name'				=> 'user_id',
				'lang_no_user'					=> lang('No user'),
				'user_list'						=> $this->bocommon->get_user_list_right2($group_filters,4,$this->user_id,$this->acl_location,'',$default=''),
				'allow_edit_status'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_link']:''
			);*/
			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($ticket);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

			$appname					= lang('helpdesk');
			$function_msg					= lang('list ticket');

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');


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

		    					if($column['name'] == 'priority')
		    					{
									switch($column['value'])
									{
										case 1:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[1].";'>".$column['value']."</div>";
											break;
										case 2:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[2].";'>".$column['value']."</div>";
											break;
										case 3:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[3].";'>".$column['value']."</div>";
											break;
										case 4:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[4].";'>".$column['value']."</div>";
											break;
										case 5:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[5].";'>".$column['value']."</div>";
											break;
										case 6:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[6].";'>".$column['value']."</div>";
											break;
										case 7:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[7].";'>".$column['value']."</div>";
											break;
										case 8:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[8].";'>".$column['value']."</div>";
											break;
										case 9:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[9].";'>".$column['value']."</div>";
											break;
										case 10:
											$json_row[$column['name']] = "<div style='background-color:".$bgcolor_array[10].";'>".$column['value']."</div>";
											break;
									}

		    					}

		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}

				// values for control select
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
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/colored.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			//$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'property', 'property' );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tts.index', 'property' );
			$this->save_sessiondata();
			//_debug_array($datatable);die;

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			//$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			//_debug_array($data);die;
			//$this->save_sessiondata();
		}

		function index2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts',
										'nextmatchs'));


			$second_display = phpgw::get_var('second_display', 'bool');

			$default_category = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');
			$default_status = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status']:'');
			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			if ($default_category && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_category;
				$this->district_id		= $default_category;
			}

			if ($default_status && !$second_display)
			{
				$this->bo->status_id	= $default_status;
				$this->status_id	= $default_status;
			}

			$bgcolor['1']	= '#dadada';
			$bgcolor['2']	= '#dad0d0';
			$bgcolor['3']	= '#dacaca';
			$bgcolor['4']	= '#dac0c0';
			$bgcolor['5']	= '#dababa';
			$bgcolor['6']	= '#dab0b0';
			$bgcolor['7']	= '#daaaaa';
			$bgcolor['8']	= '#da9090';
			$bgcolor['9']	= '#da8a8a';
			$bgcolor['10']	= '#da7a7a';


			$ticket_list = $this->bo->read($start_date,$end_date,$external=true);
			$uicols = $this->bo->uicols;

//_debug_array($uicols);
//_debug_array($ticket_list);
			while (is_array($ticket_list) && list(,$ticket) = each($ticket_list))
			{
				if ($ticket['status']=='O')
				{
					$status = lang('Open');
				}
				else
				{
					$status = lang('Closed');
				}

				$content[] = array
				(
					'id'					=> $ticket['id'],
					'bgcolor'				=> $bgcolor[$ticket['priority']],
					'new_ticket'			=> (isset($ticket['new_ticket'])?$ticket['new_ticket']:''),
					'priostr'				=> str_repeat("||", $ticket['priority']),
					'subject'				=> $ticket['subject'],
					'location_code'			=> $ticket['location_code'],
					'address'				=> $ticket['address'],
					'date'					=> $ticket['timestampopened'],
					'finnish_date'			=> $ticket['finnish_date'],
					'delay'					=> (isset($ticket['delay'])?$ticket['delay']:''),
					'user'					=> $ticket['user'],
					'assignedto'			=> $ticket['assignedto'],
					'child_date'			=> $ticket['child_date'],
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view2', 'id'=> $ticket['id'])),
					'lang_view_statustext'	=> lang('view the ticket'),
					'text_view'				=> lang('view'),
					'status'				=> $status,
				);
			}

			$table_header[] = array
			(
				'sort_priority'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'priority',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_priority'		=> lang('Priority'),
				'lang_priority_statustext'		=> lang('Sort the tickets by their priority'),

				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),

				'lang_id'		=> lang('ID'),
				'lang_id_statustext'	=> lang('Sort the tickets by their ID'),

				'lang_subject'		=> lang('Subject'),
				'lang_time_created'	=> lang('Started'),
				'lang_view'		=> lang('view'),
				'lang_location_code'	=> lang('Location'),
				'lang_address'		=> lang('Address'),
				'lang_user'		=> lang('user'),
				'sort_assigned_to'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'assignedto',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'lang_assigned_to'	=> lang('Assigned to'),
				'sort_opened_by'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'user_lid',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_tts_tickets.entry_date',
											'order'	=> $this->order,
											'extra' => array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=>$this->cat_id,
																	'filter'	=>$this->status_id,
																	'user_id'	=>$this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=>$this->query,
																	'second_display'	=> true,
																	'allrows'=>$this->allrows,
																	'start_date'	=>$start_date,
																	'end_date'	=>$end_date
																	)
										)),
				'sort_finnish_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'finnish_date',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uitts.index',
																	'cat_id'	=> $this->cat_id,
																	'filter'	=> $this->status_id,
																	'user_id'	=> $this->user_id,
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'second_display'=> true,
																	'allrows'	=> $this->allrows,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date
																	)
										)),
				'lang_finnish_date'	=> lang('finnish date'),
				'lang_delay'		=> lang('delay'),
				'lang_finnish_statustext'=> lang('presumed finnish date'),
				'lang_opened_by'	=> lang('Opened by'),
				'lang_status'		=> lang('Status')
			);

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[0]['extra'][$i]['header'] = $uicols[$i];
			}

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a ticket'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.add2'))
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uitts.index2',
				'second_display'	=> true,
				'sort'				=> $this->sort,
				'order'				=> $this->order,
				'cat_id'			=> $this->cat_id,
				'filter'			=> $this->status_id,
				'user_id'		=> $this->user_id,
				'query'				=> $this->query,
				'district_id'		=> $this->district_id,
				'start_date'		=> $start_date,
				'end_date'			=> $end_date,
				'allrows'			=> $this->allrows
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');

			$GLOBALS['phpgw']->preferences->read_repository();
			$autorefresh ='';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval']))
			{
				$autorefresh = $GLOBALS['phpgw_info']['user']['preferences']['property']['refreshinterval'].'; URL='.$GLOBALS['phpgw']->link('/index.php',$link_data);
			}

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_date_search	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
			(
				'menuaction' 		=> 'property.uitts.download2',
				'second_display'	=> true,
				'sort'				=> $this->sort,
				'order'				=> $this->order,
				'cat_id'			=> $this->cat_id,
				'filter'			=> $this->status_id,
				'user_id'		=> $this->user_id,
				'query'				=> $this->query,
				'district_id'		=> $this->district_id,
				'allrows'			=> $this->allrows,
				'start_date'		=> $start_date,
				'end_date'			=> $end_date,
				'start'				=> $this->start
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'lang_download'					=> 'download',
				'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'				=> lang('Download table to your browser'),

				'start_date'					=> $start_date,
				'end_date'						=> $end_date,
				'lang_none'						=> lang('None'),
				'lang_date_search'				=> lang('Date search'),
				'lang_date_search_help'			=> lang('Narrow the search by dates'),
				'link_date_search'				=> $link_date_search,

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'autorefresh'					=> $autorefresh,
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($ticket_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_name'					=> 'filter',
				'filter_list'					=> $this->bo->filter(array('format' => 'filter', 'filter'=> $this->status_id,'default' => 'open')),
				'lang_show_all'					=> lang('Open'),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header2'					=> $table_header,
				'values2'						=> (isset($content)?$content:''),
				'table_add'						=> $table_add,
			);

			$appname					= lang('helpdesk');
			$function_msg					= lang('list ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list2' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function add()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$bolocation		= CreateObject('property.bolocation');

			$values		= phpgw::get_var('values');

//------------------- start ticket from other location
			$bypass 		= phpgw::get_var('bypass', 'bool');
			if(isset($_POST) && $_POST && isset($bypass) && $bypass)
			{
				$boadmin_entity		= CreateObject('property.boadmin_entity');
				$location_code 		= phpgw::get_var('location_code');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

				$origin		= phpgw::get_var('origin');
				$origin_id	= phpgw::get_var('origin_id', 'int');

				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num));
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
//_debug_array($insert_record);
			if (isset($values['save']))
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
				}

				if(!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][]=array('msg'=>lang('Please give som details !'));
				}

				if((!isset($values['location']['loc1']) || !$values['location']['loc1']) && (!isset($values['extra']['p_num']) || !$values['extra']['p_num']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location - or an entity!'));
				}

				if(!$values['assignedto'] && !$values['group_id'])
				{
					$boresponsible = CreateObject('property.boresponsible');
					if(!$values['assignedto']=	$boresponsible->get_responsible($values))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a person or a group to handle the ticket !'));
					}
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values);

//------------ files
					$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);

					if($values['file_name'])
					{
						$bofiles	= CreateObject('property.bofiles');
						$to_file = $bofiles->fakebase . '/fmticket/' . $receipt['id'] . '/' . $values['file_name'];

						if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("fmticket/{$receipt['id']}");
							$bofiles->vfs->override_acl = 1;

							if(!$bofiles->vfs->cp(array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}
//--------------end files
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index'));
				}
				else
				{
					if(isset($values['location']) && $values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
					}
					if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> true,
						'lookup_type'	=> 'form',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($values['p'])?$values['p']:'')
						));


			$link_data = array
			(
				'menuaction'	=> 'property.uitts.add'
			);

			if(!isset($values['assignedto']))
			{
				$values['assignedto']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']:'');
			}
			if(!isset($values['group_id']))
			{
				$values['group_id']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']:'');
			}

			if(!isset($values['cat_id']))
			{
				$this->cat_id = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']:'');
			}
			else
			{
				$this->cat_id = $values['cat_id'];
			}

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_finnish_date');

			$data = array
			(
				'value_origin'					=> isset($values['origin']) ? $values['origin'] : '',
				'value_origin_type'				=> (isset($origin)?$origin:''),
				'value_origin_id'				=> (isset($origin_id)?$origin_id:''),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'				=> $location_data,
				'lang_assign_to'			=> lang('Assign to'),
				'lang_no_user'				=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'			=> 'values[assignedto]',
				'user_list'				=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),

				'lang_group'				=> lang('Group'),
				'lang_no_group'				=> lang('No group'),
				'group_list'				=> $this->bocommon->get_group_list('select',$values['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'			=> 'values[group_id]',

				'lang_priority'				=> lang('Priority'),
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'				=> $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:'')),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index')),
				'lang_subject'				=> lang('Subject'),
				'lang_subject_statustext'		=> lang('Enter the subject of this ticket'),

				'lang_details'				=> lang('Details'),
				'lang_details_statustext'		=> lang('Enter the details of this ticket'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'value_details'				=> (isset($values['details'])?$values['details']:''),
				'value_subject'				=> (isset($values['subject'])?$values['subject']:''),

				'lang_finnish_date'			=> lang('finnish date'),
				'value_finnish_date'			=> (isset($values['finnish_date'])?$values['finnish_date']:''),
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),
				'lang_finnish_date_statustext'		=> lang('Select the estimated date for closing the task'),

				'lang_done_statustext'			=> lang('Back to the ticket list'),
				'lang_save_statustext'			=> lang('Save the ticket'),
				'lang_no_cat'				=> lang('no category'),
				'lang_town_statustext'			=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town'			=> lang('Part of town'),
				'lang_no_part_of_town'			=> lang('No part of town'),
				'cat_select'				=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),

				'mailnotification'			=> (isset($this->bo->config->config_data['mailnotification'])?$this->bo->config->config_data['mailnotification']:''),
				'lang_mailnotification'			=> lang('Send e-mail'),
				'lang_mailnotification_statustext'	=> lang('Choose to send mailnotification'),
				'pref_send_mail'			=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
				'fileupload'				=> (isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:''),
			);

//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_ADD, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$bolocation		= CreateObject('property.bolocation');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts','files'));


			if(!$this->tenant_id)
			{
				$receipt['error'][]=array('msg'=>lang('No Tenant selected !'));
			}
			else
			{
				$values['extra']['tenant_id'] = $this->tenant_id;
				$values['location_code'] = $bolocation->get_tenant_location($this->tenant_id);


				if(!$values['location_code'])
				{
					$receipt['error'][]=array('msg'=>lang('No location for this tenant!'));
				}
				else
				{
					$location = explode('-',$values['location_code']);
					$i = 1;
					foreach ($location as $entry)
					{
						$values['location']["loc{$i}"]=$entry;
						$i++;
					}
				}
				if(is_array($values['location_code']))
				{
					$receipt['error'][]=array('msg'=>lang('Several locations for this tenant!'));
				}
			}

			$values['location_data'] = $bolocation->read_single($values['location_code'],array('extra'=>array('tenant_id'=>$this->tenant_id)));

			$values['street_name'] = $values['location_data']['street_name'];
			$values['street_number'] = $values['location_data']['street_number'];


			$values['assignedto']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']:'');
			if(!$values['assignedto'])
			{
				$receipt['error'][]=array('msg'=>lang('Please set default assign to in preferences for user %1!', $GLOBALS['phpgw']->accounts->id2name($this->account)));
			}

			$values['group_id']= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']:'');

			$values['cat_id'] = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']:'');

			if(!$values['cat_id'])
			{
				$receipt['error'][]=array('msg'=>lang('Please set default category in preferences for user %1!', $GLOBALS['phpgw']->accounts->id2name($this->account)));
			}

			if (isset($values['save']))
			{

// FIX this : relevant?
/*				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				while (is_array($insert_record['extra']) && list($key,$column) = each($insert_record['extra']))
				{
					if(isset($_POST[$key]) && $_POST[$key])
					{
						$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
					}
				}
*/
				if(!$values['subject'])
				{
					$receipt['error'][]=array('msg'=>lang('Please type a subject for this ticket !'));
				}

				if(!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][]=array('msg'=>lang('Please give som details !'));
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->add($values);
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->session->appsession('session_data','fm_tts','');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uitts.index2'));
				}
				else
				{
					if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> true,
						'lookup_type'	=> 'view',
						'lookup_entity'	=> false,
						'entity_data'	=> false
						));

			$link_data = array
			(
				'menuaction'	=> 'property.uitts.add2'
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}


			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
			(
				'lang_dateformat' 					=> strtolower($dateformat),
				'dateformat_validate'				=> $dateformat_validate,
				'onKeyUp'							=> $onKeyUp,
				'onBlur'							=> $onBlur,
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'						=> $location_data,
				'lang_assign_to'					=> lang('Assign to'),
				'lang_no_user'						=> lang('Select user'),
				'lang_user_statustext'				=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'					=> 'values[assignedto]',
				'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['assignedto'],$this->acl_location),

				'lang_group'						=> lang('Group'),
				'lang_no_group'						=> lang('No group'),
				'group_list'						=> $this->bocommon->get_group_list('select',$values['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'					=> 'values[group_id]',

				'lang_priority'						=> lang('Priority'),
				'lang_priority_statustext'			=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'				=> 'values[priority]',
				'priority_list'						=> $this->bo->get_priority_list((isset($values['priority'])?$values['priority']:'')),

				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index2')),
				'lang_subject'						=> lang('Subject'),
				'lang_subject_statustext'			=> lang('Enter the subject of this ticket'),

				'lang_details'						=> lang('Details'),
				'lang_details_statustext'			=> lang('Enter the details of this ticket'),
				'lang_category'						=> lang('category'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_details'						=> (isset($values['details'])?$values['details']:''),
				'value_subject'						=> (isset($values['subject'])?$values['subject']:''),

				'lang_finnish_date'					=> lang('finnish date'),
				'value_finnish_date'				=> (isset($values['finnish_date'])?$values['finnish_date']:''),

				'lang_done_statustext'				=> lang('Back to the ticket list'),
				'lang_save_statustext'				=> lang('Save the ticket'),
				'lang_no_cat'						=> lang('no category'),
				'lang_town_statustext'				=> lang('Select the part of town the building belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town'					=> lang('Part of town'),
				'lang_no_part_of_town'				=> lang('No part of town'),
				'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'mailnotification'					=> (isset($this->bo->config->config_data['mailnotification'])?$this->bo->config->config_data['mailnotification']:''),
				'lang_mailnotification'				=> lang('Send e-mail'),
				'lang_mailnotification_statustext'	=> lang('Choose to send mailnotification'),
				'pref_send_mail'					=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
				'lang_contact_phone'				=> lang('contact phone'),
				'lang_contact_phone_statustext'		=> lang('contact phone'),
				'value_contact_phone'				=> (isset($values['contact_phone'])?$values['contact_phone']:''),

				'lang_contact_email'				=> lang('contact email'),
				'lang_contact_email_statustext'		=> lang('contact email'),
				'value_contact_email'				=> (isset($values['contact_email'])?$values['contact_email']:''),
			);

//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('add ticket');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add2' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bolocation	= CreateObject('property.bolocation');

			$id = phpgw::get_var('id', 'int', 'GET');
			$values = phpgw::get_var('values');
			$receipt = '';

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts', 'files'));

			if(isset($values['save']))
			{
				if(!$this->acl_edit)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>4, 'acl_location'=> $this->acl_location));
				}

				$so2	= CreateObject('property.sotts2');
				$so2->acl_location	= $this->acl_location;
				$receipt = $so2->update_ticket($values,$id);

//--------- files
				$bofiles	= CreateObject('property.bofiles');
				if(isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/fmticket/{$id}/", $values);
				}

				$values['file_name']=str_replace(' ','_',$_FILES['file']['name']);

				if($values['file_name'])
				{
					$to_file = $bofiles->fakebase . '/fmticket/' . $id . '/' . $values['file_name'];

					if($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("fmticket/{$id}");
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
				}
			}
//---------end files
			$ticket = $this->bo->read_single($id);

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$request_link_data = array
			(
				'menuaction'		=> 'property.uirequest.edit',
				'bypass'		=> true,
				'location_code'		=> $ticket['location_code'],
				'p_num'			=> $ticket['p_num'],
				'p_entity_id'		=> $ticket['p_entity_id'],
				'p_cat_id'		=> $ticket['p_cat_id'],
				'tenant_id'		=> $ticket['tenant_id'],
				'origin'		=> '.ticket',
				'origin_id'		=> $id
			);


			$order_link_data = array
			(
				'menuaction'		=> 'property.uiproject.edit',
				'bypass'		=> true,
				'location_code'		=> $ticket['location_code'],
				'p_num'			=> $ticket['p_num'],
				'p_entity_id'		=> $ticket['p_entity_id'],
				'p_cat_id'		=> $ticket['p_cat_id'],
				'tenant_id'		=> $ticket['tenant_id'],
				'origin'		=> '.ticket',
				'origin_id'		=> $id
			);

			$form_link = array
			(
				'menuaction'	=> 'property.uitts.view',
				'id'		=> $id
			);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_additional_notes[] = array
			(
				'lang_count'		=> '#',
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_note'		=> lang('Note'),
			);

//_debug_array($ticket['location_data']);

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $ticket['location_data'],
						'type_id'	=> count(explode('-',$ticket['location_data']['location_code'])),
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> (isset($ticket['location_data']['tenant_id'])?$ticket['location_data']['tenant_id']:''),
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($ticket['p'])?$ticket['p']:'')
						));


			if($ticket['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}

			$start_entity	= $this->bocommon->get_start_entity('ticket');
//_debug_array($start_entity);

			$link_entity = '';
			if (isset($start_entity) AND is_array($start_entity))
			{
				$i=0;
				foreach($start_entity as $entry)
				{
					$link_entity[$i]['link'] = $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'		=> 'property.uientity.edit',
						'bypass'		=> true,
						'location_code'		=> $ticket['location_code'],
						'entity_id'		=> $entry['id'],
						'p_num'			=> $ticket['p_num'],
						'p_entity_id'		=> $ticket['p_entity_id'],
						'p_cat_id'		=> $ticket['p_cat_id'],
						'tenant_id'		=> $ticket['tenant_id'],
						'origin'		=> '.ticket',
						'origin_id'		=> $id
					));
					$link_entity[$i]['name']	= $entry['name'];
				$i++;
				}
			}

//_debug_array($link_entity);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
			(
				'menuaction'	=> 'property.uitts.view_file',
				'id'		=> $id
			);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_finnish_date');

			$data = array
			(
				'value_origin'				=> $ticket['origin'],
				'value_target'				=> $ticket['target'],
				'lang_finnish_date'			=> lang('finnish date'),
				'value_finnish_date'		=> $ticket['finnish_date'],
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),
				'lang_finnish_date_statustext'		=> lang('Select the estimated date for closing the task'),

				'link_entity'				=> $link_entity,
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'location_data'				=> $location_data,
				'lang_location_code'			=> lang('Location Code'),

				'lang_ticket'				=> lang('Ticket'),
				'table_header_additional_notes'		=> $table_header_additional_notes,
				'table_header_history'			=> $table_header_history,
				'lang_status'				=> lang('Status'),
				'status_name'				=> 'values[status]',
				'status_list'				=> $this->bo->get_status_list($ticket['status']),
				'lang_status_statustext'		=> lang('Set the status of the ticket'),

				'lang_no_user'				=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'			=> 'values[assignedto]',
				'user_list'					=> $this->bocommon->get_user_list_right2('select',4,$ticket['assignedto'],$this->acl_location),

				'lang_group'				=> lang('Group'),
				'lang_no_group'				=> lang('No group'),
				'group_list'				=> $this->bocommon->get_group_list('select',$ticket['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'			=> 'values[group_id]',

				'lang_priority'				=> lang('Priority'),
				'value_priority'			=> $ticket['priority'],
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'				=> $this->bo->get_priority_list($ticket['priority']),

				'lang_no_cat'				=> lang('no category'),
				'cat_select'				=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'lang_category'				=> lang('category'),
				'value_category_name'			=> $ticket['category_name'],

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$form_link),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index')),
				'value_subject'				=> $ticket['subject'],
				'lang_subject_statustext'		=> lang('update subject'),
				'value_id'				=> '[ #'. $id . ' ] - ',

				'lang_details'				=> lang('Details'),
				'value_details'				=> $ticket['details'],
				'lang_details_statustext'		=> lang('Add new comments'),

				'lang_additional_notes'			=> lang('Additional notes'),

				'lang_new_note'				=> lang('New Note'),
				'lang_opendate'				=> lang('Open Date'),
				'value_opendate'			=> $ticket['timestampopened'],

				'lang_assignedfrom'			=> lang('Assigned from'),
				'value_assignedfrom'			=> $ticket['user_name'],
				'lang_assignedto'			=> lang('Assigned to'),
				'value_assignedto'			=> isset($ticket['assignedto_name'])?$ticket['assignedto_name']:'',

				'lang_no_additional_notes'		=> lang('No additional notes'),
				'lang_history'				=> lang('History'),
				'lang_no_history'			=> lang('No history for this record'),
				'additional_notes'			=> $additional_notes,
				'record_history'			=> $record_history,
				'request_link'				=> $GLOBALS['phpgw']->link('/index.php',$request_link_data),
				'order_link'				=> $GLOBALS['phpgw']->link('/index.php',$order_link_data),

				'lang_generate_request'			=> lang('Generate Request'),
				'lang_generate_request_statustext'	=> lang('click this to generate a request with this information'),
				'lang_generate_order'			=> lang('Generate order'),
				'lang_generate_order_statustext'	=> lang('click this to generate an order with this information'),

				'lang_save'				=> lang('save'),
				'lang_name'				=> lang('name'),
				'lang_done'				=> lang('done'),
				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $ticket['contact_phone'],
				'mailnotification'			=> (isset($this->bo->config->config_data['mailnotification'])?true:''),
				'lang_mailnotification'			=> lang('Send e-mail'),
				'lang_mailnotification_statustext'	=> lang('Choose to send mailnotification'),
				'pref_send_mail'			=> (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_user_mailnotification']:''),
				'fileupload'				=> (isset($this->bo->config->config_data['fmttsfileupload'])?$this->bo->config->config_data['fmttsfileupload']:''),
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'					=> (isset($this->bo->config->config_data['files_url'])?$this->bo->config->config_data['files_url']:''),
				'files'							=> isset($ticket['files'])?$ticket['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_file_action'				=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'	=> lang('Check to delete file'),
				'lang_upload_file'				=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload'),

			);
//_debug_array($data);
			$appname					= lang('helpdesk');
			$function_msg					= lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view2()
		{
			if(!$this->acl->check('.ticket.external', PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> '.ticket.external'));
			}

			$bolocation	= CreateObject('property.bolocation');

			$id = phpgw::get_var('id', 'int', 'GET');
			$values = phpgw::get_var('values');
			$receipt = '';

			$GLOBALS['phpgw']->xslttpl->add_file(array('tts'));

			if(isset($values['save']))
			{
				if(!$this->acl->check('.ticket.external', PHPGW_ACL_ADD, 'property'))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>4, 'acl_location'=> '.ticket.external'));
				}

				$values['assignedto'] = 'ignore';
				$values['group_id'] = 'ignore';
				$values['cat_id'] = 'ignore';

				$so2	= CreateObject('property.sotts2');
				$so2->acl_location	= '.ticket.external';
				$receipt = $so2->update_ticket($values,$id);
			}

			$ticket = $this->bo->read_single($id);

			$additional_notes = $this->bo->read_additional_notes($id);
			$record_history = $this->bo->read_record_history($id);

			$form_link = array
			(
				'menuaction'	=> 'property.uitts.view2',
				'id'		=> $id
			);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_additional_notes[] = array
			(
				'lang_count'		=> '#',
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_note'		=> lang('Note'),
			);

//_debug_array($ticket['location_data']);

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $ticket['location_data'],
						'type_id'	=> count(explode('-',$ticket['location_data']['location_code'])),
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> (isset($ticket['location_data']['tenant_id'])?$ticket['location_data']['tenant_id']:''),
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('ticket'),
						'entity_data'	=> (isset($ticket['p'])?$ticket['p']:'')
						));


			if($ticket['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if ($ticket['cat_id'])
			{
				$this->cat_id = $ticket['cat_id'];
			}


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			if (isset($ticket['origin']) AND is_array($ticket['origin']))
			{
				for ($i=0;$i<count($ticket['origin']);$i++)
				{
					$ticket['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$ticket['origin'][$i]['link']);

					if(substr($ticket['origin'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$ticket['origin'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject('property.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$ticket['origin'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$ticket['origin'][$i]['descr']= lang($ticket['origin'][$i]['type']);
					}
				}
			}

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');

			$data = array
			(
				'value_origin'					=> (isset($ticket['origin'])?$ticket['origin']:''),
				'lang_dateformat' 				=> strtolower($dateformat),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'						=> $onKeyUp,
				'onBlur'						=> $onBlur,
				'lang_finnish_date'				=> lang('finnish date'),
				'value_finnish_date'			=> $ticket['finnish_date'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'location_data'					=> $location_data,
				'lang_location_code'			=> lang('Location Code'),

				'lang_ticket'					=> lang('Ticket'),
				'table_header_additional_notes'	=> $table_header_additional_notes,
				'table_header_history'			=> $table_header_history,
				'lang_status'					=> lang('Status'),
				'status_name'					=> 'values[status]',
				'status_list'					=> $this->bo->get_status_list($ticket['status']),
				'lang_status_statustext'		=> lang('Set the status of the ticket'),

				'lang_no_user'					=> lang('Select user'),
				'lang_user_statustext'			=> lang('Select the user the selection belongs to. To do not use a user select NO USER'),
				'select_user_name'				=> 'values[assignedto]',
				'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$ticket['assignedto'],$this->acl_location),

				'lang_group'					=> lang('Group'),
				'lang_no_group'					=> lang('No group'),
				'group_list'					=> $this->bocommon->get_group_list('select',$ticket['group_id'],$start=-1,$sort='ASC',$order='account_firstname',$query='',$offset=-1),
				'select_group_name'				=> 'values[group_id]',

				'lang_priority'					=> lang('Priority'),
				'value_priority'				=> $ticket['priority'],
				'lang_priority_statustext'		=> lang('Select the priority the selection belongs to.'),
				'select_priority_name'			=> 'values[priority]',
				'priority_list'					=> $this->bo->get_priority_list($ticket['priority']),

				'lang_no_cat'					=> lang('no category'),
				'cat_select'				=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id)),
				'lang_category'					=> lang('category'),
				'value_category_name'			=> $ticket['category_name'],

				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$form_link),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uitts.index2')),
				'value_subject'					=> $ticket['subject'],
				'lang_subject_statustext'		=> lang('update subject'),
				'value_id'						=> '[ #'. $id . ' ] - ',

				'lang_details'					=> lang('Details'),
				'value_details'					=> $ticket['details'],
				'lang_details_statustext'		=> lang('Add new comments'),

				'lang_additional_notes'			=> lang('Additional notes'),

				'lang_new_note'					=> lang('New Note'),
				'lang_opendate'					=> lang('Open Date'),
				'value_opendate'				=> $ticket['timestampopened'],

				'lang_assignedfrom'				=> lang('Assigned from'),
				'value_assignedfrom'			=> $ticket['user_name'],
				'lang_assignedto'				=> lang('Assigned to'),
				'value_assignedto'				=> (isset($ticket['assignedto_name'])?$ticket['assignedto_name']:''),

				'lang_no_additional_notes'		=> lang('No additional notes'),
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history for this record'),
				'additional_notes'				=> $additional_notes,
				'record_history'				=> $record_history,

				'lang_save'						=> lang('save'),
				'lang_name'						=> lang('name'),
				'lang_done'						=> lang('done'),
				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'					=> $ticket['contact_phone'],
			);
//_debug_array($data);
			$appname		= lang('helpdesk');
			$function_msg	= lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view2' => $data));
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('fmticket');
		}
	}

