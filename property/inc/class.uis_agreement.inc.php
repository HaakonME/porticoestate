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
	* @subpackage agreement
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	phpgw::import_class('phpgwapi.yui');

	class property_uis_agreement
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  		=> true,
			'view'			=> true,
			'edit'   		=> true,
			'delete'		=> true,
			'columns'		=> true,
			'edit_item'		=> true,
			'view_item'		=> true,
			'view_file'		=> true,
			'download'			=> true,
			'attrib_history'=> true,
			'import'		=> true
		);

		function property_uis_agreement()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::service';
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.bos_agreement',true);
			$this->bocommon		= CreateObject('property.bocommon');

			$this->role		= $this->bo->role;

			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name 	= 'fm_vendor';

			$this->acl		= & $GLOBALS['phpgw']->acl;
			$this->acl_location= '.s_agreement';

			$this->acl_read 	= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	= $this->acl->check($this->acl_location, 16, 'property');
			$this->custom		= & $this->bo->custom;

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->vendor_id	= $this->bo->vendor_id;
			$this->allrows		= $this->bo->allrows;
			$this->member_id	= $this->bo->member_id;
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
				'vendor_id'	=> $this->vendor_id,
				'allrows'	=> $this->allrows,
				'member_id'	=> $this->member_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$values                 = phpgw::get_var('values');

			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('property','s_agreement_columns',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uis_agreement.columns',
				'role'		=> $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	=> $this->bo->column_list($values['columns'],$allrows=true),
				'function_msg'	=> $function_msg,
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'	=> lang('columns'),
				'lang_none'	=> lang('None'),
				'lang_save'	=> lang('save'),
				'select_name'	=> 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('service_agreement');
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','s_agreement_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','s_agreement_receipt','');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uis_agreement.index',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'cat_id'	=>$this->cat_id,
					'filter'	=>$this->filter,
					'query'		=>$this->query,
					'role'		=> $this->role,
					'member_id'	=> $this->member_id
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uis_agreement.index',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
 	                        						."cat_id: '{$this->cat_id}',"
 	                        						."filter:'{$this->filter}',"
						 	                        ."query:'{$this->query}',"
 	                        						."role:'{$this->role}',"
						 	                        ."member_id:'{$this->member_id}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uis_agreement.index',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'cat_id'	=>$this->cat_id,
					'filter'	=>$this->filter,
					'query'		=>$this->query,
					'role'		=> $this->role,
					'member_id'	=> $this->member_id

				);

				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,'link_data' =>$link_data));
				$default_value = array ('cat_id'=>'','name'=> lang('no member'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'s_agreement','order'=>'descr'));
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_vendor_list('filter',$this->vendor_id);
				$default_value = array ('id'=>'','name'=>lang('no vendor'));
				array_unshift ($values_combo_box[2],$default_value);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction' 		=> 'property.uis_agreement.index',
								'sort'		=>$this->sort,
								'order'		=>$this->order,
								'cat_id'	=>$this->cat_id,
								'filter'	=>$this->filter,
								'query'		=>$this->query,
								'role'		=> $this->role,
								'member_id'	=> $this->member_id
							)
						),
					'fields'	=> array(
	                                    'field' => array(
				                                        array( //boton 	CATEGORY
				                                            'id' => 'btn_member_id',
				                                            'name' => 'member_id',
				                                            'value'	=> lang('Member'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 1
				                                        ),
				                                        array( //boton 	STATUS
				                                            'id' => 'btn_cat_id',
				                                            'name' => 'category_id',
				                                            'value'	=> lang('Category'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 2
				                                        ),
				                                        array( //boton 	HOUR CATEGORY
				                                            'id' => 'btn_vendor_id',
				                                            'name' => 'vendor_id',
				                                            'value'	=> lang('Vendor'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 3
				                                        ),
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_new',
							                                'value'	=> lang('add'),
							                                'tab_index' => 8
							                            ),
				                                        array( //boton     SEARCH
				                                            'id' => 'btn_search',
				                                            'name' => 'search',
				                                            'value'    => lang('search'),
				                                            'type' => 'button',
				                                            'tab_index' => 7
				                                        ),
				   										array( // TEXT INPUT
				                                            'name'     => 'query',
				                                            'id'     => 'txt_query',
				                                            'value'    => '',//$query,
				                                            'type' => 'text',
				                                            'onkeypress' => 'return pulsar(event)',
				                                            'size'    => 28,
				                                            'tab_index' => 6
				                                        ),
				                                        array(
							                                'type' => 'link',
							                                'id' => 'btn_columns',
							                                'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
																					           array
																					              (
																					               'menuaction' => 'property.uis_agreement.columns',
																								   'role'		=> $this->role
																					              ))."','','width=300,height=600')",
															'value' => lang('columns'),
															'tab_index' => 9
														)
			                           				),
			                       		'hidden_value' => array(
						                                        array( //div values  combo_box_0
								                                            'id' => 'values_combo_box_0',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id')
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

				$dry_run = true;
			}

			$list = $this->bo->read();

			$uicols	= $this->bo->uicols;
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($list) AND is_array($list))
			{
				foreach($list as $list_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $list_entry[$uicols['name'][$k]];
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

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

			$parameters2 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 's_agreement_id',
						'source'	=> 'id'
					),
				)
			);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'view',
							'statustext' 	=> lang('view this entity'),
							'text'			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.view',
										'role'			=> $this->role
									)),
						'parameters'	=> $parameters
						);
			}

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'edit',
							'statustext' 	=> lang('edit this entity'),
							'text'			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.edit',
										'role'			=> $this->role
									)),
						'parameters'	=> $parameters
						);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'delete',
							'statustext' 	=> lang('delete this entity'),
							'text'			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.delete',
										'role'			=> $this->role
									)),
						'parameters'	=> $parameters2
						);
			}

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'add',
							'statustext' 	=> lang('add an entity'),
							'text'			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.edit',
										'role'			=> $this->role
									))
						);
			}

			unset($parameters);
			unset($parameters2);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='id' || $uicols['name'][$i]=='name' || $uicols['name'][$i]=='org_name' || $uicols['name'][$i]=='category' || $uicols['name'][$i]=='start_date' || $uicols['name'][$i]=='end_date' || $uicols['name'][$i]=='status')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}

					if($uicols['name'][$i]=='category')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'org_name';
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= lang('agreement');
			$function_msg		= lang('List') . ' ' . lang($this->role);

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
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
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
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

				// right in datatable
				if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
				{
					$json ['rights'] = $datatable['rowactions']['action'];
				}

	    		return $json;
			}
			//-------------------- JSON CODE ----------------------

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'uisagreement.index', 'property' );
		}

		function list_content($list,$uicols,$edit_item='',$view_only='')
		{
			$j=0;

			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$content[$j]['id'] 			= $entry['id'];
					$content[$j]['item_id'] 	= $entry['item_id'];
					$content[$j]['index_count']	= $entry['index_count'];
					$content[$j]['cost'] 		= $entry['cost'];
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 			= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 			= $uicols['name'][$i];
						}
					}

					if($this->acl_read && $view_only != 'no_link')
					{
						$content[$j]['row'][$i]['statustext']			= lang('view the entity');
						$content[$j]['row'][$i]['text']					= lang('view');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.view_item', 's_agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id'], 'from'=> $view_only?'view':'edit'));
					}
					if($this->acl_edit && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']			= lang('edit the agreement');
						$content[$j]['row'][$i]['text']					= lang('edit');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit_item', 's_agreement_id'=> $entry['agreement_id'], 'id'=> $entry['id']));
					}
					if($this->acl_delete && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']			= lang('delete this item');
						$content[$j]['row'][$i]['text']					= lang('delete');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'delete_item'=>1, 'id'=> $entry['agreement_id'], 'item_id'=> $entry['id']));
					}

					$j++;
				}
			}

//html_print_r($content);
			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
				}
			}

			if($this->acl_read && $view_only != 'no_link')
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('view');
				$i++;
			}
			if($this->acl_edit && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('edit');
				$i++;
			}
			if($this->acl_delete && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('delete');
				$i++;
			}
			if($this->acl_manage && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('Update');
				$i++;
			}

			return array('content'=>$content,'table_header'=>$table_header);
		}

		function import()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$active_tab		= phpgw::get_var('tab', 'string', 'POST', 'items');

			$import = CreateObject('property.import');

			$importfile = $import->importfile();
			$id		= phpgw::get_var('id', 'int');
			if(isset($importfile) && is_file($importfile) && !phpgw::get_var('cancel'))
			{
				$list = $this->bo->read_details(0);
				$uicols		= $this->bo->uicols;
				$valueset = $import->prepare_data($importfile, $list, $uicols);

				if(phpgw::get_var('confirm', 'bool'))
				{
					if(is_file($importfile))
					{
						unlink ($importfile);
					}
					foreach($valueset as $values)
					{
						$this->bo->import($values,$id);
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $id, 'tab' => $active_tab));
				}
				else
				{
					$import_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.import', 'id'=> $id));
					$import->pre_import($importfile, $valueset, $import_action, $header_info = lang('service agreement'));
				}
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $id, 'tab' => $active_tab));
			}
		}

		function edit()
		{
			$id				= phpgw::get_var('id');
			$values			= phpgw::get_var('values');
			$delete_item	= phpgw::get_var('delete_item');
			$item_id		= phpgw::get_var('item_id');
			$active_tab		= phpgw::get_var('tab', 'string', 'REQUEST', 'general');

			$config			= CreateObject('phpgwapi.config','property');
			$boalarm		= CreateObject('property.boalarm');
			$get_items = false;

			if($delete_item && $id && $item_id)
			{
				$this->bo->delete_item($id,$item_id);
				$get_items = true;
			}

			$values_attribute  = phpgw::get_var('values_attribute');

			$insert_record_s_agreement = $GLOBALS['phpgw']->session->appsession('insert_record_values.s_agreement','property');


			for ($j=0;$j<count($insert_record_s_agreement);$j++)
			{
				$insert_record['extra'][$insert_record_s_agreement[$j]]	= $insert_record_s_agreement[$j];
			}


			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement', 'attributes_form', 'files'));

			if (is_array($values))
			{
				while (is_array($insert_record['extra']) && list($key,$column) = each($insert_record['extra']))
				{
					if($_POST[$key])
					{
						$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
					}
				}


				if ($values['save'] || $values['apply']):
				{
					$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
					$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
					$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
					$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

					if(!$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}

					if(!$values['last_name'])
					{
//						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}


					if($id)
					{
						$values['s_agreement_id']=$id;
						$action='edit';
					}
					else
					{
						$values['s_agreement_id']=$this->bo->request_next_id();
					}

					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/service_agreement/{$id}/", $values);
					}

					$values['file_name'] = str_replace (' ','_',$_FILES['file']['name']);
					$to_file = "{$bofiles->fakebase}/service_agreement/{$values['s_agreement_id']}/{$values['file_name']}";

					if(!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][]=array('msg'=>lang('This file already exists !'));
					}

					if(!$receipt['error'])
					{
//						$values['s_agreement_id']	= $id;
						$receipt = $this->bo->save($values,$values_attribute,$action);
						$id = $receipt['s_agreement_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if($values['file_name'])
						{
							$bofiles->create_document_dir("service_agreement/{$id}");
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


						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','s_agreement_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.index', 'role'=> $this->role));
						}
					}
				}
				elseif($values['update']):
				{
					if(!$values['date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a date !'));
					}
					if(!$values['new_index'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a index !'));
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->update($values);
						$get_items = true;

					}

				}
				elseif($values['delete_alarm'] && count($values['alarm'])):
				{

					if(!$receipt['error'])
					{
						$receipt = $boalarm->delete_alarm('s_agreement',$values['alarm']);
					}

				}
				elseif(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm'])):
				{

					if(!$receipt['error'])
					{
						$receipt = $boalarm->enable_alarm('s_agreement',$values['alarm'],$values['enable_alarm']);
					}

				}
				elseif($values['add_alarm']):
				{
					$time = intval($values['time']['days'])*24*3600 +
						intval($values['time']['hours'])*3600 +
						intval($values['time']['mins'])*60;

					if ($time > 0)
					{
						$receipt = $boalarm->add_alarm('s_agreement',$this->bo->read_event(array('s_agreement_id'=>$id)),$time,$values['user_id']);
					}

				}
				elseif (!$values['save'] && !$values['apply'] && !$values['update']):
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.index', 'role'=> $this->role));
				}
				endif;
			}

			$s_agreement = $this->bo->read_single(array('s_agreement_id'=>$id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$s_agreement = $this->bocommon->preserve_attribute_values($s_agreement,$values_attribute);
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');
			$jscal->add_listener('values_termination_date');

			if ($id)
			{
				$this->cat_id = ($s_agreement['cat_id']?$s_agreement['cat_id']:$this->cat_id);
				$this->member_id = ($s_agreement['member_of']?$s_agreement['member_of']:$this->member_id);
				$list = $this->bo->read_details($id);

				$uicols		= $this->bo->uicols;
				$list		= $this->list_content($list,$uicols);
				$content	= $list['content'];
				$table_header=$list['table_header'];
				for ($i=0; $i<count($list['content'][0]['row']); $i++)
				{
					$set_column[]=true;
				}

				if (isset($content) && is_array($content))
				{
					$jscal->add_listener('values_date');

					$table_update[] = array
					(
						'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
						'lang_datetitle'		=> lang('Select date'),
						'lang_new_index'		=> lang('New index'),
						'lang_new_index_statustext'	=> lang('Enter a new index'),
						'lang_date_statustext'		=> lang('Select the date for the update'),
						'lang_update'			=> lang('Update'),
						'lang_update_statustext'	=> lang('update selected investments')
					);
				}

			}

			$link_data = array
			(
				'menuaction'	=> 'property.uis_agreement.edit',
				'id'		=> $id,
				'role'		=> $this->role
			);

			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'	=> $s_agreement['vendor_id'],
						'vendor_name'	=> $s_agreement['vendor_name']));

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $s_agreement['b_account_id'],
						'b_account_name'	=> $s_agreement['b_account_name']));


			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'	=> 's_agreement',
						'type'		=> 'form',
						'text'		=> 'Email notification',
						'times'		=> $times,
						'id'		=> $id,
						'method'	=> $method,
						'data'		=> $data,
						'account_id'=> $account_id
						));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,link_data => array()));

			$table_add[] = array
			(
				'lang_add'				=> lang('add detail'),
				'lang_add_standardtext'	=> lang('add an item to the details'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit_item', 's_agreement_id'=> $id))
			);


			$link_file_data = array
			(
				'menuaction'	=> 'property.uis_agreement.view_file',
				'id'		=>$id
			);

			$config->read();
			$link_to_files = $config->config_data['files_url'];

			$j	= count($s_agreement['files']);
			for ($i=0;$i<$j;$i++)
			{
				$s_agreement['files'][$i]['file_name']=urlencode($s_agreement['files'][$i]['name']);
			}

			$link_download = array
			(
				'menuaction'	=> 'property.uis_agreement.download',
				'id'		=> $id
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$tabs = array();

			if (isset($s_agreement['attributes']) && is_array($s_agreement['attributes']))
			{

		/*		foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uis_agreement.attrib_history',
							'attrib_id'	=> $attribute['id'],
							'id'		=> $id,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
		*/

				phpgwapi_yui::tabview_setup('edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$location = $this->acl_location;
				$attributes_groups = $this->bo->get_attribute_groups($location, $s_agreement['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($s_agreement['attributes']);

				$tabs['items']	= array('label' => lang('items'), 'link' => '#items');
			}

//----------JSON CODE ----------------------------------------------

			//---GET ITEMS
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  $get_items )
			{
				//$this->bo->delete_item($id,$item_id);
				$list		= $this->bo->read_details($id);
				$list	 	= $this->list_content($list,$uicols);
				$content	= $list['content'];

				$content_values = array();

				for($y=0;$y<count($content);$y++)
				{
					for($z=0;$z<count($content[$y]['row']);$z++)
					{
						if($content[$y]['row'][$z+1]['name']!='')
						{
							$content_values[$y][$content[$y]['row'][$z+1]['name']] = $content[$y]['row'][$z+1]['value'];
						}
					}
				}

				/*$datavalues[0] = array
				(
						'name'					=> "0",
						'values' 				=> json_encode($content_values),
						'total_records'			=> count($content_values),
						'edit_action'			=> "''",
						'is_paginator'			=> 0,
						'permission'			=> json_encode($permissions['rowactions']),
						'footer'				=> 0
				);*/
				if(count($content_values))
				{
					return json_encode($content_values);
				}
				else
				{
					return "";
				}
			}

			//---GET ALARM
			else if( phpgw::get_var('phpgw_return_as') == 'json' && !$get_items )
			{
				$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'=>$this->acl_location,
						'alarm_type'=> 's_agreement',
						'type'		=> 'form',
						'text'		=> 'Email notification',
						'times'		=> isset($times)?$times:'',
						'id'		=> $id,
						'method'	=> isset($method)?$method:'',
						'data'		=> isset($data)?$data:'',
						'account_id'=> isset($account_id)?$account_id:''
						));
				//$alarm_data['values'] = array();
				if(count($alarm_data['values']))
				{
					return json_encode($alarm_data['values']);
				}
				else
				{
					return "";
				}
			}

			//--------------------JSON code-----




			//_debug_array($id);die;




			$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 's_agreement_id',
						'source'	=> $id,
						'ready'		=> 1
					),
					array
					(
						'name'		=> 'id',
						'source'	=> 'item_id'
					),
					array
					(
						'name'		=> 'from',
						'source'	=> $view_only?'view':'edit',
						'ready'		=> 1
					)
				)
			);

			$parameters2 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 's_agreement_id',
						'source'	=> $id,
						'ready'		=> 1
					),
					array
					(
						'name'		=> 'id',
						'source'	=> 'item_id'
					)
				)
			);

			$parameters3 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'id',
						'source'	=> $id,
						'ready'		=> 1
					),
					array
					(
						'name'		=> 'item_id',
						'source'	=> 'item_id'
					),
					array
					(
						'name'		=> 'delete_item',
						'source'	=> 1,
						'ready'		=> 1
					)
				)
			);

			//_debug_array($parameters3);die;

			/*REQUIRES VALIDATION OF PERMISSIONS*/
			$permissions['rowactions'][] = array(
					'text' 			=> lang('View'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.view_item'
									)),
					'parameters'	=> $parameters
			);

			$permissions['rowactions'][] = array(
					'text' 			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.edit_item'
									)),
					'parameters'	=> $parameters2
			);

			$permissions['rowactions'][] = array(
					'text' 			=> lang('Delete'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uis_agreement.edit'
									)),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'parameters'	=> $parameters3
			);

			$content_values = array();

			for($y=0;$y<count($content);$y++)
			{
				for($z=0;$z<count($content[$y]['row']);$z++)
				{
					if($content[$y]['row'][$z+1]['name']!='')
					{
						$content_values[$y][$content[$y]['row'][$z+1]['name']] = $content[$y]['row'][$z+1]['value'];
					}
				}
			}

//------- alarm--------
   $datavalues[0] = array
   (
    'name'   => "0",
    'values'   => json_encode($alarm_data['values']),
    'total_records' => count($alarm_data['values']),
    'is_paginator' => 0,
    'permission'=> '""',
    'footer'  => 0
   );

         $myColumnDefs[0] = array
         (
          'name'   => "0",
          'values'  => json_encode(array( array(key => time, label=>$alarm_data['header'][0]['lang_time'], sortable=>true,resizeable=>true,width=>130),
                    array(key => text, label=>$alarm_data['header'][0]['lang_text'], sortable=>true,resizeable=>true,width=>300),
                    array(key => user, label=>$alarm_data['header'][0]['lang_user'], sortable=>true,resizeable=>true,width=>150),
                          array(key => enabled,label=>$alarm_data['header'][0]['lang_enabled'],sortable=>true,resizeable=>true,formatter=>FormatterCenter,width=>50),
                          array(key => alarm_id,label=>"dummy",sortable=>true,resizeable=>true,hidden=>true),
                          array(key => select,label=>$alarm_data['header'][0]['lang_select'], sortable=>false,resizeable=>false,formatter=>myFormatterCheck,width=>50)))
   );

         $myButtons[0] = array
         (
          'name'   => "0",
          'values'  => json_encode(array( array(id =>'values[enable_alarm]',type=>buttons, value=>Enable, label=>$alarm_data[alter_alarm][0][lang_enable], funct=> onActionsClick , classname=> actionButton),
                    array(id =>'values[disable_alarm]',type=>buttons, value=>Disable, label=>$alarm_data[alter_alarm][0][lang_disable], funct=> onActionsClick , classname=> actionButton),
                    array(id =>'values[delete_alarm]',type=>buttons, value=>Delete, label=>$alarm_data[alter_alarm][0][lang_delete], funct=> onActionsClick , classname=> actionButton),
                    ))
   );

         $myButtons[1] = array
         (
          'name'   => "1",
          'values'  => json_encode(array( array(id =>'values[time][days]', type=>menu,  value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['day_list'],"1_0",'values[time][days]' ), label=>"0", classname=> actionsFilter),
                    array(id =>'values[time][hours]', type=>menu,  value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['hour_list'],"1_1",'values[time][hours]'), label=>"0", classname=> actionsFilter),
                    array(id =>'values[time][mins]', type=>menu,  value=>$this->bocommon->make_menu_date($alarm_data['add_alarm']['minute_list'],"1_2",'values[time][mins]'), label=>"0", classname=> actionsFilter),
                    array(id =>'values[user_id]',  type=>menu,  value=>$this->bocommon->make_menu_user($alarm_data['add_alarm']['user_list'],"1_3",'values[user_id]'), label=>$this->bocommon->choose_select($alarm_data['add_alarm']['user_list']),classname=> actionsFilter),

                    array(id =>'values[add_alarm]',  type=>buttons, value=>Add,  label=>$alarm_data[add_alarm][lang_add],   funct=> onAddClick , classname=> actionButton),
                    ))
   );


//---------items------------------------------------
			$datavalues[1] = array
			(
					'name'					=> "1",
					'values' 				=> json_encode($content_values),
					'total_records'			=> count($content_values),
					'is_paginator'			=> 0,
					'permission'			=> json_encode($permissions['rowactions']),
					'footer'				=> 0
			);

       		$myColumnDefs[1] = array
       		(
       			'name'		=> "1",
       			'values'	=>	json_encode(array(	array(key => item_id,label=>lang('ID'),sortable=>true,resizeable=>true),
									       			array(key => location_code,label=>lang('Location'),sortable=>true,resizeable=>true),
									       			array(key => address,label=>lang('Address'),sortable=>true,resizeable=>true),
									       			array(key => p_entity_id,label=>lang('entity_id'),sortable=>true,resizeable=>true),
									       			array(key => p_cat_id,label=>lang('cat_id'),sortable=>true,resizeable=>true),
									       			array(key => p_num,label=>lang('entity_num'),sortable=>true,resizeable=>true),
									       			array(key => cost,label=>lang('Cost'),sortable=>true,resizeable=>true),
									       			array(key => this_index,label=>lang('index'),sortable=>true,resizeable=>true),
									       			array(key => index_count,label=>lang('index_count'),sortable=>true,resizeable=>true),
									       			array(key => index_date,label=>lang('Date'),sortable=>true,resizeable=>true),
									       			array(key => enhet,label=>lang('Enhet'),sortable=>true,resizeable=>true),
									       			array(key => quantity,label=>lang('mengde'),sortable=>true,resizeable=>true),
									       			array(key => update,label=>lang('Update'),resizeable=>true,formatter=>myFormatterCheckUpdate)))
			);

			$myButtons[2] = array
       		(
       			'name'			=> "2",
       			'values'		=>	json_encode(array(	array(id =>'values[new_index]', type=>inputText, size=>12, classname=> ''),
       													array(type=>text, label=>'New index'),
       													array(id =>'values[update]',type=>buttons,	value=>Update,	label=>'Update',	funct=> onUpdateClick , classname=> '')
       													/*array(type=>img, src=>'/pgwsvn/phpgwapi/templates/base/images/cal.png', id=>'values_date-trigger')*/

       													))
			);



//--------------------------------------------JSON CODE------------

			$data = array
			(

				'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'base_java_url'						=> json_encode(array(menuaction => "property.uis_agreement.edit",id=>$id)),
				'datatable'							=> $datavalues,
				'myColumnDefs'						=> $myColumnDefs,
				'myButtons'							=> $myButtons,

				'link_import'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uis_agreement.import', 'tab' => 'items')),
				'alarm_data'				=> $alarm_data,
				'lang_alarm'				=> lang('Alarm'),
				'lang_download'				=> 'download',
				'link_download'				=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'		=> lang('Download table to your browser'),

				'fileupload'				=> true,
				'link_view_file'			=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'				=> $link_to_files,
				'files'					=> $s_agreement['files'],
				'lang_files'				=> lang('files'),
				'lang_filename'				=> lang('Filename'),
				'lang_file_action'			=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('click to view file'),
				'lang_file_action_statustext'		=> lang('Check to delete file'),
				'lang_upload_file'			=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('ID'),
				'value_s_agreement_id'			=> $id,
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_cat'				=> $s_agreement['cat'],
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the service agreement untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the service agreement and return back to the list'),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the s_agreement belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'s_agreement','order'=>'descr')),

				'lang_member_of'			=> lang('member of'),
				'member_of_name'			=> 'member_id',
				'member_of_list'			=> $member_of_data['cat_list'],

				'attributes_group'				=> $attributes,
				'lookup_functions'			=> $s_agreement['lookup_functions'],
				'dateformat'				=> $dateformat,

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_start_date'			=> lang('start date'),
				'value_start_date'			=> $s_agreement['start_date'],

				'lang_end_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_end_date'				=> lang('end date'),
				'value_end_date'			=> $s_agreement['end_date'],

				'lang_termination_date_statustext'	=> lang('Select the estimated termination date'),
				'lang_termination_date'			=> lang('termination date'),
				'value_termination_date'		=> $s_agreement['termination_date'],

				'vendor_data'				=> $vendor_data,
				'b_account_data'			=> $b_account_data,
				'lang_name'				=> lang('name'),
				'lang_name_statustext'			=> lang('name'),
				'value_name'				=> $s_agreement['name'],
				'lang_descr'				=> lang('descr'),
				'lang_descr_statustext'			=> lang('descr'),
				'value_descr'				=> $s_agreement['descr'],
				'table_add'				=> $table_add,
				'values'				=> $content,
				'table_header'				=> $table_header,
				'acl_manage'				=> $this->acl_manage,
				'table_update'				=> $table_update,
				'update_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $id)),
				'lang_select_all'			=> lang('Select All'),
				'img_check'				=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'set_column'				=> $set_column,
				'lang_import_detail'		=> lang('import detail'),
				'lang_detail_import_statustext'=> lang('import details to this agreement from spreadsheet'),
				'lang_import'				=> lang('import'),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'						=> phpgwapi_yui::tabview_generate($tabs, $active_tab)
			);

//_debug_array($data);die;

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . ($id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'uis_agreement.edit', 'property' );
		}

		function download()
		{
			$id	= phpgw::get_var('id', 'int');
			$list = $this->bo->read_details($id);
			$uicols		= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function edit_item()
		{
			$s_agreement_id	= phpgw::get_var('s_agreement_id', 'int');
			$id	= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
			$delete_last	= phpgw::get_var('delete_last', 'bool', 'GET');
			if($delete_last)
			{
				$this->bo->delete_last_index($s_agreement_id,$id);
			}

			$bolocation			= CreateObject('property.bolocation');

			$values_attribute  = phpgw::get_var('values_attribute');

			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement','attributes_form'));

			if (is_array($values))
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				$insert_record_s_agreement1 = $GLOBALS['phpgw']->session->appsession('insert_record_values.s_agreement.detail','property');
//_debug_array($insert_record_s_agreement1);

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				for ($j=0;$j<count($insert_record_s_agreement1);$j++)
				{
					$insert_record['extra'][$insert_record_s_agreement1[$j]]	= $insert_record_s_agreement1[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);
//_debug_array($values);
				if ($values['save'] || $values['apply']):
				{

					if(!$receipt['error'])
					{
						$values['s_agreement_id']	= $s_agreement_id;
						$values['id']	= $id;
						$receipt = $this->bo->save_item($values,$values_attribute);
						$s_agreement_id = $receipt['s_agreement_id'];
						$id 			= $receipt['id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','s_agreement_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $s_agreement_id));
						}
					}
					else
					{
						if($values['location'])
						{
							$location_code=implode("-", $values['location']);
							$values['location_data'] = $bolocation->read_single($location_code,$values['extra']);
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
				elseif($values['update']):
				{
					if(!$values['date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a date !'));
					}
					if(!$values['new_index'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a index !'));
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->update($values);
					}

				}
				elseif (!$values['save'] && !$values['apply'] && !$values['update']):
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.edit', 'id'=> $s_agreement_id));
				}
				endif;
			}

			$s_agreement = $this->bo->read_single(array('s_agreement_id'=>$s_agreement_id, 'view' => true));
			$values = $this->bo->read_single_item(array('s_agreement_id'=>$s_agreement_id,'id'=>$id));

			$link_data = array
			(
				'menuaction'		=> 'property.uis_agreement.edit_item',
				's_agreement_id'	=> $s_agreement_id,
				'id'			=> $id,
				'role'			=> $this->role
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,link_data => array()));

			$table_add[] = array
			(
				'lang_add'				=> lang('add detail'),
				'lang_add_standardtext'	=> lang('add an item to the details'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit_item', 's_agreement_id'=> $s_agreement_id))
			);


			if($id)
			{
				$list = $this->bo->read_prizing(array('s_agreement_id'=>$s_agreement_id,'item_id'=>$id));
				$jscal = CreateObject('phpgwapi.jscalendar');
				$jscal->add_listener('values_date');
			}

			$uicols		= $this->bo->uicols;
			$list		= $this->list_content($list,$uicols,$edit_item=true);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			for ($i=0; $i<count($list['content'][0]['row']); $i++)
			{
				$set_column[]=true;
			}
//_debug_array($list);

			$table_update[] = array
			(

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'		=> lang('Select date'),
				'lang_new_index'		=> lang('New index'),
				'lang_new_index_statustext'	=> lang('Enter a new index'),
				'lang_date_statustext'		=> lang('Select the date for the update'),
				'lang_update'			=> lang('Update'),
				'lang_update_statustext'	=> lang('update selected investments')
			);


			$lookup_type='form';

//_debug_array($values);
			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> false,
						'lookup_type'	=> $lookup_type,
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('s_agreement'),
						'entity_data'	=> $values['p']
						));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$tabs = array();

			if (isset($values['attributes']) && is_array($values['attributes']))
			{

				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uis_agreement.attrib_history',
							's_agreement_id'	=> $s_agreement_id,
							'attrib_id'	=> $values['attributes'][$i]['id'],
							'item_id'	=> $id,
							'edit'		=> true,
							'role'		=>'detail'
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}


				phpgwapi_yui::tabview_setup('edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$location = $this->acl_location . '.detail';
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($s_agreement['attributes']);

				if($content)
				{
					$tabs['history']	= array('label' => lang('history'), 'link' => '#history');
				}
			}


			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('ID'),
				'value_id'						=> $values['id'],
				'value_s_agreement_id'			=> $s_agreement_id,
				'lang_category'					=> lang('category'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the service agreement untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the service agreement and return back to the list'),

				'attributes_group'				=> $attributes,
				'lookup_functions'				=> $values['lookup_functions'],

			//	'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'				=> lang('Select date'),

				'lang_agreement'				=> lang('Agreement'),
				'agreement_name'				=> $s_agreement['name'],

				'table_add'						=> $table_add,
				'values'						=> $content,
				'table_header'					=> $table_header,
				'acl_manage'					=> $this->acl_manage,
				'table_update'					=> $table_update,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit_item', 's_agreement_id'=> $s_agreement_id, 'id'=> $id)),
				'lang_select_all'				=> lang('Select All'),
				'img_check'						=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'location_data'					=> $location_data,

				'lang_cost'						=> lang('cost'),
				'lang_cost_statustext'			=> lang('cost'),
				'value_cost'					=> $values['cost'],
				'set_column'					=> $set_column,
				'lang_delete_last'				=> lang('delete last index'),
				'lang_delete_last_statustext'	=> lang('delete the last index'),
				'delete_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.edit_item', 'delete_last'=>1, 's_agreement_id'=> $s_agreement_id, 'id'=> $id)),

				'lang_history'					=> lang('history'),
				'lang_history_help'				=> lang('history of this attribute'),
				'lang_history_date_statustext'	=> lang('Enter the date for this reading'),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general')
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . ($values['id']?lang('edit item') . ' ' . $s_agreement['name']:lang('add item') . ' ' . $s_agreement['name']);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_item' => $data));
		}

		function view_item()
		{
			$from = phpgw::get_var('from');
			$from = $from == 'edit'?'edit':'view';
			$s_agreement_id	= phpgw::get_var('s_agreement_id', 'int');
			$id	= phpgw::get_var('id', 'int');

			$bolocation			= CreateObject('property.bolocation');

			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement','attributes_view'));

			$s_agreement = $this->bo->read_single(array('s_agreement_id'=>$s_agreement_id, 'view' => true));
			$values = $this->bo->read_single_item(array('s_agreement_id'=>$s_agreement_id,'id'=>$id));

			$link_data = array
			(
				'menuaction'	=> 'property.uis_agreement.' . $from,
				'id'		=> $s_agreement_id
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			if($id)
			{
				$list = $this->bo->read_prizing(array('s_agreement_id'=>$s_agreement_id,'item_id'=>$id));
			}

			$uicols		= $this->bo->uicols;
			$list		= $this->list_content($list,$uicols,$edit_item=true);
			$content	= $list['content'];
			$table_header=$list['table_header'];

			$lookup_type='view';

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> false,
						'lookup_type'	=> $lookup_type,
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('s_agreement'),
						'entity_data'	=> $values['p']
						));


			for ($i=0;$i<count($values['attributes']);$i++)
			{
				if($values['attributes'][$i]['history']==1)
				{
					$link_history_data = array
					(
						'menuaction'	=> 'property.uis_agreement.attrib_history',
						's_agreement_id'	=> $s_agreement_id,
						'attrib_id'	=> $values['attributes'][$i]['id'],
						'item_id'	=> $id,
						'edit'		=> false,
						'role'		=>'detail'
					);

					$values['attributes'][$i]['link_history']=$GLOBALS['phpgw']->link('/index.php',$link_history_data);
				}
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('ID'),
				'value_id'						=> $values['id'],
				'value_s_agreement_id'			=> $s_agreement_id,
				'lang_category'					=> lang('category'),
				'lang_cancel'					=> lang('cancel'),
				'lang_cancel_statustext'		=> lang('Leave the service agreement untouched and return back to the list'),

				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'attributes_view'				=> $values['attributes'],

				'lang_agreement'				=> lang('Agreement'),
				'agreement_name'				=> $s_agreement['name'],
				'table_add'						=> $table_add,
				'values'						=> $content,
				'table_header'					=> $table_header,
				'location_data'					=> $location_data,

				'lang_cost'						=> lang('cost'),
				'lang_cost_statustext'			=> lang('cost'),
				'value_cost'					=> $values['cost'],
				'set_column'					=> $set_column,

				'lang_history'					=> lang('history'),
				'lang_history_help'				=> lang('history of this attribute'),
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . lang('view item') . ' ' . $s_agreement['name'];

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_item' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{
			$s_agreement_id	= phpgw::get_var('s_agreement_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			//json code delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($s_agreement_id);
				return "s_agreement_id ".$s_agreement_id." ".lang("has been deleted");
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uis_agreement.index',
				'role'		=> $this->role
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.delete', 's_agreement_id'=> $s_agreement_id, 'role'=> $this->role)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'				=> lang('no')
			);

			$appname		= lang('service agreement');
			$function_msg		= lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function view()
		{
			$s_agreement_id	= phpgw::get_var('id', 'int');
			$config		= CreateObject('phpgwapi.config','property');

			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement', 'attributes_view', 'files'));


			$s_agreement = $this->bo->read_single(array('s_agreement_id'=>$s_agreement_id));


			if ($s_agreement_id)
			{
				$this->cat_id = ($s_agreement['cat_id']?$s_agreement['cat_id']:$this->cat_id);
				$this->member_id = ($s_agreement['member_of']?$s_agreement['member_of']:$this->member_id);
				$list = $this->bo->read_details($s_agreement_id);
				$total_records = count($list);

				$uicols		= $this->bo->uicols;
				$list		= $this->list_content($list,$uicols,$edit_item=false,$view_only='view');
				$content	= $list['content'];
				$table_header=$list['table_header'];
			}

			$link_data = array
			(
				'menuaction'		=> 'property.uis_agreement.index',
				's_agreement_id'	=> $s_agreement_id,
			);

			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'	=> $s_agreement['vendor_id'],
						'vendor_name'	=> $s_agreement['vendor_name'],
						'type'		=> 'view'));

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $s_agreement['b_account_id'],
						'b_account_name'	=> $s_agreement['b_account_name'],
						'type'			=> 'view'));


			$alarm_data=$this->bocommon->initiate_ui_alarm(array(
						'acl_location'	=>$this->acl_location,
						'alarm_type'	=> 's_agreement',
						'type'		=> 'view',
						'text'		=> 'Email notification',
						'times'		=> $times,
						'id'		=> $s_agreement_id,
						'method'	=> $method,
						'data'		=> $data,
						'account_id'	=> $account_id
						));


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true,link_data => array()));

			$link_file_data = array
			(
				'menuaction'	=> 'property.uis_agreement.view_file',
				'id'		=>$s_agreement_id
			);


			$config->read();
			$link_to_files = $config->config_data['files_url'];

			$j	= count($s_agreement['files']);
			for ($i=0;$i<$j;$i++)
			{
				$s_agreement['files'][$i]['file_name']=urlencode($s_agreement['files'][$i]['name']);
			}


			$data = array
			(
				'lang_total_records'				=> lang('Total'),
				'total_records'					=> $total_records,
				'alarm_data'					=> $alarm_data,
				'lang_alarm'					=> lang('Alarm'),
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'					=> $link_to_files,
				'files'						=> $s_agreement['files'],
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_view_file_statustext'			=> lang('click to view file'),

				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'					=> lang('ID'),
				'value_s_agreement_id'				=> $s_agreement_id,
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('done'),
				'lang_apply'					=> lang('apply'),
				'value_cat'					=> $s_agreement['cat'],
				'lang_cancel_statustext'			=> lang('return back to the list'),
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' =>'s_agreement','order'=>'descr')),

				'lang_member_of'				=> lang('member of'),
				'member_of_name'				=> 'member_id',
				'member_of_list'				=> $member_of_data['cat_list'],

				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'attributes_view'				=> $s_agreement['attributes'],
				'dateformat'					=> $dateformat,

				'lang_start_date'				=> lang('start date'),
				'value_start_date'				=> $s_agreement['start_date'],

				'lang_end_date'					=> lang('end date'),
				'value_end_date'				=> $s_agreement['end_date'],

				'lang_termination_date'				=> lang('termination date'),
				'value_termination_date'			=> $s_agreement['termination_date'],

				'vendor_data'					=> $vendor_data,
				'b_account_data'				=> $b_account_data,
				'lang_name'					=> lang('name'),
				'value_name'					=> $s_agreement['name'],
				'lang_descr'					=> lang('descr'),
				'value_descr'					=> $s_agreement['descr'],
				'table_add'					=> $table_add,
				'values'					=> $content,
				'table_header'					=> $table_header,
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . lang('view');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function attrib_history()
		{			$GLOBALS['phpgw']->xslttpl->add_file(array('attrib_history','nextmatchs'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$s_agreement_id		= phpgw::get_var('s_agreement_id', 'int');
			$item_id	= phpgw::get_var('item_id', 'int');
			$attrib_id 	= phpgw::get_var('attrib_id', 'int');

			$data_lookup= array(
				'id'		=> $s_agreement_id,
				'item_id'	=> $item_id,
				'attrib_id' 	=> $attrib_id
				);

			$delete = phpgw::get_var('delete', 'bool');
			$edit = phpgw::get_var('edit', 'bool');

			if ($delete)
			{
				$data_lookup['history_id'] = phpgw::get_var('history_id', 'int');
				$this->bo->delete_history_item($data_lookup);
			}

			$values = $this->bo->read_attrib_history($data_lookup);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while (is_array($values) && list(,$entry) = each($values))
			{
				$link_delete_history_data = array
					(
						'menuaction'		=> 'property.uis_agreement.attrib_history',
						's_agreement_id'	=> $s_agreement_id,
						'item_id'			=> $item_id,
						'attrib_id'			=> $attrib_id,
						'history_id'		=> $entry['id'],
						'delete'			=> true,
						'edit'				=> true,
						'role'				=> $this->bo->role
					);

				if($edit)
				{
					$text_delete	= lang('delete');
					$link_delete	= $GLOBALS['phpgw']->link('/index.php',$link_delete_history_data);
				}

				$content[] = array
				(
					'id'						=> $entry['id'],
					'value'						=> $entry['new_value'],
					'user'						=> $entry['owner'],
					'time_created'				=> $GLOBALS['phpgw']->common->show_date($entry['datetime'],$dateformat),
					'link_delete'				=> $link_delete,
					'lang_delete_statustext'	=> lang('delete the item'),
					'text_delete'				=> $text_delete,
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
				'menuaction'	=> 'property.uis_agreement.attrib_history',
				's_agreement_id'=> $s_agreement_id,
				'item_id'		=> $item_id,
				'attrib_id'		=> $attrib_id,
				'edit'			=> $edit
			);

			$data = array
			(
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

			$attrib_data 	= $this->custom->get('property', '.s_agreement.detail', $attrib_id);
			$appname	= $attrib_data['input_text'];

			$function_msg	= lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('attrib_history' => $data));
		}
	}
