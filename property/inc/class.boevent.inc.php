<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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

	/*
	 * Import the datetime class for date processing
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_boevent
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();
		protected $event_functions = array
		(
			'send_sms'	=> 'send SMS'
		);
	
		function __construct($session=false)
		{
			$this->so 			= CreateObject('property.soevent');
			$this->custom 		= CreateObject('property.custom_fields');//& $this->so->custom;
	//		$this->bocommon 	= CreateObject('property.bocommon');
			$this->sbox 		= CreateObject('phpgwapi.sbox');
			$this->asyncservice = CreateObject('phpgwapi.asyncservice');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int');

			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->allrows		= isset($allrows) ? $allrows : false;

			//$this->location_info = $this->so->get_location_info($type, $type_id);

		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','category',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','category');

	//		_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}

		public function read()
		{
			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $values;
		}

		public function read_single($id)
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values = $this->so->read_single($id);
			if($values)
			{
				$values['start_date']		= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
				$values['end_date']		= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
				if($values['rpt_day'])
				{
					$rpt_day = array
					(
						1		=> 'Sunday',
						2		=> 'Monday',
						4		=> 'Tuesday',
						8		=> 'Wednesday',
						16		=> 'Thursday',
						32		=> 'Friday',
						64		=> 'Saturday'
					);

					foreach ($rpt_day as $mask => $name)
					{
						if($mask & $values['rpt_day'])
						{
							$values['repeat_day'][$mask] = $name;
						}
					}
				}

				$location	= phpgw::get_var('location');
				$job_id = "property{$location}::{$values['location_item_id']}::{$values['attrib_id']}";
				$job = execMethod('phpgwapi.asyncservice.read', $job_id);

				$values['next'] = $GLOBALS['phpgw']->common->show_date($job[$job_id]['next'],$dateformat);
			}

			return $values;
		}

		public function save($data)
		{
			$data['start_date'] = phpgwapi_datetime::date_to_timestamp($data['start_date']);
			$data['end_date'] = phpgwapi_datetime::date_to_timestamp($data['end_date']);

			if (isset($data['id']) && $data['id'] > 0 && $this->so->read_single($data['id']))
			{
				$receipt = $this->so->edit($data);
			}
			else
			{
				$receipt = $this->so->add($data);
			}

			$action_object		= CreateObject('property.socategory');
			$action_object->get_location_info('event_action',false);
			$action	= $action_object->read_single(array('id'=> $data['action']),$values = array());

			$rpt_day = array
			(
				1		=> 0, //'Sunday',
				2		=> 1, //'Monday',
				4		=> 2, //'Tuesday',
				8		=> 3, //'Wednesday',
				16		=> 4, //'Thursday',
				32		=> 5, //'Friday',
				64		=> 6  //'Saturday'
			);

			$repeat_day = array();
			if ($data['repeat_day'])
			{
				foreach ($data['repeat_day'] as $day)
				{
					if (isset($rpt_day[$day]))
					{
						$repeat_day[] = $rpt_day[$day];
					}
				}
				$repeat_day = implode(',', $repeat_day);
			}

			if(!isset($data['repeat_type']) || !$data['repeat_type'])
			{
				$times = $data['start_date'];
			}
			else
			{
				$dow = $rpt_day[$data['repeat_day'][0]];
				switch($data['repeat_type'])
				{
					case '0':
						$times = $data['start_date'];
						break;
					case '1': //'Daily'
						if($data['repeat_interval'])
						{
							$times = array('day' => "*/{$data['repeat_interval']}");
						}
						else
						{
							$times = array('day' => "*/1");
						}
						break;
					case '2': //'Weekly'
						if($data['repeat_interval'])
						{
							$day = $data['repeat_interval'] * 7;
							$times = array('day' => "*/{$day}");
						}
						else
						{
							$times = array('day' => "*/7");
						}
						if ($data['repeat_day'])
						{
							$times['dow'] = $repeat_day;
						}
						break;
					case '3': //'Monthly (by day)'
						if( !isset($data['repeat_day']) || !is_array($data['repeat_day']) )
						{
							$dow = 1;
						}
						
						if($data['repeat_interval'])
						{
							$times = array('month' => "*/{$data['repeat_interval']}", 'dow' => $dow);
						}
						else
						{
							$times = array('month' => "*/1", 'dow' => $dow);
						}
						break;
					case '4': //'Monthly (by date)'
						if($data['repeat_interval'])
						{
							$times = array('month' => "*/{$data['repeat_interval']}", 'day' => 1);
						}
						else
						{
							$times = array('day' => 1);
						}
						break;
					case '5': //'Yearly'
						$month = date(n, $data['start_date']);
						if($data['repeat_interval'])
						{
							$times = array('year' => "*/{$data['repeat_interval']}", 'month' => $month);
						}
						else
						{
							$times = array('month' => $month);
						}
						break;
					default:
						$times = $data['start_date'];
						break;
				}
			}

			//$times['min']= '*'; // for testing the  - every minute

			$account_id = execMethod('property.soresponsible.get_responsible_user_id', $data['responsible']);

			$timer_data = array
			(
				'start'		=> $data['start_date'],
				'enabled'	=> true,
				'owner'		=> $account_id,
				'enabled'	=> !! $data['enabled'],
				'action'	=> $action['action']
			);
				
			if($data['end_date'])
			{
				$timer_data['end'] = $data['end_date'];
			}

			if($action['data'])
			{
				str_replace(";", '', $action['data']);
				eval('$action_data = ' . htmlspecialchars_decode($action['data']) . ';');
				$timer_data = array_merge($timer_data, $action_data);
			}

			$location	= phpgw::get_var('location');

			$id = "property{$location}::{$data['item_id']}::{$data['attrib_id']}";
			$timer_data['id'] = $id;

			$this->asyncservice->cancel_timer($id);
			$this->asyncservice->set_timer($times, $id, 'property.boevent.action', $timer_data, $account_id);

			return $receipt;
		}

		public function action($data)
		{
			$parts = explode('::',$data['id']);
			$id = end($parts);

			if($data['enabled'] && !$this->so->check_event_exception($id,$data['time']))
			{
				list($module, $classname) = explode('.', $data['action'], 2);
				if ( is_file(PHPGW_INCLUDE_ROOT . "/{$module}/class.{$classname}.inc.php") )
				{
					$message = execMethod($data['action'], $data);
				}
				else
				{
					$message = "No such file: {$module}/class.{$classname}.inc.php";
				}

				$this->so->cron_log(array
					(
						'cron'		=> true, // or false for manual...
						'action'	=> $data['action'],
						'message'	=> $message
					)
				);
			}
		}

		public function delete($id)
		{
			$values = $this->read_single($id);
			$location	= phpgw::get_var('location');
			$job_id = "property{$location}::{$values['location_item_id']}::{$values['attrib_id']}";
			$job = execMethod('phpgwapi.asyncservice.cancel_timer', $job_id);

			return $this->so->delete($id);
		}

		public function get_rpt_type_list($selected='')
		{
			$rpt_type = array
			(
				0	=> 'None',
				1	=> 'Daily',
				2	=> 'Weekly',
				3	=> 'Monthly (by day)',
				4	=> 'Monthly (by date)',
				5	=> 'Yearly'
			);


			return $this->sbox->getArrayItem('values[repeat_type]', $selected, $rpt_type);
		}

		public function get_rpt_day_list($selected=array())
		{
			$rpt_day = array
			(
				1		=> 'Sunday',
				2		=> 'Monday',
				4		=> 'Tuesday',
				8		=> 'Wednesday',
				16		=> 'Thursday',
				32		=> 'Friday',
				64		=> 'Saturday'
			);

			$title = lang('(for weekly)');
			$i = 0; $boxes = '';
			foreach ($rpt_day as $mask => $name)
			{
				$boxes .= '<input type="checkbox" title = "' . $title . '"name="values[repeat_day][]" value="'.$mask.'"'.(isset($selected[$mask]) && $selected[$mask] ? ' checked' : '').'></input> '.lang($name)."\n";
				if (++$i == 5) $boxes .= '<br />';
			}
			return $boxes;
		}

		public function get_responsible($selected = '')
		{
			$responsible = CreateObject('property.soresponsible');
			
			$location = phpgw::get_var('location');
			$values = $responsible->read_type(array('start' => 0, 'query' =>'', 'sort' => '',
												'order' => '', 'location' => $location, 'allrows'=>true,
												'filter' => ''));

			$list = array(0 => lang('none'));
			foreach($values as $entry)
			{
				$list[$entry['id']] = $entry['name'];
			}

			return $this->sbox->getArrayItem('values[responsible]', $selected, $list, true);
		}

		public function get_action($selected = '')
		{
/*			$action_object					= CreateObject('property.socategory');
			$action_object->get_location_info('event_action',false);
			$values					= $action_object->read(array('allrows'=> true));
*/
			$list = array(0 => lang('none'));
/*
			foreach($values as $entry)
			{
				$list[$entry['id']] = $entry['name'];
			}
*/
			$list = array_merge($list,$this->event_functions);

			return $this->sbox->getArrayItem('values[action]', $selected, $list, true);
		}
		
		protected function send_sms()
		{
			$data = array
			(
				'p_num_text'	=> 'xxxxxxxx',//number
				'message'		=> 'dette er en melding'
			);

			execMethod('sms.bosms.send_sms', $data);
		}
	}
