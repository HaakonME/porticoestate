<?php
	/**************************************************************************\
	* phpGroupWare - chora remote cvs class test                               *
	* http://www.phpgroupware.org                                              *
	* This application written by Miles Lott <milosch@phpgroupware.org>        *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: test.php 10090 2002-04-28 20:36:01Z milosch $ */

	$phpgw_info['flags'] = array(
		'currentapp' => 'chora',
		'noheader'   => True,
		'nonavbar'   => True,
		'enable_config_class' => True
	);
	include('../header.inc.php');

	$obj = CreateObject('chora.uicvs');
	$obj->index();
?>
