<?php
  /**************************************************************************\
  * phpGroupWare - Calendar Holidays                                         *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: hook_manual.inc.php 16400 2006-02-14 08:48:21Z skwashd $ */

// Only Modify the $file variable.....
	$file = Array(
		'Account Management'	=>	'account.php',
		'Session Management'	=> 'session.php',
		'Other'	=> 'other.php'
	);
//Do not modify below this line
	display_manual_section($appname,$file);
?>
