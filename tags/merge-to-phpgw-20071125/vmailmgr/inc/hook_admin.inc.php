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
	/* $Id: hook_admin.inc.php,v 1.3 2003/04/23 01:51:47 ceb Exp $ */
	{
		$file = Array
		(
			'Site Configuration' => $GLOBALS['phpgw']->link('/vmailmgr/admin.php')
		);
//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
