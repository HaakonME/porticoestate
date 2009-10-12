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

	class property_soevent
	{

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db 			= & $GLOBALS['phpgw']->db;
			$this->_join		= & $this->_db->loin;
			$this->_like		= & $this->_db->like;
		}

		function read($data)
		{

			if(!isset($data['location_id']) || !$data['location_id'])
			{
				if(!isset($data['appname']) || !$data['appname'] || !isset($data['location']) || !$data['location'])
				{
					throw new Exception("property_soevent::read - Missing location info in input");
				}
				$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			}
			else
			{
				$location_id = $data['location_id'];
			}

			$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			if(!isset($data['location_item_id']) || !$data['location_item_id'])
			{
				throw new Exception("property_soevent::read - Missing location_item_id in input");
			}

			$location_item_id	= $data['location_item_id'];

			$events = array();

			$table = 'fm_event';

			if($query)
			{
				$query = $this->_db->db_addslashes($query);

				$querymethod = " WHERE id $this->_like '%$query%' OR descr $this->_like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->_db->next_record())
			{
				$events[] = array
				(
					'id'	=> $this->_db->f('id'),
					'descr'	=> $this->_db->f('descr')
				);
			}
			return $events;
		}

		function read_single($id)
		{
			$values = array();

			$table = 'fm_event';

			$sql = "SELECT * FROM $table WHERE id='{$id}'";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$start_date		= $this->_db->f('start_date');
				$end_date		= $this->_db->f('end_date');				
				$values = array
				(
					'id'				=> $this->_db->f('id'),
					'descr'				=> $this->_db->f('descr', true),
					'start_date'		=> $start_date,
					'responsible'		=> $this->_db->f('responsible_id'),
					'action'			=> $this->_db->f('action_id'),
					'end_date'			=> $end_date,
					'repeat_type'		=> $this->_db->f('repeat_type'),
					'rpt_day'			=> $this->_db->f('repeat_day'),
					'repeat_interval'	=> $this->_db->f('repeat_interval'),
					'enabled'			=> $this->_db->f('enabled'),
					'user_id'			=> $this->_db->f('user_id'),
					'entry_date'		=> $this->_db->f('entry_date'),
					'modified_date'		=> $this->_db->f('modified_date'),
					'location_id'		=> $this->_db->f('location_id'),
					'location_item_id'	=> $this->_db->f('location_item_id'),
					'attrib_id'			=> $this->_db->f('attrib_id')
				);

				$values['start']['month']	= date('m',$start_date);
				$values['start']['mday']	= date('d',$start_date);
				$values['start']['year']	= date('Y',$start_date);
				$values['start']['hour']	= date('G',$start_date);
				$values['start']['min']		= date('i',$start_date);
				$values['start']['sec']		= date('s',$start_date);

				$values['end']['month']	= $end_date ? date('m',$end_date) : 0;
				$values['end']['mday']	= $end_date ? date('d',$end_date) : 0;
				$values['end']['year']	= $end_date ? date('Y',$end_date) : 0;
				$values['end']['hour']	= $end_date ? date('G',$end_date) : 0;
				$values['end']['min']	= $end_date ? date('i',$end_date) : 0;
				$values['end']['sec']	= $end_date ? date('s',$end_date) : 0;

				$sql = "SELECT * FROM fm_event_exception WHERE event_id ='{$id}'";

				$this->_db->query($sql,__LINE__,__FILE__);
				while ($this->_db->next_record())
				{
					$values['repeat_exception'][] = $this->_db->f('exception_time');
				}
			}

			return $values;
		}

		function add($data)
		{
			$receipt = array();
			$table = 'fm_event';

			$data['descr'] = $this->_db->db_addslashes($data['descr']);

			$cols = array
			(
				'location_id',
				'location_item_id',
				'attrib_id',
				'descr',
				'start_date',
				'responsible_id',
				'action_id',
				'end_date',
				'repeat_type',
				'repeat_day',
				'repeat_interval',
				'enabled',
				'user_id',
				'entry_date'
			);

			$repeat_day = 0;
			if(isset($data['repeat_day']) && is_array($data['repeat_day']))
			{
				foreach ($data['repeat_day'] as $day)
				{
					$repeat_day |= $day;
				}
			}

			$vals = array
			(
				$data['location_id'],
				$data['item_id'],
				$data['attrib_id'],
				$data['descr'],				
				$data['start_date'],
				$data['responsible'],
				$data['action'],
				$data['end_date'],
				$data['repeat_type'],				
				$repeat_day,
				$data['repeat_interval'],
				$data['enabled'],
				$this->account,
				time()
			);

			$this->_db->transaction_begin();

			$id = $this->_db->next_id($table);
			$cols[] = 'id';
			$vals[] = $id;

			$cols	= implode(",", $cols);
			$vals	= $this->_db->validate_insert($vals);

			$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$vals})",__LINE__,__FILE__);

			if($this->_db->transaction_commit())
			{
				$receipt['id'] = $id;
				$receipt['message'][] = array('msg' => lang('event has been saved'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('event has not been saved'));
			}
			return $receipt;
		}

		function edit($data)
		{
			$receipt = array();
			$table = 'fm_event';

			$repeat_day = 0;
			if(isset($data['repeat_day']) && is_array($data['repeat_day']))
			{
				foreach ($data['repeat_day'] as $day)
				{
					$repeat_day |= $day;
				}
			}

			$value_set = array
			(
				'descr' 			=> $this->_db->db_addslashes($data['descr']),
				'start_date'		=> $data['start_date'],
				'responsible_id'	=> $data['responsible'],
				'action_id'			=> $data['action'],
				'end_date'			=> $data['end_date'],
				'repeat_type'		=> $data['repeat_type'],
				'repeat_day'		=> $repeat_day,
				'repeat_interval'	=> $data['repeat_interval'],
				'enabled'			=> $data['enabled'],
				'modified_date'		=> time()
			);

			
			$value_set	= $this->_db->validate_update($value_set);

			$this->_db->transaction_begin();
			$this->_db->query("UPDATE $table SET {$value_set} WHERE id='" . $data['id']. "'",__LINE__,__FILE__);

			$receipt['id'] = $data['id'];
			if($this->_db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('event has been updated'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('event has not been updated'));
			}
			return $receipt;
		}

		function check_event_exception($event_id, $time)
		{
			$event_id = (int) $event_id;
			$time = (int) $time;
			$sql = "SELECT event_id FROM fm_event_exception WHERE event_id = {$event_id} AND exception_time = {$time}";
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_db->next_record();
			return !!$this->_db->f('id');
		}

		function cron_log($data)
		{
			$insert_values= array(
				!!$data['cron'], // or manual...
				date($this->_db->datetime_format()),
				$data['action'],
				$data['message']
				);

			$insert_values	= $this->_db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->_db->query($sql,__LINE__,__FILE__);
		}

		function delete($id)
		{
			$receipt = array();
			$table = 'fm_event';
			$this->_db->transaction_begin();
			$ret = !!$this->_db->query("DELETE FROM $table WHERE id='{$id}'",__LINE__,__FILE__);
			if($this->_db->transaction_commit())
			{
				return $ret;
			}
			return false;
		}

		//FIXME adapt from calendar	
		function list_events($data = array())
		{
			$startYear		= $data['syear'];
			$startMonth		= $data['smonth'];
			$startDay		= $data['sday'];
			$endYear		= $data['eyear'] ? $data['eyear'] : 0;
			$endMonth		= $data['emonth'] ? $data['emonth'] : 0;
			$endDay			= $data['eday'] ? $data['eday'] : 0;
			$extra			= $data['eday'] ? $data['eday'] : '';
			$tz_offset		= $data['tz_offset'] ? $data['tz_offset'] : 0;
			$owner_id		= $data['owner_id'] ? $data['owner_id'] : 0;
			$location_id	= $data['location_id'];

			if(!$startYear || !$startMonth || !$startDay || ! $location_id)
			{
				throw new Exception("property_soevent::list_events - Missing start date info");
			}

			$datetime = mktime(0,0,0,$startMonth,$startDay,$startYear) - $tz_offset;
		
			$sql = ' WHERE (fm_event.user_id in (';
			if($owner_id)
			{
				$sql .= implode(',',$owner_id);
			}
			else
			{
				$sql .= $this->account;
			}
			$member_groups = $GLOBALS['phpgw']->accounts->membership($this->account);
			@reset($member_groups);
			foreach ($member_groups as $key => $group_info)
			{
				$member[] = $group_info->id;		
			}

			@reset($member);
	//		$sql .= ','.implode(',',$member);
			$sql .= ')) ';

			$sql .= 'AND ( ( (fm_event.start_date >= '.$datetime.') ';

			if($endYear != 0 && $endMonth != 0 && $endDay != 0)
			{
				$edatetime = mktime(23,59,59,intval($endMonth),intval($endDay),intval($endYear)) - $tz_offset;
				$sql .= 'AND (fm_event.end_date <= '.$edatetime.') ) '
					. 'OR ( (fm_event.start_date <= '.$datetime.') '
					. 'AND (fm_event.end_date >= '.$edatetime.') ) '
					. 'OR ( (fm_event.start_date >= '.$datetime.') '
					. 'AND (fm_event.start_date <= '.$edatetime.') '
					. 'AND (fm_event.end_date >= '.$edatetime.') ) '
					. 'OR ( (fm_event.start_date <= '.$datetime.') '
					. 'AND (fm_event.end_date >= '.$datetime.') '
					. 'AND (fm_event.end_date <= '.$edatetime.') ';
			}
			$sql .= ") ) AND location_id = {$location_id}";

			$order_by = ' ORDER BY fm_event.start_date ASC, fm_event.end_date ASC';

			return $this->get_event_ids(False,$sql.$extra.$order_by);
		}

		function list_repeated_events($data = array())
		{
			$syear			= $data['syear'];
			$smonth			= $data['smonth'];
			$sday			= $data['sday'];
			$eyear			= $data['eyear'];
			$emonth			= $data['emonth'];
			$eday			= $data['eday'];
			$owner_id		= $data['owner_id'] ? $data['owner_id'] : 0;
			$location_id	= $data['location_id'];

			if(!$syear || !$smonth || !$sday || !$eyear || !$emonth || !$eday || ! $location_id)
			{
				throw new Exception("property_soevent::list_repeated_events - Missing date info");
			}

			$user_timezone = phpgwapi_datetime::user_timezone();

			$starttime = mktime(0,0,0,$smonth,$sday,$syear) - $user_timezone;
			$endtime = mktime(23,59,59,$emonth,$eday,$eyear) - $user_timezone;
			$sql = "(fm_event.location_id = {$location_id})"
				. ' AND ((fm_event.end_date >= '.$starttime.') OR (fm_event.end_date=0))'
				. ' ORDER BY fm_event.start_date ASC, fm_event.end_date ASC';

			return $this->get_event_ids(true, $sql);
		}

		function get_event_ids($search_repeats = false, $extra = '')
		{
	//		$where = 'WHERE';
			$repeat = '';
			if($search_repeats)
			{
				$repeat = 'WHERE (fm_event.repeat_type > 0) ';
				$where = 'AND';
			}

			$sql = 'SELECT DISTINCT fm_event.id,'
					. ' fm_event.start_date,fm_event.end_date'
					. " FROM fm_event {$repeat} {$where} {$extra}";

			$this->_db->query($sql,__LINE__,__FILE__);

			$retval = array();
			if($this->_db->num_rows() == 0)
			{
				return $retval;
			}
	
			while($this->_db->next_record())
			{
				$retval[] = intval($this->_db->f('id'));
			}
			if($this->debug)
			{
				echo "Records found!<br />\n";
			}
			return $retval;
		}


	}
