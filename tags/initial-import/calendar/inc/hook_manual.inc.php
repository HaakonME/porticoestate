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

	/* $Id: hook_manual.inc.php,v 1.7 2005/05/15 06:57:37 skwashd Exp $ */

// Only Modify the $file variable.....
	$file = Array(
		'Viewing'	=>	'view.php',
		'Adding'	=> 'add.php',
		'Edit/Deleting'	=> 'edit_delete.php'
	);
//Do not modify below this line
	display_manual_section($appname,$file);
?>
