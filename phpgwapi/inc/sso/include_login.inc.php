<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/
	
	/* 
	 * Generic include for login.php like pages
	 */
	$GLOBALS['phpgw_info'] = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
	);

	$header = dirname(realpath(__FILE__)) . '/../../../header.inc.php';
	if ( !file_exists($header) )
	{
		Header('Location: setup/index.php');
		exit;
	}

	/**
	* Include phpgroupware header
	*/
	require_once $header;

	$GLOBALS['phpgw_info']['server']['template_set'] = $GLOBALS['phpgw_info']['login_template_set'];
	$GLOBALS['phpgw_info']['server']['template_dir'] = PHPGW_SERVER_ROOT
	 		. "/phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}";

	$tmpl = CreateObject('phpgwapi.Template', $GLOBALS["phpgw_info"]['server']['template_dir']);

	/*
	 * Generic include for mapping / remoteuser mode
	 */
	$phpgw_url_for_sso = '/login.php';
	if(isset($GLOBALS['phpgw_info']['server']['half_remote_user']) && $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser')         
	{
		$phpgw_url_for_sso = '/phpgwapi/inc/sso/login_server.php';
	}

	$phpgw_map_location = 'local';
	$phpgw_map_authtype = 'remoteuser';
	if ( isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) )
	{
		$phpgw_map_location = $_SERVER['HTTP_SHIB_ORIGIN_SITE'];
		$phpgw_map_authtype = 'shibboleth';
	}

	//Create the mapping if necessary :
	if(isset($GLOBALS['phpgw_info']['server']['mapping']) 
		&& !empty($GLOBALS['phpgw_info']['server']['mapping']))
	{
		if ( !is_object($GLOBALS['phpgw']->mapping) )
		{
			$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
		}
	}

	// This is used for system downtime, to prevent new logins.
	if( isset($GLOBALS['phpgw_info']['server']['deny_all_logins'])
		&& $GLOBALS['phpgw_info']['server']['deny_all_logins'] )
	{
		$tmpl->set_file
		(
			array
			(
				'login_form'  => 'login_denylogin.tpl'
			)
		);
		$tmpl->pfp('loginout','login_form');
		exit;
	}

	/*
	* Generic function for displaying login.tpl depending on needs :
	*/
	class phpgw_uilogin
	{
		var $tmpl = null;
		var $msg_only=false;

		function phpgw_uilogin(&$tmpl, $msg_only)
		{
			$this->tmpl = $tmpl;
			$this->msg_only = $msg_only;
		}

		/**
		* Check logout error code
		*
		* @param integer $code Error code
		* @return string Error message
		*/
		function check_logoutcode($code)
		{
			switch($code)
			{
				case 1:
					return lang('You have been successfully logged out');
				case 2:
					return lang('Sorry, your login has expired');
				case 5:
					return lang('Bad login or password');
				case 20:
					return lang('Cannot find the mapping ! (please advice your adminstrator)');
				case 21:
					return lang('you had inactive mapping to %1 account', phpgw::get_var('phpgw_account', 'string', 'GET', ''));
				case 22:
					$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
					$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
					$GLOBALS['phpgw']->session->phpgw_setcookie('domain');
					return lang('you seemed to have an active session elsewhere for the domain "%1", now set to expired - please try again', phpgw::get_var('domain', 'string', 'COOKIE'));
				case 99:
					return lang('Blocked, too many attempts');
				case 10:
					$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
					$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
					$GLOBALS['phpgw']->session->phpgw_setcookie('domain');

					// fix for bug php4 expired sessions bug
					if($GLOBALS['phpgw_info']['server']['sessions_type'] == 'php')
					{
						$GLOBALS['phpgw']->session->phpgw_setcookie(PHPGW_PHPSESSID);
					}

					return lang('Your session could not be verified.');
				default:
					return '&nbsp;';
			}
		}
		
		
		/**
		* Check languages
		*/
		function check_langs()
		{
			// echo "<h1>check_langs()</h1>\n";
			if (isset($GLOBALS['phpgw_info']['server']['lang_ctimes'])
					&& !is_array($GLOBALS['phpgw_info']['server']['lang_ctimes']))
			{
				$GLOBALS['phpgw_info']['server']['lang_ctimes'] = unserialize($GLOBALS['phpgw_info']['server']['lang_ctimes']);
			}
			elseif( !isset($GLOBALS['phpgw_info']['server']['lang_ctimes']) )
			{
				$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
			}
			// _debug_array($GLOBALS['phpgw_info']['server']['lang_ctimes']);
			
			$lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			$apps = (array)$GLOBALS['phpgw_info']['user']['apps'];
			$apps['phpgwapi'] = true;	// check the api too
			foreach ( array_keys($apps) as $app )
			{
				$fname = PHPGW_SERVER_ROOT . "/$app/setup/phpgw_$lang.lang";
				
				if (file_exists($fname))
				{
					$ctime = filectime($fname);
					$ltime = isset($GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang]) && 
						isset($GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app]) ? 
						(int) $GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app] : 0;
					//echo "checking lang='$lang', app='$app', ctime='$ctime', ltime='$ltime'<br>\n";
					
					if ($ctime != $ltime)
					{
						$this->update_langs();		// update all langs
						break;
					}
				}
			}
		}

		/**
		* Update languages
		*/
		function update_langs()
		{
			$langs = $GLOBALS['phpgw']->translation->get_installed_langs();
			foreach ( array_keys($langs) as $lang )
			{
				$langs[$lang] = $lang;
			}
			$GLOBALS['phpgw']->translation->update_db($langs, 'dumpold');
		}

		function phpgw_display_login($variables)
		{
			
			$lang = array
			(
				'domain'	=> lang('domain'),
				'username'	=> lang('username'),
				'password'	=> lang('password')
			);

			$text_len = 0;
			foreach($lang as $key => $text)
			{
				if($text_len < strlen($text))
				{
					$text_len = strlen($text);
				}
			}

			foreach($lang as $key => & $text)
			{
				$text = str_repeat('&nbsp;', ($text_len-strlen($text))) . $text;
			}
		
			$this->tmpl->set_file(array('login_form'  => 'login.tpl'));
			$this->tmpl->set_var('charset', lang('charset'));
			$this->tmpl->set_block('login_form', 'domain_option', 'domain_options');
			$this->tmpl->set_block('login_form', 'domain_select', 'domain_selects');
			$this->tmpl->set_block('login_form', 'login_additional_info', 'login_additional_infos');
			$this->tmpl->set_block('login_form', 'login_check_passwd', 'login_check_passwds');
			$this->tmpl->set_block('login_form', 'domain_from_host', 'domain_from_hosts');
			$this->tmpl->set_block('login_form', 'password_block', 'password_blocks');
			$this->tmpl->set_block('login_form', 'loging_block', 'loging_blocks');
			$this->tmpl->set_block('login_form', 'button_block', 'button_blocks');

			if( $GLOBALS['phpgw_info']['server']['domain_from_host'] 
				&& !$GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
			{
				$this->tmpl->set_var(
						array(
							'domain_selects'	=> '',
							'logindomain'		=> $_SERVER['SERVER_NAME']
						)
					);
				$this->tmpl->parse('domain_from_hosts', 'domain_from_host');
			}
			elseif( $GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
			{
				foreach($GLOBALS['phpgw_domain'] as $domain_name => $domain_vars)
				{	
					$this->tmpl->set_var('domain_name', $domain_name);

					if (isset($_COOKIE['last_domain']) && $_COOKIE['last_domain'] == $domain_name)
					{
						$this->tmpl->set_var('domain_selected', 'selected="selected"');
					}
					else
					{
						$this->tmpl->set_var('domain_selected', '');
					}

					$this->tmpl->parse('domain_options', 'domain_option', true);
				}
				$this->tmpl->parse('domain_selects', 'domain_select');
				$this->tmpl->set_var(
						array(
							'domain_from_hosts'	=> '',
							'lang_domain'		=> $lang['domain']
						)
					);
			}
			else
			{
				$this->tmpl->set_var(
						array(
							'domain_selects'		=> '',
							'domain_from_hosts'	=> ''
						)
					);
				
			}

			if (isset($_COOKIE['last_loginid']))
			{
				$accounts = CreateObject('phpgwapi.accounts');
				$prefs = CreateObject('phpgwapi.preferences', $accounts->name2id($_COOKIE['last_loginid']));

				if (! $prefs->account_id)
				{
					$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = 'en';
				}
				else
				{
					$GLOBALS['phpgw_info']['user']['preferences'] = $prefs->read_repository();
				}
				#print 'LANG:' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] . '<br>';
			}
			else
			{
				// If the lastloginid cookies isn't set, we will default to english.
				// Change this if you need.
				$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = 'en';
			}
			$GLOBALS['phpgw']->translation->add_app('login');
			$GLOBALS['phpgw']->translation->add_app('loginscreen');
			if ( ($login_msg = lang('loginscreen_message') ) != '!loginscreen_message')
			{
				$this->tmpl->set_var('lang_message', stripslashes($login_msg) );
			}
			else
			{
				if(isset($variables['lang_message']))
				{
					$this->tmpl->set_var('lang_message', $variables['lang_message']);
				}
				else
				{
					$this->tmpl->set_var('lang_message', '&nbsp;');
				}
			}

			if( ( !isset($GLOBALS['phpgw_info']['server']['usecookies']) || !$GLOBALS['phpgw_info']['server']['usecookies'] )
				&& (isset($_COOKIE) && is_array($_COOKIE) ) )
			{
				if ( isset($_COOKIE['last_loginid']) )
				{
					unset($_COOKIE['last_loginid']);
				}

				if ( isset($_COOKIE['last_domain']) )
				{
					unset($_COOKIE['last_domain']);
				}
			}
			
			$last_loginid = isset($_COOKIE['last_loginid']) ? $_COOKIE['last_loginid'] : '';
			if($GLOBALS['phpgw_info']['server']['show_domain_selectbox'] && $last_loginid !== '')
			{
				reset($GLOBALS['phpgw_domain']);
				list($default_domain) = each($GLOBALS['phpgw_domain']);

				if ($_COOKIE['last_domain'] != $default_domain && !empty($_COOKIE['last_domain']) && !$GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
				{
					$last_loginid .= '@' . $_COOKIE['last_domain'];
				}
			}

			if(isset($variables['lang_firstname']) && isset($variables['lang_lastname']) && isset($variables['lang_confirm_password']))
			{
				//We first put the login in it
				if(isset($variables['login']))
				{
					$last_loginid = $variables['login'];
				}

				//then first / last name
				$this->tmpl->set_var('lang_firstname', $variables['lang_firstname']);
				$this->tmpl->set_var('lang_lastname', $variables['lang_lastname']);
				if(isset($variables['firstname']))
				{
					$this->tmpl->set_var('firstname', $variables['firstname']);
				}
				if(isset($variables['lastname']))
				{
					$this->tmpl->set_var('lastname', $variables['lastname']);
				}
				//parsing the block
				$this->tmpl->parse('login_additional_infos', 'login_additional_info');
				$this->tmpl->set_var('login_additional_info','');

				//then the passwd confirm
				$this->tmpl->set_var('lang_confirm_password', $variables['lang_confirm_password']);
				//parsing the block
				$this->tmpl->parse('login_check_passwds', 'login_check_passwd');

				if(isset($variables['login_read_only']) && $variables['login_read_only'])
				{
					$this->tmpl->set_var('login_read_only', ' readonly="readonly"');
				}

			}
			else
			{
				$this->tmpl->set_var(array(
											'login_additional_info' => '',
											'login_check_psswd' => ''
											)
									);
			}

			//FIXME switch to an array
			$extra_vars = array();
			foreach($_GET as $name => $value)
			{
				if (ereg('phpgw_',$name))
				{
					$extra_vars[$name] = urlencode($value);
				}
			}

			$cd = 0;
			if ( isset($_GET['cd']) )
			{
				$cd = (int) $_GET['cd'];
			}

			$this->tmpl->set_var('login_url', $GLOBALS['phpgw_info']['server']['webserver_url'] . '/'.$variables['partial_url'].'?' . http_build_query($extra_vars) );
			$this->tmpl->set_var('registration_url',$GLOBALS['phpgw_info']['server']['webserver_url'] . '/registration/');
			$this->tmpl->set_var('version', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
			$this->tmpl->set_var('cd', $this->check_logoutcode($cd) );
			$this->tmpl->set_var('last_loginid', $last_loginid);

			$this->tmpl->set_var('lang_username', $lang['username']);
			$this->tmpl->set_var('lang_password', $lang['password']);
			if(isset($variables['lang_login']))
			{
				$this->tmpl->set_var('lang_login', $variables['lang_login']);
			}

			$this->tmpl->set_var('lang_testjs', lang('Your browser does not support javascript and/or css, please use a modern standards compliant browser.  If you have disabled either of these features please enable them for this site.') );

			if(isset($variables['lang_additional_url']) && isset($variables['additional_url']))
			{
				$this->tmpl->set_var('lang_return_sso_login', $variables['lang_additional_url']);
				$this->tmpl->set_var('return_sso_login_url', $variables['additional_url']);
			}

			$this->tmpl->set_var('website_title', isset($GLOBALS['phpgw_info']['server']['site_title'])
								? $GLOBALS['phpgw_info']['server']['site_title'] 
								: 'phpGroupWare'
								);

			$this->tmpl->set_var('template_set', $GLOBALS['phpgw_info']['login_template_set']);
			
			if( is_file("{$GLOBALS['phpgw_info']['server']['template_dir']}/css/base.css") )
			{
				$base_css = "phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}/css/base.css";
			}
			else
			{
				$base_css = 'phpgwapi/templates/base/css/base.css';
			}

			if( is_file("{$GLOBALS['phpgw_info']['server']['template_dir']}/css/login.css") )
			{
				$login_css = "phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}/css/login.css";
			}
			else
			{
				$login_css = 'phpgwapi/templates/base/css/login.css';
			}

			$this->tmpl->set_var('base_css', $base_css);
			$this->tmpl->set_var('login_css', $login_css);

			$autocomplete = '';
			if ( isset($GLOBALS['phpgw_info']['server']['autocomplete_login'])
				&& $GLOBALS['phpgw_info']['server']['autocomplete_login'] )
			{
				$autocomplete = 'autocomplete="off"';
			}
			$this->tmpl->set_var('autocomplete', $autocomplete);
			unset($autocomplete);
			if(!$this->msg_only)
			{
				$this->tmpl->parse('loging_blocks', 'loging_block');
				$this->tmpl->parse('password_blocks', 'password_block');
				$this->tmpl->parse('button_blocks', 'button_block');
			}
			$this->tmpl->pfp('loginout','login_form');
		}
	}
