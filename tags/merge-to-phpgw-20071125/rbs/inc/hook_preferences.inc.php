<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: hook_preferences.inc.php,v 1.5 2001/11/21 03:22:08 skeeter Exp $ */
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = Array(
		'Select Rooms to Display'	=> $GLOBALS['phpgw']->link('/'.$appname.'/preferences.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
