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

	/* $Id: hook_preferences.inc.php,v 1.6 2007/01/24 17:24:05 Caeies Exp $ */

	$file  = array(
		'Edit Categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=developer_tools&cats_level=True&global_cats=True')
		//'SF Project tracker preferences' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uisf_project_tracker.preferences'))
	);

	display_section('developer_tools',$file);
?>
