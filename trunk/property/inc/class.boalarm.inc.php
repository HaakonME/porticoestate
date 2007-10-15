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
 	* @version $Id: class.boalarm.inc.php,v 1.15 2007/10/14 12:18:52 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boalarm
	{

		var $public_functions = array
		(
			'send_alarm' => True
		);

		function property_boalarm()
		{

			$GLOBALS['phpgw_info']['flags']['currentapp']	=	'property';
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			if (!is_object($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
			$this->async = &$GLOBALS['phpgw']->asyncservice;
			$this->so				= CreateObject($this->currentapp.'.soalarm');
			$this->bocommon				= CreateObject($this->currentapp.'.bocommon');
			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= get_var('start',array('POST','GET'));
			$query	= get_var('query',array('POST','GET'));
			$sort	= get_var('sort',array('POST','GET'));
			$order	= get_var('order',array('POST','GET'));
			$filter	= get_var('filter',array('POST','GET'));
			$cat_id	= get_var('cat_id',array('POST','GET'));
			$method_id	= get_var('method_id',array('POST','GET'));
			$allrows			= get_var('allrows',array('POST','GET'));

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(!empty($filter))
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
			if(isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			else
			{
				unset($this->cat_id);
			}
			if(isset($method_id) && !empty($method_id))
			{
				$this->method_id = $method_id;
			}
			else
			{
				unset($this->method_id);
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','owner',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','owner');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->method_id	= $data['method_id'];
		}


		function select_method_list($selected='')
		{
			$list = $this->so->select_method_list();
			$list = $this->bocommon->select_list($selected,$list);
			return $list;
		}

		function read_single_method($id)
		{
			return $this->so->read_single_method($id);
		}

		function read()
		{
			$jobs = $this->so->read(array(id=>'%','start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'allrows'=>$this->allrows));
			$this->total_records	= $this->so->total_records;
			return $jobs;
		}

		/*!
		@function read_alarms
		@abstract read the alarms of a calendar-event specified by $cal_id
		@returns array of alarms with alarm-id as key
		@note the alarm-id is a string of 'cal:'.$cal_id.':'.$alarm_nr, it is used as the job-id too
		*/
		function read_alarms($type='',$input_id,$text='')
		{
			$alarms = array();

			if ($jobs = $this->async->read($type . ':'.intval($input_id).':%'))
			{
				foreach($jobs as $id => $job)
				{
					$alarm				= $job['data'];	// text, enabled
					$alarm['alarm_id']	= $id;
					$alarm['time']		= $GLOBALS['phpgw']->common->show_date($job['next']);
					$alarm['user']		= $GLOBALS['phpgw']->accounts->id2name($alarm['owner']);
					$alarm['text']		= $text;

					$alarms[] = $alarm;
				}
			}
			return $alarms;
		}

		/*!
		@function read_alarm
		@abstract read a single alarm specified by it's $id
		@returns array with data of the alarm
		@note the alarm-id is a string of 'cal:'.$cal_id.':'.$alarm_nr, it is used as the job-id too
		*/
		function read_alarm($alarm_type,$id)
		{
			if (!($jobs = $this->async->read($id)))
			{
				return False;
			}

			$alarm         = $jobs[$id]['data'];	// text, enabled
			$alarm['id']   = $id;
			$alarm['time'] = $jobs[$id]['next'];
			$alarm['times'] = $jobs[$id]['times'];

//			echo "<p>read_alarm('$id')="; print_r($alarm); echo "</p>\n";
			return $alarm;
		}



		/*!
		@function enable
		@abstract enable or disable one or more alarms identified by its ids
		@syntax enable($ids,$enable=True)
		@param $ids array with alarm ids as keys (!)
		@returns the number of alarms enabled or -1 for insuficent permission to do so
		@note Not found alarms or insuficent perms stop the enableing of multiple alarms
		*/
		function enable_alarm($alarm_type,$alarms,$enable=True)
		{
			$enabled = 0;
			foreach ($alarms as $id => $field)
			{
				$temp = explode(':',$id);
				$alarm_type = $temp[0];

				if (!($alarm = $this->read_alarm($alarm_type,$id)))
				{
					return 0;	// alarm not found
				}
				if (!$alarm['enabled'] == !$enable)
				{
					continue;	// nothing to do
				}
/*				if ($enable && !$this->check_perms(PHPGW_ACL_SETALARM,$alarm['owner']) ||
					!$enable && !$this->check_perms(PHPGW_ACL_DELETEALARM,$alarm['owner']))
				{
					return -1;
				}
*/
				$alarm['enabled'] = intval(!$alarm['enabled']);

				if ($this->save_alarm($alarm_type,$alarm['event_id'],$alarm))
				{
					++$enabled;
				}
			}
			return $enabled;
		}


		/*!
		@function save_alarm
		@abstract saves a new or updated alarm
		@syntax save_alarm($cal_id,$alarm,$id=False)
		@param $cal_id Id of the calendar-entry
		@param $alarm array with fields: text, owner, enabled, ..
		*/
		function save_alarm($alarm_type,$event_id,$alarm,$method='')
		{
			if(!$method)
			{
				$method = $this->currentapp .'.boalarm.send_alarm';
			}
//			echo "<p>save_alarm(event_id=$event_id, alarm="; print_r($alarm); echo ")</p>\n";

			if (!$alarm['id'])
			{
				$alarms = $this->read_alarms($alarm_type,$event_id);	// find a free alarm#
				$n = count($alarms);
				do
				{
					$id = $alarm_type .':'.intval($event_id).':'.$n;
					++$n;
				}
				while (@isset($alarms[$id]));

				$alarm[$alarm_type.'_id'] = $event_id;		// we need the back-reference

				$alarm['id'] = $id;

				if (!$this->async->set_timer($alarm['times'],$id,$method,$alarm))
				{
					return False;
				}
				return $id;
			}
			else
			{
				$this->async->cancel_timer($alarm['id']);
				$this->async->set_timer($alarm['times'],$alarm['id'],$method,$alarm);
				return $alarm['id'];
			}
		}

		/*!
		@function add_alarm
		@abstract adds a new alarm to an event
		@syntax add(&$event,$time,$login_id)
		@param &$event event to add the alarm too
		@param $time for the alarm in sec before the starttime of the event
		@param $login_id user to alarm
		@returns the alarm or False
		*/
		function add_alarm($alarm_type,&$event,$time,$owner)
		{
/*			if (!$this->check_perms(PHPGW_ACL_SETALARM,$owner) || !($cal_id = $event['id']))
			{
				return False;
			}
*/
			if(!$owner>0)
			{
				$receipt['error'][]=array('msg'=>lang('No user selected'));
				return	$receipt;
			}

			$alarm = Array(
				'time'    => ($event['alarm_date'] - $time), //($etime=$this->bo->maketime($event['start'])) - $time,
				'offset'  => $time,
				'owner'   => $owner,
				'enabled' => 1,
				'event_id' => $event['id'],
				'event_name' => $event['name']
			);

			$alarm['times'] = $alarm['time'];
			$alarm['id'] = $this->save_alarm($alarm_type,$event['id'],$alarm);

			$event['alarm'][$alarm['id']] = $alarm;

			return $alarm;
		}


		/*!
		@function delete
		@abstract delete one or more alarms identified by its ids
		@syntax delete($ids)
		@param $ids array with alarm ids as keys (!)
		@returns the number of alarms deleted or -1 for insuficent permission to do so
		@note Not found alarms or insuficent perms stop the deleting of multiple alarms
		*/
		function delete_alarm($alarm_type,$alarms)
		{
			$deleted = 0;
			foreach ($alarms as $id => $field)
			{
				if (!($alarm = $this->read_alarm($alarm_type,$id)))
				{
					return 0;	// alarm not found
				}
/*				if (!$this->check_perms(PHPGW_ACL_DELETEALARM,$alarm['owner']))
				{
					return -1;
				}
*/
				if ($this->async->cancel_timer($id))
				{
					++$deleted;
				}
			}
			return $deleted;
		}


		function test_cron()
		{
			$this->async->check_run('crontab');
		}


		function send_alarm($alarm)
		{

	//		echo "<p>boalarm::send_alarm("; print_r($alarm); echo ")</p>\n";
			$GLOBALS['phpgw_info']['user']['account_id'] = $this->owner = $alarm['owner'];

			if (!$alarm['enabled'] || !$alarm['owner'])
			{
				return False;	// event not found
			}

			$this->config		= CreateObject('phpgwapi.config');
			$this->config->read_repository();
			$this->send			= CreateObject('phpgwapi.send');

			$members = array();

			// build subject
			$subject = lang('Alarm').': '.$alarm['event_name'];

			$prefs_user = $this->bocommon->create_preferences($this->currentapp,$alarm['owner']);

			$from_address=$prefs_user['email'];

	//-----------from--------


			$current_user_id=$GLOBALS['phpgw_info']['user']['account_id'];

			$current_user_firstname	= 'FM';

			$current_user_lastname	= 'System';

			$current_user_name= $user_firstname . " " .$user_lastname ;

			$current_prefs_user = $this->bocommon->create_preferences($this->currentapp,$alarm['owner']);
			$current_user_address=$current_prefs_user['email'];

			$headers = "Return-Path: <". $current_user_address .">\r\n";
			$headers .= "From: " . $current_user_name . "<" . $current_user_address .">\r\n";
//			$headers .= "Bcc: " . $current_user_name . "<" . $current_user_address .">\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "MIME-Version: 1.0\r\n";

	//-----------from--------
		// build body
			$body  = '';
			$body .= lang('Alarm').' #'.$alarm['event_id']."\n";
			$body .= lang('Name').': '.$alarm['event_name']."\n";
			if(!is_array($alarm['time']))
			{
				$body .= lang('Deadline').': '. $GLOBALS['phpgw']->common->show_date(($alarm['time']+$alarm['offset'])) ."\n";
			}
			$body .= lang('Assigned To').': '.$GLOBALS['phpgw']->accounts->id2name($alarm['owner'])."\n";

			// add assigned to recipients
			$members[] = array('account_id' => $alarm['owner'], 'account_name' => $GLOBALS['phpgw']->accounts->id2name($alarm['owner']));

			$error = Array();
			$toarray = Array();
			$i=0;
			for ($i=0;$i<count($members);$i++)
			{
				if ($members[$i]['account_id'])
				{
					$prefs = $this->bocommon->create_preferences($this->currentapp,$members[$i]['account_id']);
					if (strlen($prefs['email'])> (strlen($members[$i]['account_name'])+1))
					{
						$toarray[$prefs['email']] = $prefs['email'];
					}
					else
					{
						$receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
						$receipt['error'][] = array('msg'=>lang('This user has not defined an email address !') . ' : ' . $members[$i]['account_name']);
					}
				}
			}

			if(count($toarray) > 1)
			{
				$to = implode(',',$toarray);
			}
			else
			{
				$to = current($toarray);
			}

			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				$rc = $this->send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc,$current_user_address,$current_user_name,'txt');
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
			}

		//	$rc=1;
			if (!$rc)
			{
				$receipt['error'][] = array('msg'=> lang('Your message could not be sent by mail!'));
				$receipt['error'][] = array('msg'=> lang('The mail server returned'));
				$receipt['error'][] = array('msg'=> 'From :' . $current_user_name . '<' . $current_user_address .'>');
				$receipt['error'][] = array('msg'=> 'to: '.$to);
				$receipt['error'][] = array('msg'=> 'subject: '.$subject);
				$receipt['error'][] = array('msg'=> $body );
	//			$receipt['error'][] = array('msg'=> 'cc: ' . $cc);
	//			$receipt['error'][] = array('msg'=> 'bcc: '.$bcc);
				$receipt['error'][] = array('msg'=> 'group: '.$group_name);
				$receipt['error'][] = array('msg'=> 'err_code: '.$this->send->err['code']);
				$receipt['error'][] = array('msg'=> 'err_msg: '. htmlspecialchars($this->send->err['msg']));
				$receipt['error'][] = array('msg'=> 'err_desc: '. $GLOBALS['phpgw']->err['desc']);
			}

//_debug_array($receipt);
//			return $receipt;
		}

	}
?>
