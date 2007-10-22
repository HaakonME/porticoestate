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
	* @subpackage entity
 	* @version $Id: class.uientity.inc.php,v 1.52 2007/10/13 20:29:15 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uientity
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
			'columns'	=> True,
			'excel'  	=> True,
			'index'  	=> True,
			'view'   	=> True,
			'edit'   	=> True,
			'delete' 	=> True,
			'view_file'	=> True,
			'attrib_history'=> True,
			'attrib_help'	=> True
		);

		function property_uientity()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject($this->currentapp.'.boentity',True);
			$this->bocommon				= CreateObject($this->currentapp.'.bocommon');
			$this->menu				= CreateObject($this->currentapp.'.menu');

			$this->boadmin_entity			= CreateObject($this->currentapp.'.boadmin_entity',True);

			$this->entity_id			= $this->bo->entity_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->acl 				= CreateObject('phpgwapi.acl');
			if(!$this->cat_id)
			{
				$this->acl_location		= '.entity.' . $this->entity_id;
			}
			else
			{
				$this->acl_location		= '.entity.' . $this->entity_id . '.' . $this->cat_id;
			}
			$this->acl_read 			= $this->acl->check($this->acl_location,1);
			$this->acl_add 				= $this->acl->check($this->acl_location,2);
			$this->acl_edit 			= $this->acl->check($this->acl_location,4);
			$this->acl_delete 			= $this->acl->check($this->acl_location,8);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->part_of_town_id			= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->fakebase 			= $this->bo->fakebase;
			$this->category_name			= $this->bo->category_name;
			$this->menu->sub			='entity_'.$this->entity_id;
			$GLOBALS['phpgw']->session->appsession('entity_id',$this->currentapp,$this->entity_id);
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->allrows				= $this->bo->allrows;
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
				'entity_id'		=> $this->entity_id,
				'status'		=> $this->status,
				'start_date'		=> $this->start_date,
				'end_date'		=> $this->end_date,
				'allrows'		=> $this->allrows,
			);
			$this->bo->save_sessiondata($data);
		}

		function excel()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = True;
			$GLOBALS['phpgw_info']['flags'][nofooter] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$list = $this->bo->read(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'allrows'=>true,'start_date'=>$start_date,'end_date'=>$end_date));
			$uicols	= $this->bo->uicols;

 			$filename= $GLOBALS['phpgw_info']['user']['account_lid'].'.xls';
			$count_uicols_name=count($uicols['name']);

			$workbook	= CreateObject($this->currentapp.'.excel',"-");
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename,'application/vnd.ms-excel');

			$worksheet1 =& $workbook->add_worksheet('First One');

			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$m=0;
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$content[$j][$m]	= $entry[$uicols['name'][$k]];
							$worksheet1->write_string(0, $m, $uicols['descr'][$k]);
							$m++;
						}
					}
					$j++;
				}

				foreach($content as $row)
				{
					$line++;
					for ($i=0; $i<count($row); $i++)
					{
						$worksheet1->write($line,$i,$row[$i]);
					}
				}
			}
			$workbook->close();
		}


		function columns()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			
			$values 		= get_var('values',array('POST','GET'));

			if (isset($values['save']) && $values['save'] && $this->cat_id)
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->add($this->currentapp,"entity_columns_" . $this->entity_id . '_' . $this->cat_id,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			if(!$this->cat_id)
			{
				$receipt['error'][] = array('msg' => lang('Choose a category'));
			}
			$function_msg	= lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.columns',
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'		=> $this->bo->column_list($values['columns'],$entity_id=$this->entity_id,$cat_id=$this->cat_id,$allrows=True),
				'function_msg'		=> $function_msg,
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'		=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_file()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$file_name	= urldecode(get_var('file_name',array('POST','GET')));
			$loc1 		= get_var('loc1',array('POST','GET'));
			$id 		= get_var('id',array('POST','GET'));

			$file = $this->fakebase. SEP . $this->category_name . SEP . $loc1 . SEP . $id . SEP . $file_name;

//echo 'file: ' . $file . '<br>';
			if($this->bo->vfs->file_exists(array(
				'string' => $file,
				'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$ls_array = $this->bo->vfs->ls (array (
						'string'	=>  $file,
						'relatives' => Array(RELATIVE_NONE),
						'checksubdirs'	=> False,
						'nofiles'	=> True
					)
				);

				$this->bo->vfs->override_acl = 1;

				$document= $this->bo->vfs->read(array(
					'string' => $file,
					'relatives' => Array(RELATIVE_NONE)));

				$this->bo->vfs->override_acl = 0;

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($ls_array[0]['name'],$ls_array[0]['mime_type'],$ls_array[0]['size']);

				echo $document;
			}
		}

		function index()
		{
			if(!$this->acl_read && $this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('entity',
									'menu',
									'nextmatchs'));

			$links = $this->menu->links();

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','entity_receipt_' . $this->entity_id . '_' . $this->cat_id);
			$GLOBALS['phpgw']->session->appsession('session_data','entity_receipt_' . $this->entity_id . '_' . $this->cat_id,'');

			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$entity_list = $this->bo->read(array('start_date'=>$start_date,'end_date'=>$end_date));

			$uicols	= $this->bo->uicols;

			$j=0;

			$content = array();
			if (isset($entity_list) AND is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($entity_entry['query_location'][$uicols['name'][$i]]) && $entity_entry['query_location'][$uicols['name'][$i]])
							{
								$content[$j]['row'][$i]['statustext']		= lang('search');
								$content[$j]['row'][$i]['text']			= $entity_entry[$uicols['name'][$i]];
								$content[$j]['row'][$i]['link']			= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'query'=> $entity_entry['query_location'][$uicols['name'][$i]]));
							}
							else
							{
								$content[$j]['row'][$i]['value'] 		= $entity_entry[$uicols['name'][$i]];
								$content[$j]['row'][$i]['name'] 		= $uicols['name'][$i];
								if($uicols['input_type'][$i]=='link' && $entity_entry[$uicols['name'][$i]])
								{
									$content[$j]['row'][$i]['text']		= lang('link');
									$content[$j]['row'][$i]['link']		= $entity_entry[$uicols['name'][$i]];
									$content[$j]['row'][$i]['target']	= '_blank';
								
								}
							}
						}
					}

					if($this->acl_read)
					{
						$content[$j]['row'][$i]['statustext']				= lang('view the entity');
						$content[$j]['row'][$i]['text']					= lang('view');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.view', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'id'=> $entity_entry['id']));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']				= lang('edit the entity');
						$content[$j]['row'][$i]['text']					= lang('edit');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.edit', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'id'=> $entity_entry['id']));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']				= lang('delete the entity');
						$content[$j]['row'][$i]['text']					= lang('delete');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.delete', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'id'=> $entity_entry['id']));
					}

					$j++;
				}
			}

			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 		= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if(!isset($uicols['datatype'][$i]) || ($uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH'))
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
							(
								'sort'	=> $this->sort,
								'var'	=> $uicols['name'][$i],
								'order'	=> $this->order,
								'extra'	=> array('menuaction'	=> $this->currentapp.'.uientity.index',
		//									'type_id'	=> $type_id,
											'query'		=> $this->query,
											'lookup'	=> isset($lookup)?$lookup:'',
											'district_id'	=> $this->district_id,
											'entity_id'	=> $this->entity_id,
											'cat_id'	=> $this->cat_id,
											'start_date'	=> $start_date,
											'end_date'	=> $end_date)
							));
					}
				}
			}

			if($this->acl_read)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('view');
				$i++;
			}
			if($this->acl_edit)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('edit');
				$i++;
			}
			if($this->acl_delete)
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']			= lang('delete');
				$i++;
			}


			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a entity'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.edit', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id))
				);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'entity_id'	=> $this->entity_id,
				'district_id'	=> $this->district_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'start_date'	=> $start_date,
				'end_date'	=> $end_date
			);

			$link_excel = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.excel',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id,
				'district_id'	=> $this->district_id,
				'status_id'	=> $this->status,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'start_date'	=> $start_date,
				'end_date'	=> $end_date,
				'start'		=> $this->start
			);

			$link_columns = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.columns',
				'entity_id'	=>$this->entity_id,
				'cat_id'	=>$this->cat_id
			);

			$link_date_search	= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiproject.date_search'));

			if(isset($GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['group_filters']) && $GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['group_filters'])
			{
				$group_filters = 'select';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field_grouped'));
			}
			else
			{
				$group_filters = 'filter';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));
			}

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			if($this->entity_id)
			{
				$entity 	= $this->boadmin_entity->read_single($this->entity_id,false);
				$appname	= $entity['name'];
			}

			$district_list ='';

			if($this->cat_id)
			{
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg	= 'list ' . $category['name'];
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
				if (isset($category['location_level']) && $category['location_level']>0)
				{
					$district_list	= $this->bocommon->select_district_list($group_filters,$this->district_id);
				}
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'group_filters'				=> isset($GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['group_filters'])?$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['group_filters']:'',
				'lang_excel'				=> 'excel',
				'link_excel'				=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'			=> lang('Download table to MS Excel'),
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'lang_columns'				=> lang('columns'),
				'link_columns'				=> $GLOBALS['phpgw']->link('/index.php',$link_columns),
				'lang_columns_help'			=> lang('Choose columns'),

				'start_date'				=> $start_date,
				'end_date'					=> $end_date,
				'lang_none'					=> lang('None'),
				'lang_date_search'			=> lang('Date search'),
				'lang_date_search_help'		=> lang('Narrow the search by dates'),
				'link_date_search'			=> $link_date_search,
				'lang_date_search'			=> lang('Date search'),

				'links'						=> $links,
				'allow_allrows'				=> true,
				'allrows'					=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($entity_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),

				'district_list'				=> $district_list,
				'lang_no_district'			=> lang('no district'),
				'lang_district_statustext'	=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'		=> 'district_id',
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_no_cat'				=> lang('no category'),
				'lang_cat_statustext'		=> lang('Select the category. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'cat_id',
				'cat_list'					=> $this->bo->select_category_list($group_filters,$this->cat_id),

				'lang_status_statustext'	=> lang('Select the status. To do not use a status select NO STATUS'),
				'status_name'				=> 'status',
				'lang_no_status'			=> lang('No status'),
				'status_list'				=> $this->bo->select_status_list($group_filters,$this->status),

				'lang_user_statustext'		=> lang('Select the user. To do not use a category select NO USER'),
				'select_user_name'			=> 'filter',
				'lang_no_user'				=> lang('No user'),

				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);

			if(!$this->entity_id || !$this->cat_id)
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				$receipt['error'][]=array('msg'=>lang('Please select type'));
				$msgbox_data = $this->bocommon->msgbox_data($receipt);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname;
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('empty' => $data));
			}
			else
			{
				$data['user_list']	= $this->bocommon->get_user_list_right2($group_filters,4,$this->filter,$this->acl_location,array('all'),$default='all');
				$GLOBALS['phpgw']->js->set_onload('document.search.query.focus();');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
				$this->save_sessiondata();
			}
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

		//	$config		= CreateObject('phpgwapi.config',$this->currentapp);
			$bolocation	= CreateObject($this->currentapp.'.bolocation');

			$id 			= get_var('id',array('POST','GET'));
			$values			= get_var('values',array('POST','GET'));
			$values_attribute	= get_var('values_attribute',array('POST','GET'));
			$bypass 		= get_var('bypass',array('POST','GET'));
			$lookup_tenant 		= get_var('lookup_tenant',array('POST','GET'));
			$tenant_id 		= get_var('tenant_id',array('POST','GET'));

			$GLOBALS['phpgw']->xslttpl->add_file(array('entity','attributes_form'));

			$values['vendor_id']		= get_var('vendor_id',array('POST'));
			$values['vendor_name']		= get_var('vendor_name',array('POST'));
			$values['date']			= get_var('date',array('POST'));
			$ticket_id			= get_var('ticket_id',array('POST','GET'));
			$request_id 			= get_var('request_id',array('POST','GET'));
			$receipt = array();

			if($_POST && !$bypass)
			{
				$insert_record 		= $GLOBALS['phpgw']->session->appsession('insert_record',$this->currentapp);
				$insert_record_entity	= $GLOBALS['phpgw']->session->appsession('insert_record_entity',$this->currentapp);

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			else
			{
				$location_code 		= get_var('location_code',array('POST','GET'));
				$values['descr']	= get_var('descr',array('POST','GET'));
				$p_entity_id		= get_var('p_entity_id',array('POST','GET'));
				$p_cat_id		= get_var('p_cat_id',array('POST','GET'));
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= get_var('p_num',array('POST','GET'));


				$origin		= get_var('origin',array('POST','GET'));
				$origin_id	= get_var('origin_id',array('POST','GET'));


				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $this->boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
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

			if(isset($origin) && $origin)
			{
				unset($values['origin']);
				unset($values['origin_id']);
				$values['origin'][0]['type']= $origin;
				$values['origin'][0]['link']=$this->bocommon->get_origin_link($origin);
				$values['origin'][0]['data'][]= array(
					'id'=> $origin_id,
					'type'=> $origin
					);
			}

			if(isset($tenant_id) && $tenant_id)
			{
				$lookup_tenant=True;
			}

			if($this->cat_id)
			{
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			}
			else
			{
				$cat_list = $this->bo->select_category_list('select',$this->cat_id);
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id));
			}

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if(!$values['location'] && isset($category['location_level']) && $category['location_level'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
					$error_id=true;
				}

				if(!$this->cat_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select entity type !'));
					$error_id=true;
				}

				if(isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute )
					{
						if($attribute['allow_null'] != 'True' && !$attribute['value'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}
					}
				}
				
				if(isset($id) && $id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$values['id']=$this->bo->generate_id(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id));
				}

				$values['file_name']=str_replace (' ','_',$_FILES['file']['name']);
				$to_file = $this->fakebase. SEP . $this->category_name . SEP . $values['location']['loc1'] . SEP . $values['id'] . SEP . $values['file_name'];

				if((!isset($values['document_name_orig']) || !$values['document_name_orig']) && $this->bo->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][]=array('msg'=>lang('This file already exists !'));
				}

				if(!isset($receipt['error']))
				{
					$receipt	= $this->bo->create_home_dir($receipt);
					$receipt = $this->bo->save($values,$values_attribute,$action,$this->entity_id,$this->cat_id);

					$id = $values['id'];
					$function_msg = lang('edit entity');

					if($values['file_name'])
					{
						$this->bo->create_document_dir($values['location']['loc1'], $values['id']);
						$this->bo->vfs->override_acl = 1;

						if(!$this->bo->vfs->cp (array (
							'from'	=> $_FILES['file']['tmp_name'],
							'to'	=> $to_file,
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$this->bo->vfs->override_acl = 0;
					}
					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','entity_receipt_' . $this->entity_id . '_' . $this->cat_id,$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id));
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
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=$_POST['entity_cat_name_'.$values['extra']['p_entity_id']];
					}
				}
			}

			if ($id)
			{
				$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$id));
			}
			else
			{
				if($this->cat_id)
				{
					$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id));
				}

			}

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			$lookup_type='form';

			$entity = $this->boadmin_entity->read_single($this->entity_id,false);

			if ($id)
			{
				$function_msg	= lang('edit') . ' ' . $category['name'];
			}
			else
			{
				$function_msg	= lang('add') . ' ' . $category['name'];
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{	for ($i=0;$i<count($entity['lookup_entity']);$i++)
				{
					$lookup_entity[$i]['id'] = $entity['lookup_entity'][$i];
					$entity_lookup = $this->boadmin_entity->read_single($entity['lookup_entity'][$i],false);
					$lookup_entity[$i]['name'] = $entity_lookup['name'];
				}
			}

			if(isset($category['lookup_tenant']) && $category['lookup_tenant'])
			{
				$lookup_tenant=True;
			}

			if($bypass && $location_code)
			{
				$category['location_level']= count(explode('-',$location_code));
			}
			
			if(!$category['location_level'])
			{
				$category['location_level']= -1;
			}

			if($entity['location_form'] && $category['location_level'] > 0 )
			{
				$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> $category['location_level'],
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'lookup_type'	=> $lookup_type,
						'tenant'	=> $lookup_tenant,
						'lookup_entity'	=> isset($lookup_entity)?$lookup_entity:'',
						'entity_data'	=> isset($values['p'])?$values['p']:''
						));
			}

/*			if($category['lookup_vendor'])
			{
				$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
						'vendor_id'		=> $values['vendor_id'],
						'vendor_name'	=> $values['vendor_name']));
			}
*/

			$attributes_header[] 	= array(
					'lang_name'	=> lang('Name'),
					'lang_descr'	=> lang('Description'),
					'lang_datatype'	=> lang('Datatype'),
					'lang_value'	=> lang('Value')
				);

			if(isset($error_id) && $error_id)
			{
				unset($values['id']);
				unset($id);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.edit',
				'id'		=> $id,
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
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

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.view_file',
				'loc1'		=> $values['location_data']['loc1'],
				'id'		=> $id,
				'cat_id'	=> $this->cat_id,
				'entity_id'	=> $this->entity_id
			);

	//		$config->read_repository();
	//		$link_to_files = $config->config_data['files_url'];

			if(isset($values['files']) && is_array($values['files']))
			{
				$j	= count($values['files']);
				for ($i=0;$i<$j;$i++)
				{
					$values['files'][$i]['file_name']=urlencode($values['files'][$i]['name']);
				}
			}

			$project_link_data = array
			(
				'menuaction'		=> $this->currentapp.'.uiproject.edit',
				'bypass'			=> true,
				'location_code'		=> $values['location_code'],
				'p_num'				=> $values['p_num'],
				'p_entity_id'		=> $values['p_entity_id'],
				'p_cat_id'			=> $values['p_cat_id'],
				'tenant_id'			=> $values['tenant_id'],
				'origin'			=> 'entity_' . $this->entity_id . '_' . $this->cat_id,
				'origin_id'			=> $id
			);

			$ticket_link_data = array
			(
				'menuaction'		=> $this->currentapp.'.uitts.add',
				'bypass'			=> true,
				'location_code'		=> $values['location_code'],
				'p_num'				=> $values['p_num'],
				'p_entity_id'		=> $values['p_entity_id'],
				'p_cat_id'			=> $values['p_cat_id'],
				'tenant_id'			=> $values['tenant_id'],
				'origin'			=> 'entity_' . $this->entity_id . '_' . $this->cat_id,
				'origin_id'			=> $id
			);


//_debug_array($values['origin']);
			for ($i=0;$i<count($values['origin']);$i++)
			{
				$values['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['origin'][$i]['link']);
				if(substr($values['origin'][$i]['type'],0,6)=='entity')
				{
					$type		= explode("_",$values['origin'][$i]['type']);
					$entity_id	= $type[1];
					$cat_id		= $type[2];

					if(!is_object($boadmin_entity))
					{
						$boadmin_entity	= CreateObject($this->currentapp.'.boadmin_entity');
					}
					$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
					$values['origin'][$i]['descr'] = $entity_category['name'];
				}
				else
				{
					$values['origin'][$i]['descr']= lang($values['origin'][$i]['type']);
				}
			}

			if(isset($values['destination']) && is_array($values['destination']))
			{
				for ($i=0;$i<count($values['destination']);$i++)
				{
					$values['destination'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['destination'][$i]['link']);
					if(substr($values['destination'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$values['destination'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject($this->currentapp.'.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$values['destination'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$values['destination'][$i]['descr']= lang($values['destination'][$i]['type']);
					}
				}
			}

			for ($i=0;$i<count($values['attributes']);$i++)
			{
				if($values['attributes'][$i]['history']==1)
				{
					$link_history_data = array
					(
						'menuaction'	=> $this->currentapp.'.uientity.attrib_history',
						'entity_id'	=> $this->entity_id,
						'cat_id'	=> $this->cat_id,
						'attrib_id'	=> $values['attributes'][$i]['attrib_id'],
						'id'		=> $id,
						'edit'		=> True
					);

					$values['attributes'][$i]['link_history']=$GLOBALS['phpgw']->link('/index.php',$link_history_data);
				}
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);
			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat',$this->currentapp);
			
			$table_apply[] = array
			(
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
			);

			$data = array
			(
				'start_project'					=> $category['start_project'],
				'lang_start_project'			=> lang('start project'),
				'project_link'					=> $GLOBALS['phpgw']->link('/index.php',$project_link_data),
				'start_ticket'					=> $category['start_ticket'],
				'lang_start_ticket'			=> lang('start ticket'),
				'ticket_link'					=> $GLOBALS['phpgw']->link('/index.php',$ticket_link_data),
				'fileupload'					=> $category['fileupload'],
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
		//		'link_to_files'					=> $link_to_files,
				'files'							=> isset($values['files'])?$values['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_delete_file'				=> lang('Delete file'),
				'lang_view_file_statustext'		=> lang('Klick to view file'),
				'lang_delete_file_statustext'	=> lang('Check to delete file'),
				'lang_upload_file'				=> lang('Upload file'),
				'lang_file_statustext'			=> lang('Select file to upload'),

				'value_origin'					=> $values['origin'],
				'value_origin_type'				=> isset($origin)?$origin:'',
				'value_origin_id'				=> isset($origin_id)?$origin_id:'',

				'value_destination'				=> isset($values['destination'])?$values['destination']:'',
				'lang_destination'				=> lang('destination'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> isset($cat_list)?$cat_list:'',
				'location_code'					=> isset($location_code)?$location_code:'',
				'lookup_tenant'					=> $lookup_tenant,

				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'] . ' - ' . $category['descr'],
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'						=> $onKeyUp,
				'onBlur'						=> $onBlur,
				'lang_attributes'				=> lang('Attributes'),
				'attributes_header'				=> $attributes_header,
				'attributes_values'				=> $values['attributes'],
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'dateformat'					=> $dateformat,
				'lang_none'						=> lang('None'),
	//			'vendor_data'					=> isset($vendor_data)?$vendor_data:'',
				'location_data'					=> $location_data,
				'lookup_type'					=> $lookup_type,
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id)),
				'lang_id'						=> lang('ID'),
				'value_id'						=> $values['id'],
				'value_num'						=> $values['num'],
				'error_flag'					=> isset($error_id)?$error_id:'',
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the entity'),
				'lang_history'					=> lang('history'),
				'lang_history_help'				=> lang('history of this attribute'),

				'lang_history_date_statustext'	=> lang('Enter the date for this reading'),
				'lang_date'						=> lang('date'),
				'help_url'						=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.attrib_help', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id)),
				'lang_help'						=> lang('help'),
				'table_apply' 					=> $table_apply,				
			);

			$appname	= $entity['name'];
//_debug_array($attributes_values);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function attrib_help()
		{
			$t =& $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;

			$entity_id	= get_var('entity_id',array('POST','GET'));
			$cat_id 	= get_var('cat_id',array('POST','GET'));
			$attrib_id 	= get_var('attrib_id',array('POST','GET'));

			$data_lookup= array(
				'entity_id'	=> $entity_id,
				'cat_id' 	=> $cat_id,
				'attrib_id' 	=> $attrib_id
				);
			
			$boadmin_entity	= CreateObject($this->currentapp.'.boadmin_entity');

			$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);

			$help_msg	= $this->bo->read_attrib_help($data_lookup);

			$attrib_data 	= $this->boadmin_entity->read_single_attrib($entity_id,$cat_id,$attrib_id);
			$attrib_name	= $attrib_data['input_text'];
			$function_msg	= lang('Help');


			$t->set_file('help','help.tpl');
			$t->set_var('title',lang('Help') . '<br>' . $entity_category['descr'] .  ' - "' . $attrib_name . '"');
			$t->set_var('help_msg',$help_msg );
			$t->set_var('lang_close',lang('close') );
											
			$GLOBALS['phpgw']->common->phpgw_header();
			$t->pfp('out','help');
		}
		
		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$id = get_var('id',array('POST','GET'));
			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.index',
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
			);

			if (get_var('confirm',array('POST')))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.delete', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'id'=> $id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname		= lang('entity');
			$function_msg		= lang('delete entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

		//	$config		= CreateObject('phpgwapi.config',$this->currentapp);
			$bolocation			= CreateObject($this->currentapp.'.bolocation');

			$id	= get_var('id',array('POST','GET'));

			$GLOBALS['phpgw']->xslttpl->add_file(array('entity','attributes_view'));


			if ($id)
			{
				$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$id));
			}

			$lookup_type='view';

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$entity = $this->boadmin_entity->read_single($this->entity_id,false);
			$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);

			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{	for ($i=0;$i<count($entity['lookup_entity']);$i++)
				{
					if(isset($values['p'][$entity['lookup_entity'][$i]]) && $values['p'][$entity['lookup_entity'][$i]])
					{
						$lookup_entity[$i]['id'] = $entity['lookup_entity'][$i];
						$entity_lookup = $this->boadmin_entity->read_single($entity['lookup_entity'][$i],false);
						$lookup_entity[$i]['name'] = $entity_lookup['name'];
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> count(explode('-',$values['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'lookup_type'	=> $lookup_type,
						'tenant'	=> $category['lookup_tenant'],
						'lookup_entity'	=> isset($lookup_entity)?$lookup_entity:'', // Needed ?
						'entity_data'	=> isset($values['p'])?$values['p']:'' // Needed ?
						));

			$appname		= $entity['name'];
			$function_msg	= lang('view') . ' ' . $category['name'];

			$attributes_values=$values['attributes'];

			$attributes_header[] 	= array(
					'lang_name'	=> lang('Name'),
					'lang_descr'	=> lang('Description'),
					'lang_datatype'	=> lang('Datatype'),
					'lang_value'	=> lang('Value')
				);


			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.edit',
				'id'		=> $id,
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$link_file_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.view_file',
				'loc1'		=> $values['location_data']['loc1'],
				'id'		=> $id,
				'cat_id'	=> $this->cat_id,
				'entity_id'	=> $this->entity_id
			);

		//	$config->read_repository();
		//	$link_to_files = $config->config_data['files_url'];

			if(isset($values['files']) && is_array($values['files']))
			{
				$j	= count($values['files']);
				for ($i=0;$i<$j;$i++)
				{
					$values['files'][$i]['file_name']=urlencode($values['files'][$i]['name']);
				}
			}

			if(is_array($values['origin']))
			{
				for ($i=0;$i<count($values['origin']);$i++)
				{
					$values['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['origin'][$i]['link']);
					if(substr($values['origin'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$values['origin'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject($this->currentapp.'.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$values['origin'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$values['origin'][$i]['descr']= lang($values['origin'][$i]['type']);
					}
				}
			}

			if(isset($values['destination']) && is_array($values['destination']))
			{
				for ($i=0;$i<count($values['destination']);$i++)
				{
					$values['destination'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['destination'][$i]['link']);
					if(substr($values['destination'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$values['destination'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject($this->currentapp.'.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$values['destination'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$values['destination'][$i]['descr']= lang($values['destination'][$i]['type']);
					}
				}
			}

			for ($i=0;$i<count($attributes_values);$i++)
			{
				if($attributes_values[$i]['history']==1)
				{
					$link_history_data = array
					(
						'menuaction'	=> $this->currentapp.'.uientity.attrib_history',
						'entity_id'	=> $this->entity_id,
						'cat_id'	=> $this->cat_id,
						'attrib_id'	=> $values['attributes'][$i]['attrib_id'],
						'id'		=> $id
					);

					$attributes_values[$i]['link_history']=$GLOBALS['phpgw']->link('/index.php',$link_history_data);
				}
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);

			$data = array
			(
				'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
		//		'link_to_files'					=> $link_to_files,
				'files'							=> isset($values['files'])?$values['files']:'',
				'lang_files'					=> lang('files'),
				'lang_filename'					=> lang('Filename'),
				'lang_view_file_statustext'			=> lang('Klick to view file'),

				'value_origin'					=> $values['origin'],
				'value_origin_type'				=> isset($origin)?$origin:'',
				'value_origin_id'				=> isset($origin_id)?$origin_id:'',
				'lang_destination'				=> lang('destination'),
				'value_destination'				=> isset($values['destination'])?$values['destination']:'',
				
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],
				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'lang_attributes'				=> lang('Attributes'),
				'attributes_view'				=> $attributes_values,
				'dateformat'					=> $dateformat,

	//			'vendor_data'					=> $vendor_data,
				'location_data'					=> $location_data,
				'lookup_type'					=> $lookup_type,
				'edit_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id)),
				'lang_category'					=> lang('category'),
				'lang_edit'					=> lang('Edit'),
				'lang_done'					=> lang('done'),
				'lang_id'					=> lang('ID'),
				'value_id'					=> $values['id'],
				'value_num'					=> $values['num'],

				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Edit the entity'),
				'status_list'					=> $this->bo->select_status_list('select',$values['status']),

				'lang_history'					=> lang('history'),
				'lang_history_help'				=> lang('history of this attribute'),
				'lang_history_date_statustext'	=> lang('Enter the date for this reading'),

				);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function attrib_history()
		{			$GLOBALS['phpgw']->xslttpl->add_file(array('attrib_history','nextmatchs'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;

			$id		= get_var('id',array('POST','GET'));
			$entity_id	= get_var('entity_id',array('POST','GET'));
			$cat_id 	= get_var('cat_id',array('POST','GET'));
			$attrib_id 	= get_var('attrib_id',array('POST','GET'));

			$data_lookup= array(
				'id'		=> $id,
				'entity_id'	=> $entity_id,
				'cat_id' 	=> $cat_id,
				'attrib_id' 	=> $attrib_id
				);

			$delete = get_var('delete',array('POST','GET'));
			$edit = get_var('edit',array('POST','GET'));

			if ($delete)
			{
				$data_lookup['history_id'] = get_var('history_id',array('POST','GET'));
				$this->bo->delete_history_item($data_lookup);
			}

			$values = $this->bo->read_attrib_history($data_lookup);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while (is_array($values) && list(,$entry) = each($values))
			{
				$link_delete_history_data = array
					(
						'menuaction'	=> $this->currentapp.'.uientity.attrib_history',
						'entity_id'	=> $data_lookup['entity_id'],
						'cat_id'	=> $data_lookup['cat_id'],
						'id'		=> $data_lookup['id'],
						'attrib_id'	=> $data_lookup['attrib_id'],
						'history_id'	=> $entry['id'],
						'delete'	=> true,
						'edit'		=> true
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
				'lang_user'		=> lang('user'),
				'lang_time_created'	=> lang('time created'),
				'lang_delete'		=> lang('delete')
			);

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uientity.attrib_history',
				'id'			=> $id,
				'entity_id'		=> $entity_id,
				'cat_id'		=> $cat_id,
				'entity_id'		=> $entity_id,
				'edit'			=> $edit
			);

			$data = array
			(
				'allow_allrows'		=> false,
				'start_record'		=> $this->start,
				'record_limit'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'		=> count($values),
				'all_records'		=> $this->bo->total_records,
				'link_url'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'		=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'values' 		=> $content,
				'table_header'		=> $table_header,
			);
//_debug_array($data);
			$attrib_data 	= $this->boadmin_entity->read_single_attrib($entity_id,$cat_id,$attrib_id);
			$appname	= $attrib_data['input_text'];
			$function_msg	= lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;//_debug_array($GLOBALS['phpgw_info']['flags']['app_header']);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('attrib_history' => $data));
		}
	}
?>
