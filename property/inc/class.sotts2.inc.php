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
	* @subpackage helpdesk
 	* @version $Id: class.sotts2.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sotts2
	{
		var $acl_location;
		
		function property_sotts2()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bo 		= CreateObject('property.botts');
			$this->historylog	= CreateObject('property.historylog','tts');
			$this->config		= CreateObject('phpgwapi.config');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;
		}

		function update_status($ticket,$id='')
		{
			// DB Content is fresher than http posted value.
			$this->db->query("select * from fm_tts_tickets where id='$id'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_status  		= $this->db->f('status');

			$this->db->transaction_begin();

			/*
			** phpgw_fm_tts_append.append_type - Defs
			** R - Reopen ticket
			** X - Ticket closed
			** O - Ticket opened
			** C - Comment appended
			** A - Ticket assignment
			** G - Ticket group assignment
			** P - Priority change
			** T - Category change
			** S - Subject change
			** B - Billing rate
			** H - Billing hours
			** F - finnish date
			*/

			if ($old_status != $ticket['status'])
			{
				$fields_updated = True;
				if($old_status=='X')
				{
					$this->historylog->add('R',$id,$ticket['status'],$old_status);

					$this->db->query("update fm_tts_tickets set status='O' where id='$id'",__LINE__,__FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'],$id,$ticket['status'],$old_status);

					$this->db->query("update fm_tts_tickets set status='"
					. $ticket['status'] . "' where id='$id'",__LINE__,__FILE__);
				}
			}

			$this->db->transaction_commit();

			if ($fields_updated)
			{
				$this->config->read_repository();

				if (isset($this->config->config_data['mailnotification']) && $this->config->config_data['mailnotification'])
				{
					$receipt=$this->bo->mail_ticket($id,$fields_updated,'',$location_code);

				}

				$receipt['message'][]= array('msg' => lang('Ticket %1 has been updated',$id));
			}

			return $receipt;

		}

		function update_ticket($ticket,$id='')
		{
			// DB Content is fresher than http posted value.
			$this->db->query("select * from fm_tts_tickets where id='$id'",__LINE__,__FILE__);
			$this->db->next_record();


			$location_code 	= $this->db->f('location_code');
			$oldfinnish_date 	= $this->db->f('finnish_date');
			$oldfinnish_date2 	= $this->db->f('finnish_date2');
			$oldassigned 		= $this->db->f('assignedto');
			$oldgroup_id 		= $this->db->f('group_id');
			$oldpriority 		= $this->db->f('priority');
			$oldcat_id 			= $this->db->f('cat_id');
			$old_status  		= $this->db->f('status');
		//	$old_billable_hours	= $this->db->f('billable_hours');
		//	$old_billable_rate	= $this->db->f('billable_rate');
			$old_subject		= $this->db->f('subject');
			if($oldcat_id ==0){$oldcat_id ='';}
			if($oldassigned ==0){$oldassigned ='';}
			if($oldgroup_id ==0){$oldgroup_id ='';}

			// Figure out and last note

			$history_values = $this->historylog->return_array(array(),array('C'),'history_timestamp','DESC',$id);
			$old_note = $history_values[0]['new_value'];

			if(!$old_note)
			{
				$old_note = $this->db->f('details');
			}


			$this->db->transaction_begin();

			/*
			** phpgw_fm_tts_append.append_type - Defs
			** R - Reopen ticket
			** X - Ticket closed
			** O - Ticket opened
			** C - Comment appended
			** A - Ticket assignment
			** G - Ticket group assignment
			** P - Priority change
			** T - Category change
			** S - Subject change
			** B - Billing rate
			** H - Billing hours
			** F - finnish date
			*/

			$finnish_date	= (isset($ticket['finnish_date'])?$this->bocommon->date_to_timestamp($ticket['finnish_date']):'');

			if ($oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date']):
			{
				$this->db->query("update fm_tts_tickets set finnish_date2='" . $finnish_date
					. "' where id='$id'",__LINE__,__FILE__);
			}
			elseif(!$oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date']):
			{
				$this->db->query("update fm_tts_tickets set finnish_date='" . $finnish_date
					. "' where id='$id'",__LINE__,__FILE__);
			}
			endif;

			if($oldfinnish_date2>0)
			{
				$oldfinnish_date = $oldfinnish_date2;
			}
			if(isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				if ($oldfinnish_date != $finnish_date)
				{
					$fields_updated = True;
					$this->historylog->add('F',$id,$finnish_date,$oldfinnish_date);
				}
			}

			if ($old_status != $ticket['status'])
			{
				$fields_updated = True;
				if($old_status=='X')
				{
					$this->historylog->add('R',$id,$ticket['status'],$old_status);

					$this->db->query("update fm_tts_tickets set status='O' where id='$id'",__LINE__,__FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'],$id,$ticket['status'],$old_status);

					$this->db->query("update fm_tts_tickets set status='"
					. $ticket['status'] . "' where id='$id'",__LINE__,__FILE__);
				}
			}

			if (($oldassigned != $ticket['assignedto']) && $ticket['assignedto'] != 'ignore')
			{
				$fields_updated = True;

				$value_set=array('assignedto'	=> $ticket['assignedto']);
				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('A',$id,$ticket['assignedto'],$oldassigned);
			}

			if (($oldgroup_id != $ticket['group_id']) && $ticket['group_id'] != 'ignore')
			{
				$fields_updated = True;

				$value_set=array('group_id'	=> $ticket['group_id']);
				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('G',$id,$ticket['group_id'],$oldgroup_id);
			}

			if ($oldpriority != $ticket['priority'])
			{
				$fields_updated = True;
				$this->db->query("update fm_tts_tickets set priority='" . $ticket['priority']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('P',$id,$ticket['priority'],$oldpriority);
			}

			if (($oldcat_id != $ticket['cat_id']) && $ticket['cat_id'] != 'ignore')
			{
				$fields_updated = True;
				$this->db->query("update fm_tts_tickets set cat_id='" . $ticket['cat_id']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('T',$id,$ticket['cat_id'],$oldcat_id);
			}

	/*		if ($old_billable_hours != $ticket['billable_hours'])
			{
				$fields_updated = True;
				$this->db->query("update fm_tts_tickets set billable_hours='" . $ticket['billable_hours']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('H',$id,$ticket['billable_hours'],$old_billable_hours);
			}

			if ($old_billable_rate != $ticket['billable_rate'])
			{
				$fields_updated = True;
				$this->db->query("update fm_tts_tickets set billable_rate='" . $ticket['billable_rate']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('B',$id,$ticket['billable_rate'],$old_billable_rate);
			}
	*/
			if ($old_subject != $ticket['subject'])
			{
				$this->db->query("update fm_tts_tickets set subject='" . $ticket['subject']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('S',$id,$ticket['subject'],$old_subject);
				$receipt['message'][]= array('msg' => lang('Subject has been updated'));
			}

			if (($old_note != $ticket['note']) && $ticket['note'])
			{
				$fields_updated = True;
				$this->historylog->add('C',$id,$this->db->db_addslashes($ticket['note']),$old_note);
			}

			$this->db->transaction_commit();

			if (isset($fields_updated))
			{
				$this->config->read_repository();

				if (isset($this->config->config_data['mailnotification']) && $ticket['send_mail'])
				{
					$receipt=$this->bo->mail_ticket($id,$fields_updated,'',$location_code);

				}

				$receipt['message'][]= array('msg' => lang('Ticket has been updated'));

				$soadmin_custom = CreateObject('property.soadmin_custom');
				$custom_functions = $soadmin_custom->read(array('acl_location' => $this->acl_location,'allrows'=>True));

				if (isSet($custom_functions) AND is_array($custom_functions))
				{
					foreach($custom_functions as $entry)
					{
						if (is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']) && $entry['active'])
						include (PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']);
					}
				}

			}
			return (isset($receipt)?$receipt:'');
		}
	}
?>
