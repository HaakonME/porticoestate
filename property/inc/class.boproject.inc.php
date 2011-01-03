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
	phpgw::import_class('phpgwapi.datetime');

	class property_boproject
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
			(
				'read'				=> true,
				'read_single'		=> true,
				'save'				=> true,
				'delete'			=> true,
				'check_perms'		=> true
			);

		function property_boproject($session=false)
		{
			$this->so 					= CreateObject('property.soproject');
			$this->bocommon 			= & $this->so->bocommon;
			$this->cats					= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$this->cats->supress_info	= true;
			$this->interlink 			= & $this->so->interlink;
			$this->custom 				= & $this->so->custom;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$status_id				= phpgw::get_var('status_id');
			$user_id				= phpgw::get_var('user_id', 'int');
			$wo_hour_cat_id			= phpgw::get_var('wo_hour_cat_id', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$criteria_id			= phpgw::get_var('criteria_id', 'int');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : $this->query;
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->status_id		= isset($status_id) && $status_id ? $status_id : '';
			$this->user_id			= isset($user_id) && $user_id ? $user_id : '';
			$this->wo_hour_cat_id	= isset($wo_hour_cat_id) && $wo_hour_cat_id ? $wo_hour_cat_id : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->criteria_id		= isset($criteria_id) && $criteria_id ? $criteria_id : '';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','project',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','project');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->status_id		= isset($data['status_id'])?$data['status_id']:'';
			$this->user_id			= isset($data['user_id'])?$data['user_id']:'';
			$this->wo_hour_cat_id	= isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
			$this->criteria_id		= isset($data['criteria_id'])?$data['criteria_id']:'';
		}

		function column_list($selected = array(),$type_id='',$allrows='')
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] : '';
			}
			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = array();
			$columns[] = array
				(
					'id' => 'entry_date',
					'name'=> lang('entry date')
				);
			$columns[] = array
				(
					'id' => 'start_date',
					'name'=> lang('start date')
				);
			$columns[] = array
				(
					'id' => 'end_date',
					'name'=> lang('end date')
				);
			$columns[] = array
				(
					'id' => 'billable_hours',
					'name'=> lang('billable hours')
				);

			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
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

		/*	for ($i=0;$i<count($branch_list);$i++)
			{
				if ($branch_list[$i]['selected'] != 'selected')
				{
					unset($branch_list[$i]['selected']);
				}
			}
		 */

			return $branch_list;
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
						'name'	=> lang('address')
					),
					array
					(
						'id'	=> '4',
						'name'	=> lang('location code')
					),
					array
					(
						'id'	=> '5',
						'name'	=> lang('title')
					),
					array
					(
						'id'	=> '6',
						'name'	=> lang('module')
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
					'field'	=> 'fm_project.address',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'",
				);
			$criteria[4] = array
				(
					'field'	=> 'fm_project.location_code',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'",
					'back' => "%'"
				);
			$criteria[5] = array
				(
					'field'	=> 'fm_project.name',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'"
				);
			$criteria[6] = array
				(
					'field'	=> 'fm_project.p_num',
					'type'	=> 'varchar',
					'matchtype' => 'exact',
					'front' => "'",
					'back' => "'"
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

		function select_key_location_list($selected='')
		{

			$key_location_entries= $this->so->select_key_location_list();

			return $this->bocommon->select_list($selected,$key_location_entries);
		}

		function read($data = array())
		{
			$start_date	= $this->bocommon->date_to_timestamp($data['start_date']);
			$end_date	= $this->bocommon->date_to_timestamp($data['end_date']);

			$project = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,'wo_hour_cat_id' => $this->wo_hour_cat_id,
				'start_date'=>$start_date,'end_date'=>$end_date,'allrows'=>isset($data['allrows']) ? $data['allrows'] : '','dry_run' => $data['dry_run'],
				'district_id' => $this->district_id, 'criteria' => $this->get_criteria($this->criteria_id)));
			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->uicols	= $this->so->uicols;

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] : array();

			foreach ($custom_cols as $col)
			{
				$this->uicols['input_type'][]	= 'text';
				$this->uicols['name'][]			= $col;
				$this->uicols['descr'][]		= lang(str_replace('_', ' ', $col));
				$this->uicols['statustext'][]	= $col;
				$this->uicols['exchange'][]		= false;
				$this->uicols['align'][] 		= '';
				$this->uicols['datatype'][]		= false;
			}

			if(!isset($data['skip_origin']) || !$data['skip_origin'])
			{
				$this->uicols['input_type'][]	= 'text';
				$this->uicols['name'][]			= 'ticket';
				$this->uicols['descr'][]		= lang('ticket');
				$this->uicols['statustext'][]	= false;
				$this->uicols['exchange'][]		= false;
				$this->uicols['align'][] 		= '';
				$this->uicols['datatype'][]		= 'link';
			}

			$cols_extra		= $this->so->cols_extra;

			foreach ($project as & $entry)
			{
				$entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$dateformat);
				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
				$entry['end_date'] = $GLOBALS['phpgw']->common->show_date($entry['end_date'],$dateformat);
				if(!isset($data['skip_origin']) || !$data['skip_origin'])
				{
					$origin = $this->interlink->get_relation('property', '.project', $entry['project_id'], 'origin');
					if(isset($origin[0]['location']) && $origin[0]['location'] == '.ticket')
					{
						$entry['ticket'] = array
							(
								'url' 			=> $GLOBALS['phpgw']->link('/index.php', array
								(
									'menuaction'	=> 'property.uitts.view',
									'id'			=> $origin[0]['data'][0]['id']
								)
							),
							'text'			=> $origin[0]['data'][0]['id'],
							'statustext'	=> $origin[0]['data'][0]['statustext'],											
						);
					}
				}
			}
			return $project;
		}

		function read_single($project_id = 0, $values = array(), $view = false)
		{
			$contacts	= CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor',false);

			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			$tax = 1+(isset($config->config_data['fm_tax'])?$config->config_data['fm_tax']:0)/100;

			$values['attributes'] = $this->custom->find('property', '.project', 0, '', 'ASC', 'attrib_sort', true, true);
			if($project_id)
			{
				$values = $this->so->read_single($project_id, $values);
			}

			$values = $this->custom->prepare($values, 'property', '.project', $view);

			if(!$project_id)
			{
				return $values;
			}

			$dateformat				= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']			= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']			= isset($values['end_date']) && $values['end_date'] ? $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat) : '';
			$workorder_data				= $this->so->project_workorder_data($project_id);

			$sum_workorder_budget = 0;
			$sum_deviation = 0;
			$sum_workorder_calculation = 0;
			$sum_workorder_actual_cost = 0;

			for ($i=0;$i<count($workorder_data);$i++)
			{
				$sum_workorder_budget= $sum_workorder_budget+$workorder_data[$i]['budget'];
				$sum_deviation= $sum_deviation+$workorder_data[$i]['deviation'];
				$sum_workorder_calculation= $sum_workorder_calculation+$workorder_data[$i]['calculation'];
				$sum_workorder_actual_cost= $sum_workorder_actual_cost+$workorder_data[$i]['act_mtrl_cost']+$workorder_data[$i]['act_vendor_cost'];

				$values['workorder_budget'][$i]['title']=$workorder_data[$i]['title'];
				$values['workorder_budget'][$i]['workorder_id']=$workorder_data[$i]['workorder_id'];
				$values['workorder_budget'][$i]['budget']=number_format($workorder_data[$i]['budget'], 2, ',', '');
				$values['workorder_budget'][$i]['calculation']=number_format($workorder_data[$i]['calculation']*$tax, 2, ',', '');
				$values['workorder_budget'][$i]['charge_tenant'] = $workorder_data[$i]['charge_tenant'];
				$values['workorder_budget'][$i]['status'] = $workorder_data[$i]['status'];
				$values['workorder_budget'][$i]['actual_cost'] = $workorder_data[$i]['act_mtrl_cost']+$workorder_data[$i]['act_vendor_cost'];
				$values['workorder_budget'][$i]['b_account_id'] = $workorder_data[$i]['b_account_id'];

				if(isset($workorder_data[$i]['vendor_id']) && $workorder_data[$i]['vendor_id'])
				{
					$vendor['attributes'] = $this->custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

					$vendor	= $contacts->read_single(array('id' => $workorder_data[$i]['vendor_id']), $vendor);
					foreach($vendor['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$values['workorder_budget'][$i]['vendor_name']=$attribute['value'];
							break;
						}
					}
				}
			}
			if($workorder_data)
			{
				$values['sum_workorder_budget']= number_format($sum_workorder_budget, 2, ',', '');
				$values['deviation']= $sum_deviation;
				$values['sum_workorder_calculation']= number_format($sum_workorder_calculation*$tax, 2, ',', '');
				$values['sum_workorder_actual_cost']= number_format($sum_workorder_actual_cost, 2, ',', '');
			}

			if($values['location_code'])
			{
				$values['location_data'] = execMethod('property.solocation.read_single', $values['location_code']);
			}

			if($values['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($values['tenant_id']);
				$values['location_data']['tenant_id']= $values['tenant_id'];
				$values['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$values['location_data']['last_name']	= $tenant_data['last_name'];
				$values['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($values['location_data']['tenant_id']);
				unset($values['location_data']['contact_phone']);
				unset($values['location_data']['last_name']);
				unset($values['location_data']['first_name']);
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

			$values['origin'] = $this->interlink->get_relation('property', '.project', $project_id, 'origin');
			$values['target'] = $this->interlink->get_relation('property', '.project', $project_id, 'target');

			//_debug_array($values);
			return $values;
		}

		function read_single_mini($project_id)
		{
			$project						= $this->so->read_single($project_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$project['start_date']			= $GLOBALS['phpgw']->common->show_date($project['start_date'],$dateformat);
			$project['end_date']			= isset($project['end_date']) && $project['end_date'] ? $GLOBALS['phpgw']->common->show_date($project['end_date'],$dateformat) : '';

			if($project['location_code'])
			{
				$project['location_data'] = execMethod('property.solocation.read_single', $project['location_code']);
			}

			if($project['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($project['tenant_id']);
				$project['location_data']['tenant_id']= $project['tenant_id'];
				$project['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$project['location_data']['last_name']	= $tenant_data['last_name'];
				$project['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($project['location_data']['tenant_id']);
				unset($project['location_data']['contact_phone']);
				unset($project['location_data']['last_name']);
				unset($project['location_data']['first_name']);
			}

			//_debug_array($project);
			return $project;
		}


		function read_record_history($id)
		{
			$historylog	= CreateObject('property.historylog','project');
			$history_array = $historylog->return_array(array('O'),array(),'','',$id);
			$i=0;
			foreach ($history_array as $value) 
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
				case 'B':
					$type = lang('Budget');
					break;
				case 'BR':
					$type = lang('reserve');
					break;
				case 'R':
					$type = lang('Re-opened');
					break;
				case 'RM':
					$type = lang('remark');
					break;
				case 'X':
					$type = lang('Closed');
					break;
				case 'O':
					$type = lang('Opened');
					break;
				case 'A':
					$type = lang('Re-assigned');
					break;
				case 'P':
					$type = lang('Priority changed');
					break;
				case 'CO':
					$type = lang('Initial Coordinator');
					break;
				case 'C':
					$type = lang('Coordinator changed');
					break;
				case 'TO':
					$type = lang('Initial Category');
					break;
				case 'T':
					$type = lang('Category changed');
					break;
				case 'SO':
					$type = lang('Initial Status');
					break;
				case 'S':
					$type = lang('Status changed');
					break;
				case 'SC':
					$type = lang('Status confirmed');
					break;
				case 'AP':
					$type = lang('Ask for approval');
					break;
				case 'ON':
					$type = lang('Owner notified');
					break;
				default:
					break;
				}

				if($value['new_value']=='O')
				{
					$value['new_value']=lang('Opened');
				}
				if($value['new_value']=='X')
				{
					$value['new_value']=lang('Closed');
				}

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
				else if ($value['status'] == 'B' || $value['status'] == 'BR')
				{
					$record_history[$i]['value_new_value']	=number_format($value['new_value'], 0, ',', ' ');
					$record_history[$i]['value_old_value']	=number_format($value['old_value'], 0, ',', ' ');
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
					$record_history[$i]['value_old_value']	= $value['old_value'];
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}


		function next_project_id()
		{
			return $this->so->next_project_id();
		}

		function save($project,$action='',$values_attribute = array())
		{

			//_debug_array($project);
			while (is_array($project['location']) && list(,$value) = each($project['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$project['location_code']=implode("-", $location);

			$project['start_date']	=  phpgwapi_datetime::date_to_timestamp($project['start_date']);
			$project['end_date']	=  phpgwapi_datetime::date_to_timestamp($project['end_date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				$receipt = $this->so->edit($project, $values_attribute);
			}
			else
			{
				$receipt = $this->so->add($project, $values_attribute);
			}
			return $receipt;
		}

		function add_request($add_request,$id)
		{

			return $this->so->add_request($add_request,$id);
		}

		function delete($project_id)
		{
			$this->so->delete($project_id);
		}

	}
