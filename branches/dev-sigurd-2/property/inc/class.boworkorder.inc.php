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

	/**
	 * Description
	 * @package property
	 */

	class property_boworkorder
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $order_sent_adress; // in case we want to resend the order as an reminder

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
		);

		function property_boworkorder($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.soworkorder');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name		= 'property.project';
			$this->cats->supress_info	= true;
			$this->interlink 	= & $this->so->interlink;
			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$status_id		= phpgw::get_var('status_id');
			$wo_hour_cat_id	= phpgw::get_var('wo_hour_cat_id', 'int');
			$start_date		= phpgw::get_var('start_date');
			$end_date		= phpgw::get_var('end_date');
			$b_group		= phpgw::get_var('b_group');
			$paid			= phpgw::get_var('paid', 'bool');
			$b_account		= phpgw::get_var('b_account');
			$district_id	= phpgw::get_var('district_id', 'int');
			$criteria_id	= phpgw::get_var('criteria_id', 'int');

			$this->start			= $start ? $start : 0;
			$this->criteria_id		= isset($criteria_id) && $criteria_id ? $criteria_id : '';

			if(array_key_exists('b_account',$_POST) || array_key_exists('b_account',$_GET) )
			{
				$this->b_account = $b_account;
			}
			if(array_key_exists('district_id',$_POST) || array_key_exists('district_id',$_GET) )
			{
				$this->district_id = $district_id;
			}

			if(isset($paid))
			{
				$this->paid = $paid;
			}
			if(isset($b_group))
			{
				$this->b_group = $b_group;
			}
			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET) )
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET) )
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if(array_key_exists('status_id',$_POST)  || array_key_exists('status_id',$_GET))
			{
				$this->status_id = $status_id;
			}
			if(array_key_exists('wo_hour_cat_id',$_POST)  || array_key_exists('wo_hour_cat_id',$_GET))
			{
				$this->wo_hour_cat_id = $wo_hour_cat_id;
			}
			if(array_key_exists('start_date',$_POST) || array_key_exists('start_date',$_GET))
			{
				$this->start_date = $start_date;
			}
			if(array_key_exists('end_date',$_POST) || array_key_exists('end_date',$_GET))
			{
				$this->end_date = $end_date;
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','workorder');

			$this->start			= isset($data['start']) ? $data['start'] : '';
			$this->query			= isset($data['query']) ? $data['query']: '';
			$this->filter			= isset($data['filter']) ? $data['filter']: '';
			$this->sort				= isset($data['sort']) ? $data['sort']: '';
			$this->order			= isset($data['order']) ? $data['order']: '';
			$this->cat_id			= isset($data['cat_id']) ? $data['cat_id']: '';
			$this->status_id		= isset($data['status_id']) ? $data['status_id']: '';
			$this->wo_hour_cat_id	= isset($data['wo_hour_cat_id']) ? $data['wo_hour_cat_id']: '';
			$this->start_date		= isset($data['start_date']) ? $data['start_date']: '';
			$this->end_date			= isset($data['end_date']) ? $data['end_date']: '';
			$this->b_group			= isset($data['b_group']) ? $data['b_group']: '';
			$this->paid				= isset($data['paid']) ? $data['paid']: '';
			$this->b_account		= isset($data['b_account']) ? $data['b_account']: '';
			$this->district_id		= isset($data['district_id']) ? $data['district_id']: '';
			$this->criteria_id		= isset($data['criteria_id'])?$data['criteria_id']:'';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','workorder',$data);
			}
		}

		function next_id()
		{
			return $this->so->next_id();
		}

		function select_status_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_filter'));
					break;
			}

			$status_entries= $this->so->select_status_list();

			return $this->bocommon->select_list($selected,$status_entries);
		}

		function select_branch_list($selected='')
		{
			$branch_entries= $this->so->select_branch_list();
			return $this->bocommon->select_list($selected,$branch_entries);
		}

		function select_branch_p_list($project_id='')
		{
			$selected		= $this->so->branch_p_list($project_id);
			$branch_entries	= $this->so->select_branch_list();

			$j=0;
			while (is_array($branch_entries) && list(,$branch) = each($branch_entries))
			{
				$branch_list[$j]['id'] = $branch['id'];
				$branch_list[$j]['name'] = $branch['name'];

				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['branch_id'] == $branch['id'])
					{
						$branch_list[$j]['selected'] = 'selected';
					}
				}
				$j++;
			}

			return $branch_list;
		}

		function select_key_location_list($selected='')
		{
			$key_location_entries= $this->so->select_key_location_list();
			return $this->bocommon->select_list($selected,$key_location_entries);
		}

		function get_criteria_list($selected='')
		{
			$criteria = array
			(
				array
				(
					'id'	=> '1',
					'name'	=> lang('project group')
				),
				array
				(
					'id'	=> '2',
					'name'	=> lang('project id')
				),
				array
				(
					'id'	=> '3',
					'name'	=> lang('workorder id')
				),
				array
				(
					'id'	=> '4',
					'name'	=> lang('address')
				),

				array
				(
					'id'	=> '5',
					'name'	=> lang('location code')
				),
				array
				(
					'id'	=> '6',
					'name'	=> lang('title')
				),
				array
				(
					'id'	=> '7',
					'name'	=> lang('vendor')
				),
				array
				(
					'id'	=> '8',
					'name'	=> lang('vendor id')
				),
			);
			return $this->bocommon->select_list($selected,$criteria);
		}


		function get_criteria($id='')
		{
			$criteria = array();
			$criteria[1] = array
			(
				'field'		=> 'project_group',
				'type'		=> 'int',
				'matchtype' => 'exact',
				'front' => '',
				'back' => ''
			);
			$criteria[2] = array
			(
				'field'		=> 'fm_project.id',
				'type'		=> 'int',
				'matchtype' => 'exact',
				'front' => '',
				'back' => ''
			);
			$criteria[3] = array
			(
				'field'		=> 'fm_workorder.id',
				'type'		=> 'int',
				'matchtype' => 'exact',
				'front' => '',
				'back' => ''
			);
			$criteria[4] = array
			(
				'field'	=> 'fm_project.address',
				'type'	=> 'varchar',
				'matchtype' => 'like',
				'front' => "'%",
				'back' => "%'",
			);
			$criteria[5] = array
			(
				'field'	=> 'fm_project.location_code',
				'type'	=> 'varchar',
				'matchtype' => 'like',
				'front' => "'",
				'back' => "%'"
			);
			$criteria[6] = array
			(
				'field'	=> 'fm_workorder.title',
				'type'	=> 'varchar',
				'matchtype' => 'like',
				'front' => "'%",
				'back' => "%'"
			);
			$criteria[7] = array
			(
				'field'	=> 'fm_vendor.org_name',
				'type'	=> 'varchar',
				'matchtype' => 'like',
				'front' => "'%",
				'back' => "%'"
			);
			$criteria[8] = array
			(
				'field'	=> 'fm_vendor.id',
				'type'	=> 'int',
				'matchtype' => 'exact',
				'front' => '',
				'back' => ''
			);

			if($id)
			{
				return array($criteria[$id]);
			}
			else
			{
				return $criteria;
			}			
		}


		function read($data = array())
		{
			$start_date	= $this->bocommon->date_to_timestamp($data['start_date']);
			$end_date	= $this->bocommon->date_to_timestamp($data['end_date']);

	
			$workorder = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,
											'wo_hour_cat_id' => $this->wo_hour_cat_id,
											'start_date'=>$start_date,'end_date'=>$end_date,'allrows'=>$data['allrows'],
											'b_group'=>$this->b_group,'paid'=>$this->paid,'b_account' => $this->b_account,
											'district_id' => $this->district_id,'dry_run'=>$data['dry_run'], 'criteria' => $this->get_criteria($this->criteria_id)));
			
			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($workorder); $i++)
			{
				$workorder[$i]['entry_date'] = $GLOBALS['phpgw']->common->show_date($workorder[$i]['entry_date'],$dateformat);
			}

			return $workorder;
		}

		function read_single($workorder_id)
		{
			$contacts		= CreateObject('property.soactor');
			$contacts->role='vendor';
			$workorder						= $this->so->read_single($workorder_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$workorder['start_date']		= $GLOBALS['phpgw']->common->show_date($workorder['start_date'],$dateformat);
			$workorder['end_date']			= $GLOBALS['phpgw']->common->show_date($workorder['end_date'],$dateformat);

			if(isset($workorder['vendor_id']) && $workorder['vendor_id'])
			{
				$custom 		= createObject('property.custom_fields');
				$vendor['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
				$vendor			= $contacts->read_single($workorder['vendor_id'],$vendor);
				foreach($vendor['attributes'] as $attribute)
				{
					if($attribute['name']=='org_name')
					{
						$workorder['vendor_name']=$attribute['value'];
					}
					if($attribute['name']=='email')
					{
						$workorder['vendor_email']=$attribute['value'];
					}
				}
			}

			$workorder['b_account_name']	= $this->so->get_b_account_name($workorder['b_account_id']);

			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			$tax = 1+($config->config_data['fm_tax'])/100;
			$workorder['calculation']	=number_format($workorder['calculation']*$tax, 2, ',', '');
			$workorder['actual_cost']	=number_format(($workorder['act_mtrl_cost']+$workorder['act_vendor_cost']), 2, ',', '');

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$workorder['files'] = $vfs->ls(array(
			     'string' => '/property/workorder/' . $workorder_id,
			     'relatives' => array(RELATIVE_NONE)
			     ));

			$vfs->override_acl = 0;

			$j	= count($workorder['files']);
			for ($i=0;$i<$j;$i++)
			{
				$workorder['files'][$i]['file_name']=urlencode($workorder['files'][$i]['name']);
			}

			if(!isset($workorder['files'][0]['file_id']) || !$workorder['files'][0]['file_id'])
			{
				unset($workorder['files']);
			}

			$workorder['origin'] = $this->interlink->get_relation('property', '.project.workorder', $workorder_id, 'origin');
			$workorder['target'] = $this->interlink->get_relation('property', '.project.workorder', $workorder_id, 'target');

			if($workorder['location_code'])
			{
				$solocation 	= CreateObject('property.solocation', $this->bocommon);
				$workorder['location_data'] = $solocation->read_single($workorder['location_code']);
			}

			if($workorder['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($workorder['tenant_id']);
				$workorder['location_data']['tenant_id']= $workorder['tenant_id'];
				$workorder['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$workorder['location_data']['last_name']	= $tenant_data['last_name'];
				$workorder['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($workorder['location_data']['tenant_id']);
				unset($workorder['location_data']['contact_phone']);
				unset($workorder['location_data']['last_name']);
				unset($workorder['location_data']['first_name']);
			}

			if($workorder['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($workorder['p_entity_id'],$workorder['p_cat_id']);

				$workorder['p'][$workorder['p_entity_id']]['p_num']=$workorder['p_num'];
				$workorder['p'][$workorder['p_entity_id']]['p_entity_id']=$workorder['p_entity_id'];
				$workorder['p'][$workorder['p_entity_id']]['p_cat_id']=$workorder['p_cat_id'];
				$workorder['p'][$workorder['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$event_criteria = array
			(
				'appname'		=> 'property',
				'location'		=> '.project.workorder',
				'location_item_id'	=> $workorder_id
			);

			$events = execMethod('property.soevent.read', $event_criteria);
			$workorder['event_id'] = $events ? $events[0]['id'] : '';

			return $workorder;
		}

		function read_record_history($id)
		{
			$historylog	= CreateObject('property.historylog','workorder');
			$history_array = $historylog->return_array(array('O'),array(),'','',$id);

			$i=0;
			foreach ($history_array as $value) 
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
					case 'R': $type = lang('Re-opened'); break;
					case 'RM': $type = lang('remark'); break;
					case 'X': $type = lang('Closed');    break;
					case 'O': $type = lang('Opened');    break;
					case 'A': $type = lang('Re-assigned'); break;
					case 'P': $type = lang('Priority changed'); break;
					case 'M':
						$type = lang('Sendt by email to');
						$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
						break;
					case 'B': $type = lang('Budget changed'); break;
					case 'CO': $type = lang('Initial Coordinator'); break;
					case 'C': $type = lang('Coordinator changed'); break;
					case 'TO': $type = lang('Initial Category'); break;
					case 'T': $type = lang('Category changed'); break;
					case 'SO': $type = lang('Initial Status'); break;
					case 'S': $type = lang('Status changed'); break;
					case 'SC': $type = lang('Status confirmed'); break;
					default: break;
				}

				if($value['new_value']=='O'){$value['new_value']=lang('Opened');}
				if($value['new_value']=='X'){$value['new_value']=lang('Closed');}


				$record_history[$i]['value_action']	= $type?$type:'';
				unset($type);

				if ($value['status'] == 'A')
				{
					if (! $value['new_value'])
					{
						$record_history[$i]['value_new_value']	= lang('None');
					}
					else
					{
						$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
					if (! $value['old_value'])
					{
						$record_history[$i]['value_old_value']	= '';
					}
					else
					{
						$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
					}
				}
				else if ($value['status'] == 'C' || $value['status'] == 'CO')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					if (! $value['old_value'])
					{
						$record_history[$i]['value_old_value']	= '';
					}
					else
					{
						$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
					}
				}
				else if ($value['status'] == 'T' || $value['status'] == 'TO')
				{
					$category 								= $this->cats->return_single($value['new_value']);
					$record_history[$i]['value_new_value']	= $category[0]['name'];
					if($value['old_value'])
					{
						$category 								= $this->cats->return_single($value['old_value']);
						$record_history[$i]['value_old_value']	= $category[0]['name'];
					}
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
					$record_history[$i]['value_old_value']	= $value['old_value'];
				}
				else if ($value['status'] != 'B' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	=number_format($value['new_value'], 0, ',', ' ');
					$record_history[$i]['value_old_value']	=number_format($value['old_value'], 0, ',', ' ');
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}

		function save($workorder,$action='')
		{
			$workorder['start_date']	= $this->bocommon->date_to_timestamp($workorder['start_date']);
			$workorder['end_date']	= $this->bocommon->date_to_timestamp($workorder['end_date']);
			$workorder['location_code'] = isset($workorder['location']) && $workorder['location'] ? implode('-',$workorder['location']) : '';

			if ($action=='edit')
			{
					$receipt = $this->so->edit($workorder);
			}
			else
			{
				$receipt = $this->so->add($workorder);
			}
			return $receipt;
		}

		function delete($workorder_id)
		{
			$this->so->delete($workorder_id);
		}

	}
