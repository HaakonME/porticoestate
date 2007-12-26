<?php
	/**
	 * preferences - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package preferences 
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	/**
	 * Menus
	 *
	 * @package preferences
	 */	
	class preferences_menu
	{
		/**
		 * Get the menus for the preferences
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'preferences' => array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php'),
					'image'	=> array('preferences', 'navbar'),
					'order'	=> 1,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array();

			$menus['navigation'] = array();

			$menus['navigation'][] = array
			(
				'text'	=> lang('My Preferences'),
				'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'preferences')),
				'image'	=> array('preferences', 'preferences')
			);

			if ($GLOBALS['phpgw']->acl->check('changepassword',1))
			{
				$menus['navigation'][] = array
				(
					'text'	=> lang('Change your Password'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/changepassword.php')
				);
			}
			if ( (isset($GLOBALS['phpgw_info']['server']['auth_type']) && $GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser') 
				|| (isset($GLOBALS['phpgw_info']['server']['half_remote_user']) && $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser') )
			{
				if($GLOBALS['phpgw_info']['server']['mapping'] == 'table' || $GLOBALS['phpgw_info']['server']['mapping'] == 'all')
				{
					$menus['navigation'][] = array
					(
						'text'	=> lang('Mapping'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uimapping.index', 'appname' => 'preferences') )
					);
				}
			}
			
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['navigation'][] = array
				(
					'text'	=> lang('Default Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php', array('type' => 'default') )
				);
				$menus['navigation'][] = array
				(
					'text'	=> lang('Forced Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php', array('type' => 'forced') )
				);
			}
			return $menus;
		}
	}
