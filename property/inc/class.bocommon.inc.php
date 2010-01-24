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
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bocommon
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $district_id;


		var $public_functions = array
		(
			'select_part_of_town'	=> true,
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function __construct()
		{
//_debug_array($bt = debug_backtrace());
			$this->socommon			= CreateObject('property.socommon');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			if (!isset($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
			$this->async = &$GLOBALS['phpgw']->asyncservice;

			$this->join		= $this->socommon->join;
			$this->left_join	= $this->socommon->left_join;
			$this->like		= $this->socommon->like;

			switch($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mssql':
					$this->dateformat 		= "M d Y";
					$this->datetimeformat 	= "M d Y g:iA";
					break;
				case 'mysql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
				case 'pgsql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
//					$this->dateformat 		= "F j, Y";
//					$this->datetimeformat 	= "F j, Y g:iA";
					break;
				case 'postgres':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
			}

		}

		function check_perms($rights, $required)
		{
			return ($rights & $required);
		}

		function create_preferences($app='',$user_id='')
		{
			return $this->socommon->create_preferences($app,$user_id);
		}

		function get_lookup_entity($location='')
		{
			return $this->socommon->get_lookup_entity($location);
		}

		function get_start_entity($location='')
		{
			return $this->socommon->get_start_entity($location);
		}

		function msgbox_data($receipt)
		{
			$msgbox_data_error	 = array();
			$msgbox_data_message = array();
			if (isSet($receipt['error']) AND is_array($receipt['error']))
			{
				foreach($receipt['error'] as $dummy => $error)
				{
					$msgbox_data_error[$error['msg']] = false;
				}
			}

			if (isSet($receipt['message']) AND is_array($receipt['message']))
			{
				foreach($receipt['message'] as $dummy => $message)
				{
					$msgbox_data_message[$message['msg']] = true;
				}
			}

			$msgbox_data = array_merge($msgbox_data_error, $msgbox_data_message);

			return $msgbox_data;
		}

		function moneyformat($amount)
		{
			if ($GLOBALS['phpgw_info']['server']['db_type']=='mssql')
			{
				$moneyformat	= "CONVERT(MONEY,"."'$amount'".",0)";
			}
			else
			{
				$moneyformat	= "'" . $amount . "'";
			}

			return $moneyformat;
		}


		function date_array($datestr = '')
		{
			if(!$datestr)
			{
				return false;
			}
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$fields = split('[./-]',$datestr);
			foreach(split('[./-]',$dateformat) as $n => $field)
			{
				$date[$field] = intval($fields[$n]);

				if($field == 'M')
				{
					for($i=1; $i <=12; $i++)
					{
						if(date('M',mktime(0,0,0,$i,1,2000)) == $fields[$n])
						{
							$date['m'] = $i;
						}
					}
				}
			}

			$ret = array(
				'year'  => $date['Y'],
				'month' => $date['m'],
				'day'   => $date['d']
			);
			return $ret;
		}

		function date_to_timestamp($date='')
		{
			if (!$date)
			{
				return false;
			}

			$date_array	= $this->date_array($date);
			$date	= mktime (8,0,0,$date_array['month'],$date_array['day'],$date_array['year']);

			return $date;
		}

		function select_multi_list($selected='',$input_list)
		{
			$j=0;
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					$output_list[$j]['id'] = $entry['id'];
					$output_list[$j]['name'] = $entry['name'];

					if(isset($selected) && is_array($selected))
					{
						for ($i=0;$i<count($selected);$i++)
						{
							if($selected[$i] == $entry['id'])
							{
								$output_list[$j]['selected'] = 'selected';
							}
						}
					}
					$j++;
				}
			}
			return $output_list;
		}

		function select_list($selected='',$input_list='')
		{
			$entry_list = array();
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					if ($entry['id']==$selected)
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name'],
							'selected'	=> 'selected'
						);
					}
					else
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name'],
						);
					}
				}
				return $entry_list;
			}
		}


		function get_user_list($format='',$selected='',$extra='',$default='',$start='', $sort='', $order='', $query='',$offset='', $enabled = false)
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_filter'));
					break;
			}

			if(!$selected && $default)
			{
				$selected = $default;
			}

			$all_users = array();

			if (is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$all_users[]=array
					(
						'account_id' => $extra_user,
						'account_firstname' => lang($extra_user)
					);
				}
			}

			$accounts = & $GLOBALS['phpgw']->accounts;
			$users = $accounts->get_list('accounts', $start, $sort, $order, $query,$offset);

			unset($accounts);
			if (is_array($users))
			{
				foreach($users as $user)
				{
					if (($enabled && $user->enabled) || !$enabled)
					{
						$all_users[] = array
						(
							'user_id'	=> $user->id,
							'name'		=> $user->__toString(),
						);
					}
				}
			}

			if (count($all_users)>0)
			{
				foreach($all_users as $user)
				{
					$sel_user = '';
					if ($user['user_id'] == $selected)
					{
						$user_list[] = array
						(
							'user_id'	=> $user['user_id'],
							'name'		=> $user['name'],
							'selected'	=> 'selected'
						);
					}
					else
					{
						$user_list[] = array
						(
							'user_id'	=> $user['user_id'],
							'name'		=> $user['name'],
						);

					}
				}
			}
//_debug_array($user_list);
			return $user_list;
		}

		function get_group_list($format='',$selected='',$start='', $sort='', $order='', $query='',$offset='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_filter'));
					break;
			}

			$accounts	= & $GLOBALS['phpgw']->accounts;

			$users = $accounts->get_list('groups', $start, $sort, $order, $query,$offset);
			unset($accounts);
			if (isset($users) AND is_array($users))
			{
				foreach($users as $user)
				{
					$sel_user = '';
					if ($user->id == $selected)
					{
						$sel_user = 'selected';
					}

					$user_list[] = array
					(
						'id'	=> $user->id,
						'name'		=> $user->firstname,
						'selected'	=> $sel_user
					);
				}
			}

			$user_count= count($user_list);
			for ($i=0;$i<$user_count;$i++)
			{
				if ($user_list[$i]['selected'] != 'selected')
				{
					unset($user_list[$i]['selected']);
				}
			}

//_debug_array($user_list);
			return $user_list;
		}


		function get_user_list_right($rights,$selected='',$acl_location='',$extra='',$default='')
		{
			if(!$selected && $default)
			{
				$selected = $default;
			}

			if (!is_array($rights))
			{
				$rights = array($rights);
			}

			if (is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$users_extra[]=array
					(
						'account_lid' 		=> $extra_user,
						'account_firstname'	=> lang($extra_user),
						'account_lastname'	=> ''
					);
				}
			}

			if(!$users = $this->socommon->fm_cache('acl_userlist_'. $rights[0] . '_' . $acl_location))
			{
				$users_gross = array();
				foreach ($rights as $right)
				{
					$users_gross = array_merge($users_gross, $GLOBALS['phpgw']->acl->get_user_list_right($right, $acl_location));
				}
				
				$accounts	= array();
				$users			= array();

				foreach ($users_gross as $entry => $user)
				{

					if( !isset($accounts[$user['account_id']]) )
					{
						$users[] = $user;
					}
					$accounts[$user['account_id']] = true;
				}
				unset($users_gross);
				unset($accounts);

				foreach ($users as $key => $row) 
				{
					$account_lastname[$key]  = $row['account_lastname'];
					$account_firstname[$key] = $row['account_firstname'];
				}

				// Sort the data with account_lastname ascending, account_firstname ascending
				// Add $data as the last parameter, to sort by the common key
				if($users)
				{
					array_multisort($account_lastname, SORT_ASC, $account_firstname, SORT_ASC, $users);
				}

				$this->socommon->fm_cache('acl_userlist_'. $rights[0] . '_' . $acl_location,$users);
			}

			if (isset($users_extra) && is_array($users_extra) && is_array($users))
			{
				$users = array_merge($users_extra, $users);
			}

			$user_list = array();

			foreach ($users as $user)
			{
				if ($user['account_lid']==$selected)
				{
					$user_list[] = array
					(
						'lid'			=> $user['account_lid'],
						'firstname'		=> $user['account_firstname'],
						'lastname'		=> $user['account_lastname'],
						'selected'		=> 'selected'
					);
				}
				else
				{
					$user_list[] = array
					(
						'lid'			=> $user['account_lid'],
						'firstname'		=> $user['account_firstname'],
						'lastname'		=> $user['account_lastname'],
					);
				}
			}
			return $user_list;
		}

		function get_user_list_right2($format='',$right='',$selected='',$acl_location='',$extra='',$default='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_filter'));
					break;
			}

			if(!$selected && $default)
			{
				$selected = $default;
			}

			if (isset($extra) AND is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$users_extra[]=array
					(
						'account_id' => $extra_user,
						'account_firstname' => lang($extra_user)
					);
				}
			}

			if(!$users = $this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location))
			{
				$users = $GLOBALS['phpgw']->acl->get_user_list_right($right, $acl_location);
				$this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location,$users);
			}

			if ((isset($users_extra) && is_array($users_extra)) && is_array($users))
			{
				foreach($users as $users_entry)
				{
					array_push($users_extra,$users_entry);
				}
				$users=$users_extra;
			}

			while (is_array($users) && list(,$user) = each($users))
			{
				$name = (isset($user['account_lastname'])?$user['account_lastname'].' ':'').$user['account_firstname'];
				if ($user['account_id']==$selected)
				{
					$user_list[] = array
					(
						//'user_id'	=> $user['account_id'],
						'id'	=> $user['account_id'],
						'name'		=> $name,
						'selected'	=> 'selected'
					);
				}
				else
				{
					$user_list[] = array
					(
						//'user_id'	=> $user['account_id'],
						'id'	=> $user['account_id'],
						'name'		=> $name
					);
				}
			}

			if(isset($user_list) && is_array($user_list))
			{
				return $user_list;
			}
		}

		function initiate_ui_vendorlookup($data)
		{
//_debug_array($data);

			if( isset($data['type']) && $data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('vendor_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('vendor_form'));
			}

			$vendor['value_vendor_id']		= $data['vendor_id'];
			$vendor['value_vendor_name']		= $data['vendor_name'];

			if(isset($data['vendor_id']) && $data['vendor_id'] && !$data['vendor_name'])
			{
				$contacts	= CreateObject('property.soactor');
				$contacts->role='vendor';
				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single($data['vendor_id'],$vendor_data);
				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$vendor['value_vendor_name']=$attribute['value'];
							break;
						}
					}
				}
				unset($contacts);
			}

			$vendor['vendor_link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor'));
			$vendor['lang_vendor']			= lang('Vendor');
			$vendor['lang_select_vendor_help']	= lang('click this link to select vendor');
			$vendor['lang_vendor_name']		= lang('Vendor Name');

//_debug_array($vendor);
			return $vendor;
		}


		function initiate_ui_contact_lookup($data)
		{
//_debug_array($data);

			$field = $data['field'];
			if( isset($data['type']) && $data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('contact_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('contact_form'));
			}

			$contact['value_contact_id']		= $data['contact_id'];
//			$contact['value_contact_name']		= $data['contact_name'];

			if(isset($data['contact_id']) && $data['contact_id'] && !$data['contact_name'])
			{
				$contacts							= CreateObject('phpgwapi.contacts');
				$contact_data						= $contacts->read_single_entry($data['contact_id'], array('fn','tel_work','email'));
				$contact['value_contact_name']		= $contact_data[0]['fn'];
				$contact['value_contact_email']		= $contact_data[0]['email'];
				$contact['value_contact_tel']		= $contact_data[0]['tel_work'];

				unset($contacts);
			}

			$contact['field']						= $field;
			$contact['contact_link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column' => $field));
			$contact['lang_contact']				= lang('contact');
			$contact['lang_select_contact_help']	= lang('click this link to select');
//_debug_array($contact);
			return $contact;
		}

		function initiate_ui_tenant_lookup($data)
		{
			if($data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_form'));
			}

			$tenant['value_tenant_id']			= $data['tenant_id'];
			$tenant['value_first_name']			= $data['first_name'];
			$tenant['value_last_name']			= $data['last_name'];
			$tenant['tenant_link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.tenant'));
			if($data['role']=='customer')
			{
				$tenant['lang_select_tenant_help']		= lang('click this link to select customer');
				$tenant['lang_tenant']				= lang('Customer');

			}
			else
			{
				$tenant['lang_select_tenant_help']		= lang('click this link to select tenant');
				$tenant['lang_tenant']				= lang('Tenant');
			}


			if($data['tenant_id'] && !$data['tenant_name'])
			{
				$tenant_object	= CreateObject('property.soactor');
				$tenant_object->role = 'tenant';
				$custom 		= createObject('property.custom_fields');
				$tenant_data['attributes'] = $custom->find('property','.tenant', 0, '', 'ASC', 'attrib_sort', true, true);
				$tenant_data	= $tenant_object->read_single($data['tenant_id'],$tenant_data);
				if(is_array($tenant_data['attributes']))
				{
//_debug_array($tenant_data);
					foreach ($tenant_data['attributes'] as $entry)
					{

						if ($entry['name'] == 'first_name')
						{
							$tenant['value_first_name']	= $entry['value'];
						}
						if ($entry['name'] == 'last_name')
						{
							$tenant['value_last_name']	= $entry['value'];
						}
					}
				}
			}

//_debug_array($tenant);
			return $tenant;
		}

		function initiate_ui_budget_account_lookup($data)
		{
			if( isset($data['type']) && $data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('b_account_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('b_account_form'));
			}

			$b_account['value_b_account_id']		= $data['b_account_id'];
			$b_account['value_b_account_name']		= $data['b_account_name'];
			$b_account['b_account_link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.b_account'));
			$b_account['lang_select_b_account_help']	= lang('click this link to select budget account');
			$b_account['lang_b_account']			= lang('Budget account');
			if($data['b_account_id'] && !$data['b_account_name'])
			{
				$b_account_object	= CreateObject('property.sob_account');
				$b_account_data	= $b_account_object->read_single($data['b_account_id']);
				$b_account['value_b_account_name']	= $b_account_data['descr'];
			}
			$b_account['disabled']				= isset($data['disabled']) && $data['disabled'] ? true : false;
//_debug_array($b_account);
			return $b_account;
		}

		function initiate_project_group_lookup($data)
		{
			$project_group = array();

			if( isset($data['type']) && $data['type']=='view')
			{
				if(!isset($data['project_group']) || !$data['project_group'])
				{
					return $project_group;
				}

				$GLOBALS['phpgw']->xslttpl->add_file(array('project_group_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('project_group_form'));
			}

			$project_group['value_project_group']				= $data['project_group'];
			$project_group['value_project_group_descr']			= $data['project_group_descr'];
			$project_group['project_group_url']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.project_group'));
			$project_group['lang_select_project_group_help']	= lang('click to select project group');
			$project_group['lang_project_group']				= lang('project group');
			if($data['project_group'] && (!isset($data['project_group_descr']) || !$data['project_group_descr']))
			{
				$project_group_object				= CreateObject('property.socategory');
				$project_group_object->get_location_info('project_group',false);
				$project_group_data					= $project_group_object->read_single(array('id'=> $data['project_group']));
				$project_group['value_project_group_descr']	= $project_group_data['descr'];
			}

			return $project_group;
		}

		function initiate_ecodimb_lookup($data)
		{
			$ecodimb = array();

			if( isset($data['type']) && $data['type']=='view')
			{
				if(!isset($data['ecodimb']) || !$data['ecodimb'])
				{
					return $ecodimb;
				}

				$GLOBALS['phpgw']->xslttpl->add_file(array('ecodimb_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('ecodimb_form'));
			}

			$ecodimb['value_ecodimb']				= $data['ecodimb'];
			$ecodimb['value_ecodimb_descr']			= $data['ecodimb_descr'];
			$ecodimb['ecodimb_url']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ecodimb'));
			$ecodimb['lang_select_ecodimb_help']	= lang('click to select dimb');
			$ecodimb['lang_ecodimb']				= lang('dimb');
			if($data['ecodimb'] && (!isset($data['ecodimb_descr']) || !$data['ecodimb_descr']))
			{
				$ecodimb_object					= CreateObject('property.socategory');
				$ecodimb_object->get_location_info('dimb',false);
				$ecodimb_data					= $ecodimb_object->read_single(array('id'=> $data['ecodimb']));
				$ecodimb['value_ecodimb_descr']	= $ecodimb_data['descr'];
			}
			$ecodimb['disabled']				= isset($data['disabled']) && $data['disabled'] ? true : false;

			return $ecodimb;
		}


		function initiate_event_lookup($data)
		{
			$event = array();
			$event['name'] = $data['name']; // attribute name
			$event['event_name'] = $data['event_name']; // Human readable description
			if( isset($data['type']) && $data['type']=='view')
			{
				if(!isset($data['event']) || !$data['event'])
				{
//					return $event;
				}

				$GLOBALS['phpgw']->xslttpl->add_file(array('event_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('event_form'));
			}

			// If the record is not saved - issue a warning
			if(isset($data['item_id']) || $data['item_id'])
			{
				$event['item_id'] = $data['item_id'];
			}
			else if(isset($data['location_code']) || $data['location_code'])
			{
				$event['item_id'] = execMethod('property.solocation.get_item_id', $data['location_code']);
			}
			else
			{
				$event['warning']			= lang('Warning: the record has to be saved in order to plan an event');
			}

			if(isset($data['event_id']) && $data['event_id'])
			{
				$event['value']			= $data['event_id'];
				$event_info 			= execMethod('property.soevent.read_single', $data['event_id']);
				$event['descr']			= $event_info['descr'];
				$event['enabled']		= $event_info['enabled'] ? lang('yes') : lang('no');
				$event['lang_enabled']	= lang('enabled');

				$job_id					= "property{$data['location']}::{$data['item_id']}::{$data['name']}";
				$job					= execMethod('phpgwapi.asyncservice.read', $job_id);

				$event['next']			= $GLOBALS['phpgw']->common->show_date($job[$job_id]['next'],$dateformat);
				$event['lang_next_run']	= lang('next run');

				$criteria = array
				(
					'start_date'		=> $event_info['start_date'],
					'end_date'			=> $event_info['end_date'],
					'location_id'		=> $event_info['location_id'],
					'location_item_id'	=> $event_info['location_item_id']
				);

				$event['count'] = 0;
				$boevent	= CreateObject('property.boevent');
				$boevent->find_scedules($criteria);
				$schedules =  $boevent->cached_events;
				foreach($schedules as $day => $set)
				{
					foreach ($set as $entry)
					{
						if($entry['enabled'] && (!isset($entry['exception']) || !$entry['exception']==true))
						{
							$event['count']++;
						}
					}
				}

				unset($event_info);
				unset($job_id);
				unset($job);
			}

			$event['event_link'] = $GLOBALS['phpgw']->link('/index.php',array
			(
				'menuaction'	=> 'property.uievent.edit',
				'location'		=> $data['location'],
				'attrib_id'		=> $event['name'],
				'item_id'		=> isset($event['item_id']) ? $event['item_id'] : '',
				'id'			=> isset($event['value']) && $event['value'] ? $event['value'] : '')
			);

			$event['function_name']	= 'lookup_'. $event['name'] .'()';

			return $event;
		}


		function initiate_ui_alarm($data)
		{
			$boalarm		= CreateObject('property.boalarm');

			if($data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('alarm_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('alarm_form'));
			}

			$alarm['header'][] = array
			(
				'lang_time'		=> lang('Time'),
				'lang_text'	=> lang('Text'),
				'lang_user'			=> lang('User'),
				'lang_enabled'		=> lang('Enabled'),
				'lang_select'		=> lang('Select')
				);

			$alarm['values'] = $boalarm->read_alarms($data['alarm_type'],$data['id'],$data['text']);
			if(!count($alarm['values'])>0)
			{
				unset($alarm['values']);
			}

			if($data['type']=='form')
			{
				$alarm['alter_alarm'][] = array
				(
					'lang_enable'		=> lang('Enable'),
					'lang_disable'		=> lang('Disable'),
					'lang_delete'		=> lang('Delete')
					);

				for ($i=1; $i<=31; $i++)
				{
					$alarm['add_alarm']['day_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_day']					= lang('Day');
				$alarm['add_alarm']['lang_day_statustext']		= lang('Day');

				for ($i=1; $i<=24; $i++)
				{
					$alarm['add_alarm']['hour_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_hour']					= lang('Hour');
				$alarm['add_alarm']['lang_hour_statustext']			= lang('Hour');

				for ($i=1; $i<=60; $i++)
				{
					$alarm['add_alarm']['minute_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_minute']					= lang('Minutes before the event');
				$alarm['add_alarm']['lang_minute_statustext']		= lang('Minutes before the event');

				$alarm['add_alarm']['user_list'] = $this->get_user_list_right2('select',4,false,$data['acl_location'],false,$default=$this->account);

				$alarm['add_alarm']['lang_user']					= lang('User');
				$alarm['add_alarm']['lang_user_statustext']			= lang('Select the user the alarm belongs to.');
				$alarm['add_alarm']['lang_no_user']					= lang('No user');
				$alarm['add_alarm']['lang_add']						= lang('Add');
				$alarm['add_alarm']['lang_add_alarm']						= lang('Add alarm');
				$alarm['add_alarm']['lang_add_statustext']			= lang('Add alarm for selected user');

			}

//_debug_array($alarm['values']);
			return $alarm;
		}


		function select_multi_list_2($selected='',$list,$input_type='')
		{
			if (isset($list) AND is_array($list))
			{
				foreach($list as &$choice)
				{
					$choice['input_type'] = $input_type;
					if(isset($selected) && is_array($selected))
					{
						foreach ($selected as &$sel)
						{
							if($sel == $choice['id'])
							{
								$choice['checked'] = 'checked';
							}
						}
					}
				}
			}
			return $list;
		}

		function translate_datatype($datatype)
		{
			$datatype_text = array(
				'V' => 'Varchar',
				'I' => 'Integer',
				'C' => 'char',
				'N' => 'Float',
				'D' => 'Date',
				'T' => 'Memo',
				'R' => 'Muliple radio',
				'CH' => 'Muliple checkbox',
				'LB' => 'Listbox',
				'AB' => 'Contact',
				'VENDOR' => 'Vendor',
				'email' => 'Email',
				'link' => 'Link',
				'pwd' => 'Password',
				'user' => 'phpgw user'
			);

			$datatype  = lang($datatype_text[$datatype]);

			return $datatype;
		}

		function translate_datatype_insert($datatype)
		{
			$datatype_text = array(
				'V' => 'varchar',
				'I' => 'int',
				'C' => 'char',
				'N' => 'decimal',
				'D' => 'timestamp',
				'T' => 'text',
				'R' => 'int',
				'CH' => 'text',
				'LB' => 'int',
				'AB' => 'int',
				'VENDOR' => 'int',
				'email' => 'varchar',
				'link' => 'varchar',
				'pwd' => 'varchar',
				'user' => 'int'
			);

			return $datatype_text[$datatype];
		}

		function translate_datatype_precision($datatype)
		{
			$datatype_precision = array(
				'I' => 4,
				'R' => 4,
				'LB' => 4,
				'AB' => 4,
				'VENDOR' => 4,
				'email' => 64,
				'link' => 255,
				'pwd' => 32,
				'user' => 4
			);

			return (isset($datatype_precision[$datatype])?$datatype_precision[$datatype]:'');
		}

		/**
		 * Convert a datatype to a format to output
		 *
		 * @param string $datatype the dataype to convert
		 *
		 * @return string the format - incoming of translation is not found
		 */
		function translate_datatype_format($datatype)
		{
			$datatype_text = array(
				'V' => 'varchar',
				'I' => 'number',
				'C' => 'char',
				'N' => 'float',
				'D' => 'date',
				'T' => 'memo',
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox',
				'AB' => 'contact',
				'VENDOR' => 'vendor',
				'email' => 'email',
				'link' => 'link',
				'pwd' => 'password',
				'user' => 'phpgw_user'
			);


			if ( isset($datatype_text[$datatype]) )
			{
				return $datatype_text[$datatype];
			}
			return $datatype;
		}


		function save_attributes($values_attribute,$type)
		{

			for ($i=0;$i<count($values_attribute);$i++)
			{
				if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
				}
				if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
				}

				if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
				}

				if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
				{
					$dateformat= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
					$dateformat = str_replace(".","",$dateformat);
					$dateformat = str_replace("-","",$dateformat);
					$dateformat = str_replace("/","",$dateformat);
					$y=strpos($dateformat,'Y');
					$d=strpos($dateformat,'d');
					$m=strpos($dateformat,'m');

			 		$dateparts = explode('/', $values_attribute[$i]['value']);
			 		$day		= $dateparts[$d];
			 		$month		= $dateparts[$m];
			 		$year		= $dateparts[$y];
					$values_attribute[$i]['value'] = date($this->dateformat,mktime(2,0,0,$month,$day,$year));
				}
			}

			$this->socommon->save_attributes($values_attribute,$type);
		}

		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'read' => array(
							'function'  => 'read',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Read a single entry by passing the id and fieldlist.')
						),
						'save' => array(
							'function'  => 'save',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Update a single entry by passing the fields.')
						),
						'delete' => array(
							'function'  => 'delete',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt)),
							'docstring' => lang('Delete a single entry by passing the id.')
						),
						'list' => array(
							'function'  => '_list',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read a list of entries.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		function add_leading_zero($num,$id_type='')
		{
			if ($id_type == "hex")
			{
				$num = hexdec($num);
				$num++;
				$num = dechex($num);
			}
			else
			{
				$num++;
			}

			if (strlen($num) == 4)
				$return = $num;
			if (strlen($num) == 3)
				$return = "0$num";
			if (strlen($num) == 2)
				$return = "00$num";
			if (strlen($num) == 1)
				$return = "000$num";
			if (strlen($num) == 0)
				$return = "0001";

			return strtoupper($return);
		}


		function read_location_data($location_code)
		{
			$soadmin_location	= CreateObject('property.soadmin_location');

			$location_types	= $soadmin_location->select_location_type();
			unset($soadmin_location);

			return $this->socommon->read_location_data($location_code,$location_types);
		}

		function read_single_tenant($tenant_id)
		{
			return $this->socommon->read_single_tenant($tenant_id);
		}

		function check_location($location_code='',$type_id='')
		{
			return $this->socommon->check_location($location_code,$type_id);
		}

		function generate_sql($data)
		{
//_debug_array($data);

			$cols 				= (isset($data['cols'])?$data['cols']:'');
			$entity_table 		= (isset($data['entity_table'])?$data['entity_table']:'');
			$cols_return 		= (isset($data['cols_return'])?$data['cols_return']:'');
			$uicols 			= (isset($data['uicols'])?$data['uicols']:'');
			$joinmethod 		= (isset($data['joinmethod'])?$data['joinmethod']:'');
			$paranthesis 		= (isset($data['paranthesis'])?$data['paranthesis']:'');
			$lookup 			= (isset($data['lookup'])?$data['lookup']:'');
			$location_level 	= (isset($data['location_level'])?$data['location_level']:'');
			$no_address 		= (isset($data['no_address'])?$data['no_address']:'');
			$force_location		= (isset($data['force_location'])?$data['force_location']:'');
			$cols_extra 		= array();
			$cols_return_lookup	= array();

			$soadmin_location	= CreateObject('property.soadmin_location');
			$location_types	= $soadmin_location->select_location_type();
			$config = $soadmin_location->read_config('');

			if($location_level || $force_location)
			{

				if($location_level)
				{
					$type_id = $location_level;
				}
				else
				{
					$type_id	= count($location_types);
				}

				$this->join = $this->socommon->join;
				$joinmethod .= " $this->join  fm_location1 ON ($entity_table.loc1 = fm_location1.loc1))";
				$paranthesis .='(';
				$joinmethod .= " $this->join  fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id))";
				$paranthesis .='(';
				$joinmethod .= " $this->join  fm_owner ON (fm_location1.owner_id = fm_owner.id))";
				$paranthesis .='(';
			}
			else
			{
				$type_id	= 0;//count($location_types);
				$no_address	= true;
			}
			$this->type_id	= $type_id;

			for ($i=0; $i<$type_id; $i++)
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'loc' . $location_types[$i]['id'];
				$uicols['descr'][]		= $location_types[$i]['name'];
				$uicols['statustext'][]		= $location_types[$i]['descr'];
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
			}
/*
			$fm_location_cols = $soadmin_location->read_attrib(array('type_id'=>$type_id,'lookup_type'=>$type_id));
			$location_cols_count	= count($fm_location_cols);

			for ($i=0;$i<$location_cols_count;$i++)
			{
				if($fm_location_cols[$i]['list']==1)
				{
					$cols_extra[] 				= $fm_location_cols[$i]['column_name']; // only for lookup
					$cols_return[] 				= $fm_location_cols[$i]['column_name'];
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= $fm_location_cols[$i]['column_name'];
					$uicols['descr'][]			= $fm_location_cols[$i]['input_text'];
					$uicols['statustext'][]		= $fm_location_cols[$i]['statustext'];
				}
			}

*/
			unset($soadmin_location);

			for ($i=0; $i< $this->type_id; $i++)
			{
				$cols_return[] = 'loc' . $location_types[$i]['id'];
			}

			if($lookup)
			{
				$cols_return[] 				= 'loc1_name';
				$cols_extra[] 				= 'loc1_name';
				$uicols['input_type'][]			= 'text';
				$uicols['name'][]			= 'loc1_name';
				$uicols['descr'][]			= lang('Property Name');
				$uicols['statustext'][]			= lang('Property Name');
				$uicols['exchange'][]		= true;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';

				for ($i=2;$i<($type_id+1);$i++)
				{
					$cols_return_lookup[] 		= 'loc' . $i . '_name';
					$uicols['input_type'][]		= 'hidden';
					$uicols['name'][]		= 'loc' . $i . '_name';
					$uicols['descr'][]		= '';
					$uicols['statustext'][]		= '';
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= '';
					$uicols['datatype'][]		= '';
				}
			}

			if(!$no_address)
			{
				$cols.= ",$entity_table.address";
				$cols_return[] 				= 'address';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'address';
				$uicols['descr'][]			= lang('address');
				$uicols['statustext'][]		= lang('address');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
			}

			$config_count	= count($config);
			for ($i=0;$i<$config_count;$i++)
			{

				if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['query_value'] ==1))
				{

					if($config[$i]['column_name']=='street_id')
					{

						$cols_return[] 				= 'street_name';
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= 'street_name';
						$uicols['descr'][]			= lang('street name');
						$uicols['statustext'][]		= lang('street name');
						$uicols['exchange'][]		= false;
						$uicols['align'][] 			= '';
						$uicols['datatype'][]		= '';

						$cols_return[] 				= 'street_number';
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= 'street_number';
						$uicols['descr'][]			= lang('street number');
						$uicols['statustext'][]		= lang('street number');
						$uicols['exchange'][]		= false;
						$uicols['align'][] 			= '';
						$uicols['datatype'][]		= '';

						$cols_return[] 				= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= $config[$i]['column_name'];
						$uicols['descr'][]			= lang($config[$i]['input_text']);
						$uicols['statustext'][]		= lang($config[$i]['input_text']);
						$uicols['exchange'][]		= false;
						$uicols['align'][] 			= '';
						$uicols['datatype'][]		= '';
						if($lookup)
						{
							$cols_extra[] 			= 'street_name';
							$cols_extra[] 			= 'street_number';
							$cols_extra[] 			= $config[$i]['column_name'];
						}

					}
					else
					{
						$cols_return[] 				= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]			= $config[$i]['column_name'];
						$uicols['descr'][]			= $config[$i]['input_text'];
						$uicols['statustext'][]		= $config[$i]['input_text'];
						$uicols['exchange'][]		= false;
						$uicols['align'][] 			= '';
						$uicols['datatype'][]		= '';

						if($lookup)
						{
							$cols_extra[] 		= $config[$i]['column_name'];
						}
					}
				}
			}

			$this->uicols 			= $uicols;
			$this->cols_return		= $cols_return;
			$this->cols_extra		= $cols_extra;
			$this->cols_return_lookup	= $cols_return_lookup;

			$from = " FROM $paranthesis $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";

			return $sql;

		}

		function select_part_of_town($format='',$selected='',$district_id='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_part_of_town'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_part_of_town'));
					break;
			}

			$parts= $this->socommon->select_part_of_town($district_id);
			$part_of_town_list = array();
			//cr@ccfirst.com 09/09/08 validate for YUI.
			if(is_array($parts)&& (count($parts))){
			foreach($parts as $entry)
			{
				if ($entry['id']==$selected)
				{
					$part_of_town_list[] = array
					(
						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'district_id'	=> $entry['district_id'],
						'selected'		=> 'selected'
					);
				}
				else
				{
					$part_of_town_list[] = array
					(
						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'district_id'	=> $entry['district_id'],
					);
				}
			}
			}

			return $part_of_town_list;
		}

		function select_district_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_district'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_district'));
					break;
			}

			$districts= $this->socommon->select_district_list();

			return $this->select_list($selected,$districts);
		}


		function select_category_list($data)
		{
			switch($data['format'])
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$socategory = CreateObject('property.socategory');

			$categories= $socategory->select_category_list(array('type'=>$data['type'],
										'type_id'=>(isset($data['type_id'])?$data['type_id']:''),
										'order'	=>$data['order']));

			return $this->select_list($data['selected'],$categories);
		}


		function validate_db_insert($values)
		{
			foreach($values as $value)
			{
				if($value || $value === 0)
				{
					$insert_value[]	= "'".$value."'";
				}
				else
				{
					$insert_value[]	= 'NULL';
				}
			}

			$values	= implode(",", $insert_value);
			return $values;
		}

		function validate_db_update($value_set)
		{
			while (is_array($value_set) && list($field,$value) = each($value_set))
			{
				if($value || $value === 0)
				{
					$value_entry[]= "$field='$value'";
				}
				else
				{
					$value_entry[]= "$field=NULL";
				}
			}

			$value_set	= implode(",", $value_entry);
			return $value_set;
		}

		function fm_cache($name='',$value='')
		{
			return $this->socommon->fm_cache($name,$value);
		}

		/**
		* Clear all content from cache
		*
		*/

		function reset_fm_cache()
		{
			$this->socommon->reset_fm_cache();
		}

		/**
		* Clear computed userlist for location and rights from cache
		*
		* @return integer number of values was found and cleared
		*/

		function reset_fm_cache_userlist()
		{
			return $this->socommon->reset_fm_cache_userlist();
		}

		function next_id($table,$key='')
		{
			return $this->socommon->next_id($table,$key);
		}

		function select_datatype($selected='', $sub_module = '')
		{

			$custom 		= createObject('phpgwapi.custom_fields');

			foreach( $custom->datatype_text as $key => $name)
			{
				$datatypes[] = array
				(
					'id'	=> $key,
					'name'	=> $name,
				);
			}

			return $this->select_list($selected,$datatypes);
		}

		function select_nullable($selected='')
		{
			$nullable[0]['id']= 'True';
			$nullable[0]['name']= lang('true');
			$nullable[1]['id']= 'False';
			$nullable[1]['name']= lang('false');

			return $this->select_list($selected,$nullable);
		}

		/**
		* Choose which  download format to use - and call the appropriate function
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function download($list,$name,$descr,$input_type=array())
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';

			switch ($export_format)
			{
				case 'csv':
					$this->csv_out($list,$name,$descr,$input_type);
					break;
				case 'excel':
					$this->excel_out($list,$name,$descr,$input_type);
					break;
				case 'ods':
					$this->ods_out($list,$name,$descr,$input_type);
					break;
			}
		}

		/**
		* downloads data as MsExcel to the browser
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function excel_out($list,$name,$descr,$input_type=array())
		{
 			$filename= str_replace(' ','_',$GLOBALS['phpgw_info']['user']['account_lid']).'.xls';

			$workbook	= CreateObject('phpgwapi.excel',"-");
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename,'application/vnd.ms-excel');

			$count_uicols_name=count($name);

			$worksheet1 =& $workbook->add_worksheet('First One');

			$m=0;
			for ($k=0;$k<$count_uicols_name;$k++)
			{
				if(!isset($input_type[$k]) || $input_type[$k]!='hidden')
				{
					$worksheet1->write_string(0, $m, $this->utf2ascii($descr[$k]));
					$m++;
				}
			}

			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$m=0;
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if(!isset($input_type[$k]) || $input_type[$k]!='hidden')
						{
							$content[$j][$m]	= str_replace("\r\n"," ",$entry[$name[$k]]);
							$m++;
						}
					}
					$j++;
				}

				$line = 0;

				foreach($content as $row)
				{
					$line++;
					$rows = count($row);
					for ($i=0; $i < $rows; $i++)
					{
						$worksheet1->write($line,$i, $this->utf2ascii($row[$i]));
					}
				}
			}
			$workbook->close();
		}

		/**
		* downloads data as CSV to the browser
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function csv_out($list, $name, $descr, $input_type = array() )
		{
			$filename= str_replace(' ','_',$GLOBALS['phpgw_info']['user']['account_lid']).'.csv';
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename, 'application/csv');

 			if ( !$fp = fopen('php://output','w') )
 			{
  				die('Unable to write to "php://output" - pleace notify the Administrator');
 			}

			$count_uicols_name=count($name);

			$header = array();
			for ( $i = 0; $i < $count_uicols_name; ++$i )
			{
				if ( !isset($input_type[$i]) || $input_type[$i] != 'hidden' )
				{
					$header[] = $this->utf2ascii($descr[$i]);
				}
			}
			fputcsv($fp, $header);
			unset($header);

			if ( is_array($list) )
			{
				foreach ( $list as $entry )
				{
					$row = array();
					for ( $i = 0; $i < $count_uicols_name; ++$i )
					{
						if ( !isset($input_type[$i]) || $input_type[$i] != 'hidden' )
						{
							$row[] = preg_replace("/\r\n/", ' ', $entry[$name[$i]]);
						}
					}
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
		}
		/**
		* downloads data as ODS to the browser
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function ods_out($list, $name, $descr, $input_type = array() )
		{
			$filename= str_replace(' ','_',$GLOBALS['phpgw_info']['user']['account_lid']).'.ods';
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename, 'application/ods');

			$count_uicols_name=count($name);

			$ods = createObject('property.ods');
			$object = $ods->newOds(); //create a new ods file

			$m=0;
			for ($k=0;$k<$count_uicols_name;$k++)
			{
				if(!isset($input_type[$k]) || $input_type[$k]!='hidden')
				{
					$object->addCell(1, 0, $m, $descr[$k], 'string');
					$m++;
				}
			}

			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$m=0;
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if(!isset($input_type[$k]) || $input_type[$k]!='hidden')
						{
							$content[$j][$m]	= str_replace(array('&'), array('og'), $entry[$name[$k]]);//str_replace("\r\n"," ",$entry[$name[$k]]);
							$m++;
						}
					}
					$j++;
				}

				$line = 0;
				foreach($content as $row)
				{
					$line++;
					for ($i=0; $i<count($row); $i++)
					{
						$object->addCell(1, $line, $i, $row[$i], 'string');
					}
				}
			}

			$temp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
			$ods->saveOds($object,"{$temp_dir}/{$filename}");

			echo file_get_contents("{$temp_dir}/{$filename}");
			unlink("{$temp_dir}/{$filename}");
		}

		function increment_id($name)
		{
			return $this->socommon->increment_id($name);
		}

		function get_origin_link($type)
		{
			if($type=='tts'):
			{
				$link = array('menuaction' => 'property.uitts.view');
			}
			elseif($type=='request'):
			{
				$link = array('menuaction' => 'property.uirequest.view');
			}
			elseif($type=='project'):
			{
				$link = array('menuaction' => 'property.uiproject.view');
			}
			elseif(substr($type,0,6)=='entity'):
			{
				$type		= explode("_",$type);
				$entity_id	= $type[1];
				$cat_id		= $type[2];
				$link =	array
				(
					'menuaction'	=> 'property.uientity.view',
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id
				);
			}
			endif;

			return (isset($link)?$link:'');
		}

		function new_db($db ='')
		{
			return $this->socommon->new_db($db);
		}

		function get_max_location_level()
		{
			return $this->socommon->get_max_location_level();
		}

		function active_group_members($group_id)
		{
			return $this->socommon->active_group_members($group_id);
		}

		/**
		* Preserve attribute values from post in case of an error
		*
		* @param array $values value set with
		* @param array $values_attributes attribute definitions and values from posting
		*
		* @return array attribute definitions and values
		*/
		public function preserve_attribute_values($values, $values_attributes)
		{

			if ( !is_array($values_attributes ) )
			{
				return array();
			}

			foreach ( $values_attributes as $attribute )
			{
				foreach ( $values['attributes'] as &$val_attrib )
				{

					if ( $val_attrib['id'] != $attribute['attrib_id'] )
					{
						continue;
					}

					if( !isset($attribute['value']) && !isset($values['extra'][$val_attrib['name']]))
					{
						continue;
					}

					if ( is_array($attribute['value']) )
					{
						foreach ( $val_attrib['choice'] as &$choice )
						{
							foreach ( $attribute['value'] as $selected )
							{
								if ( $selected == $choice['id'] )
								{
									$choice['checked'] = 'checked';
								}
							}
						}
					}
					else if ( isset($val_attrib['choice'])
						&& is_array($val_attrib['choice']) )
					{
						foreach ( $val_attrib['choice'] as &$choice)
						{
							if ( $choice['id'] == $attribute['value'] )
							{
								$choice['checked'] = 'checked';
							}
						}
					}
					else if (isset($values['extra'][$val_attrib['name']]))
					{
						$val_attrib['value'] =$values['extra'][$val_attrib['name']];
					}
					else
					{
						$val_attrib['value'] = $attribute['value'];
					}
				}
			}
			return $values;
		}

		/**
		* Converts utf-8 to ascii
		*
		* @param string $text string
		* @return string ascii encoded
		*/
		function utf2ascii($text = '')
		{
			if(!isset($GLOBALS['phpgw_info']['server']['charset']) || $GLOBALS['phpgw_info']['server']['charset']=='utf-8')
			{
				if ($text == utf8_decode($text))
				{
					return $text;
				}
				else
				{
					return utf8_decode($text);
				}
			}
			else
			{
				return $text;
			}
		}

		/**
		* Converts ascii to utf-8
		*
		* @param string $text string
		* @return string utf-8 encoded
		*/
		function ascii2utf($text = '')
		{
			if(!isset($GLOBALS['phpgw_info']['server']['charset']) || $GLOBALS['phpgw_info']['server']['charset']=='utf-8')
			{
				return utf8_encode($text);
			}
			else
			{
				return $text;
			}
		}

		/**
		* Collects locationdata from location form and appends to values
		*
		* @param array $values array with data fom post
		* @param array $insert_record array containing fields to collect from post
		* @return updated values
		*/
		function collect_locationdata($values = '',$insert_record = '')
		{
			if($insert_record)
			{
				for ($i=0; $i<count($insert_record['location']); $i++)
				{
					if(isset($_POST[$insert_record['location'][$i]]) && $_POST[$insert_record['location'][$i]])
					{
						$values['location'][$insert_record['location'][$i]]= phpgw::get_var($insert_record['location'][$i], 'string', 'POST');
					}
				}

				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					foreach ($insert_record['extra'] as $key => $column)
					{
						if(isset($_POST[$key]))
						{
							$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
						}
					}
				}
			}

			$values['street_name'] 		= phpgw::get_var('street_name');
			$values['street_number']	= phpgw::get_var('street_number');
			if(isset($values['location']) && is_array($values['location']))
			{
				$values['location_name']	= phpgw::get_var('loc' . (count($values['location'])).'_name', 'string', 'POST'); // if not address - get the parent name as address
			}

			return $values;
		}

		function get_menu($app = 'property')
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = false;
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['property']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['property']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));

			if(!$menu = $GLOBALS['phpgw']->session->appsession($GLOBALS['phpgw_info']['flags']['menu_selection'], "menu_{$app}"))
			{
				$menu_gross = execMethod("{$app}.menu.get_menu", 'horisontal');
				$selection = explode('::',$GLOBALS['phpgw_info']['flags']['menu_selection']);
				$level=0;
				$menu['navigation'] = $this->get_sub_menu($menu_gross['navigation'],$selection,$level);
				$GLOBALS['phpgw']->session->appsession(isset($GLOBALS['phpgw_info']['flags']['menu_selection']) && $GLOBALS['phpgw_info']['flags']['menu_selection'] ? $GLOBALS['phpgw_info']['flags']['menu_selection'] : 'property_missing_selection', "menu_{$app}", $menu);
				unset($menu_gross);
			}
			return $menu;
		}

		function get_sub_menu($children = array(), $selection=array(),$level='')
		{
			$level++;
			$i=0;
			foreach($children as $key => $vals)
			{
				$menu[] = $vals;
				if($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = $this->get_sub_menu($menu[$i]['children'],$selection,$level);
					}
				}
				else
				{
					if(isset($menu[$i]['children']))
					{
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}
			return $menu;
		}


		function no_access()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access','menu'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $this->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'			=> $this->get_menu(),
			);

			$appname	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}

		/**
		* Get list of accessible physical locations for current user
		*
		* @param integer $required Right the user has to be granted at location
		*
		* @return array $access_location list of accessible physical locations
		*/
		public function get_location_list($required)
		{
			return $this->socommon->get_location_list($required);
		}



		public function select2String($array_values, $id = 'id', $name = 'name',$name2 = '' )
        {
             $str_array_values = "";
             for($i = 0; $i < count($array_values); $i++)
             {
                foreach( $array_values[$i] as $key => $value )
                {
                    if ($key == $id)
                    {
                     $str_array_values .= $value;
                     $str_array_values .= "#";
                    }
                    if ($key == $name)
                    {
                      $str_array_values .= $value;
                      $str_array_values .= "@";
                    }
                 if ($key == $name2)
                 {
                 // eliminate hte last @ in $str_array_values
                 $str_array_values = substr($str_array_values, 0, strrpos($str_array_values,'@'));
                 $str_array_values .= " ".$value;
                 $str_array_values .= "@";
                 }
                }
             }
             return $str_array_values;
        }
        
		public function make_menu_date($array,$id_buttons,$name_hidden) 
		{
			$split_values = array ();			
			foreach ($array as $value)
			{
				array_push($split_values,array (text => "$value[id]", value => $value[id], onclick => array(fn => onDateClick, obj => array (id_button=>$id_buttons, opt=>$value[id], hidden_name=>$name_hidden))));
			}	
			return 	$split_values;	
		}
		
		public function make_menu_user($array,$id_buttons,$name_hidden)  
		{
			$split_values = array ();			
			foreach ($array as $value)
			{
				array_push($split_values,array (text => "$value[name]", value => $value[id], onclick => array(fn => onUserClick, obj => array (id_button=>$id_buttons, id=>$value[id], name =>$value[name], hidden_name=>$name_hidden))));
			}	
			return 	$split_values;	
		}
		
		public function choose_select($array, $index_return) 
		{
			foreach ($array as $value)
			{
				if($value["selected"]=="selected")
				{
					return 	$value[$index_return];
				}
			}	
			//for avoid erros, return the last value
			return $array[count($array)-1][$index_return];
		}		

		/**
		* pending action for items across the system.
		*
		* @param array   $data array containing string  'appname'			- the name of the module being looked up
		*										string  'location'			- the location within the module to look up
		* 										integer 'id'				- id of the referenced item - could possibly be a bigint
		* 										integer 'responsible'		- the user_id asked for approval
		* 										string  'responsible_type'  - what type of responsible is asked for action (user,vendor or tenant)
		* 										string  'action'			- what type of action is pending
		* 										string  'remark'			- a general remark - if any
		* 										integer 'deadline'			- unix timestamp if any deadline is given.
		*
		* @return integer $reminder  number of request for this action
		*/

		public function set_pending_action($action_params)
		{
			return $this->socommon->set_pending_action($action_params);
		}
	}
