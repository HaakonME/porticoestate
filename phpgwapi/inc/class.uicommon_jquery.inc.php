<?php
	/**
	* phpGroupWare
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package phpgwapi
	* @subpackage utilities
 	* @version $Id: class.uicommon.inc.php 11988 2014-05-23 13:26:30Z sigurdne $
	*/

	//phpgw::import_class('phpgwapi.yui');


	abstract class phpgwapi_uicommon_jquery
	{
		const UI_SESSION_FLASH = 'flash_msgs';

		protected
			$filesArray;

		private
			$ui_session_key,
			$flash_msgs;

		public $dateFormat;

		public $type_of_user;

	//	public $flash_msgs;

		public function __construct($currentapp ='', $yui = '')
		{

			$yui = isset($yui) && $yui == 'yui3' ? 'yui3' : 'yahoo';
			$currentapp = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];


			$this->tmpl_search_path = array();
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/base');
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/' . $currentapp . '/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);

			if($yui == 'yui3')
			{
				self::add_javascript('phpgwapi', 'yui3', 'yui/yui-min.js');
				self::add_javascript('phpgwapi', $yui, 'common.js');
			}
			//self::add_javascript('phpgwapi', "jquery", 'common.js');

			$this->url_prefix = str_replace('_', '.', get_class($this));

			$this->dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->locations = & $GLOBALS['phpgw']->locations;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($currentapp);
			
			phpgwapi_jquery::load_widget('core');
			self::add_javascript('phpgwapi', 'DataTables', 'media/js/jquery.dataTables.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'extensions/Responsive/js/dataTables.responsive.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'extensions/ColVis/js/dataTables.colVis.min.js');
			self::add_javascript('phpgwapi', 'DataTables', 'extensions/TableTools/js/dataTables.tableTools.js');

//			self::add_javascript('phpgwapi', 'jquery-mobile', 'jquery.mobile-1.4.3.min.js');

			//FIXME: working?
//			self::add_javascript('phpgwapi', 'DataTables', 'media/js/jquery.dataTables.columnFilter.js');

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/media/css/jquery.dataTables.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/extensions/Responsive/css/dataTables.responsive.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/extensions/ColVis/css/dataTables.colVis.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/extensions/ColVis/css/dataTables.colvis.jqueryui.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/DataTables/extensions/TableTools/css/dataTables.tableTools.css');
//			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/jquery-mobile/jquery.mobile-1.4.3.min.css');

		}

		private function get_ui_session_key()
		{
			return $this->ui_session_key;
		}

		private function restore_flash_msgs()
		{
			if (($flash_msgs = $this->session_get(self::UI_SESSION_FLASH)))
			{
				if (is_array($flash_msgs))
				{
					$this->flash_msgs = $flash_msgs;
					$this->session_set(self::UI_SESSION_FLASH, array());
					return true;
				}
			}

			$this->flash_msgs = array();
			return false;
		}

		private function store_flash_msgs()
		{
			return $this->session_set(self::UI_SESSION_FLASH, $this->flash_msgs);
		}

		private function reset_flash_msgs()
		{
			$this->flash_msgs = array();
			$this->store_flash_msgs();
		}

		private function session_set($key, $data)
		{
			return phpgwapi_cache::session_set($this->get_ui_session_key(), $key, $data);
		}

		private function session_get($key)
		{
			return phpgwapi_cache::session_get($this->get_ui_session_key(), $key);
		}

		/**
		 * Provides a private session cache setter per ui class.
		 */
		protected function ui_session_set($key, $data)
		{
			return $this->session_set(get_class($this).'_'.$key, $data);
		}

		/**
		 * Provides a private session cache getter per ui class .
		 */
		protected function ui_session_get($key)
		{
			return $this->session_get(get_class($this).'_'.$key);
		}

		protected function generate_secret($length = 10)
		{
			return substr(base64_encode(rand(1000000000,9999999999)),0, $length);
		}

		public function add_js_event($event, $js)
		{
			$GLOBALS['phpgw']->js->add_event($event, $js);
		}

		public function add_js_load_event($js)
		{
			$this->add_js_event('load', $js);
		}

		public function link($data)
		{
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		public function redirect($link_data)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		public function flash($msg, $type='success')
		{
			$this->flash_msgs[$msg] = $type == 'success';
		}

		public function flash_form_errors($errors)
		{
			foreach($errors as $field => $msg)
			{
				$this->flash_msgs[$msg] = false;
			}
		}

		public function add_stylesheet($path)
		{
			$GLOBALS['phpgw']->css->add_external_file($path);
		}

		public function add_javascript($app, $pkg, $name)
		{
  			return $GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}

		public function set_active_menu($item)
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $item;
		}

		/**
		* A more flexible version of xslttemplate.add_file
		*/
		public function add_template_file($tmpl)
		{
			if(is_array($tmpl))
			{
				foreach($tmpl as $t)
				{
					$this->add_template_file($t);
				}
				return;
			}
			foreach(array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $tmpl . '.xsl';
				if (file_exists($filename))
				{
					$GLOBALS['phpgw']->xslttpl->xslfiles[$tmpl] = $filename;
					return;
				}
			}
			echo "Template $tmpl not found in search path: ";
			print_r($this->tmpl_search_path);
			die;
		}

		public function render_template($output)
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			if($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				foreach($msgbox_data as & $message)
				{
					echo "<div class='{$message['msgbox_class']}'>";
					echo $message['msgbox_text'];
					echo '</div>';
				}
			}
			echo htmlspecialchars_decode($output);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		/**
		 * Creates an array of translated strings.
		 */
		function lang_array()
		{
			$keys = func_get_args();
			foreach($keys as &$key)
			{
				$key = lang($key);
			}
			return $keys;
		}

		public function add_yui_translation(&$data)
		{
			$this->add_template_file('yui_phpgw_i18n');
			$previous = lang('prev');
			$next = lang('next');
			$first = lang('first');
			$last = lang('last');
			$showing_items = lang('showing items');
			$of = lang('of');
			$to = lang('to');
			$shows_from = lang('shows from');
			$of_total = lang('of total');

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$rows_per_page = 10;
			}

			$data['yui_phpgw_i18n'] = array(
				'Calendar' => array(
					'WEEKDAYS_SHORT' => json_encode($this->lang_array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa')),
					'WEEKDAYS_FULL' => json_encode($this->lang_array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')),
					'MONTHS_LONG' => json_encode($this->lang_array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')),
				),
				'DataTable' => array(
					'MSG_EMPTY' => json_encode(lang('No records found.')),
					'MSG_LOADING' => json_encode(lang("Loading...")),
					'MSG_SORTASC' => json_encode(lang('Click to sort ascending')),
					'MSG_SORTDESC' => json_encode(lang('Click to sort descending')),
				),
				'setupDatePickerHelper' => array(
					'LBL_CHOOSE_DATE' => json_encode(lang('Choose a date')),
				),
				'setupPaginator' => array(
					'pageReportTemplate' => json_encode("{$showing_items} {startRecord} - {endRecord} {$of} {totalRecords}"),
					'previousPageLinkLabel' => json_encode("&lt; {$previous}"),
					'nextPageLinkLabel' => json_encode("{$next} &gt;"),
					'firstPageLinkLabel' => json_encode("&lt;&lt; {$first}"),
					'lastPageLinkLabel' => json_encode("{$last} &gt;&gt;"),
					'template' => json_encode("{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}"),
					'pageReportTemplate' => json_encode("{$shows_from} {startRecord} {$to} {endRecord} {$of_total} {totalRecords}."),
					'rowsPerPage'	=> $rows_per_page
				),
				'common' => array(
					'LBL_NAME' => json_encode(lang('Name')),
					'LBL_TIME' => json_encode(lang('Time')),
					'LBL_WEEK' => json_encode(lang('Week')),
					'LBL_RESOURCE' => json_encode(lang('Resource')),
				),
			);
		}
		public function add_jquery_translation(&$data)
		{
			$this->add_template_file('jquery_phpgw_i18n');
			$previous = lang('prev');
			$next = lang('next');
			$first = lang('first');
			$last = lang('last');
			$showing_items = lang('showing items');
			$of = lang('of');
			$to = lang('to');
			$shows_from = lang('shows from');
			$of_total = lang('of total');

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$rows_per_page = 10;
			}
			$lengthmenu = array();
			for($i = 1; $i < 4; $i++)
			{
				$lengthmenu[0][] = $i * $rows_per_page;
				$lengthmenu[1][] = $i * $rows_per_page;
			}

			if(isset($data['datatable']['allrows']) && $data['datatable']['allrows'])
			{
				$lengthmenu[0][] = -1;
				$lengthmenu[1][] = lang('all');
			}
			$data['jquery_phpgw_i18n'] = array(
				'datatable' => array(
					'emptyTable'	=>	json_encode("No data available in table"),
					'info'			=>	json_encode("Showing _START_ to _END_ of _TOTAL_ entries"),
					'infoEmpty'		=>	json_encode("Showing 0 to 0 of 0 entries"),
					'infoFiltered'	=>  json_encode("(filtered from _MAX_ total entries)"),
					'infoPostFix'	=>	json_encode(""),
					'thousands'		=>	json_encode(","),
					'lengthMenu'	=>	json_encode("Show _MENU_ entries"),
					'loadingRecords'=>	json_encode("Loading..."),
					'processing'	=>	json_encode("Processing..."),
					'search'		=>	json_encode(lang('search')),
					'zeroRecords'	=>	json_encode("No matching records found"),
					'paginate'		=>	json_encode(array(
								'first'		=>	$first,
								'last'		=>	$last,
								'next'		=>	$next,
								'previous'	=>	$previous

					)),
					'aria'	=> json_encode(array(
								'sortAscending'=>  ": activate to sort column ascending",
								'sortDescending'=> ": activate to sort column descending"
					)),
				),
				'lengthmenu' => array('_' => json_encode($lengthmenu))
			);
//			_debug_array($data['jquery_phpgw_i18n']);die();

		}

		public function add_template_helpers()
		{
			$this->add_template_file('helpers');
		}

  		public function render_template_xsl($files, $data)
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if($this->flash_msgs)
			{
				$data['msgbox_data'] = $GLOBALS['phpgw']->common->msgbox($this->flash_msgs);
			}
			else
			{
				$this->add_template_file('msgbox');
			}

			$this->reset_flash_msgs();

//			$this->add_yui_translation($data);
			$this->add_jquery_translation($data);
			$data['webserver_url'] = $GLOBALS['phpgw_info']['server']['webserver_url'];

			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			$this->add_template_file($files);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
		}

		// Add link key to a result array
		public function _add_links(&$value, $key, $menuaction)
		{
			$unset = 0;
			// FIXME: Fugly workaround
			// I cannot figure out why this variable isn't set, but it is needed
			// by the ->link() method, otherwise we wind up in the phpgroupware
			// errorhandler which does lot of weird things and breaks the output
			if (!isset($GLOBALS['phpgw_info']['server']['webserver_url'])) {
				$GLOBALS['phpgw_info']['server']['webserver_url'] = "/";
				$unset = 1;
			}

			$value['link'] = self::link(array('menuaction' => $menuaction, 'id' => $value['id']));

			// FIXME: Fugly workaround
			// I kid you not my friend. There is something very wonky going on
			// in phpgroupware which I cannot figure out.
			// If this variable isn't unset() (if it wasn't set before that is)
			// then it will contain extra slashes and break URLs
			if ($unset) {
				unset($GLOBALS['phpgw_info']['server']['webserver_url']);
			}
		}

		// Build a YUI result style array
		public function yui_results($results)
		{
			if (!$results)
			{
				$results['total_records'] = 0;
				$result['results'] = array();
			}

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			return array(
				'ResultSet' => array(
					'totalResultsAvailable'	=> $results['total_records'],
					'totalRecords' 		=> $results['total_records'],// temeporary
					'recordsReturned'	=> count($results['results']),
					'pageSize' 			=> $num_rows,
					'startIndex' 		=> $results['start'],
					'sortKey' 			=> $results['sort'],
					'sortDir' 			=> $results['dir'],
					'Result' 			=> $results['results'],
					'actions'			=> $results['actions']
				)
			);
		}

		// Build a jquery result style array
		public function jquery_results($results)
		{
			if (!$results)
			{
				$results['total_records'] = 0;
				$results['recordsFiltered'] = 0;
				$result['data'] = array();
			}
	//		_debug_array($result);
			return array(
				'recordsTotal'		=> $results['total_records'],
				'recordsFiltered'	=> $results['total_records'],
				'draw'				=> $results['draw'],
				'data'				=> $results['results']
			);
		}

		public function use_yui_editor($targets)
		{
			/*
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');
			*/
			if(!is_array($targets))
			{
				$targets = array($targets);
			}

			$lang_font_style = lang('Font Style');
			$lang_lists = lang('Lists');
			$lang_insert_item = lang('Insert Item');
			$js = '';
			foreach ( $targets as $target )
			{
				$js .= <<<SCRIPT
			(function() {
				var Dom = YAHOO.util.Dom,
				Event = YAHOO.util.Event;

				var editorConfig = {
					toolbar:
						{buttons: [
	 						{ group: 'textstyle', label: '{$lang_font_style}',
								buttons: [
									{ type: 'push', label: 'Fet CTRL + SHIFT + B', value: 'bold' }
								]
							},
							{ type: 'separator' },
							{ group: 'indentlist', label: '{$lang_lists}',
								buttons: [
									{ type: 'push', label: 'Opprett punktliste', value: 'insertunorderedlist' },
									{ type: 'push', label: 'Opprett nummerert liste', value: 'insertorderedlist' }
								]
							},
							{ type: 'separator' },
							{ group: 'insertitem', label: '{$lang_insert_item}',
								buttons: [
									{ type: 'push', label: 'HTML Lenke CTRL + SHIFT + L', value: 'createlink', disabled: true },
									{ type: 'push', label: 'Sett inn bilde', value: 'insertimage' }
								]
							},
							{ type: 'separator' },
							{ group: 'undoredo', label: 'Angre/Gjenopprett',
								buttons: [
									{ type: 'push', label: 'Angre', value: 'undo' },
									{ type: 'push', label: 'Gjenopprett', value: 'redo' }
								]
							}
						]
					},
					height: '200px',
					width: '700px',
					animate: true,
					dompath: true,
 					handleSubmit: true
				};

				var editorWidget = new YAHOO.widget.Editor('{$target}', editorConfig);
				editorWidget.render();
			})();

SCRIPT;
			}

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/editor/assets/skins/sam/editor.css');
			phpgw::import_class('phpgwapi.yui');
			phpgwapi_yui::load_widget('editor');
			$GLOBALS['phpgw']->js->add_event('load', $js);
		}

		public function render($template,$local_variables = array())
		{
			foreach($local_variables as $name => $value)
			{
				$$name = $value;

			}

			ob_start();
			foreach(array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $template;
				if (file_exists($filename))
				{
					include($filename);
					break;
				}
			}
			$output = ob_get_contents();
			ob_end_clean();
			self::render_template($output);
		}

		/**
		 * Method for JSON queries.
		 *
		 * @return YUI result
		 */
		public abstract function query();

		/**
		 * Generate javascript for the extra column definitions for a partial list
		 *
		 * @param $array_name the name of the javascript variable that contains the column definitions
		 * @param $extra_cols the list of extra columns to set
		 * @return string javascript
		 */
		public static function get_extra_column_defs($array_name, $extra_cols = array())
		{
			$result = "";

			foreach($extra_cols as $col){
				$literal  = '{';
				$literal .= 'key: "' . $col['key'] . '",';
				$literal .= 'label: "' . $col['label'] . '"';
				if (isset($col['formatter'])) {
					$literal .= ',formatter: ' . $col['formatter'];
				}
				if (isset($col['parser'])) {
					$literal .= ',parser: ' . $col['parser'];
				}
				$literal .= '}';

				if($col["index"]){
					$result .= "{$array_name}.splice(".$col["index"].", 0,".$literal.");";
				} else {
					$result .= "{$array_name}.push($literal);";
				}
			}

			return $result;
		}

		/**
		 * Generate javascript definitions for any editor widgets set on columns for
		 * a partial list.
		 *
		 * @param $array_name the name of the javascript variable that contains the column definitions
		 * @param $editors the list of editors, keyed by column key
		 * @return string javascript
		 */
		public static function get_column_editors($array_name, $editors = array())
		{
			$result  = "for (var i in {$array_name}) {\n";
			$result .= "	switch ({$array_name}[i].key) {\n";
			foreach ($editors as $field => $editor) {
				$result .= "		case '{$field}':\n";
				$result .= "			{$array_name}[i].editor = {$editor};\n";
				$result .= "			break;\n";
			}
			$result .= " }\n";
			$result .= "}";

			return $result;
		}

		/**
		 * Returns a html-formatted error message if one is defined in the
		 * list of validation errors on the object we're given.  If no
		 * error is defined, an empty string is returned.
		 *
		 * @param $object the object to display errors for
		 * @param $field the name of the attribute to display errors for
		 * @return string a html formatted error message
		 */
		public static function get_field_error($object, $field)
		{
			if(isset($object))
			{
				$errors = $object->get_validation_errors();

				if ($errors[$field]) {
					return '<label class="error" for="' . $field . '">' . $errors[$field] . '</label>';
				}
				return '';
			}
		}

		public static function get_messages($messages, $message_type)
		{
			$output = '';
			if(is_array($messages) && count($messages) > 0) // Array of messages
			{
				$output = "<div class=\"{$message_type}\">";
				foreach($messages as $message)
				{
					$output .= "<p class=\"message\">{$message}</p>";
				}
				$output .= "</div>";
			}
			else if($messages) {
				$output = "<div class=\"{$message_type}\"><p class=\"message\">{$messages}</p></div>";
			}
			return $output;
		}
		/**
		 * Returns a html-formatted error message to display on top of the page.  If
		 * no error is defined, an empty string is returned.
		 *
		 * @param $error the error to display
		 * @return string a html formatted error message
		 */
		public static function get_page_error($errors)
		{
			return self::get_messages($errors, 'error');
		}

		/**
		 * Returns a html-formatted error message to display on top of the page.  If
		 * no error is defined, an empty string is returned.
		 *
		 * @param $error the error to display
		 * @return string a html formatted error message
		 */
		public static function get_page_warning($warnings)
		{
			return self::get_messages($warnings, 'warning');
		}

		/**
		 * Returns a html-formatted info message to display on top of the page.  If
		 * no message is defined, an empty string is returned.
		 *
		 * @param $message the message to display
		 * @return string a html formatted info message
		 */
		public static function get_page_message($messages)
		{
			return self::get_messages($messages, 'info');
		}

		/**
		 * Download xls, csv or similar file representation of a data table
		 */
		public function download()
		{
			$list = $this->query();
			$list = $list['ResultSet']['Result'];

			$keys = array();

			if(count($list[0]) > 0) {
				foreach($list[0] as $key => $value) {
					if(!is_array($value)) {
						array_push($keys, $key);
					}
				}
			}

			// Remove newlines from output
			$count = count($list);
			for($i = 0; $i < $count; $i++)
			{
 				foreach ($list[$i] as $key => &$data)
 				{
	 				$data = str_replace(array("\n","\r\n", "<br>"),'',$data);
 				}
			}

			 // Use keys as headings
			$headings = array();
			$count_keys = count($keys);
			for($j=0;$j<$count_keys;$j++)
			{
				array_push($headings, lang($keys[$j]));
			}

			$property_common = CreateObject('property.bocommon');
			$property_common->download($list, $keys, $headings);
		}

		/**
		 * Returns a human-readable string from a lower case and underscored word by replacing underscores
		 * with a space, and by upper-casing the initial characters.
		 *
		 * @param  string $lower_case_and_underscored_word String to make more readable.
		 *
		 * @return string Human-readable string.
		 */
		public static function humanize($lower_case_and_underscored_word)
		{
			if (substr($lower_case_and_underscored_word, -3) === '_id')
			{
				$lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
			}

			return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
		}

	  /**
	   * Retrieves an array of files from $_FILES
	   *
	   * @param  string $key  	A key
	   * @return array  		An associative array of files
	   */
		public function get_files($key = null)
		{
			if (!$this->filesArray)
			{
				$this->filesArray = self::convert_file_information($_FILES);
			}

			return is_null($key) ? $this->filesArray : (isset($this->filesArray[$key]) ? $this->filesArray[$key] : array());
		}

		public function toggle_show_showall()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}
			else
			{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => $this->url_prefix.'.index'));
		}

/*
		public function use_yui_editor()
		{
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');
		}

*/		static protected function fix_php_files_array($data)
		{
			$fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
			$keys = array_keys($data);
			sort($keys);

			if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name']))
			{
			 	return $data;
			}

			$files = $data;
			foreach ($fileKeys as $k)
			{
			 	unset($files[$k]);
			}
			foreach (array_keys($data['name']) as $key)
			{
				$files[$key] = self::fix_php_files_array(array(
					'error'	=> $data['error'][$key],
					'name'	 => $data['name'][$key],
					'type'	 => $data['type'][$key],
					'tmp_name' => $data['tmp_name'][$key],
					'size'	 => $data['size'][$key],
				));
			}

			return $files;
		}

		/**
		* It's safe to pass an already converted array, in which case this method just returns the original array unmodified.
		*
		* @param  array $taintedFiles An array representing uploaded file information
		*
		* @return array An array of re-ordered uploaded file information
		*/
		static public function convert_file_information(array $taintedFiles)
		{
			$files = array();
			foreach ($taintedFiles as $key => $data)
			{
				$files[$key] = self::fix_php_files_array($data);
			}

			return $files;
		}
	}
