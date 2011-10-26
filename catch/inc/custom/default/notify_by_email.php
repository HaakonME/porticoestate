<?php

	$validator = CreateObject('phpgwapi.EmailAddressValidator');

	if(!isset($config_data['notify_email']) || !$config_data['notify_email'])
	{
		throw new Exception("notify_accounting_by_email: missing 'notify_email' in config for this catch schema:{$schema_text}");
	}

	$to_array = explode(',', $config_data['notify_email']);

//_debug_array($to_array);
/*
	foreach($to_array as $_to)
	{
		if( !$validator->check_email_address($_to) )
		{
			throw new Exception("notify_accounting_by_email: an unvalid 'notify_email': {$_to} in config for schema:{$schema_text}");
		}
	}
*/					
	$socommon		= CreateObject('property.socommon');
	$prefs = $socommon->create_preferences('property',$user_id);
//_debug_array($prefs);
	if ($validator->check_email_address($prefs['email']))
	{
		$account_name = $GLOBALS['phpgw']->accounts->id2name($user_id);
		// avoid problems with the delimiter in the send class
		if(strpos($account_name,','))
		{
			$_account_name = explode(',', $account_name);
			$account_name = ltrim($_account_name[1]) . ' ' . $_account_name[0];
		}
		$from_email = "{$account_name}<{$prefs['email']}>";

		$to_array[] = $from_email;
	}

	if (!is_object($GLOBALS['phpgw']->send))
	{
		$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
	}

	$_to = implode(';',$to_array);

	$from_name = 'noreply';
	$from_email = isset($from_email) && $from_email ? $from_email : "{$from_name}<sigurd.nes@bergen.kommune.no>";
	$cc = '';
	$bcc ='';
	$subject = "{$schema_text}::{$id}";
	
	/*
	$_link_to_item = 'http://'. $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uientity.view',
							'type'			=> 'catch',
							'entity_id'		=> $entity_id,
							'cat_id'		=> $cat_id,
							'id'			=> $id
						));
	*/
	//$body = "<a href='{$_link_to_item}'>{$subject}</a>";
	unset($_link_to_item);

	if(isset($config_data['email_message']) && $config_data['email_message'])
	{
		$body = str_replace(array('[', ']'), array('<', '>'), $config_data['email_message']);
	}
	else
	{
		$body ="<H2>Det er registrert ny post i {$schema_text}</H2>";
	}

	$jasper_id = isset($config_data['jasper_id']) && $config_data['jasper_id'] ? $config_data['jasper_id'] : 0;

	$attachments = array();	

	if(!$jasper_id)
	{
		$this->receipt['error'][]=array('msg'=>lang('notify_by_email: missing "jasper_id" in config for catch %1 schema', $schema_text));
	}
	else
	{
		$jasper_parameters = '';
		$_parameters = array();

		$_parameters[] =  "ID|{$id}";
		$jasper_parameters = '"' . implode(';', $_parameters) . '"';

		unset($_parameters);

		$output_type 		= 'PDF';
		$values_jasper		= execMethod('property.bojasper.read_single', $jasper_id);
		$report_source		= "{$GLOBALS['phpgw_info']['server']['files_dir']}/property/jasper/{$jasper_id}/{$values_jasper['file_name']}";
		$jasper_wrapper		= CreateObject('phpgwapi.jasper_wrapper');

	//_debug_array($jasper_parameters);
	//_debug_array($output_type);
	//_debug_array($report_source);die();

		try
		{
			$report = $jasper_wrapper->execute($jasper_parameters, $output_type, $report_source, true);
		}
		catch(Exception $e)
		{
			$error = $e->getMessage();
			echo "<H1>{$error}</H1>";
		}

		$jasper_fname = tempnam($GLOBALS['phpgw_info']['server']['temp_dir'], 'PDF_') . '.pdf';
		file_put_contents($jasper_fname, $report['content'], LOCK_EX);

		$attachments[] = array
		(
			'file' => $jasper_fname,
			'name' => $report['filename'],
			'type' => $report['mime']
		);
		
		if($attachments)
		{
			$body .= "</br>Se vedlegg";
		}
	}

	if ($GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments , true))
	{
		$this->receipt['message'][]=array('msg'=> "email notification sent to: {$_to}");	
	}
	if( isset($jasper_fname) && is_file($jasper_fname) )
	{
		unlink($jasper_fname);
	}
