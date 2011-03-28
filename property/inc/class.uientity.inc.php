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
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	02110-1301	USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage entity
	 * @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

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
				'columns'		=> true,
				'download'		=> true,
				'view'	 		=> true,
				'edit'	 		=> true,
				'delete' 		=> true,
				'view_file'		=> true,
				'attrib_history'=> true,
				'attrib_help'	=> true,
				'print_pdf'		=> true,
				'index'		=> true,
				'addfiles'		=> true,
				'get_files'		=> true
			);

		function property_uientity()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('property.boentity',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->soadmin_entity		= & $this->bo->soadmin_entity;

			$this->entity_id			= $this->bo->entity_id;
			$this->cat_id				= $this->bo->cat_id;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->part_of_town_id			= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->location_code		= $this->bo->location_code;
			$this->category_dir			= $this->bo->category_dir;
			$GLOBALS['phpgw']->session->appsession('entity_id','property',$this->entity_id);
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->allrows				= $this->bo->allrows;
			$this->type					= $this->bo->type;
			$this->type_app				= $this->bo->type_app;
			$this->acl 					= & $GLOBALS['phpgw']->acl;

			$this->acl_location			= ".{$this->type}.$this->entity_id";
			if( $this->cat_id )
			{
				$this->acl_location		.= ".{$this->cat_id}";
			}
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->type_app[$this->type]);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->type_app[$this->type]);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->type_app[$this->type]);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "{$this->type_app[$this->type]}::entity_{$this->entity_id}";
			if($this->cat_id > 0)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$this->cat_id}";
			}
		}



		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'part_of_town_id'	=> $this->part_of_town_id,
					'district_id'		=> $this->district_id,
					'entity_id'			=> $this->entity_id,
					'status'			=> $this->status,
					'start_date'		=> $this->start_date,
					'end_date'			=> $this->end_date,
					'criteria_id'		=> $this->criteria_id
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$list = $this->bo->read(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'allrows'=>true,'start_date'=>$start_date,'end_date'=>$end_date, 'type' => $this->type));
			$uicols	= $this->bo->uicols;

			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}


		function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$id				= phpgw::get_var('id', 'int');
			$jasperfile		= phpgw::get_var('jasperfile', 'bool');

			$fileuploader	= CreateObject('property.fileuploader');


			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if(!$id)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$test = false;//true;
			if ($test)
			{
				foreach ($_FILES as $fieldName => $file)
				{
					move_uploaded_file($file['tmp_name'], "{$GLOBALS['phpgw_info']['server']['temp_dir']}/" . strip_tags(basename($file['name'])));
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$id));

			$loc1 = isset($values['location_data']['loc1']) && $values['location_data']['loc1'] ? $values['location_data']['loc1'] : 'dummy';
			if($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}

			$bofiles	= CreateObject('property.bofiles');

			$fileuploader->upload($bofiles, "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}");
		}

		function get_files()
		{
			$id 	= phpgw::get_var('id', 'int');

			if( !$this->acl_read)
			{
				return;
			}

			$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$id));

			$link_file_data = array
				(
					'menuaction'	=> 'property.uientity.view_file',
					'loc1'			=> $values['location_data']['loc1'],
					'id'			=> $id,
					'cat_id'		=> $this->cat_id,
					'entity_id'		=> $this->entity_id,
					'type'			=> $this->type
				);

			if(isset($values['files']) && is_array($values['files']))
			{
				$j	= count($values['files']);
				for ($i=0;$i<$j;$i++)
				{
					$values['files'][$i]['file_name']=urlencode($values['files'][$i]['name']);
				}
			}


			$content_files = array();
			foreach($values['files'] as $_entry )
			{
				$content_files[] = array
					(
						'file_name' => '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data).'&amp;file_name='.$_entry['name'].'" target="_blank" title="'.lang('click to view file').'">'.$_entry['name'].'</a>',
						'delete_file' => '<input type="checkbox" name="values[file_action][]" value="'.$_entry['name'].'" title="'.lang('Check to delete file').'">'
					);
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content_files))
				{
					return json_encode($content_files);
				}
				else
				{
					return "";
				}
			}
			return $content_files;
		}


		function columns()
		{
			//cramirez: necesary for windows.open . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');
			$receipt = array();

			if (isset($values['save']) && $values['save'] && $this->cat_id)
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add($this->type_app[$this->type],"entity_columns_" . $this->entity_id . '_' . $this->cat_id,$values['columns'],'user');
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
					'menuaction'	=> 'property.uientity.columns',
					'entity_id'		=> $this->entity_id,
					'cat_id'		=> $this->cat_id,
					'type'			=> $this->type
				);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'		=> $this->bo->column_list($values['columns'],$entity_id=$this->entity_id,$cat_id=$this->cat_id,$allrows=true),
					'function_msg'		=> $function_msg,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'		=> lang('columns'),
					'lang_none'		=> lang('None'),
					'lang_save'		=> lang('save'),
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

			$file_name	= urldecode(phpgw::get_var('file_name'));
			$loc1 		= phpgw::get_var('loc1', 'string', 'REQUEST', 'dummy');
			if($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}
			$id 		= phpgw::get_var('id', 'int');
			$jasper		= phpgw::get_var('jasper', 'bool');

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file("{$this->category_dir}/{$loc1}", '', $jasper);
		}

		function index()
		{
			//redirect. If selected the title of module.
			if($this->entity_id == 1 && !$this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=>1, 'cat_id'=> 1, 'type' => $this->type));
			}
			elseif($this->entity_id == 2 && !$this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=>2, 'cat_id'=> 1, 'type' => $this->type));
			}

			//redirect if no rights
			if(!$this->acl_read && $this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$start_date	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);
			$dry_run = false;
			$second_display = phpgw::get_var('second_display', 'bool');

			$this->save_sessiondata();

			//Preferencias sets
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters'] == 'yes')
			{
				$group_filters = 'select';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field_grouped'));
			}
			else
			{
				$group_filters = 'filter';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));
			}
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();
			$values_combo_box = array();

			// 	enters the first time
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['menu']						=	$this->bocommon->get_menu($this->type_app[$this->type]);

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uientity.index',
						'entity_id'        		=> $this->entity_id,
						'cat_id'            	=> $this->cat_id,
						'type'					=> $this->type,
						'district_id'			=> $this->district_id
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url']	=	"menuaction:'property.uientity.index',".
					"second_display:1,".
					"entity_id:'{$this->entity_id}',".
					"cat_id:'{$this->cat_id}',".
					"type:'{$this->type}',".
					"district_id:'{$this->district_id}'";

				// this array "$arr_filter_hide" indicate what filters are hidden or not
				$arr_filter_hide = array();

				////// ---- CATEGORY filter----------------------
/*				$values_combo_box[0]  = $this->bo->select_category_list($group_filters,$this->cat_id);
				if(count($values_combo_box[0]))
				{
					$default_value = array ('id'=>'','name'=> lang('no category'));
					array_unshift ($values_combo_box[0],$default_value);
					$arr_filter_hide[0] = 1;
				}
				else
				{
					$arr_filter_hide[0] = 1;
				}
 */
				//// ---- DISTRICT filter----------------------
				if($this->cat_id)
				{
					$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
					//this validation comes to previous versions
					if (isset($category['location_level']) && $category['location_level']>0)
					{
						$values_combo_box[1]	= $this->bocommon->select_district_list($group_filters,$this->district_id);
						if(count($values_combo_box[1]))
						{
							$default_value = array ('id'=>'','name'=>lang('no district'));
							array_unshift ($values_combo_box[1],$default_value);
							$arr_filter_hide[1] = 0;
						}
						else
						{
							$arr_filter_hide[1] = 1;
						}
					}
					else
					{
						$values_combo_box[1] = array();
						$arr_filter_hide[1] = 1;
					}
				}

				//// ---- USER filter----------------------
				$values_combo_box[2]  = $this->bocommon->get_user_list_right2($group_filters,4,$this->filter,$this->acl_location,array('all'),$default='all');

				if(count($values_combo_box[2]))
				{
					$default_value = array ('id'=>'','name'=>lang('no user'));
					array_unshift ($values_combo_box[2],$default_value);
					$arr_filter_hide[2] = 0;
				}
				else
				{
					$arr_filter_hide[2] = 1;
				}

				$values_combo_box[3]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'  => $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'property.uientity.index',
								'second_display'	=> $second_display,
								'entity_id'			=> $this->entity_id,
								'cat_id'			=> $this->cat_id,
								'type'				=> $this->type
							)),
							'fields'  => array
							(
								'field' => array
								(
							/*		array
									( //boton 	CATEGORY
													'id'   => 'btn_cat_id',
													'name' => 'cat_id',
													'value'=> lang('Category'),
													'type' => 'button',
													'style' => 'filter',
													'tab_index' => 1
										),*/
									array
									( //boton 	DISTINT
										'id'   => 'btn_district_id',
										'name' => 'district_id',
										'value'=> lang('District'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 2
									),
									array
									( //boton 	USER
										'id'   => 'btn_user_id',
										'name' => 'user_id',
										'value'=> lang('User'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 3
									),
									array
									( //boton 	search criteria
										'id' => 'btn_criteria_id',
										'name' => 'criteria_id',
										'value'	=> lang('search criteria'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 4
									),
									array
									(//for link "columns", next to Export button
										'type'=> 'link',
										'id'  => 'btn_columns',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction'	=> 'property.uientity.columns',
											'entity_id'		=> $this->entity_id,
											'cat_id'		=> $this->cat_id,
											'type'			=> $this->type
										))."','link','width=300,height=600,scrollbars=1')",
										'value' => lang('columns'),
										'tab_index' => 11
									),
									array
									(
										'type' => 'button',
										'id'   => 'btn_export',
										'value'=> lang('download'),
										'tab_index' => 10
									),
									array
									(
										'type' => 'button',
										'id'   => 'btn_new',
										'value'=> lang('add'),
										'tab_index' => 9
									),
									array
									( //boton	 SEARCH
										'id'   => 'btn_search',
										'name' => 'search',
										'value'=> lang('search'),
										'type' => 'button',
										'tab_index' => 8
									),
									array
									( // TEXT IMPUT
										'name' => 'query',
										'id'   => 'txt_query',
										'value'=> $this->query,
										'type' => 'text',
										'size' => 28,
										'onkeypress' => 'return pulsar(event)',
										'tab_index' => 7
									),
									array
									(//for link "None",
										'type'=> 'label_date'
									),
									array
									( //hidden end_date
										'type'	=> 'hidden',
										'id'	=> 'end_date',
										'name'	=> 'end_date',
										'value'	=> $end_date
									),
									array
									( //hidden start_date
										'type'	=> 'hidden',
										'id'	=> 'start_date',
										'name'	=> 'start_date',
										'value'	=> $start_date
									),
									array
									(//for link "Date search",
										'type'=> 'link',
										'id'  => 'btn_data_search',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' => 'property.uiproject.date_search'))."','link','width=350,height=250')",
											'value' => lang('Date search'),
											'tab_index' => 6
										)),
								'hidden_value' => array
								(
									array
									(
										'id'   => 'values_combo_box_0',
										'value'=> $this->bocommon->select2String($values_combo_box[0])
									),
									array
									(
										'id'	=> 'values_combo_box_1',
										'value' => $this->bocommon->select2String($values_combo_box[1])
									),
									array
									(
										'id' => 'values_combo_box_2',
										'value'	=> $this->bocommon->select2String($values_combo_box[2])
									),
									array
									(
										'id' => 'values_combo_box_3',
										'value'	=> $this->bocommon->select2String($values_combo_box[3])
									),
								)
							)));

				$custom	= createObject('phpgwapi.custom_fields');
				$attrib_data = $custom->find($this->type_app[$this->type],".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '','','',true, true);

				$button_def[] = "oMenuButton_0";
				$button_def[] = "oMenuButton_1";
				$button_def[] = "oMenuButton_2";
				$button_def[] = "oMenuButton_3";
				$code_inner[] = "{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'genericbutton',dependiente:[]}";
				$code_inner[] = "{order:1, var_URL:'district_id',name:'btn_district_id',style:'genericbutton',dependiente:[]}";
				$code_inner[] = "{order:2, var_URL:'filter',name:'btn_user_id',style:'genericbutton',dependiente:[]}";
				$code_inner[] = "{order:3, var_URL:'criteria_id',name:'btn_criteria_id',style:'genericbutton',dependiente:[]}";


				if($attrib_data)
				{
					$i = 4;
					foreach ( $attrib_data as $attrib )
					{
						if($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R')
						{
							$datatable['actions']['form'][0]['fields']['field'][] = array
							(
								'id' => "btn_{$attrib['column_name']}",
								'name' => $attrib['column_name'],
								'value'	=> $attrib['input_text'],
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => $i
							);

							$button_def[] = "oMenuButton_{$i}"; 
							$code_inner[] = "{order:{$i}, var_URL:'{$attrib['column_name']}',name:'btn_{$attrib['column_name']}',style:'genericbutton',dependiente:[]}";

							$values_combo_box[$i][]  = array
							(
								'id' 	=> '',
								'name'	=> $attrib['input_text'],
							);


							foreach($attrib['choice'] as $choice)
							{
								$values_combo_box[$i][]  = array
								(
									'id' 	=> $choice['id'],
									'name'	=> htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
								);
							}

							$datatable['actions']['form'][0]['fields']['hidden_value'][] = array
							(
								'id' 	=> "values_combo_box_{$i}",
								'value'	=> $this->bocommon->select2String($values_combo_box[$i])						
							);
							$i++;
						}
					}
				}

				$code = 'var ' . implode(',', $button_def)  . ";\n";
				$code .= 'var selectsButtons = [' . "\n" . implode(",\n",$code_inner) . "\n];";

				$GLOBALS['phpgw']->js->add_code('', $code);

				//	eliminates those empty filters
				$eliminate = 0;
				foreach( $arr_filter_hide as $key => $value )
				{
					if ($value)
					{
						//eliminates the respective entry in $datatable..['field']
						array_splice($datatable['actions']['form'][0]['fields']['field'],$eliminate, 1);
					}
					else
					{
						$eliminate++;
					}
				}

				// sets for initial ordering
				$this->sort = "ASC";
				$this->order = "num";
				$dry_run = true;
			}

			$entity_list = array();

			$entity_list = $this->bo->read(array('start_date'=>$start_date,'end_date'=>$end_date, 'dry_run' => $dry_run));

			$uicols = $this->bo->uicols;

			$content = array();
			$j=0;
			if (isset($entity_list) && is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($entity_entry['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $entity_entry[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $entity_entry['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $entity_entry[$uicols['name'][$i]];
								//$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $i;
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								//$datatable['rows']['row'][$j]['column'][$i]['lookup'] 		= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= isset($uicols['align'][$i])?$uicols['align'][$i]:'center';

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $entity_entry[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 		= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']			= $entity_entry[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	   = '_blank';
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']			= $entity_entry[$uicols['name'][$i]];
						}
						$datatable['rows']['row'][$j]['hidden'][$i]['value']				= $entity_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name']					= $uicols['name'][$i];
					}

					$j++;
				}
			}

			//indica que de la fila seleccionada escogera de la columna "id" el valor "id". Para agregarlo al URL
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
							'name'		=> 'location_code',
							'source'	=> 'location_code'
						),
						array
						(
							'name'		=> 'origin_id',
							'source'	=> 'id'
						),
						array
						(
							'name'		=> 'p_num',
							'source'	=> 'id'
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
							'menuaction'	=> 'property.uientity.view',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'			=> $parameters
					);
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'view',
						'text' 			=> lang('open view in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.view',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type,
							'target'		=> '_blank'
						)),
						'parameters'			=> $parameters
					);
			}
			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.edit',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'			=> $parameters
					);
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.edit',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type,
							'target'		=> '_blank'
						)),
						'parameters'			=> $parameters
					);
			}

			if(	$category['start_ticket'])
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('start ticket'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uitts.add',
							'p_entity_id'	=> $this->entity_id,
							'p_cat_id'		=> $this->cat_id,
							'type'			=> $this->type,
							'target'		=> '_blank',
							'bypass'		=> true,
							'origin'		=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",

						)),
						'parameters'			=> $parameters2
					);
			}

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location)));

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

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.delete',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'	=> $parameters
					);
			}
			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.edit',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						))
					);
			}

			unset($parameters);

			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			//Columns Order
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];;
					//$datatable['headers']['header'][$i]['formatter']		= $uicols['formatter'][$i];
					//according to stable bruch this columns is not SORTABLE'
					$denied = array('merknad');//$denied = array('merknad','account_lid');
					//if not include
					if(in_array ($uicols['name'][$i], $denied))
					{
						$datatable['headers']['header'][$i]['sortable']		= false;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
					else if(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH'))
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}

				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		 = false;
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
				$datatable['pagination']['records_returned']= count($entity_list);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

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


			//-BEGIN----------------------------- JSON CODE ------------------------------

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

			//---no exits depended filters
			$json['hidden']['dependent'][] = array ( );

			// checks right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-END------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}


			$appname = lang('entity');

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

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			if($this->entity_id && $this->cat_id)
			{
				$entity	   = $this->soadmin_entity->read_single($this->entity_id,false);
				$appname	  = $entity['name'];
				$category	 = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg = 'list ' . $category['name'];
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			}

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'entity.index', 'property' );

			//die(_debug_array($datatable));
		}

		function edit($mode = 'edit')
		{
			$id 	= phpgw::get_var('id', 'int');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array
					(
						'menuaction'	=> 'property.uientity.view', 'id'=> $id, 'entity_id'	=> $this->entity_id,
						'cat_id'		=> $this->cat_id,
						'type'			=> $this->type));
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

			//	$config		= CreateObject('phpgwapi.config','property');
			$bolocation	= CreateObject('property.bolocation');

			$values				= phpgw::get_var('values');
			$values_attribute	= phpgw::get_var('values_attribute');
			$bypass 			= phpgw::get_var('bypass', 'bool');
			$lookup_tenant 		= phpgw::get_var('lookup_tenant', 'bool');
			$tenant_id 			= phpgw::get_var('tenant_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('entity','attributes_form', 'files'));

			$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name']		= phpgw::get_var('vendor_name', 'string', 'POST');
			$values['date']				= phpgw::get_var('date');

			$receipt = array();

			if($_POST && !$bypass)
			{
				$insert_record 		= $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity	= $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location,$this->type_app[$this->type]);

				if(is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			elseif ($mode == 'edit')
			{
				$location_code 		= phpgw::get_var('location_code');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']			= phpgw::get_var('p_num');


				$origin		= phpgw::get_var('origin');
				$origin_id	= phpgw::get_var('origin_id', 'int');


				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $this->soadmin_entity->read_single_category($p_entity_id,$p_cat_id);
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

			if(isset($tenant_id) && $tenant_id)
			{
				$lookup_tenant=true;
			}

			if($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			}
			else
			{
				$cat_list = $this->bo->select_category_list('select', '', PHPGW_ACL_ADD);
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id,'type' => $this->type));
			}

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

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
						if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && ! ctype_digit($attribute['value']))
						{
							$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));						
						}
					}
				}

				if(isset($id) && $id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if(!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values,$values_attribute,$action,$this->entity_id,$this->cat_id);
					$id = $receipt['id'];
					$function_msg = lang('edit entity');
					//--------------files
					$loc1 = isset($values['location']['loc1']) && $values['location']['loc1'] ? $values['location']['loc1'] : 'dummy';
					if($this->type_app[$this->type] == 'catch')
					{
						$loc1 = 'dummy';
					}

					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/{$this->category_dir}/{$loc1}/{$id}/", $values);
					}

					if(isset($values['file_jasperaction']) && is_array($values['file_jasperaction']))
					{
						$values['file_action'] = $values['file_jasperaction'];
						$bofiles->delete_file("/{$this->category_dir}/{$loc1}/{$id}/", $values);
					}

					$files = array();
					if(isset($_FILES['file']['name']) && $_FILES['file']['name'])
					{
						$file_name = str_replace (' ','_',$_FILES['file']['name']);
						$to_file	= "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}/{$file_name}";

						if ($bofiles->vfs->file_exists(array
							(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$files[] = array
								(
									'from_file'	=> $_FILES['file']['tmp_name'],
									'to_file'	=> $to_file
								);
						}

						unset($to_file);
						unset($file_name);
					}

					if(isset($_FILES['jasperfile']['name']) && $_FILES['jasperfile']['name'])
					{
						$file_name = 'jasper::' . str_replace (' ','_',$_FILES['jasperfile']['name']);
						$to_file	= "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}/{$file_name}";

						if($bofiles->vfs->file_exists(array
							(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$files[] = array
								(
									'from_file'	=> $_FILES['jasperfile']['tmp_name'],
									'to_file'	=> $to_file
								);
						}

						unset($to_file);
						unset($file_name);
					}

					foreach ($files as $file)
					{
						$bofiles->create_document_dir("{$this->category_dir}/{$loc1}/{$id}");
						$bofiles->vfs->override_acl = 1;

						if(!$bofiles->vfs->cp (array (
							'from'	=> $file['from_file'],
							'to'	=> $file['to_file'],
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
					unset($loc1);
					unset($files);
					unset($file);					
					//-------------end files

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','entity_receipt_' . $this->entity_id . '_' . $this->cat_id,$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id,'type' => $this->type));
					}
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code,$values['extra']);
					}
					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id']);
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
					$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id),$values);
				}

			}

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			$lookup_type = $mode == 'edit' ? 'form' : 'view';

			$entity = $this->soadmin_entity->read_single($this->entity_id);

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

			$lookup_entity = array();
			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{	
				foreach ($entity['lookup_entity'] as $lookup_id)
				{
					$entity_lookup = $this->soadmin_entity->read_single($lookup_id);
					$lookup_entity[] = array
						(
							'id'		=> $lookup_id,
							'name'		=> $entity_lookup['name']
						);
				}
			}

			if(isset($category['lookup_tenant']) && $category['lookup_tenant'])
			{
				$lookup_tenant=true;
			}

			if($location_code)
			{
				$category['location_level']= count(explode('-',$location_code));
			}

			if( $this->cat_id && ( !isset($category['location_level']) || !$category['location_level']) )
			{
				$category['location_level']= -1;
			}

			$_no_link = false;
			if($lookup_entity && $category['location_link_level'])
			{
				$_no_link = (int)$category['location_link_level'] + 2;
			}
			if($entity['location_form'] )
			{
				$location_data=$bolocation->initiate_ui_location(array
					(
						'values'	=> $values['location_data'],
						'type_id'	=> (int)$category['location_level'],
						'no_link'	=> $_no_link, // disable lookup links for location type less than type_id
						'lookup_type'	=> $lookup_type,
						'tenant'	=> $lookup_tenant,
						'lookup_entity'	=> $lookup_entity,
						'entity_data'	=> isset($values['p'])?$values['p']:''
					));
			}

			if(isset($error_id) && $error_id)
			{
				unset($values['id']);
				unset($id);
			}

			$link_data = array
				(
					'menuaction'	=> "property.uientity.{$mode}",
					'id'			=> $id,
					'entity_id'		=> $this->entity_id,
					'cat_id'		=> $this->cat_id,
					'type'			=> $this->type
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			//		$config->read();
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
					'menuaction'		=> 'property.uiproject.edit',
					'bypass'			=> true,
					'location_code'		=> $values['location_code'],
					'p_num'				=> $id,
					'p_entity_id'		=> $this->entity_id,
					'p_cat_id'			=> $this->cat_id,
					'tenant_id'			=> $values['tenant_id'],
					'origin'			=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
					'origin_id'			=> $id
				);

			$add_to_project_link_data = array
			(
					'menuaction'		=> 'property.uiproject.index',
					'from'				=> 'workorder',
					'lookup'			=> true,
					'query'				=> isset($values['location_data']['loc1']) ? $values['location_data']['loc1'] : '',
			//		'p_num'				=> $id,
			//		'p_entity_id'		=> $this->entity_id,
			//		'p_cat_id'			=> $this->cat_id,
					'tenant_id'			=> $values['tenant_id'],
					'origin'			=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
					'origin_id'			=> $id
			);

			$ticket_link_data = array
				(
					'menuaction'		=> 'property.uitts.add',
					'bypass'			=> true,
					'location_code'		=> $values['location_code'],
					'p_num'				=> $id,
					'p_entity_id'		=> $this->entity_id,
					'p_cat_id'			=> $this->cat_id,
					'tenant_id'			=> $values['tenant_id'],
					'origin'			=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
					'origin_id'			=> $id
				);


			//_debug_array($values['origin']);

			//			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$table_apply[] = array
				(
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
				);

			$pdf_data = array
				(
					'menuaction'	=> 'property.uientity.print_pdf',
					'id'		=> $id,
					'entity_id'	=> $this->entity_id,
					'cat_id'	=> $this->cat_id,
					'type'		=> $this->type
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
								'menuaction'	=> 'property.uientity.attrib_history',
								'acl_location'	=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
								//		'entity_id'	=> $this->entity_id,
								//		'cat_id'	=> $this->cat_id,
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true,
								'type'		=> $this->type
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				phpgwapi_yui::tabview_setup('entity_edit_tabview');
				if($category['location_level'] > 0)
				{
					$tabs['location']	= array('label' => lang('location'), 'link' => '#location');
					$active_tab = 'location';
				}

				$location = ".{$this->type}.{$this->entity_id}.{$this->cat_id}";
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$_tab_name = str_replace(' ', '_', $group['name']);
						$active_tab = $active_tab ? $active_tab : $_tab_name;
						$tabs[$_tab_name] = array('label' => $group['name'], 'link' => '#' . $_tab_name);
						$group['link'] = $_tab_name;
						$attributes[] = $group;
						unset($_tab_name);
					}
				}
				unset($attributes_groups);

				if($category['fileupload'] || (isset($values['files']) &&  $values['files']))
				{
					$tabs['files']	= array('label' => lang('files'), 'link' => '#files');
				}
/*
				if($category['jasperupload'])
				{
					$tabs['jasper']	= array('label' => lang('jasper reports'), 'link' => '#jasper');
				}
 */
			}

			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location));
			$_integration_config = isset($custom_config->config_data['integration']) ? $custom_config->config_data['integration'] : array();

			// required settings:
/*
			integration_tab
			integration_url
			integration_parametres
			integration_action
			integration_action_view
			integration_action_edit
			integration_auth_key_name
			integration_auth_url
			integration_auth_hash_name
			integration_auth_hash_value
			integration_location_data
 */


			$integration = '';
			if(isset($_integration_config['tab']) && $values['id'])
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

				$integration	= true;
				$_integration_config['url']		= htmlspecialchars_decode($_integration_config['url']);
				$_integration_config['parametres']	= htmlspecialchars_decode($_integration_config['parametres']);

				parse_str($_integration_config['parametres'], $output);

				foreach ($output as $_dummy => $_substitute)
				{
					$_keys[] = $_substitute;

					$__value = false;
					if(!$__value = urlencode($values[trim($_substitute, '_')]))
					{
						foreach ($values['attributes'] as $_attribute)
						{
							if(trim($_substitute, '_') == $_attribute['name'])
							{
								$__value = urlencode($_attribute['value']);
								break;
							}
						}
					}

					if($__value)
					{
						$_values[] = $__value;
					}
				}

				//_debug_array($_integration_config['parametres']);
				//_debug_array($_values);
				unset($output);
				unset($__value);
				$_sep = '?';
				if (stripos($_integration_config['url'],'?'))
				{
					$_sep = '&';
				}
				$_param = str_replace($_keys, $_values, $_integration_config['parametres']);
				unset($_keys);
				unset($_values);
				//				$integration_src = phpgw::safe_redirect("{$_integration_config['url']}{$_sep}{$_param}");
				$integration_src = "{$_integration_config['url']}{$_sep}{$_param}";
				if($_integration_config['action'])
				{
					$_sep = '?';
					if (stripos($integration_src,'?'))
					{
						$_sep = '&';
					}
					$integration_src .= "{$_sep}{$_integration_config['action']}=" . $_integration_config["action_{$mode}"];
				}

				$arguments = array($_integration_config['auth_key_name'] => $response);

				if(isset($_integration_config['location_data']) && $_integration_config['location_data'])
				{
					$_integration_config['location_data']	= htmlspecialchars_decode($_integration_config['location_data']);
					parse_str($_integration_config['location_data'], $output);
					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;
						$_values[] = urlencode($values['location_data'][trim($_substitute, '_')]);
					}
					$integration_src .= '&' . str_replace($_keys, $_values, $_integration_config['location_data']);
				}

				$integration_src .= "&{$_integration_config['auth_key_name']}={$response}";
				//_debug_array($values);
				//_debug_array($integration_src);die();
				$tabs['integration']	= array('label' => $_integration_config['tab'], 'link' => '#integration', 'function' => "document.getElementById('integration_content').src = '{$integration_src}';");

			}
			unset($values['attributes']);
			$link_file_data = array
				(
					'menuaction'	=> 'property.uientity.view_file',
					'loc1'			=> $values['location_data']['loc1'],
					'id'			=> $id,
					'cat_id'		=> $this->cat_id,
					'entity_id'		=> $this->entity_id,
					'type'			=> $this->type
				);

			$content_files = array();
			for($z=0; $z<count($values['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data).'&amp;file_name='.$values['files'][$z]['name'].'" target="_blank" title="'.lang('click to view file').'">'.$values['files'][$z]['name'].'</a>';			
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$values['files'][$z]['name'].'" title="'.lang('Check to delete file').'">';
			}									

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')))
				);

/*
			$link_file_data['jasper']		= true;
			$content_jasperfiles = array();
			for($z=0; $z<count($values['jasperfiles']); $z++)
			{
				$link_file_data['file_name']	= $values['jasperfiles'][$z]['name'];
				$content_jasperfiles[$z]['file_name'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data).'" target="_blank" title="'.lang('click to view file').'">'.$values['jasperfiles'][$z]['name'].'</a>';			
				$content_jasperfiles[$z]['delete_file'] = '<input type="checkbox" name="values[file_jasperaction][]" value="'.$values['jasperfiles'][$z]['name'].'" title="'.lang('Check to delete file').'">';
			}									

			$datavalues[1] = array
			(
					'name'					=> "1",
					'values' 				=> json_encode($content_jasperfiles),
					'total_records'			=> count($content_jasperfiles),
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
 */			
			if ($id)
			{
				$get_docs = false;
				$check_doc = $this->bocommon->get_lookup_entity('document');
				foreach ($check_doc as $_check)
				{
					if ($_check['id'] == $this->entity_id)
					{
						$get_docs = true;
						break;
					}
				}

				if($get_docs)
				{
					$document = CreateObject('property.sodocument');
					$documents = $document->get_files_at_location(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'num'=>$values['num']));

					if($documents)
					{
						$tabs['document']	= array('label' => lang('document'), 'link' => '#document');
						$documents = json_encode($documents);				
					}
				}

				$related = $this->bo->read_entity_to_link(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$values['num']));
				$related_link = array();

				if(isset($related['related']))
				{
					$tabs['related']	= array('label' => lang('related'), 'link' => '#related');

					foreach($related as $related_key => $related_data)
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
			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat','property');


			$data = array
				(
					'lang_dateformat' 				=> strtolower($dateformat),
					'dateformat_validate'			=> $dateformat_validate,
					'onKeyUp'						=> $onKeyUp,
					'onBlur'						=> $onBlur,

					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,	
					'related_link'					=> $related_link,			
					'link_pdf'						=> $GLOBALS['phpgw']->link('/index.php',$pdf_data),
					'start_project'					=> $category['start_project'],
					'lang_start_project'			=> lang('start project'),
					'project_link'					=> $GLOBALS['phpgw']->link('/index.php',$project_link_data),
					'add_to_project_link'			=> $GLOBALS['phpgw']->link('/index.php',$add_to_project_link_data),
					'start_ticket'					=> $category['start_ticket'],
					'lang_start_ticket'				=> lang('start ticket'),
					'ticket_link'					=> $GLOBALS['phpgw']->link('/index.php',$ticket_link_data),
					'fileupload'					=> $category['fileupload'],
			//		'jasperupload'					=> $category['jasperupload'],
					'link_view_file'				=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
			//		'link_to_files'					=> $link_to_files,
					'files'							=> isset($values['files'])?$values['files']:'',
			//		'jasperfiles'					=> isset($values['jasperfiles'])?$values['jasperfiles']:'',
					'multiple_uploader'				=> $id ? true : '',
					'fileuploader_action'			=> "{menuaction:'property.fileuploader.add',"
															."upload_target:'property.uientity.addfiles',"
															."id:'{$id}',"
															."_entity_id:'{$this->entity_id}',"
															."_cat_id:'{$this->cat_id}',"
															."_type:'{$this->type}'}",
					'value_origin'					=> isset($values['origin'])?$values['origin']:'',
					'value_origin_type'				=> isset($origin)?$origin:'',
					'value_origin_id'				=> isset($origin_id)?$origin_id:'',

					'value_target'					=> isset($values['target'])?$values['target']:'',
					'lang_target'					=> lang('target'),
					'lang_no_cat'					=> lang('no category'),
					'lang_cat_statustext'			=> lang('Select the category. To do not use a category select NO CATEGORY'),
					'select_name'					=> 'cat_id',
					'cat_list'						=> isset($cat_list)?$cat_list:'',
					'location_code'					=> isset($location_code)?$location_code:'',
					'lookup_tenant'					=> $lookup_tenant,

					'lang_entity'					=> lang('entity'),
					'entity_name'					=> $entity['name'],
					'lang_category'					=> lang('category'),
					'category_name'					=> $category['name'],
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'attributes_group'				=> $attributes,
					'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
					'lang_none'						=> lang('None'),
					'location_data'					=> $location_data,
					'lookup_type'					=> $lookup_type,
					'mode'							=> $mode,
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uientity.index', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'type' => $this->type)),
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
					'table_apply' 					=> $table_apply,
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
					'tabs'							=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
					'integration'					=> $integration,
					'value_integration_src'			=> $integration_src,
					'base_java_url'					=>	"{menuaction:'property.uientity.get_files',".
														"id:'{$id}',".
														"entity_id:'{$this->entity_id}',".
														"cat_id:'{$this->cat_id}',".
														"type:'{$this->type}'}",
					'documents'						=> $documents
				);

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/examples/treeview/assets/css/folders/tree.css');
			phpgwapi_yui::load_widget('treeview');
			$appname	= $entity['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'entity.edit', 'property' );


		}

		function attrib_help()
		{
			$t =& $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id 	= phpgw::get_var('cat_id', 'int');
			$attrib_id 	= phpgw::get_var('attrib_id', 'int');

			$data_lookup= array
				(
					'entity_id'	=> $entity_id,
					'cat_id' 	=> $cat_id,
					'attrib_id' 	=> $attrib_id
				);

			$entity_category = $this->soadmin_entity->read_single_category($entity_id,$cat_id);

			$help_msg	= $this->bo->read_attrib_help($data_lookup);

			$custom			= createObject('phpgwapi.custom_fields');
			$attrib_data 	= $custom->get($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $attrib_id);

			$attrib_name	= $attrib_data['input_text'];
			$function_msg	= lang('Help');


			$t->set_file('help','help.tpl');
			$t->set_var('title',lang('Help') . '<br>' . $entity_category['descr'] .	' - "' . $attrib_name . '"');
			$t->set_var('help_msg',$help_msg );
			$t->set_var('lang_close',lang('close') );

			$GLOBALS['phpgw']->common->phpgw_header();
			$t->pfp('out','help');
		}

		function delete()
		{
			$id = phpgw::get_var('id', 'int');

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
			}


			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}


			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction'	=> 'property.uientity.index',
					'entity_id'	=> $this->entity_id,
					'cat_id'	=> $this->cat_id,
					'type'		=> $this->type
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uientity.delete', 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'id'=> $id, 'type' => $this->type)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname		= lang('entity');
			$function_msg		= lang('delete entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
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
				$this->bo->delete_history_item($data_lookup);
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
					'lang_user'		=> lang('user'),
					'lang_time_created'	=> lang('time created'),
					'lang_delete'		=> lang('delete')
				);

			$link_data = array
				(
					'menuaction'	=> 'property.uientity.attrib_history',
					'acl_location'	=> $acl_location,
					'id'			=> $id,
					'detail_id' 	=> $data_lookup['detail_id'],
			//		'entity_id'		=> $entity_id,
			//		'cat_id'		=> $cat_id,
			//		'entity_id'		=> $entity_id,
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
	/*					array
						(
							'name'  => 'entity_id',
							'source' => $data_lookup['entity_id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'cat_id',
							'source' => $data_lookup['cat_id'],
							'ready'  => 1
						),
	 */
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
						'action'  	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uientity.attrib_history' )),
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
			$data = array
				(
					'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'base_java_url'		=> json_encode(array(menuaction => "property.uientity.attrib_history")),
					'datatable'			=> $datavalues,
					'myColumnDefs'		=> $myColumnDefs,

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
			$attrib_data 	= $custom->get($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $attrib_id);
			$appname		= $attrib_data['input_text'];
			$function_msg	= lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			//_debug_array($GLOBALS['phpgw_info']['flags']['app_header']);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('attrib_history' => $data));
		}


		function print_pdf()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$bolocation			= CreateObject('property.bolocation');

			$id	= phpgw::get_var('id', 'int');

			if ($id)
			{
				$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'id'=>$id, 'view' => true));
			}
			else
			{
				if($this->cat_id)
				{
					$values	= $this->bo->read_single(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id));
				}
				else
				{
					echo 'Nothing';
					return;
				}
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$entity = $this->soadmin_entity->read_single($this->entity_id);
			$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);

			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{	for ($i=0;$i<count($entity['lookup_entity']);$i++)
				{
					if(isset($values['p'][$entity['lookup_entity'][$i]]) && $values['p'][$entity['lookup_entity'][$i]])
					{
						$lookup_entity[$i]['id'] = $entity['lookup_entity'][$i];
						$entity_lookup = $this->soadmin_entity->read_single($entity['lookup_entity'][$i]);
						$lookup_entity[$i]['name'] = $entity_lookup['name'];
					}
				}
			}

			$location_data=$bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> $category['location_level'],
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'lookup_type'	=> 'view',
					'tenant'	=> $category['lookup_tenant'],
					'lookup_entity'	=> isset($lookup_entity)?$lookup_entity:'', // Needed ?
					'entity_data'	=> isset($values['p'])?$values['p']:'' // Needed ?
				)
			);

			//_debug_array($values);
			$pdf	= CreateObject('phpgwapi.pdf');

			$date = $GLOBALS['phpgw']->common->show_date('',$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$entry_date = $GLOBALS['phpgw']->common->show_date($values['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(90,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,760,578,760);

			$pdf->addText(50,790,10,$GLOBALS['phpgw']->accounts->id2name($values['user_id']) . ': ' . $entry_date);
			$pdf->addText(50,770,16,$entity['name'] . '::' . $category['name'] . ' #' . $id);
			$pdf->addText(300,28,10,$date);

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');
			$pdf->ezStartPageNumbers(500,28,10,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$pdf->ezTable($content_heading,'','',
				array('xPos'=>220,'xOrientation'=>'right','width'=>300,0,'shaded'=>0,'fontSize' => 10,'showLines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>0,'showHeadings'=>0
				,'cols'=>array('text'=>array('justification'=>'left','width'=>100),
					'value'=>array('justification'=>'left','width'=>200))
				)
			);

			$table_header = array(
				'name'=>array('justification'=>'left','width'=>110),
				'sep'=>array('justification'=>'center','width'=>15),
				'value'=>array('justification'=>'left','width'=>300)
			);

			if(is_array($location_data['location']))
			{
				foreach($location_data['location'] as $entry)
				{
					$value = '';
					if($entry['input_type'] != 'hidden')
					{
						$value = $entry['value'];
					}
					if(isset($entry['extra']) && is_array($entry['extra']))
					{
						foreach($entry['extra'] as $extra)
						{
							if($extra['input_type'] != 'hidden')
							{
								$value .= ' ' . $extra['value'];
							}
						}
					}

					$content[] = array
						(
							'name'		=> $entry['name'],
							'sep'			=> '-',
							'value'			=> trim($value)
						);
				}
			}

			if(is_array($values['attributes']))
			{
				foreach($values['attributes'] as $entry)
				{
					if(isset($entry['choice']) && is_array($entry['choice']))
					{
						$values = array();
						foreach($entry['choice'] as $choice)
						{
							if(isset($choice['checked']) && $choice['checked'])
							{
								$values[] = "[*{$choice['value']}*]";
							}
							else
							{
								$values[] = $choice['value'];
							}
						}
						$value = implode(' , ',$values);
					}
					else
					{
						$value = $entry['value'];
					}

					$content[] = array
						(
							'name'		=> $entry['input_text'],
							'sep'			=> '-',
							'value'			=> $value
						);

					if ($entry['datatype'] == 'T' || $entry['datatype'] == 'V')
					{
						$content[] = array
							(
								'name'	=> '|',
								'sep'	=> '',
								'value'	=> ''
							);
						$content[] = array
							(
								'name'	=> '|',
								'sep'	=> '',
								'value'	=> ''
							);

					}
				}
				$pdf->ezTable($content,'','',
					array('xPos'=>50,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 10,'showLines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>2,'showHeadings'=>0
					,'cols'=>$table_header
				)
			);
			}

			$document = $pdf->ezOutput();
			$pdf->print_pdf($document,$entity['name'] . '_' . str_replace(' ','_',$GLOBALS['phpgw']->accounts->id2name($this->account)));
		}
	}
