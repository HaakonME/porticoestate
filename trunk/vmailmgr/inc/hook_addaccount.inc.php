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

	/* $Id: hook_addaccount.inc.php,v 1.2 2002/05/02 01:29:52 seek3r Exp $ */
	
	// Add matching vmailmgr user account
	if ($GLOBALS['hook_values']['account_id'] == 0)
	{
		$account_lid = $GLOBALS['hook_values']['account_lid'];
	}
	else
	{
		$account_lid = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['hook_values']['account_id']);
	}
	$GLOBALS['phpgw']->vmailmgr = CreateObject('vmailmgr.vmailmgr');
	$returnvals = $GLOBALS['phpgw']->vmailmgr->vadduser($account_lid, $GLOBALS['hook_values']['new_passwd']);
?>
