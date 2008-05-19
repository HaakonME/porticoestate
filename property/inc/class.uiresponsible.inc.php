<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.uiresponsible.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_uiresponsible
	{
		
		/**
		* @var ??? $start ???
		*/
		private $start = 0;
		
		/**
		* @var ??? $sort ???
		*/
		private $sort;

		/**
		* @var ??? $order ???
		*/
		private $order;

		/**
		* @var object $nextmatches paging handler
		*/
		private $nextmatches;

		/**
		* @var object $bo business logic
		*/
		private $bo;

		/**
		* @var object $acl reference to global access control list manager
		*/
		private $acl;

		/**
		* @var string $acl_location the access control location
		*/
		private $acl_location;

		/**
		* @var bool $acl_read does the current user have read access to the current location
		*/
		private $acl_read;

		/**
		* @var bool $acl_add does the current user have add access to the current location
		*/
		private $acl_add;

		/**
		* @var bool $acl_edit does the current user have edit access to the current location
		*/
		private $acl_edit;

		/**
		* @var bool $allrows display all rows of result set?
		*/
		private $allrows;

		/**
		* @var array $public_functions publicly available methods of the class
		*/
		public $public_functions = array
		(
			'index' 		=> true,
			'contact' 		=> true,
			'edit_type' 	=> true,
			'edit_contact' 	=> true,
			'no_access'		=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::responsible_matrix';
			$this->bo				= CreateObject('property.boresponsible',true);
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location 	= $this->bo->get_acl_location();
			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ);
			$this->acl_add 			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD);
			$this->acl_edit 		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT);
			$this->acl_delete 		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE);
			$this->bolocation			= CreateObject('preferences.boadmin_acl');
			$this->bolocation->acl_app 	= 'property';
			$this->location				= $this->bo->location;
		}

		private function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
		//		'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		/**
		* list available responsible types
		*
		* @return void
		*/

		public function index()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::responsible';

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs','search_field'));

			$values	= phpgw::get_var('values', 'string', 'POST');

			if($values)
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}
				$this->bo->responsible($values);
			}
			
			$responsible_info = $this->bo->read();

			$lang_select_responsible_text		= '';
			$text_select					= '';

			if(isset($responsible_info) && is_array($responsible_info))
			{
				foreach ( $responsible_info as $entry )
				{
					if ( $this->acl_edit)
					{
						$lang_select_responsible_text		= lang('select responsible') . ': ' . $responsible;
					}

					$content[] = array
					(
						'user'							=> $entry['user'],
						'supervisor'					=> $entry['parent'],
						'location'						=> $entry['location'],
						'action_type'					=> $entry['action_type'],
						'lang_select_responsible_text'	=> $lang_select_responsible_text,
					);
				}
			}

			$table_header[] = array
			(
				'sort_location'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'location',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.index',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location
					)
				)),
				'lang_user'			=> lang('user'),
				'lang_location'		=> lang('location'),
				'lang_action'		=> lang('action'),
				'lang_supervisor'	=> lang('supervisor'),
				'lang_select'		=> (isset($this->acl_edit)?lang('select'):''),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
		//		'appname'		=> $appname,
				'location'		=> $this->location,

			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add type'),
			);

			$table_add_action = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_type',
				'location'		=> $this->location
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'allow_allrows'							=> true,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'num_records'							=> ($responsible_info?count($responsible_info):0),
				'all_records'							=> ($responsible_info?count($responsible_info):0),
				'select_action'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header_type'						=> $table_header,
				'table_add'								=> $table_add,
				'add_action'							=> $GLOBALS['phpgw']->link('/index.php', $table_add_action),
				'values_type'							=> (isset($content)?$content:''),
				'lang_no_location'						=> lang('No location'),
				'lang_location_statustext'				=> lang('Select submodule'),
				'select_name_location'					=> 'location',
				'location_list'							=> $this->bolocation->select_location('filter',$this->location,False),
			);

			$function_msg= lang('list available responsible types');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_type' => $data));
			$this->save_sessiondata();
		}

		/**
		* Add or Edit available responsible types
		*
		* @return void
		*/

		public function edit_type()
		{
			if(!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$id		= phpgw::get_var('id', 'int');
			$values	= phpgw::get_var('values', 'string', 'POST');


			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));

			if (isset($values) && is_array($values))
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if(!$values['cat_id'] || $values['cat_id'] == 'none')
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category!'));
					}
					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($id)
					{
						$values['id']=$id;
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save($values,$values_attribute);
						$id = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','responsible_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uiresponsible.index'));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uiresponsible.index'));
				}
			}

			$values = $this->bo->read_single($id);


			if ($id)
			{
				$function_msg = lang('edit responsible type');
			}
			else
			{
				$function_msg = lang('add responsible type');
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_type',
				'id'			=> $id,
				'location'		=> $this->location
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'value_entry_date'				=> isset($values['entry_date']) ? $values['entry_date'] : '',
				'value_name'					=> isset($values['name']) ? $values['name'] : '',
				'value_descr'					=> isset($values['descr']) ? $values['descr'] : '',

				'lang_entry_date'				=> lang('Entry date'),
				'lang_name'						=> lang('name'),
				'lang_descr'					=> lang('descr'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('ID'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $id,
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the responsible type'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => (isset($values['cat_id'])?$values['cat_id']:''))),

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		public function contact()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::contact';

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs','search_field'));

			$values	= phpgw::get_var('values', 'string', 'POST');

			if($values)
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}
				$this->bo->responsible($values);
			}
			
			$responsible_info = $this->bo->read();

			$lang_select_responsible_text		= '';
			$text_select					= '';

			if(isset($responsible_info) && is_array($responsible_info))
			{
				foreach ( $responsible_info as $entry )
				{
					if ( $this->acl_edit)
					{
						$lang_select_responsible_text		= lang('select responsible') . ': ' . $responsible;
					}

					$content[] = array
					(
						'user'							=> $entry['user'],
						'supervisor'					=> $entry['parent'],
						'location'						=> $entry['location'],
						'action_type'					=> $entry['action_type'],
						'lang_select_responsible_text'	=> $lang_select_responsible_text,
					);
				}
			}

			$table_header[] = array
			(
				'sort_location'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'location',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.index',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location
					)
				)),
				'lang_user'			=> lang('user'),
				'lang_location'		=> lang('location'),
				'lang_action'		=> lang('action'),
				'lang_supervisor'	=> lang('supervisor'),
				'lang_select'		=> (isset($this->acl_edit)?lang('select'):''),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
		//		'appname'		=> $appname,
				'location'		=> $this->location,

			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add contact'),
			);

			$table_add_action = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_contact',
				'location'		=> $this->location
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'allow_allrows'							=> true,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'num_records'							=> ($responsible_info?count($responsible_info):0),
				'all_records'							=> ($responsible_info?count($responsible_info):0),
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header_contact'					=> $table_header,
				'table_add'								=> $table_add,
				'add_action'							=> $GLOBALS['phpgw']->link('/index.php', $table_add_action),
				'values_contact'						=> (isset($content)?$content:''),
				'lang_no_location'						=> lang('No location'),
				'lang_location_statustext'				=> lang('Select submodule'),
				'select_name_location'					=> 'location',
				'location_list'							=> $this->bolocation->select_location('filter',$this->location,False,True),
			);

			$function_msg= lang('list available responsible contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_contact' => $data));
			$this->save_sessiondata();
		}



		public function no_access()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data)
			);

			$function_msg	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}
	}
