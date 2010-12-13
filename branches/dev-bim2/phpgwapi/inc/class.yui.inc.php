<?php
	/**
	 * phpGroupWare YUI wrapper class
	 *
	 * @author Dave Hall
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * phpGroupWare YUI wrapper class
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category gui
	 */


	class phpgwapi_yui
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
			$load = array();
			switch ( $widget )
			{
				case 'animation':
					$load = array('animation-min', 'container-min');
					break;

				case 'autocomplete':
					$load = array('autocomplete-min', 'connection-min');
					break;

				case 'button':
					$load = array('button-min', 'element-min');
					break;

				case 'calendar':
					$load = array('calendar-min');
					break;

				case 'colorpicker':
				case 'colourpicker': // be nice to the speakers of H.M. English :)
					$load = array('colorpicker-min');
					break;

				case 'container':
					$load = array('container-min', 'dragdrop-min');
					break;

				case 'history':
					$load = array('history-min');
					break;

				case 'utilities':
					$load = array('container-min');
					break;

				case 'connection':
					$load = array('connection-min');
					break;

				case 'datasource':
					$load = array('json-min', 'datasource-min', 'connection-min');
					break;

				case 'datatable':
					$load = array('json-min', 'element-min', 'datasource-min', 'datatable-min' );
					break;
				// cramirez: necesary for include a partucular js
				case 'loader':
					$load = array('yuiloader-min');
					break;

				case 'dom':
					// do nothing - auto included
					break;

				case 'dragdrop':
					$load = array('dragdrop-min');
					break;

				case 'editor':
					$load = array('dragdrop-min', 'element-min', 'animation-min', 'resize-min', 'container_core-min', 'menu-min', 'button-min', 'editor-min');
					break;

				case 'element':
					$load = array('element-min');
					break;

				case 'paginator':
					$load = array('paginator-min');
					break;

				case 'event':
					// do nothing - auto included
					break;

				// not including history - as it isn't needed - need to handle the not included/used types somewhere

				case 'imageloader':
					$load = array('imageloader-min');
					break;

				case 'logger':
					$load = array('dragdrop-min', 'logger-min');
					break;

				case 'menu':
					$load = array('container_core-min', 'menu-min');
					break;

                case 'resize':
					$load = array('dragdrop-min', 'element-min', 'resize-min');
					break;

				case 'layout':
					$load = array('dragdrop-min', 'element-min', 'resize-min', 'layout-min');
					break;

				case 'slider':
					$load = array('dragdrop-min', 'animation-min', 'slider-min');
					break;

				case 'tabview':
					$load = array('element-min', 'tabview-min');
					break;

				case 'treeview':
					$load = array('treeview-min');
					break;

				case 'uploader':
					$load = array('element-min', 'uploader-min', 'datasource-min', 'datatable-min');
					break;

				default:
					$err = "Unsupported YUI widget '%1' supplied to phpgwapi_yui::load_widget()";
					trigger_error(lang($err, $widget), E_USER_WARNING);
					return '';
			}

			$ok = true;
			$GLOBALS['phpgw']->js->validate_file('yahoo', 'yahoo-dom-event/yahoo-dom-event');
			foreach ( $load as $script )
			{
				$script_part = explode('-',$script);
				$test = $GLOBALS['phpgw']->js->validate_file('yahoo', "{$script_part[0]}/{$script}");
				if ( !$test )
				{
					$script_part = explode('_',$script);
					$test = $GLOBALS['phpgw']->js->validate_file('yahoo', "{$script_part[0]}/{$script}");
				}
				if ( !$test || !$ok )
				{
					$err = "Unable to load YUI script '%1' when attempting to load widget: '%2'";
					trigger_error(lang($err, $script, $widget), E_USER_WARNING);
					return '';
				}
			}
			return "phpgroupware.{$widget}" . ++self::$counter;
		}

		/**
		* Create a tabs "bar"
		*
		* @param array   $tabs      list of tabs as an array($id => $tab)
		* @param integer $selection array key of selected tab
		*
		* @return string HTML output string
		*/
		public static function tabview_generate($tabs, $selection)
		{
			self::load_widget('tabview');
			$output = <<<HTML
				<ul class="yui-nav">

HTML;
			foreach($tabs as $id => $tab)
			{
				$selected = $id == $selection ? ' class="selected"' : '';
				$label = $tab['label'];
				$_function = '';
				if(isset($tab['function']))
				{
					$_function = " onclick=\"javascript: {$tab['function']};\"";
				}
				$output .= <<<HTML
					<li{$selected}><a href="{$tab['link']}"{$_function}><em>{$label}</em></a></li>

HTML;
			}
			$output .= <<<HTML
				</ul>

HTML;
			return $output;
		}

		/**
		 * Add the events required for tabs to work
		 *
		 * @param string $id html element id for the widget
		 *
		 * @return void
		 */
		public static function tabview_setup($id)
		{
			$css = 'phpgwapi/js/yahoo/tabview/assets/skins/sam/tabview.css';
			$GLOBALS['phpgw']->css->add_external_file($css);

			$js = "var tabs_{$id} = new YAHOO.widget.TabView('{$id}');";
			$GLOBALS['phpgw']->js->add_event('load', $js);
		}
	}
