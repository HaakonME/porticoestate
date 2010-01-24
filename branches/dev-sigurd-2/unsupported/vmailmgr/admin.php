<?php
  /**************************************************************************\
	* phpGroupWare - VMailMgr                                               *
	* http://www.phpgroupware.org                                              *
	* Written by Dan Kuykendall <dan@kuykendall.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'vmailmgr', 
		'noheader'    => True, 
		'nonavbar'    => True, 
		'noappheader' => True,
		'noappfooter' => True,
		'enable_config_class'     => True,
		'enable_nextmatchs_class' => True
	);

	include('../header.inc.php');

	$GLOBALS['phpgw']->config->read_repository();

	if ($_POST['cancel'])
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/admin/index.php'));
		exit;
	}

	if ($_POST['submit'])
	{
		/*
		if ($_POST['usemailnotification'])
		{
			$GLOBALS['phpgw']->config->config_data['mailnotification'] = True;
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['mailnotification']);
		}
		*/

		$GLOBALS['phpgw']->config->config_data['vmailmgr_domain'] = $_POST['vmailmgr_domain'];
		$GLOBALS['phpgw']->config->config_data['vmailmgr_domainpass'] = $_POST['vmailmgr_domainpass'];
		$GLOBALS['phpgw']->config->config_data['vmailmgr_tcphost'] = $_POST['vmailmgr_tcphost'];
		$GLOBALS['phpgw']->config->config_data['vmailmgr_tcphost_port'] = $_POST['vmailmgr_tcphost_port'];
		$GLOBALS['phpgw']->config->save_repository(True);
		Header('Location: ' . $GLOBALS['phpgw']->link('/admin/index.php'));
	}

	$GLOBALS['phpgw']->common->phpgw_header();

	/*
	$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin.tpl'));
	$GLOBALS['phpgw']->template->set_block('admin', 'tts_select_options','tts_select_options');

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/tts/admin.php'));
	
	$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);
	*/

	echo '
	<form method="POST" action="'.$GLOBALS['phpgw']->link('/vmailmgr/admin.php').'">
		<table border="0" align="center">
			<tr bgcolor="D3DCE3">
    		<td colspan="2"><font color="000000">&nbsp;<b>Site configuration</b></font></td>
			</tr>
  		<tr bgcolor="DDDDDD">
	    	<td colspan="2">&nbsp;</td>
			</tr>
			<tr bgcolor="EEEEEE">
  		  <td colspan="2"><b>VMailMgr Server Settings</b></td>
			</tr>
			<tr bgcolor="DDDDDD">
		    <td>Domain Name?</td>
				<td><input name="vmailmgr_domain" value="'.$GLOBALS['phpgw']->config->config_data['vmailmgr_domain'].'"></td>
			</tr>
			<tr bgcolor="DDDDDD">
		    <td>Domain Password?</td>
				<td><input name="vmailmgr_domainpass" value="'.$GLOBALS['phpgw']->config->config_data['vmailmgr_domainpass'].'"></td>
			</tr>
			<tr bgcolor="DDDDDD">
		    <td>Server Name/IP? (optional)</td>
				<td><input name="vmailmgr_tcphost" value="'.$GLOBALS['phpgw']->config->config_data['vmailmgr_tcphost'].'"></td>
			</tr>
			<tr bgcolor="DDDDDD">
		    <td>Server Port? (optional)</td>
				<td><input name="vmailmgr_tcphost_port" value="'.$GLOBALS['phpgw']->config->config_data['vmailmgr_tcphost_port'].'"></td>
			</tr>
			<tr bgcolor="D3DCE3">
		    <td colspan="2">
					&nbsp;
    		</td>
  		</tr>
  		<tr>
    		<td colspan="2" align="center">
      		<input type="submit" name="submit" value="Submit">
		      <input type="submit" name="cancel" value="Cancel">
    		</td>
		  </tr>
		</table>
	</form>
	';
	
//	$GLOBALS['phpgw']->template->pparse('out','admin');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
