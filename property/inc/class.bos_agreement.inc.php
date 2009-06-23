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

	class property_bos_agreement
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $role;
		var $member_id;
		var $uicols = array();

		/**
		* @var object $custom reference to custom fields object
		*/
		public $custom;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
		);

		function property_bos_agreement($session=false)
		{
			$this->so			= CreateObject('property.sos_agreement');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');
			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name 		= 'fm_vendor';
			$this->cats->supress_info	= true;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$vendor_id	= phpgw::get_var('vendor_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$role	= phpgw::get_var('role');
			$member_id	= phpgw::get_var('member_id', 'int');


			$this->role	= $role;
			$this->so->role	= $role;

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : $this->query;
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->member_id		= isset($member_id) && $member_id ? $member_id : '';
			$this->vendor_id		= isset($vendor_id) && $vendor_id ? $vendor_id : '';
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','s_agreement',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','s_agreement');

			//_debug_array($data);

			$this->start	= isset($data['start']) ? $data['start'] : '';
			$this->query	= isset($data['query']) ? $data['query'] : '';
			$this->filter	= isset($data['filter']) ? $data['filter'] : '';
			$this->sort		= isset($data['sort']) ? $data['sort'] : '';
			$this->order	= isset($data['order']) ? $data['order'] : '';
			$this->cat_id	= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->vendor_id= isset($data['vendor_id']) ? $data['vendor_id'] : '';
			$this->member_id= isset($data['member_id']) ? $data['member_id'] : '';
			$this->allrows	= isset($data['allrows']) ? $data['allrows'] : '';
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}

		function select_vendor_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_vendor'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_vendor'));
					break;
			}

			$input_list= $this->so->select_vendor_list();
			$vendor_list= $this->bocommon->select_list($selected,$input_list);

			return $vendor_list;
		}

		function read()
		{
			$s_agreement = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											'vendor_id'=>$this->vendor_id));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($s_agreement); $i++)
			{
				if($s_agreement[$i]['start_date'])
				{
					$s_agreement[$i]['start_date']  = $GLOBALS['phpgw']->common->show_date($s_agreement[$i]['start_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
				if($s_agreement[$i]['end_date'])
				{
					$s_agreement[$i]['end_date']  = $GLOBALS['phpgw']->common->show_date($s_agreement[$i]['end_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
			}
			return $s_agreement;
		}

		function read_details($id)
		{
			$list = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											's_agreement_id'=>$id,'detail'=>true));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $list;
		}

		function read_prizing($data)
		{
			$list = $this->so->read_prizing($data);
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $list;
		}

		function read_event($data)
		{
			$boalarm			= CreateObject('property.boalarm');
			$event				= $this->so->read_single($data['s_agreement_id']);
			$event['alarm_date']= $event['termination_date'];
			$event['alarm']		= $boalarm->read_alarms($type='s_agreement',$data['s_agreement_id']);
			return $event;
		}

		function read_single($data)
		{
			$values['attributes'] = $this->custom->find('property', '.s_agreement', 0, '', 'ASC', 'attrib_sort', true, true);

			if(isset($data['s_agreement_id']) && $data['s_agreement_id'])
			{
				$values = $this->so->read_single($data['s_agreement_id'], $values);
			}

			$values = $this->custom->prepare($values, 'property', '.s_agreement', $data['view']);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']		= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']		= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
			if($values['termination_date'])
			{
				$values['termination_date']= $GLOBALS['phpgw']->common->show_date($values['termination_date'],$dateformat);
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$values['files'] = $vfs->ls (array(
			     'string' => "/property/service_agreement/{$data['s_agreement_id']}",
			     'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			if(!$values['files'][0]['file_id'])
			{
				unset($values['files']);
			}

			return $values;
		}

		function read_single_item($data)
		{
			$values['attributes'] = $this->custom->find('property', '.s_agreement.detail', 0, '', 'ASC', 'attrib_sort', true, true);

			if(isset($data['s_agreement_id']) && $data['s_agreement_id'] && isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single_item($data, $values);
			}
			$values = $this->custom->prepare($values, 'property', '.s_agreement.detail');

//_debug_array($item);

			if($values['location_code'])
			{
				$solocation	= CreateObject('property.solocation');
				$values['location_data'] =$solocation->read_single($values['location_code']);
			}

			if($values['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($values['p_entity_id'],$values['p_cat_id']);

				$values['p'][$values['p_entity_id']]['p_num']=$values['p_num'];
				$values['p'][$values['p_entity_id']]['p_entity_id']=$values['p_entity_id'];
				$values['p'][$values['p_entity_id']]['p_cat_id']=$values['p_cat_id'];
				$values['p'][$values['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			return $values;
		}

		/**
		* Arrange attributes within groups
		*
		* @param string  $location    the name of the location of the attribute
		* @param array   $attributes  the array of the attributes to be grouped
		*
		* @return array the grouped attributes
		*/

		public function get_attribute_groups($location, $attributes = array())
		{
			return $this->custom->get_attribute_groups('property', $location, $attributes);
		}

		function save($values,$values_attribute='',$action='')
		{

			$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			$values['termination_date']	= $this->bocommon->date_to_timestamp($values['termination_date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
//			if ($values['s_agreement_id'])
			{
				if ($values['s_agreement_id'] != 0)
				{
					$receipt=$this->so->edit($values,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute);
			}
			return $receipt;
		}

		function save_item($values,$values_attribute='')
		{

			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=@implode("-", $location);

			if (is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($values['id'])
			{
				if ($values['id'] != 0)
				{
					$receipt=$this->so->edit_item($values,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add_item($values,$values_attribute);
			}
			return $receipt;
		}


		function import($import_data,$id)
		{
			$this->so->role = 'detail';
			$custom_attributes = $this->so->read_attrib(array('allrows'=>true));

			foreach($custom_attributes as $attrib)
			{
				if(array_key_exists($attrib['column_name'],$import_data)
					&& ($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'R' || $attrib['datatype'] == 'CH')
				)
				{
					$import_data[$attrib['column_name']] = $this->so->attrib_choise2id($attrib['id'],$import_data[$attrib['column_name']]);
				}
			}

			$values = array(
			'extra' 			=> $import_data,
			's_agreement_id' 	=> $id,
			'location_code'		=> $import_data['location_code'],
			'location_name'		=> $import_data['address'],
			'cost'				=> $import_data['cost']
			);
			unset($values['extra']['location_code']);
			unset($values['extra']['address']);
			unset($values['extra']['cost']);
			return $this->so->add_item($values);
		}

		function update($values)
		{
			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);

			return $this->so->update($values);
		}

		function delete_last_index($s_agreement_id,$id)
		{
			$this->so->delete_last_index($s_agreement_id,$id);
		}

		function delete_item($s_agreement_id,$item_id)
		{
			$this->so->delete_item($s_agreement_id,$item_id);
		}

		function delete($s_agreement_id)
		{
			$this->so->delete($s_agreement_id);
		}

		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']['s_agreement_columns'];
			}

			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find('property','.s_agreement', 0, '','','',true, false, $filter);

			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}

		function request_next_id()
		{
				return $this->so->request_next_id();
		}

		function read_attrib_history($data)
		{
		//	_debug_array($data);
			$historylog = CreateObject('property.historylog','s_agreement');
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','ASC',$data['id'],$data['attrib_id'],$data['item_id']);
			$this->total_records = count($history_values);
		//	_debug_array($history_values);
			return $history_values;
		}

		function delete_history_item($data)
		{
			$historylog = CreateObject('property.historylog','s_agreement');
			$historylog->delete_single_record($data['history_id']);
		}

		function get_year_list($agreement_id = '')
		{
			if($agreement_id)
			{
				$list = $this->so->get_year_filter_list($agreement_id);
			}
			else
			{
				$list = array();
			}
			$year = date('Y');
			$limit = $year + 4;
			
			while ($year < $limit)
			{
				$list[] =  $year;
				$year++;
			}

			$list = array_unique($list);
			sort($list);
			
			$values;
			foreach($list as $entry)
			{
				$values[] = array
				(
					'id'	=> $entry,
					'name'	=> $entry
				);
			}
			return $values;
		}

		function get_budget($agreement_id)
		{
			$values = $this->so->get_budget($agreement_id);

			$this->cats->app_name		= 'property.project';
			foreach($values as & $entry)
			{
				$category = $this->cats->return_single($entry['cat_id']);
				$entry['category']		= $category[0]['name'];
			}
			$this->cats->app_name 		= 'fm_vendor';	
			return $values;		
		}

		function delete_year_from_budget($data,$agreement_id)
		{
			return $this->so->delete_year_from_budget($data,$agreement_id);
		}

	}

