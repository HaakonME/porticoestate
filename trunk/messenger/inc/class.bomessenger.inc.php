<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.bomessenger.inc.php 17907 2007-01-24 16:51:08Z Caeies $ */

	class bomessenger
	{
		var $so;
		var $public_functions = array
		(
			'delete_message'      => true,
			'send_message'        => true,
			'send_global_message' => true,
			'reply'               => true,
			'forward'             => true,
			'list_methods'        => true
		);
		var $soap_functions = array();

		function bomessenger()
		{
			$this->so = createobject('messenger.somessenger');
		}
		
		function get_available_users()
		{
			$users = array();

			$config = createObject('phpgwapi.config', 'messenger');
			$config->read_repository();

			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) 
				&& isset($config->data['restrict_to_group'] )
				&& $config->data['restrict_to_group'] )
			{
				$tmp_users = $GLOBALS['phpgw']->accounts->member($config->data['restrict_to_group']);
				foreach ( $tmp_users as $user )
				{
					$users[$user['account_id']] = $user['account_name'];
				}
			}
			else
			{
				$tmp_users = $GLOBALS['phpgw']->accounts->get_list('accounts', -1, 'ASC', 'account_lid', '', -1);
				foreach ( $tmp_users as $user )
				{
					$users[$user['account_id']] = $GLOBALS['phpgw']->common->display_fullname($user['account_lid'], $user['account_firstname'], $user['account_lastname']);
				}
			}
			return $users;
		}

		function send_global_message($data='')
		{
			if(is_array($data))
			{
				$message = $data['message'];
				$send    = $data['send'];
				$cancel  = $data['cancel'];
			}
			else
			{
				$message = get_var('message',Array('POST'));
				$send    = get_var('send',Array('POST'));
				$cancel  = get_var('cancel',Array('POST'));
			}

			if (! $GLOBALS['phpgw']->acl->check('run',1,'admin') || $cancel)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
				return False;
			}

			if (! $message['subject'])
			{
				$errors[] = lang('You must enter a subject');
			}

			if (! $message['content'])
			{
				$errors[] = lang("You didn't enter anything for the message");
			}

			if (is_array($errors))
			{
				ExecMethod('messenger.uimessenger.compose',$errors);
				//$this->ui->compose($errors);
			}
			else
			{
				$account_info = $GLOBALS['phpgw']->accounts->get_list('accounts');

				$this->so->db->transaction_begin();
				while (list(,$account) = each($account_info))
				{
					$message['to'] = $account['account_lid'];
					$this->so->send_message($message,True);
				}
				$this->so->db->transaction_commit();
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
			}
		}

		function check_for_missing_fields($message)
		{
			$errors = array();
			if ($message['to'] > 0)
			{
				$user = $this->get_available_users();
				if ( isset($user[$message['to']]) )
				{
					$errors[] = lang('You are not allow to send messages to the user you have selected');
				}
			}
			else
			{
				$errors[] = lang('You must select a user to send this message to');
			}
			
			$acct = createobject('phpgwapi.accounts', $message['to']);
			$acct->read_repository();
			if ($acct->is_expired() && $GLOBALS['phpgw']->accounts->name2id($message['to']))
			{
				$errors[] = lang("Sorry, %1's account is not currently active",$message['to']);
			}

			if (! $message['subject'])
			{
				$errors[] = lang('You must enter a subject');
			}

			if (! $message['content'])
			{
				$errors[] = lang("You didn't enter anything for the message");
			}
			return $errors;
		}
		
		function is_connected()
		{
			return $this->so->connected;
		}

		function send_message($data='')
		{
			if(is_array($data))
			{
				$message = $data['message'];
				$send    = $data['send'];
				$cancel  = $data['cancel'];
			}
			else
			{
				$message	= $_POST['message'];
				$send		= isset($_POST['send']) ? !!$_POST['send'] : false;
				$cancel		= isset($_POST['cancel']) ? !!$_POST['cancel'] : false;
			}

			if ($cancel)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
				exit;
			}

			$errors = $this->check_for_missing_fields($message);

			if ( count($errors))
			{
				ExecMethod('messenger.uimessenger.compose',$errors);
			}
			else
			{
				$this->so->send_message($message);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
			}
		}

		function read_inbox($params)
		{
			$_messages = array();

			$messages = $this->so->read_inbox($params);
			foreach ( $messages as $message )
			{
				if ($message['from'] == -1)
				{
					$cached['-1']       = -1;
					$cached_names['-1'] = lang('Global Message');
				}

				// Cache our results, so we don't query the same account multiable times
				if ( !isset($cached[$message['from']]) || !$cached[$message['from']] )
				{
					$acct = createobject('phpgwapi.accounts',$message['from']);
					$acct->read_repository();
					$cached[$message['from']]       = $message['from'];
					$cached_names[$message['from']] = $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']);
				}

				/*
				** N - New
				** R - Replied
				** O - Old (read)
				** F - Forwarded
				*/
				if ($message['status'] == 'N')
				{
					$message['subject'] = '<b>' . $message['subject'] . '</b>';
					$message['status'] = '&nbsp;';
					$message['date'] = '<b>' . $GLOBALS['phpgw']->common->show_date($message['date']) . '</b>';
					$message['from'] = '<b>' . $cached_names[$message['from']] . '</b>';
				}
				else
				{
					$message['date'] = $GLOBALS['phpgw']->common->show_date($message['date']);
					$message['from'] = $cached_names[$message['from']];
				}

				if ($message['status'] == 'O')
				{
					$message['status'] = '&nbsp;';
				}

				$_messages[] = array(
					'id'      => $message['id'],
					'from'    => $message['from'],
					'status'  => $message['status'],
					'date'    => $message['date'],
					'subject' => $message['subject']
				);
			}
			return $_messages;
		}

		function read_message($message_id)
		{
			$message = $this->so->read_message($message_id);

			$message['date'] = $GLOBALS['phpgw']->common->show_date($message['date']);

			if ($message['from'] == -1)
			{
				$message['from']           = lang('Global Message');
				$message['global_message'] = True;
			}
			else
			{
				$acct = createobject('phpgwapi.accounts',$message['from']);
				$acct->read_repository();
				$message['from'] = $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']);
			}

			return $message;
		}

		function read_message_for_reply($message_id,$type,$n_message='')
		{
			if(!$n_message)
			{
				$n_message = get_var('n_message',Array('POST'));
			}

			$message = $this->so->read_message($message_id);

			$acct = createobject('phpgwapi.accounts',$message['from']);
			$acct->read_repository();

			if (! $n_message['content'])
			{
				$content_array = explode("\n",$message['content']);

				$new_content_array[] = ' ';
				$new_content_array[] = '> ' . $GLOBALS['phpgw']->common->display_fullname($acct->data['account_lid'],$acct->data['firstname'],$acct->data['lastname']) . ' wrote:';
				$new_content_array[] = '>';
				while (list(,$line) = each($content_array))
				{
					$new_content_array[] = '> ' . $line;
				}
				$message['content'] = implode("\n",$new_content_array);
			}

			$message['subject'] = $type . ': ' . $message['subject'];
			$message['from']    = $acct->data['account_lid'];

			return $message;
		}

		function delete_message($messages='')
		{
			if(!$messages)
			{
				$messages = get_var('messages',Array('GET','POST'));
			}

			if (! is_array($messages))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
				return False;
			}
			$this->so->transaction_begin();
			while (list(,$message_id) = each($messages))
			{
				$this->so->delete_message($message_id);
			}
			$this->so->transaction_commit();
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
		}

		function reply($message_id='',$n_message='')
		{
			if(!$message_id)
			{
				$message_id = get_var('message_id',Array('POST'));
				$n_message  = get_var('n_message',Array('POST'));
			}

			$errors = $this->check_for_missing_fields($n_message);
			if (is_array($errors))
			{
				ExecMethod('messenger.uimessenger.reply',array($errors,$n_message));
				//$this->ui->reply($errors, $n_message);
			}
			else
			{
				$this->so->send_message($n_message);
				$this->so->update_message_status('R',$message_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
			}
		}

		function forward($message_id='',$n_message='')
		{
			if(!$message_id)
			{
				$message_id = get_var('message_id',Array('POST'));
				$n_message  = get_var('n_message',Array('POST'));
			}

			$errors = $this->check_for_missing_fields($n_message);

			if (is_array($errors))
			{
				ExecMethod('messenger.uimessenger.forward',array($errors,$n_message));
				//$this->ui->forward($errors, $n_message);
			}
			else
			{
				$this->so->send_message($n_message);
				$this->so->update_message_status('F',$message_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox'));
			}
		}

		function total_messages($extra_where_clause = '')
		{
			return $this->so->total_messages($extra_where_clause);
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
						'delete_message' => array(
							'function'  => 'delete_message',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Delete a message.')
						),
						'read_message' => array(
							'function'  => 'read_message',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read a single message.')
						),
						'read_inbox' => array(
							'function'  => 'read_inbox',
							'signature' => array(array(xmlrpcStruct,xmlrpcString,xmlrpcString,xmlrpcString)),
							'docstring' => lang('Read a list of messages.')
						),
						'send_message' => array(
							'function'  => 'send_message',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Send a message to a single recipient.')
						),
						'send_global_message' => array(
							'function'  => 'send_global_message',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Send a global message.')
						),
						'reply' => array(
							'function'  => 'reply',
							'signature' => array(array(xmlrpcInt,xmlrpcInt)),
							'docstring' => lang('Reply to a received message.')
						),
						'forward' => array(
							'function'  => 'forward',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Forward a message to another user.')
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
	}
