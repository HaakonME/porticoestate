<?php
  /**************************************************************************\
  * phpGroupWare - Polls                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_website_right.inc.php 9572 2002-02-20 13:42:21Z milosch $ */

	$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
	include_once(PHPGW_SERVER_ROOT . '/polls/inc/functions.inc.php');
	$GLOBALS['phpgw_info']['wcm']['right'] = display_poll('website');
?>
