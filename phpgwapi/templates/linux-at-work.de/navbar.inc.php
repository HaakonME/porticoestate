<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php 17902 2007-01-24 16:04:52Z Caeies $
	*/


	/**
	* Parse navigation var
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = False)
	{
		$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(
			array(
				'navbar' => 'navbar.tpl'
			)
		);
		$tpl->set_block('navbar','preferences','preferences_icon');

		//$tpl->set_block('navbar','B_powered_top','V_powered_top');
		//$tpl->set_block('navbar','B_num_users','V_num_users');

		$var['img_root'] = PHPGW_IMAGES_DIR;
		$var['img_root_roll'] = PHPGW_IMAGES_DIR . '/rollover';
		$var['table_bg_color'] = $GLOBALS['phpgw_info']['theme']['navbar_bg'];

		#  echo '<pre>'; print_r($GLOBALS['phpgw_info']['navbar']); echo '</pre>';
		$applications = '';
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($app != 'home' && $app != 'preferences' && ! ereg('about',$app) && $app != 'logout')
			{
				$applications .= '<tr><td class="main_menu_apps"><a class="main_menu" href="' . $app_data['url'] . '"';
				if (isset($GLOBALS['phpgw_info']['flags']['navbar_target']))
				{
					$applications .= ' target="' . $GLOBALS['phpgw_info']['flags']['navbar_target'] . '"';
				}

				$applications .= '>'.$app_data['title'].'</a></td></tr>'."\r\n";
			}
			$img_src_over = $GLOBALS['phpgw']->common->image($app,'navbar-over.gif');
			if($img_src_over)
			{
				$pre_load[] = $img_src_over;
			}
		}

		$var['applications'] = $applications;
     
		$var['home_link'] 	= $GLOBALS['phpgw_info']['navbar']['home']['url'];
		$var['preferences_link'] = $GLOBALS['phpgw_info']['navbar']['preferences']['url'];
		$var['logout_link'] 	= $GLOBALS['phpgw_info']['navbar']['logout']['url'];
		$var['help_link'] 	= $GLOBALS['phpgw_info']['navbar']['about']['url'];
		$var['lang_welcome']	= lang('welcome');
		$var['lang_preferences']	= lang('preferences');
		$var['lang_logout']	= lang('logout');
		$var['lang_help']	= lang('help');

		// "powered_by_color" and "_size" are is also used by number of current users thing
		$var['powered_by_size'] = '2';
		$var['powered_by_color'] = '#ffffff';
		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'top')
		{
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
			$tpl->set_var($var);
		}
		else
		{
			$var['powered_by'] = '';
			$tpl->set_var($var);
		}
		$var['phpgw_version'] = lang("version").": ".$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];
		
		$tpl->set_var($var);

		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a class="main_menu_bottom" href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
			 	. '">' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
			$tpl->set_var($var);
		}
		else
		{
			$var['current_users'] = '';
			$tpl->set_var($var);
		}

		$var['user_info_name'] = $GLOBALS['phpgw']->common->display_fullname();
		$var['user_info_date'] =
				  lang($GLOBALS['phpgw']->common->show_date(time(),'l')) . ' '
				. lang($GLOBALS['phpgw']->common->show_date(time(),'F')) . ' '
				. $GLOBALS['phpgw']->common->show_date(time(),'d, Y');
		$var['user_info'] = $var['user_info_name'] .' - ' .$var['user_info_date'];
		$var['user_info_size'] = '2';
		$var['user_info_color'] = '#000000';

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
				. '<br> Click this image on the navbar: <img src="'
				. $GLOBALS['phpgw']->common->image('preferences','navbar.gif').'">';
		}
		elseif ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}
 
		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . "<br>" . checkcode($cd);
		}

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
			$var['th_bg'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$var['message_top'] = '30px';
			$var['app_top'] = '40px';
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '';
			$var['message_top'] = '0px';
			$var['app_top'] = '15px';
		}
		$tpl->set_var($var);
		// check if user is allowed to change his prefs
		if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
		{
			$tpl->parse('preferences_icon','preferences');
		}
		else
		{
			$tpl->set_var('preferences_icon','');
		}
		$tpl->pfp('out','navbar');
		// If the application has a header include, we now include it
		if (!@$GLOBALS['phpgw_info']['flags']['noappheader'] && @isset($GLOBALS['HTTP_GET_VARS']['menuaction']))
		{
			list($app,$class,$method) = explode('.',$GLOBALS['HTTP_GET_VARS']['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && $GLOBALS[$class]->public_functions['header'])
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->hooks->process('after_navbar');
		return;
	}

	/**
	* Parse navigation bar end
	* @ignore
	*/
	function parse_navbar_end()
	{
/*
		$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(array('footer' => 'footer.tpl'));
		$tpl->set_block('footer','B_powered_bottom','V_powered_bottom');

		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'bottom')
		{
			$var = Array(
				'powered'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
				'img_root'	=> PHPGW_IMAGES_DIR,
				'power_backcolor'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'power_textcolor'	=> $GLOBALS['phpgw_info']['theme']['navbar_text']
				'version'	=> $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
			);
			$tpl->set_var($var);
 			$tpl->parse('V_powered_bottom','B_powered_bottom');
		}
		else
		{
			$tpl->set_var('V_powered_bottom','');
		}

		$GLOBALS['phpgw']->hooks->process('navbar_end');
		$tpl->pfp('out','footer');
*/
	}
