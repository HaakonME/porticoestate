<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: hook_deleteaccount.inc.php,v 1.9 2006/10/03 14:04:58 Caeies Exp $ */

	if ( isset($_POST['account_id']) && (int) $_POST['account_id'] )
	{
		// delete all mapping to account
		// Using Single Sign-On
		if(isset($GLOBALS['phpgw_info']['server']['mapping']) && ($GLOBALS['phpgw_info']['server']['mapping'] == 'all' || $GLOBALS['phpgw_info']['server']['mapping'] == 'table'))
		{
			$phpgw_map_location = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? $_SERVER['HTTP_SHIB_ORIGIN_SITE'] : 'local';
			$phpgw_map_authtype = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? 'shibboleth':'remoteuser';
			if(!is_object($GLOBALS['phpgw']->mapping))
			{
				$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
			}
			$account = CreateObject('phpgwapi.accounts',$_POST['account_id'],'u');
			$data = $account->read();
			$account_lid = $data['account_lid'];
			$GLOBALS['phpgw']->mapping->delete_mapping(array('account_lid' => $account_lid));
		}											
		
		$GLOBALS['phpgw']->accounts->delete($_POST['account_id']);
		$GLOBALS['phpgw']->db->lock(Array('phpgw_acl'));
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_acl WHERE acl_location='" . (int) $_POST['account_id'] . "'"
			. ' OR acl_account=' . (int) $_POST['account_id'], __LINE__, __FILE__);
		$GLOBALS['phpgw']->db->unlock();
	}
?>
