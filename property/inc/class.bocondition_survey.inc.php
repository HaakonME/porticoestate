<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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

	/**
	 * Description
	 * @package property
	 */

	class property_bocondition_survey
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();
		var $appname;
		var $allrows;
		public $acl_location = '.project.condition_survey';

		var $public_functions = array
		(
			'addfiles'		=> true
		);

		function __construct($session=false)
		{
			$this->so 			= CreateObject('property.socondition_survey');
			$this->custom 		= & $this->so->custom;
			$this->bocommon		= CreateObject('property.bocommon');
			$this->dateformat			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$appname 			= phpgw::get_var('appname', 'string');

			if($appname)
			{
				$this->appname		= $appname;
				$this->so->appname	= $appname;
			}

			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int', 'REQUEST', 0);
			$this->type 		= $type;
			$this->type_id 		= $type_id;

			if ($session)
			{
				$this->read_sessiondata($type);
				$this->use_session = true;
			}

			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) && $_REQUEST['order'] ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->allrows		= isset($allrows) ? $allrows : false;


		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',$this->acl_location,$data);
			}
		}

		function read_sessiondata($type)
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$this->acl_location);

			//		_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}

		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected = $GLOBALS['phpgw_info']['user']['preferences']['property']["columns_{$this->acl_location}"];
			}

			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find('property',$this->acl_location, 0, '','','',true, false, $filter);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}

		public function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$acl 			= & $GLOBALS['phpgw']->acl;
			$acl_add 		= $acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 		= $acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$id				= phpgw::get_var('id', 'int');
			$check			= phpgw::get_var('check', 'bool');
			$fileuploader	= CreateObject('property.fileuploader');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if(!$id)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$test = true;
			if ($test)
			{
				if (!empty($_FILES))
				{
					$tempFile = $_FILES['Filedata']['tmp_name'];
					$targetPath = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/";
					$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
					move_uploaded_file($tempFile,$targetFile);
					echo str_replace($GLOBALS['phpgw_info']['server']['temp_dir'],'',$targetFile);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
	
			if($check)
			{
				$fileuploader->check("condition_survey/{$id}");
			}
			else
			{
				$fileuploader->upload("condition_survey/{$id}");
			}
		}

		public function read($data = array())
		{
			$values = $this->so->read($data);
			$this->total_records = $this->so->total_records;
			return $values;
		}

		public function read_single($data=array())
		{
			$custom_fields = false;
			if($GLOBALS['phpgw']->locations->get_attrib_table('property', $this->acl_location))
			{
				$custom_fields = true;
				$data['attributes'] = $this->custom->find('property', $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);
			}

			$values = array();
			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data);
			}
			if($custom_fields)
			{
				$values = $this->custom->prepare($values, 'property', $this->acl_location, $data['view']);
			}

			$values['report_date']	= $GLOBALS['phpgw']->common->show_date($values['report_date'],$this->dateformat);

			if(isset($values['vendor_id']) && $values['vendor_id'] && !$values['vendor_name'])
			{
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single(array('id' => $values['vendor_id']),$vendor_data);
				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$values['vendor_name']=$attribute['value'];
							break;
						}
					}
				}
				unset($contacts);
			}

			if($values['coordinator_id'])
			{
				$values['coordinator_name']	= $GLOBALS['phpgw']->accounts->get($values['coordinator_id'])->__toString();
			}
			return $values;
		}

		public function save($data = array())
		{
			if(isset($data['attributes']) && is_array($data['attributes']))
			{
				$data['attributes'] = $this->custom->convert_attribute_save($data['attributes']);
			}

			try
			{
				if (isset($data['id']) && $data['id'])
				{
					$id = $this->so->edit($data);
				}
				else
				{
					$id = $this->so->add($data);
				}
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;				
				}
			}

			return $id;
		}

		public function delete($id)
		{
			$this->so->delete($id);
		}
	}
