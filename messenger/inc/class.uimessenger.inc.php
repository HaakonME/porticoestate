<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	class uimessenger
	{
		var $bo;
		var $template;
		var $public_functions = array
		(
			'index'				=> true,
			'inbox'				=> true,
			'compose'			=> true,
			'compose_groups'	=> true,
			'compose_global'	=> true,
			'read_message'		=> true,
			'reply'				=> true,
			'forward'			=> true,
			'delete'			=> true
		);

		function __construct()
		{
			$this->template   = $GLOBALS['phpgw']->template;
			$this->bo         = CreateObject('messenger.bomessenger');
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
			if ( !$this->bo->is_connected() )
			{
				$this->_error_not_connected();
			}
		}

		function compose($errors = '')
		{
			if (!$GLOBALS['phpgw']->acl->check('.compose', PHPGW_ACL_ADD, 'messenger'))
			{
				$this->_no_access('compose');
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'messenger::compose';

			$message = isset($_POST['message']) ? $_POST['message'] : array('subject' => '', 'content' => '');

			$this->_display_headers();
			$this->_set_compose_read_blocks();

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->_set_common_langs();
			$this->template->set_var('header_message',lang('Compose message'));

			$users = $this->bo->get_available_users();
			foreach ( $users as $uid => $name )
			{
				$this->template->set_var(array
				(
					'uid'		=> $uid,
					'full_name'	=> $name
				));
				$this->template->parse('select_tos', 'select_to', true);
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.send_message') ) );
			//$this->template->set_var('value_to','<input name="message[to]" value="' . $message['to'] . '" size="30">');
			$this->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '" size="30">');
			$this->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function compose_groups()
		{
			if (!$GLOBALS['phpgw']->acl->check('.compose_groups', PHPGW_ACL_ADD, 'messenger'))
			{
				$this->_no_access('compose_groups');
			}

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'messenger::compose_groups';

			$values = phpgw::get_var('values');
			$values['account_groups'] = (array) phpgw::get_var('account_groups', 'int', 'POST');
			$receipt = array();

			if (isset($values['save']))
			{
				if(!$values['account_groups'])
				{
					$receipt['error'][]=array('msg'=>lang('Missing groups'));
				}

				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('repost'));
				}

				if(!isset($values['subject']) || !$values['subject'])
				{
					$receipt['error'][]=array('msg'=>lang('Missing subject'));
				}

				if(!isset($values['content']) || !$values['content'])
				{
					$receipt['error'][]=array('msg'=>lang('Missing content'));
				}

				if(isset($values['save']) && $values['account_groups'] && !$receipt['error'])
				{
					$receipt = $this->bo->send_to_groups($values);
				}
			}
			$group_list = array();

			$all_groups = $GLOBALS['phpgw']->accounts->get_list('groups');

			if(!$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$available_apps = $GLOBALS['phpgw_info']['apps'];
				$valid_groups = array();
				foreach($available_apps as $_app => $dummy)
				{
					if($GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, $_app))
					{
						$valid_groups	= array_merge($valid_groups,$GLOBALS['phpgw']->acl->get_ids_for_location('run', phpgwapi_acl::READ, $_app));
					}
				}

				$valid_groups = array_unique($valid_groups);
			}
			else
			{
				$valid_groups = array_keys($all_groups);
			}

			foreach ( $all_groups as $group )
			{
				$group_list[$group->id] = array
				(
					'account_id'	=> $group->id,
					'account_lid'	=> $group->__toString(),
					'i_am_admin'	=> in_array($group->id, $valid_groups),
					'selected'		=> in_array($group->id, $values['account_groups'])
				);
			}

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'messenger.uimessenger.compose_groups')),
				'group_list'	=> $group_list,
				'value_subject'	=> isset($values['subject']) ? $values['subject'] : '',
				'value_content'	=> isset($values['content']) ? $values['content'] : ''
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('messenger'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('compose_groups' => $data));

		}

		function compose_global($errors = '')
		{
			if (!$GLOBALS['phpgw']->acl->check('.compose_global', PHPGW_ACL_ADD, 'messenger'))
			{
				$this->_no_access('compose_global');
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'messenger::compose_global';

			global $message;

			$this->_display_headers();
			$this->_set_compose_read_blocks();

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->_set_common_langs();
			$this->template->set_var('header_message',lang('Compose global message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.send_global_message') ) );
			$this->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '">');
			$this->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function delete()
		{
			$messages = $_REQUEST['messages'];
			$this->bo->delete_message($messages);

			$this->inbox();
		}

		function index()
		{
//_debug_array($_REQUEST);
			$this->acl_location = '.scheduled_events';
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::scheduled_events";

			$values = phpgw::get_var('values');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));


			$receipt = array();
			if($values && $this->acl_edit)
			{
				$receipt = $this->bo->update_receipt($values);
			}
			$this->save_sessiondata();

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uievent.index',
					'location_id'	=> $this->location_id,
					'user_id'		=> $this->user_id
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uievent.index',"
	    												."location_id:'{$this->location_id}',"
	    												."user_id:'{$this->user_id}'";

				$values_combo_box = array();

				$values_combo_box[0]  = $this->bo->get_event_location();
				
				$default_value = array ('id'=> -1, 'name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list_right2('filter',2,$this->user_id,$this->acl_location);
				array_unshift ($values_combo_box[1],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine tasks')));
				$default_value = array('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
				(
					array
					(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'	=> 'property.uievent.index',
									'type'			=> $type,
									'type_id'		=> $type_id
								)
							),
					'fields'	=> array
					(
	                		'field' => array
	                		(
								array
								( //boton 	CATEGORY
									'id' => 'btn_location_id',
									'name' => 'location_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	USER
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array( // boton SAVE
									'id'	=> 'btn_save',
									//'name' => 'save',
									'value'	=> lang('save'),
									'tab_index' => 6,
									'type'	=> 'button'
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
									 'tab_index' => 5
								),
								array
								( //button     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 4
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 3
								),
								array
								( //place holder for selected events
									'type'	=> 'hidden',
									'id'	=> 'event',
									'value'	=> ''
								)
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
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								)
							)
						)
					)
				);				
				$dry_run = true;
			}

			$values = $this->bo->read($dry_run);
			$uicols = array();$this->bo->uicols;

			$uicols['name'][]		= 'schedule_time';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'location';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'location_item_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'attrib_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'id';
			$uicols['descr'][]		= lang('id');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'id';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'date';
			$uicols['descr'][]		= lang('date');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'date';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'descr';
			$uicols['descr'][]		= lang('Descr');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'exception';
			$uicols['descr'][]		= lang('exception');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'FormatterCenter';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'receipt_date';
			$uicols['descr'][]		= lang('receipt date');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'location_name';
			$uicols['descr'][]		= lang('location name');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'url';
			$uicols['descr'][]		= lang('url');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= 'link';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'select';
			$uicols['descr'][]		= lang('select');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'myFormatterCheck';
			$uicols['input_type'][]	= '';

			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($values as $entry)
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
					$datatable['rows']['row'][$j]['column'][$k]['value']			= $entry[$uicols['name'][$k]];
					if($uicols['format'][$k]=='link' &&  $entry[$uicols['name'][$k]])
					{
						$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
						$datatable['rows']['row'][$j]['column'][$k]['value']		= lang('link');
						$datatable['rows']['row'][$j]['column'][$k]['link']			= $entry[$uicols['name'][$k]];
						$datatable['rows']['row'][$j]['column'][$k]['target']	   = '_blank';
					}
				}
				$j++;
			}

			$datatable['rowactions']['action'] = array();

			$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'location',
						'source'	=> 'location'
					),
					array
					(
						'name'		=> 'attrib_id',
						'source'	=> 'attrib_id'
					),
					array
					(
						'name'		=> 'item_id',
						'source'	=> 'location_item_id'
					),
					array
					(
						'name'		=> 'id',
						'source'	=> 'id'
					)
				)
			);

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('edit serie'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'		=> 'property.uievent.edit',
											'type'				=> $type,
											'type_id'			=> $type_id,
											'target'			=> '_blank'
										)),
					'parameters'	=> $parameters
				);
			}

/*
			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('delete the actor'),
					'text'			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uievent.delete',
											'type'			=> $type,
											'type_id'		=> $type_id
										)),
					'parameters'	=> $parameters
				);
			}
*/
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'add',
					'statustext' 	=> lang('add'),
					'text'			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uievent.edit',
											'type'			=> $type,
											'type_id'		=> $type_id
										))
				);
			}

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				$datatable['headers']['header'][$i]['formatter'] 		= $uicols['formatter'][$i] ? $uicols['formatter'][$i] : '""';
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= $uicols['input_type'][$i]!='hidden';
				$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
				$datatable['headers']['header'][$i]['sort_field']   	= $uicols['sort_field'][$i];
				$datatable['headers']['header'][$i]['format'] 			= $uicols['format'][$i];
			}

			//path for property.js
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
				$datatable['pagination']['records_returned']= count($values);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			= lang('scheduled events');
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			= 'date'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'event.index', 'property' );
		}


		function inbox()
		{
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
			$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
			$total = $this->bo->total_messages();

			$extra_menuaction = '&menuaction=messenger.uimessenger.inbox';
			$extra_header_info['nextmatchs_left']  = $this->nextmatchs->left('/index.php',$start,$total,$extra_menuaction);
			$extra_header_info['nextmatchs_right'] = $this->nextmatchs->right('/index.php',$start,$total,$extra_menuaction);

			$this->_display_headers($extra_header_info);

			$this->template->set_file('_inbox','inbox.tpl');
			$this->template->set_block('_inbox', 'row', 'rows');
			$this->template->set_block('_inbox','list');
			$this->template->set_block('_inbox','row_empty');

			$this->_set_common_langs();
			$this->template->set_var('sort_date','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_date',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Date') . '</a>');
			$this->template->set_var('sort_subject','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_subject',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Subject') . '</a>');
			$this->template->set_var('sort_from','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_from',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('From') . '</a>');

			$params = array(
				'start' => $start,
				'order' => $order,
				'sort'  => $sort
			);
			$messages = $this->bo->read_inbox($params);

			if (! is_array($messages))
			{
				$this->template->set_var('lang_empty',lang('You have no messages'));
				$this->template->fp('rows','row_empty',True);
			}
			else
			{
				$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.delete') ) );
				$this->template->set_var('button_delete','<input type="image" src="' . PHPGW_IMAGES . '/delete.gif" name="delete" title="' . lang('Delete selected') . '" border="0">');
				$i = 0;
				foreach ( $messages as $message)
				{
					$status = $message['status'];
					if ($message['status'] == 'N' || $message['status'] == 'O')
					{
						$status = '&nbsp;';
					}
	
					$this->template->set_var(array
					(
						'row_class'		=> $i % 2 ? 'row_on' : 'row_off',
						'row_date'		=> $message['date'],
						'row_from'		=> $message['from'],
						'row_msg_id'	=> $message['id'],
						'row_status'	=> $status,
						'row_subject'	=> $GLOBALS['phpgw']->strip_html($message['subject']),
						'row_url'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.read_message', 'message_id' => $message['id']))
					));
					$this->template->parse('rows', 'row', true);
					++$i;
				}


			}

			$this->template->pfp('out','list');
		}

		function read_message()
		{
			$message_id = $_REQUEST['message_id'];
			$message = $this->bo->read_message($message_id);

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();

			$this->template->set_var('header_message',lang('Read message'));

			$this->template->set_var('value_from', $message['from']);
			$this->template->set_var('value_subject', $GLOBALS['phpgw']->strip_html($message['subject']));
			$this->template->set_var('value_date', $message['date']);
			$this->template->set_var('value_content', nl2br(wordwrap($GLOBALS['phpgw']->strip_html($message['content']), 80)));

			$this->template->set_var('link_delete','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.delete', 'messages[]' => $message['id']) )
					. '">' . lang('Delete') . '</a>');

			$this->template->set_var('link_reply','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.reply', 'message_id' => $message['id']) )
					. '">' . lang('Reply') . '</a>');

			$this->template->set_var('link_forward','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.forward', 'message_id' => $message['id']) )
					. '">' . lang('Forward') . '</a>');

			switch($message['status'])
			{
				case 'N': $this->template->set_var('value_status',lang('New'));		break;
				case 'R': $this->template->set_var('value_status',lang('Replied'));	break;
				case 'F': $this->template->set_var('value_status',lang('Forwarded'));	break;
			}

			if ( isset($message['global_message']) && $message['global_message'] )
			{
				$this->template->fp('read_buttons','form_read_buttons_for_global');
			}
			else
			{
				$this->template->fp('read_buttons','form_read_buttons');
			}

			$this->template->fp('date','form_date');
			$this->template->fp('from','form_from');
			$this->template->pfp('out','form');
		}

		function reply($errors = '', $message = '')
		{
			$message_id = $_REQUEST['message_id'];

			if(is_array($errors))
			{
				$errors  = $errors['errors'];
				$message = $errors['message'];
			}

			if (!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'RE');
			}

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();
			$this->template->set_block('_form','form_reply_to');

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('header_message',lang('Reply to a message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.reply', 'message_id' => $message['id']) ) );
			$this->template->set_var('value_to',"<input type= 'hidden' name='n_message[to]' value={$message['from']}>{$message['from_fullname']}");
			$this->template->set_var('value_subject','<input name="n_message[subject]" value="' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['subject'])) . '" size="30">');
			$this->template->set_var('value_content','<textarea name="n_message[content]" rows="20" wrap="hard" cols="76">' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['content'])) . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_reply_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function forward($errors = array(), $message = '')
		{
			$message_id = $_REQUEST['message_id'];

			if($errors)
			{
				$errors  = $errors['errors'];
//				$message = $errors['message'];
			}

			if (!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'FW');
			}

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();

			$users = $this->bo->get_available_users();
			foreach ( $users as $uid => $name )
			{
				$this->template->set_var(array
				(
					'uid'		=> $uid,
					'full_name'	=> $name
				));
				$this->template->parse('select_tos', 'select_to', true);
			}


			if ($errors)
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('header_message',lang('Forward a message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.forward', 'message_id' => $message['id']) ) );
			$this->template->set_var('value_to','<input name="message[to]" value="' . $message['from'] . '" size="30">');
			$this->template->set_var('value_subject','<input name="message[subject]" value="' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['subject'])) . '" size="30">');
			$this->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['content'])) . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function _display_headers($extras = '')
		{
			$this->template->set_file('_header','header.tpl');
			$this->template->set_block('_header','global_header');
			$this->template->set_var('lang_inbox','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox') ) . '">' . lang('Inbox') . '</a>');
			$this->template->set_var('lang_compose','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.compose') ) . '">' . lang('Compose') . '</a>');

			if ( isset($extras['nextmatchs_left']) && $extras['nextmatchs_left'] )
			{
				$this->template->set_var('nextmatchs_left',$extras['nextmatchs_left']);
			}

			if ( isset($extras['nextmatchs_right']) && $extras['nextmatchs_right'] )
			{
				$this->template->set_var('nextmatchs_right',$extras['nextmatchs_right']);
			}

			$this->template->fp('app_header','global_header');

			$GLOBALS['phpgw']->common->phpgw_header(true);
		}

		function _error_not_connected()
		{
			$this->_display_headers();
			die( lang('exiting with error!') . "<br />\n" . lang('Unable to connect to server, please contact your system administrator') );
		}
		
		function _set_common_langs()
		{
			$this->template->set_var('lang_to',lang('Send message to'));
			$this->template->set_var('lang_from',lang('Message from'));
			$this->template->set_var('lang_subject',lang('Subject'));
			$this->template->set_var('lang_content',lang('Message'));
			$this->template->set_var('lang_date',lang('Date'));
		}

		function _set_compose_read_blocks()
		{
			$this->template->set_file('_form','form.tpl');

			$this->template->set_block('_form','form');
			$this->template->set_block('_form','select_to', 'select_tos');
			$this->template->set_block('_form','form_to');
			$this->template->set_block('_form','form_date');
			$this->template->set_block('_form','form_from');
			$this->template->set_block('_form','form_buttons');
			$this->template->set_block('_form','form_read_buttons');
			$this->template->set_block('_form','form_read_buttons_for_global');
		}

		function _no_access($location)
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$log_args = array
			(
				'severity'	=> 'W',
				'text'		=> 'W-Permissions, Attempted to access %1',
				'p1'		=> "{$GLOBALS['phpgw_info']['flags']['currentapp']}::{$location}"
			);

			$GLOBALS['phpgw']->log->warn($log_args);

			$lang_denied = lang('Access not permitted');
			echo <<<HTML
			<div class="error">$lang_denied</div>
HTML;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}
