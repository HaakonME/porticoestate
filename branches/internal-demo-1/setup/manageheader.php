<?php
	/**
	* phpGroupWare Setup - http://phpgroupware.org
	*
	* @copyright Portions Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id: manageheader.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'nocachecontrol'	=> true,
		'noheader'			=> true,
		'nonavbar'			=> true,
		'currentapp'		=> 'setup',
		'noapi' 			=> true
	);
	
	/**
	 * Include setup functions
	 */
	require_once('./inc/functions.inc.php');

	//$GLOBALS['phpgw_info']['server']['versions']['current_header'] = $setup_info['phpgwapi']['versions']['current_header'];
	unset($setup_info);

	$adddomain = phpgw::get_var('adddomain', 'string', 'POST');

	/**
	 * Check form values
	 */
	function check_form_values()
	{
		$errors = '';
		$domains = phpgw::get_var('domains', 'string', 'POST');
		if ( !is_array($domains) )
		{
			$domains = array();
		}

		foreach($domains as $k => $v)
		{
			$deletedomain = phpgw::get_var('deletedomain', 'string', 'POST');
			if ( isset($deletedomain[$k]) )
			{
				continue;
			}
			
			if(!$_POST['settings'][$k]['config_pass'])
			{
				$errors .= '<br />' . lang("You didn't enter a config password for domain %1",$v);
			}
		}

		$setting = phpgw::get_var('setting', 'string', 'POST');
		if(!$setting['HEADER_ADMIN_PASSWORD'])
		{
			$errors .= '<br />' . lang("You didn't enter a header admin password");
		}

		if($errors)
		{
			$GLOBALS['phpgw_setup']->html->show_header('Error',True);
			echo $errors;
			exit;
		}
	}

	/* authentication phase */
	$GLOBALS['phpgw_info']['setup']['stage']['header'] = $GLOBALS['phpgw_setup']->detection->check_header();

	// added these to let the app work, need to templatize still
	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
	$setup_tpl->set_file(array
	(
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl',
		'T_login_main' => 'login_main.tpl',
		'T_login_stage_header' => 'login_stage_header.tpl',
		'T_setup_manage' => 'manageheader.tpl'
	));
	$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
	$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
	$setup_tpl->set_block('T_setup_manage','manageheader','manageheader');
	$setup_tpl->set_block('T_setup_manage','domain','domain');
	
	$setup_tpl->set_var('HeaderLoginWarning', lang('Warning: All your passwords (database, phpGroupWare admin,...)<br /> will be shown in plain text after you log in for header administration.'));
	$setup_tpl->set_var('lang_cookies_must_be_enabled', lang('<b>NOTE:</b> You must have cookies enabled to use setup and header admin!') );

	/* Detect current mode */
	switch($GLOBALS['phpgw_info']['setup']['stage']['header'])
	{
		case 1:
			$GLOBALS['phpgw_info']['setup']['HeaderFormMSG'] = lang('Create your header.inc.php');
			$GLOBALS['phpgw_info']['setup']['PageMSG'] = lang('You have not created your header.inc.php yet!<br /> You can create it now.');
			break;
		case 2:
			$GLOBALS['phpgw_info']['setup']['HeaderFormMSG'] = lang('Your header admin password is NOT set. Please set it now!');
			$GLOBALS['phpgw_info']['setup']['PageMSG'] = lang('Your header admin password is NOT set. Please set it now!');
			break;
		case 3:
			$GLOBALS['phpgw_info']['setup']['HeaderFormMSG'] = lang('Your header.inc.php needs upgrading.');
			$GLOBALS['phpgw_info']['setup']['PageMSG'] = lang('<p class="msg">Your header.inc.php needs upgrading.<br />WARNING! MAKE BACKUPS!</p>');
			$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('Your header.inc.php needs upgrading.');
			if (!$GLOBALS['phpgw_setup']->auth('Header'))
			{
				$GLOBALS['phpgw_setup']->html->show_header('Please login',True);
				$GLOBALS['phpgw_setup']->html->login_form();
				$GLOBALS['phpgw_setup']->html->show_footer();
				exit;
			}
			break;
		case 10:
			if (!$GLOBALS['phpgw_setup']->auth('Header'))
			{
				$GLOBALS['phpgw_setup']->html->show_header('Please login',True);
				$GLOBALS['phpgw_setup']->html->login_form();
				$GLOBALS['phpgw_setup']->html->show_footer();
				exit;
			}
			$GLOBALS['phpgw_info']['setup']['HeaderFormMSG'] = lang('Edit your header.inc.php');
			$GLOBALS['phpgw_info']['setup']['PageMSG'] = lang('Edit your existing header.inc.php');
			break;
	}

	$action = phpgw::get_var('action', 'string', 'POST');
	if ( is_array($action) )
	{
		$action = array_shift(array_keys($action));
	}
	switch($action)
	{
		case 'download':
			check_form_values();
			$header_template = CreateObject('phpgwapi.Template','../');
			$b = CreateObject('phpgwapi.browser');
			$b->content_header('header.inc.php','application/octet-stream');
			/*
			header('Content-disposition: attachment; filename="header.inc.php"');
			header('Content-type: application/octet-stream');
			header('Pragma: no-cache');
			header('Expires: 0');
			*/
			$newheader = $GLOBALS['phpgw_setup']->html->generate_header();
			echo $newheader;
			break;
		case 'view':
			check_form_values();
			$header_template = CreateObject('phpgwapi.Template','../');
			$GLOBALS['phpgw_setup']->html->show_header('Generated header.inc.php', False, 'header');
			echo '<br />' . lang('Save this text as contents of your header.inc.php') . '<br /><hr />';
			$newheader = $GLOBALS['phpgw_setup']->html->generate_header();
			echo '<pre>';
			echo htmlentities($newheader);
			echo '</pre><hr />';
			echo '<form action="index.php" method="post">';
			echo '<br />' . lang('After retrieving the file, put it into place as the header.inc.php.  Then, click "continue".') . '<br />';
			echo '<input type="hidden" name="FormLogout" value="header" />';
			echo '<input type="submit" name="junk" value="' . lang('Continue') . '" />';
			echo '</form>';
			echo '</body></html>';
			break;
		case 'write':
			check_form_values();
			$header_template = CreateObject('phpgwapi.Template','../');
			if(is_writeable('../header.inc.php') || (!file_exists('../header.inc.php') && is_writeable('../')))
			{
				$newheader = $GLOBALS['phpgw_setup']->html->generate_header();
				$fsetup = fopen('../header.inc.php','wb');
				fwrite($fsetup,$newheader);
				fclose($fsetup);
				$GLOBALS['phpgw_setup']->html->show_header('Saved header.inc.php', False, 'header');
				echo '<form action="index.php" method="post">';
				echo '<br />Created header.inc.php! ';
				echo '<input type="hidden" name="FormLogout" value="header" />';
				echo '<input type="submit" name="junk" value="' . lang('Continue') . '" />';
				echo '</form>';
				echo '</body></html>';
				break;
			}
			else
			{
				$GLOBALS['phpgw_setup']->html->show_header('Error generating header.inc.php', False, 'header');
				echo lang('Could not open header.inc.php for writing!') . '<br />' . "\n";
				echo lang('Please check read/write permissions on directories, or back up and use another option.') . '<br />';
				echo '</td></tr></table></body></html>';
			}
			break;
		default:
			$GLOBALS['phpgw_setup']->html->show_header($GLOBALS['phpgw_info']['setup']['HeaderFormMSG'], False, 'header');

			$detected = '';

			$detected .= $GLOBALS['phpgw_info']['setup']['PageMSG'];
			if (!function_exists('filter_var')) // ext/filter was added in 5.2.0
			{
				$detected .= '<b><p align="center" class="msg">'
					. lang('You appear to be using PHP %1, phpGroupWare requires 5.2.0 or later', PHP_VERSION). "\n"
					. '</p></b><td></tr></table></body></html>';
				die($detected);
			}
			
			$phpver = lang('<li>You appear to be using PHP 5.2+') . "</li>\n";
			$supported_sessions_type[] = 'db';
			$supported_sessions_type[] = 'php';

			$detected .= '<table id="manageheader">' . "\n";

			
			if ( !isset($ConfigLang) || !$ConfigLang )
			{
				$_POST['ConfigLang'] = 'en';
				$detected .= '<tr><td colspan="2"><form action="manageheader.php" method="post">Please Select your language ' . lang_select(True) . "</form></td></tr>\n";
			}

			$manual = '<a href="../doc/en_US/html/admin/" target="manual">'.lang('phpGroupWare Administration Manual').'</a>';
			$detected .= '<tr><td colspan="2"><p><strong>' . lang('Please consult the %1.', $manual) . "</strong></td></tr>\n";

			$detected .= '<tr class="th"><td colspan="2">' . lang('Analysis') . "</td></tr><tr><td colspan=\"2\">\n<ul id=\"analysis\">\n$phpver";

			$supported_db = array();
			if (extension_loaded('mysql') || function_exists('mysql_connect'))
			{
				$detected .= '<li>' . lang('You appear to have MySQL support enabled') . "</li>\n";
				$supported_db[] = 'mysql';
			}
			else
			{
				$detected .= '<li class="warn">' . lang('No MySQL support found. Disabling') . "</li>\n";
			}
			if (extension_loaded('pgsql') || function_exists('pg_connect'))
			{
				$detected .= '<li>' . lang('You appear to have Postgres-DB support enabled') . "</li>\n";
				$supported_db[]  = 'postgres';
			}
			else
			{
				$detected .= '<li class="warn">' . lang('No Postgres-DB support found. Disabling') . "</li>\n";
			}
			if (extension_loaded('mssql') || function_exists('mssql_connect'))
			{
				$detected .= '<li>' . lang('You appear to have Microsoft SQL Server support enabled') . "</li>\n";
				$supported_db[] = 'mssql';
			}
			else
			{
				$detected .= '<li class="warn">' . lang('No Microsoft SQL Server support found. Disabling') . "</li>\n";
			}
			if (extension_loaded('oci8'))
			{
				$detected .= '<li>' . lang('You appear to have Oracle V8 (OCI) support enabled') . "</li>\n";
				$supported_db[] = 'oracle';
			}
			else
			{
				if(extension_loaded('oracle'))
				{
					$detected .= '<li>' . lang('You appear to have Oracle support enabled') . "</li>\n";
					$supported_db[] = 'oracle';
				}
				else
				{
					$detected .= '<li class="warn">' . lang('No Oracle-DB support found. Disabling') . "</li>\n";
				}
			}

			/* Not currently supported
			if (extension_loaded('odbc') || function_exists('odbc_connect'))
			{
				$detected .= lang('You appear to have ODBC/SAPDB support enabled') . '<br />' . "\n";
				$supported_db[] = 'sapdb';
			}
			else
			{
				$detected .= lang('No ODBC/SAPDB support found. Disabling') . '<br />' . "\n";
			}
			*/
			if ( !count($supported_db) )
			{
				$lang_nodb = lang('Did not find any valid DB support!');
				$lang_fix = lang('Try to configure your php to support one of the above mentioned DBMS, or install phpGroupWare by hand.');
				$detected .= <<<HTML
							<li class="err">$lang_nodb</li>
						</ul>
						<h2>$lang_fix</h2>
					</b>
				<td>
			</tr>
		</table>
	</body>
</html>

HTML;
				die($detected);
			}

			/*
			if (extension_loaded('xml') || function_exists('xml_parser_create'))
			{
				$detected .= lang('You appear to have XML support enabled') . '<br />' . "\n";
				$xml_enabled = 'True';
			}
			else
			{
				$detected .= lang('No XML support found. Disabling') . '<br />' . "\n";
			}
			*/

			if(extension_loaded('imap') || function_exists('imap_open'))
			{
				$detected .= '<li>' . lang('You appear to have IMAP support enabled') . "</li>\n";
			}
			else
			{
				$detected .= '<li class="warn">' . lang('No IMAP support found. Email functions will be disabled') . "</li>\n";
			}
			if( extension_loaded('xsl') && class_exists('XSLTProcessor') )
			{
				$detected .= '<li>' . lang('You appear to have XML/XSLT support enabled') . "</li>\n";
			}
			else
			{
				$lang_noxsl = lang('No XSL support found.');
				$lang_fix = lang('You must install the php-xsl extension to continue');
				$detected .= <<<HTML
							<li class="err">$lang_noxsl</li>
						</ul>
						<h2>$lang_fix</h2>
					</b>
				<td>
			</tr>
		</table>
	</body>
</html>

HTML;
				die($detected);

			}
			
			$no_guess = false;
			if(file_exists('../header.inc.php') && is_file('../header.inc.php') && is_readable('../header.inc.php'))
			{
				$detected .= '<li>' . lang('Found existing configuration file. Loading settings from the file...') . "</li>\n";
				$GLOBALS['phpgw_info']['flags']['noapi'] = True;
				$no_guess = true;
				/* This code makes sure the newer multi-domain supporting header.inc.php is being used */
				if(!isset($GLOBALS['phpgw_domain']))
				{
					$detected .= '<li class="warn">' . lang("You're using an old configuration file format...") . "</li>\n";
					$detected .= '<li>' . lang('Importing old settings into the new format....') . "</li>\n";
				}
				else
				{
					if(@$GLOBALS['phpgw_info']['server']['header_version'] != @$GLOBALS['phpgw_info']['server']['current_header_version'])
					{
						$detected .= '<li class="warn">' . lang("You're using an old header.inc.php version...") . "</li>\n";
						$detected .= '<li>' . lang('Importing old settings into the new format....') . "</li>\n";
					}
					reset($GLOBALS['phpgw_domain']);
					$default_domain = each($GLOBALS['phpgw_domain']);
					$GLOBALS['phpgw_info']['server']['default_domain'] = $default_domain[0];
					unset($default_domain); // we kill this for security reasons
					$GLOBALS['phpgw_info']['server']['config_passwd'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['config_passwd'];

					if ( phpgw::get_var('adddomain', 'string', 'POST') )
					{
						$GLOBALS['phpgw_domain'][lang('new')] = array();
					}

					if( !isset($GLOBALS['phpgw_domain']) )
					{
						$GLOBALS['phpgw_domain'] = array();
					}
					
					foreach($GLOBALS['phpgw_domain'] as $key => $val)
					{
						$setup_tpl->set_var('lang_domain',lang('Domain'));
						$setup_tpl->set_var('lang_delete',lang('Delete'));
						$setup_tpl->set_var('db_domain',$key);
						$setup_tpl->set_var('db_host',$GLOBALS['phpgw_domain'][$key]['db_host']);
						$setup_tpl->set_var('db_name',$GLOBALS['phpgw_domain'][$key]['db_name']);
						$setup_tpl->set_var('db_user',$GLOBALS['phpgw_domain'][$key]['db_user']);
						$setup_tpl->set_var('db_pass',$GLOBALS['phpgw_domain'][$key]['db_pass']);
						$setup_tpl->set_var('db_type',$GLOBALS['phpgw_domain'][$key]['db_type']);
						$setup_tpl->set_var('config_pass',$GLOBALS['phpgw_domain'][$key]['config_passwd']);

						$selected = '';
						$dbtype_options = '';
						$found_dbtype = False;
						@reset($supported_db);
						while(list($k,$v) = @each($supported_db))
						{
							if($v == $GLOBALS['phpgw_domain'][$key]['db_type'])
							{
								$selected = ' selected="selected" ';
								$found_dbtype = true;
							}
							else
							{
								$selected = '';
							}
							$dbtype_options .= '<option ' . $selected . 'value="' . $v . '">' . $v . "\n";
						}
						$setup_tpl->set_var('dbtype_options',$dbtype_options);

						$setup_tpl->parse('domains','domain',True);
					}
					$setup_tpl->set_var('domain','');
				}
				if (defined('PHPGW_SERVER_ROOT'))
				{
					$GLOBALS['phpgw_info']['server']['server_root'] = PHPGW_SERVER_ROOT;
					$GLOBALS['phpgw_info']['server']['include_root'] = PHPGW_INCLUDE_ROOT; 
				}
				elseif(!@isset($GLOBALS['phpgw_info']['server']['include_root']) && @$GLOBALS['phpgw_info']['server']['header_version'] <= 1.6)
				{
					$GLOBALS['phpgw_info']['server']['include_root'] = @$GLOBALS['phpgw_info']['server']['server_root'];
				}
				elseif(!@isset($GLOBALS['phpgw_info']['server']['header_version']) && @$GLOBALS['phpgw_info']['server']['header_version'] <= 1.6)
				{
					$GLOBALS['phpgw_info']['server']['include_root'] = @$GLOBALS['phpgw_info']['server']['server_root'];
				}
			}
			else
			{
				$detected .= '<li class="warn">' . lang('Sample configuration not found. using built in defaults') . "</li>\n";
				$GLOBALS['phpgw_info']['server']['server_root'] = realpath('../'); //'/path/to/phpgroupware';
				$GLOBALS['phpgw_info']['server']['include_root'] = realpath('../');//'/path/to/phpgroupware';
				/* This is the basic include needed on each page for phpGroupWare application compliance */
				$GLOBALS['phpgw_info']['flags']['htmlcompliant'] = True;

				/* These are the settings for the database system */
				$setup_tpl->set_var('lang_domain',lang('Domain'));
				$setup_tpl->set_var('lang_delete',lang('Delete'));
				$setup_tpl->set_var('db_domain','default');
				$setup_tpl->set_var('db_host','localhost');
				$setup_tpl->set_var('db_name','phpgroupware');
				$setup_tpl->set_var('db_user','phpgroupware');
				$setup_tpl->set_var('db_pass','your_password');
				$setup_tpl->set_var('db_type','mysql');
				$setup_tpl->set_var('config_pass','changeme');

				$dbtype_options = '';
				foreach($supported_db as $k => $v)
				{
					$dbtype_options .= '<option value="' . $v . '">' . $v . "\n";
				}
				$setup_tpl->set_var('dbtype_options',$dbtype_options);

				$setup_tpl->parse('domains','domain',True);
				$setup_tpl->set_var('domain','');

				$setup_tpl->set_var('comment_l','<!-- ');
				$setup_tpl->set_var('comment_r',' -->');

				/* These are a few of the advanced settings */
				$GLOBALS['phpgw_info']['server']['db_persistent'] = True;
				$GLOBALS['phpgw_info']['server']['mcrypt_enabled'] = extension_loaded('mcrypt');
				$GLOBALS['phpgw_info']['server']['versions']['mcrypt'] = '';

				srand((double)microtime()*1000000);
				$random_char = array(
					'0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f',
					'g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v',
					'w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L',
					'M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
				);

				$GLOBALS['phpgw_info']['server']['mcrypt_iv'] = '';
				for($i=0; $i < 30; ++$i)
				{
					$GLOBALS['phpgw_info']['server']['mcrypt_iv'] .= $random_char[rand(0,count($random_char)-1)];
				}				
			}

			// now guessing better settings then the default ones 
			if(!$no_guess)
			{
				$detected .= '<li>' . lang('Now guessing better values for defaults...') . "</li>\n";
				$this_dir = dirname($_SERVER['SCRIPT_FILENAME']);
				$updir    = realpath('../'); //str_replace('/setup','',$this_dir);
				$GLOBALS['phpgw_info']['server']['server_root'] = $updir; 
				$GLOBALS['phpgw_info']['server']['include_root'] = $updir; 
			}

			$detected .= "</ul>\n";
			$setup_tpl->set_var('detected',$detected);
			/* End of detected settings, now display the form with the detected or prior values */

			$setup_tpl->set_var('server_root',@$GLOBALS['phpgw_info']['server']['server_root']);
			$setup_tpl->set_var('include_root',@$GLOBALS['phpgw_info']['server']['include_root']);
			$setup_tpl->set_var('header_admin_password',@$GLOBALS['phpgw_info']['server']['header_admin_password']);

			if(@$GLOBALS['phpgw_info']['server']['db_persistent'])
			{
				$setup_tpl->set_var('db_persistent_yes',' selected');
			}
			else
			{
				$setup_tpl->set_var('db_persistent_no',' selected');
			}

			$selected = '';
			$session_options = '';
			while(list($k,$v) = each($supported_sessions_type))
			{
				if($v == @$GLOBALS['phpgw_info']['server']['sessions_type'])
				{
					$selected = ' selected="selected" ';
				}
				else
				{
					$selected = '';
				}
				$session_options .= '<option ' . $selected . 'value="' . $v . '">' . $v . "\n";
			}
			$setup_tpl->set_var('session_options',$session_options);

			if(@$GLOBALS['phpgw_info']['server']['mcrypt_enabled'])
			{
				$setup_tpl->set_var('mcrypt_enabled_yes',' selected');
			}
			else
			{
				$setup_tpl->set_var('mcrypt_enabled_no',' selected');
			}

			$setup_tpl->set_var('mcrypt',$GLOBALS['phpgw_info']['server']['versions']['mcrypt']);
			$setup_tpl->set_var('mcrypt_iv',$GLOBALS['phpgw_info']['server']['mcrypt_iv']);

			if ( !isset($GLOBALS['phpgw_info']['server']['setup_acl']) || !$GLOBALS['phpgw_info']['server']['setup_acl'] )
			{
				$GLOBALS['phpgw_info']['server']['setup_acl'] = '127.0.0.1';
			}
			$setup_tpl->set_var('lang_setup_acl',lang('Limit access to setup to the following addresses or networks (e.g. 10.1.1,127.0.0.1)'));
			$setup_tpl->set_var('setup_acl', $GLOBALS['phpgw_info']['server']['setup_acl']);

			if(@$GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
			{
				$setup_tpl->set_var('domain_selectbox_yes',' selected');
			}
			else
			{
				$setup_tpl->set_var('domain_selectbox_no',' selected');
			}

			if(@$GLOBALS['phpgw_info']['server']['domain_from_host'])
			{
				$setup_tpl->set_var('domain_from_host_yes',' selected');
			}
			else
			{
				$setup_tpl->set_var('domain_from_host_no',' selected');
			}

			$errors = '';
			if( !isset($found_dbtype) || !$found_dbtype )
			{
				/*
				$errors .= '<br /><font color="red">' . lang('Warning!') . '<br />'
					. lang('The db_type in defaults (%1) is not supported on this server. using first supported type.',$GLOBALS['phpgw_info']['server']['db_type'])
					. '</font>';
				*/
			}

			if(is_writeable('../header.inc.php') ||
				(!file_exists('../header.inc.php') && is_writeable('../')))
			{
				$errors .= '<br /><input type="submit" name="action[write]" value="' . lang('Write config') . '" />&nbsp;'
					. lang('or') . '&nbsp;<input type="submit" name="action[download]" value="' . lang('Download') . '" />&nbsp;'
					. lang('or') . '&nbsp;<input type=submit name="action[view]" value="' . lang('View') . '" /> ' . lang('the file') . '.</form>';
			}
			else
			{
				$errors .= '<br />'
					. lang('Cannot create the header.inc.php due to file permission restrictions.<br /> Instead you can %1 the file.',
					'<input type="submit" name="action[download]" value="' . lang('Download') . '" />' . lang('or') . '&nbsp;<input type="submit" name="action[view]" value="' . lang('View') . '" />')
					. '</form>';
			}

			$setup_tpl->set_var('errors',$errors);

			$setup_tpl->set_var('lang_settings',lang('Settings'));
			$setup_tpl->set_var('lang_adddomain',lang('Add a domain'));
			$setup_tpl->set_var('lang_serverroot',lang('Server Root'));
			$setup_tpl->set_var('lang_includeroot',lang('Include Root (this should be the same as Server Root unless you know what you are doing)'));
			$setup_tpl->set_var('lang_adminpass',lang('Admin password to header manager'));
			$setup_tpl->set_var('lang_dbhost',lang('DB Host'));
			$setup_tpl->set_var('lang_dbhostdescr',lang('Hostname/IP of database server'));
			$setup_tpl->set_var('lang_dbname',lang('DB Name'));
			$setup_tpl->set_var('lang_dbnamedescr',lang('Name of database'));
			$setup_tpl->set_var('lang_dbuser',lang('DB User'));
			$setup_tpl->set_var('lang_dbuserdescr',lang('Name of db user phpGroupWare uses to connect'));
			$setup_tpl->set_var('lang_dbpass',lang('DB Password'));
			$setup_tpl->set_var('lang_dbpassdescr',lang('Password of db user'));
			$setup_tpl->set_var('lang_dbtype',lang('DB Type'));
			$setup_tpl->set_var('lang_whichdb',lang('Which database type do you want to use with phpGroupWare?'));
			$setup_tpl->set_var('lang_configpass',lang('Configuration Password'));
			$setup_tpl->set_var('lang_passforconfig',lang('Password needed for configuration'));
			$setup_tpl->set_var('lang_persist',lang('Persistent connections'));
			$setup_tpl->set_var('lang_persistdescr',lang('Do you want persistent connections (higher performance, but consumes more resources)'));
			$setup_tpl->set_var('lang_sesstype',lang('Sessions Type'));
			$setup_tpl->set_var('lang_sesstypedescr',lang('What type of sessions management do you want to use (PHP4 session management may perform better)?'));
			$setup_tpl->set_var('lang_enablemcrypt',lang('Enable MCrypt'));
			$setup_tpl->set_var('lang_mcryptversion',lang('MCrypt version'));
			$setup_tpl->set_var('lang_mcryptversiondescr',lang('Set this to "old" for versions &lt; 2.4, otherwise the exact mcrypt version you use.'));
			$setup_tpl->set_var('lang_mcryptiv',lang('MCrypt initialization vector'));
			$setup_tpl->set_var('lang_mcryptivdescr',lang('This should be around 30 bytes in length.<br />Note: The default has been randomly generated.'));
			$setup_tpl->set_var('lang_domselect',lang('Domain select box on login'));
			$setup_tpl->set_var('lang_domain_from_host', lang('Automatically detect domain from hostname'));
			$setup_tpl->set_var('lang_note_domain_from_host', lang('Note: This option will only work if show domain select box is off.'));
			$setup_tpl->set_var('lang_finaldescr',lang('After retrieving the file, put it into place as the header.inc.php.  Then, click "continue".'));
			$setup_tpl->set_var('lang_continue',lang('Continue'));

			$setup_tpl->pfp('out','manageheader');
			// ending the switch default
	}
?>
