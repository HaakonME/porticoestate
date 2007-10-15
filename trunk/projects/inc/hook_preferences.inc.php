<?php
	/**
	* Project Manager - Project Prefs 
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_preferences.inc.php,v 1.31 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/hook_preferences.inc.php,v $
	*/

	{
		$title = $appname;
		$file = Array
		(
			'Preferences'     => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.preferences')),
			'Grant Access'    => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiaclprefs.index','acl_app'=>$appname)),
			'Edit categories' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>'projects','cats_level'=>'True','global_cats'=>'True'))
		);

		$pro_soconfig = CreateObject('projects.soconfig');
		if($pro_soconfig->isprojectadmin('pad') || $pro_soconfig->isprojectadmin('pmanager'))
		{
			$afile = Array
			(
				'roles'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_roles','action'=>'role')),
				'events'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_events')),
				'surcharges'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_surcharges'))
			);
			unset($pro_soconfig);
		}

		if(is_array($afile))
		{
			$file += $afile;
		}
		display_section($appname,$title,$file);
	}
?>
