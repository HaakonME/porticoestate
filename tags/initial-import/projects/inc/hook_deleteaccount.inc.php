<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_deleteaccount.inc.php,v 1.9 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/hook_deleteaccount.inc.php,v $
	*/

	// Delete all records for a user
	$pro = CreateObject('projects.boprojects');

	if(intval($_POST['new_owner']) == 0)
	{
		$pro->delete_project(intval($_POST['account_id']),0,'account');
	}
	else
	{
		$pro->change_owner(intval($_POST['account_id']),intval($_POST['new_owner']));
	}
?>
