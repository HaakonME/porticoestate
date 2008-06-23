<?php
	/**
	* phpGroupWare - Manual
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package manual
 	* @version $Id$
	*/

	/**
	 * Manual Renderer
	 * @package manual
	 */

	class manual_uimanual
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'			=> true,
			'help'			=> true,
			'attrib_help'	=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw']->help = CreateObject('manual.help_helper');
		}

		function index()
		{
//			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp		= phpgw::get_var('app');

			if (!$this->currentapp || $this->currentapp == 'manual')
			{
				$this->currentapp = 'help';
			}
		
			if ($this->currentapp == 'help')
			{
				$GLOBALS['phpgw']->hooks->process('help',array('manual'));
			}
			else
			{
				$GLOBALS['phpgw']->hooks->single('help',$this->currentapp);
			}

			$appname		= lang('Help');
			$function_msg	= lang($this->currentapp);
		
			$GLOBALS['phpgw_info']['flags']['app_header'] = $appname . ' - ' . $appname;

			$GLOBALS['phpgw']->common->phpgw_header(true);
//			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('help' => $GLOBALS['phpgw']->help->output));

		}

		function help()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$odt2xhtml	= CreateObject('manual.odt2xhtml');
			$app = phpgw::get_var('app', 'string', 'GET');
			$section = phpgw::get_var('section', 'string', 'GET');

			if(!$section)
			{
				$menuaction = phpgw::get_var('referer');
				if($menuaction)
				{
					list($app_from_referer, $class, $method) = explode('.',$menuaction);
					if ( strpos($class, 'ui') === 0 )
					{
						$class = ltrim($class, 'ui');
					}
					$section = "{$class}.{$method}";
				}
			}	

			if(!$app)
			{
				$app = isset($app_from_referer) && $app_from_referer ? $app_from_referer : 'manual';
			}

			$section 	= $section ? $section : 'overview';
			$lang 		= strtoupper(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] ? $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']: 'en');
			$navbar 	= phpgw::get_var('navbar', 'string', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $app . '::' . lang($section);
			$GLOBALS['phpgw']->common->phpgw_header();
			if($navbar)
			{
				$GLOBALS['phpgw']->help->currentapp = $app;
				$GLOBALS['phpgw']->help->section = $section;
				$GLOBALS['phpgw']->hooks->process('help',array('manual'));
				parse_navbar();
			}
				
			$odtfile = PHPGW_SERVER_ROOT . "/{$app}/help/{$lang}/{$section}.odt";

			if(is_file($odtfile))
			{
				echo $odt2xhtml->oo_convert($odt2xhtml->oo_unzip($odtfile));
			}
			else
			{
				$error = lang('Invalid or missing manual entry requested, please contact your system administrator');
				echo <<<HTML
					<div class="err">$error</div>

HTML;
			}
			
			$GLOBALS['phpgw']->common->phpgw_footer();

		}

		function attrib_help()
		{
			$t =& $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;

			$appname	= phpgw::get_var('appname');
			$location 	= phpgw::get_var('location');
			$id			= phpgw::get_var('id', 'int');

			$attrib_data 	= $GLOBALS['phpgw']->custom_fields->get($appname, $location, $id);

			$function_msg	= lang('Help');

			$t->set_file('help', 'help.tpl');
			$t->set_var('title', lang('Help') . " - \"{$attrib_data['input_text']}\"");
			$t->set_var('help_msg', $attrib_data['helpmsg'] );
			$t->set_var('lang_close', lang('close'));
											
			$GLOBALS['phpgw']->common->phpgw_header();
			$t->pfp('out','help');
		}
	}
