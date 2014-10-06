<?php
	/**
	 * phpGroupWare jQuery wrapper class
	 *
	 * @author Sigurd Nes
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * phpGroupWare jQuery wrapper class
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category gui
	 */


	class phpgwapi_jquery
	{
		/**
		* @var int $counter the widget id counter
		*/
		private static $counter = 0;

		/**
		* Load all the dependencies for a YUI widget
		*
		* @param string $widget the name of the widget to load, such as autocomplete
		*
		* @return string yahoo namespace for widget - empty string on failure
		*
		* @internal this does not render the widget it only includes the header js files
		*/
		public static function load_widget($widget)
		{
			$_type = '.min';	// save some download

			$load = array();
			switch ( $widget )
			{
				case 'core':
					$load = array
					(
						"js/jquery-1.11.1{$_type}",
//						"js/jquery-migrate-1.2.1"
					);
					break;
				
				case 'datepicker':
					$load = array
					(
						"js/jquery-1.11.1{$_type}",
						"js/jquery-ui-1.10.4.custom{$_type}",
						"development-bundle/ui/i18n/jquery.ui.datepicker-{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}",
//						"js/jquery-migrate-1.2.1"
					);
					break;

				case 'menu':
				case 'autocomplete':
					$load = array
					(
						"js/jquery-1.11.1{$_type}",
						"js/jquery-ui-1.10.4.custom{$_type}",
//						"js/jquery-migrate-1.2.1"
					);

					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/ui-lightness/jquery-ui-1.10.4.custom{$_type}.css");

					break;

				default:
					$err = "Unsupported YUI widget '%1' supplied to phpgwapi_yui::load_widget()";
					trigger_error(lang($err, $widget), E_USER_WARNING);
					return '';
			}

			foreach ( $load as $script )
			{
				$test = $GLOBALS['phpgw']->js->validate_file('jquery', $script);

				if ( !$test )
				{
					$err = "Unable to load jQuery script '%1' when attempting to load widget: '%2'";
					trigger_error(lang($err, $script, $widget), E_USER_WARNING);
					return '';
				}
			}
			return "phpgroupware.{$widget}" . ++self::$counter;
		}

	}
