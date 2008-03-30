<?php

	function parse_navbar($force = False)
	{
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');

		$var = array
		(
			'about_url'		=> $GLOBALS['phpgw']->link('/about.php', array('appname' => $GLOBALS['phpgw_info']['flags']['currentapp']) ),
			'about_text'	=> lang('about'),
			'logout_url'	=> $GLOBALS['phpgw']->link('/logout.php'),
			'logout_text'	=> lang('logout'),
			'user_fullname' => $GLOBALS['phpgw']->common->display_fullname()
		);

		if ( isset($navbar['preferences']) )
		{
			$var['preferences_url'] = $navbar['preferences']['url'];
			$var['preferences_text'] = $navbar['preferences']['text'];
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';

		prepare_navbar($navbar);
		$navigation = execMethod('phpgwapi.menu.get', 'navigation');

		$treemenu = '';
		foreach($navbar as $app => $app_data)
		{
			if(!in_array($app, array('logout', 'about', 'preferences')))
			{
				$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app]) : '';
				$treemenu .= render_item($app_data, "navbar::{$app}", $submenu);
			}
		}
		$var['treemenu'] = <<<HTML
			<ul id="navbar">
{$treemenu}
			</ul>

HTML;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		register_shutdown_function('parse_footer_end');
	}

	function item_expanded($id)
	{
		static $navbar_state;
		if( !isset( $navbar_state ) )
		{
			$navbar_state = execMethod('phpgwapi.template_newdesign.retrieve_local', 'navbar_config');
		}
		return isset( $navbar_state[ $id ]);
	}

	function render_item($item, $id='', $children='')
	{
		$icon_style = $expand_class = $current_class = $link_class = $parent_class = '';
		static $blank_image;
		if ( !isset($blank_image) )
		{
			$blank_image = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'blank.png');
		}
		if ( isset($item['image']) )
		{
			$icon_style = ' style="background-image: url(' . $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]) . ')"';
		}
		if ( $children )
		{
			$expand_class = item_expanded($id) ? ' class="expanded"' : ' class="collapsed"';
			$parent_class = ' parent';
		}
		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
		{
			$current_class = 'current';
		}

		$link_class =" class=\"{$current_class}{$parent_class}\"";

		$out = <<<HTML
				<li{$expand_class}>

HTML;
		if( $expand_class )
		{
		$out .= <<<HTML
							<img src="{$blank_image}"{$expand_class}width="16" height="16" alt="+/-" />

HTML;
		}
		return <<<HTML
$out
					<a href="{$item['url']}"{$link_class}{$icon_style} id="{$id}">
						<span>{$item['text']}</span>
					</a>
{$children}
				</li>

HTML;
	}

	function render_submenu($parent, $menu)
	{
		$out = '';
		foreach ( $menu as $key => $item )
		{
			$children = isset($item['children']) ? render_submenu(	"{$parent}::{$key}", $item['children']) : '';
			$out .= render_item($item, "navbar::{$parent}::{$key}", $children);
			//$debug .= "{$parent}::{$key}<br>";
		}

		$out = <<<HTML
			<ul>
{$out}
			</ul>

HTML;
		return $out;
	}

	function parse_footer_end()
	{
		// Stop the register_shutdown_function causing the footer to be included twice - skwashd dec07
		static $footer_included = false;
		if ( $footer_included )
		{
			return true;
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');

		$var = array
		(
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
		);

		$GLOBALS['phpgw']->template->set_var($var);

		$GLOBALS['phpgw']->template->pfp('out', 'footer');

		$footer_included = true;
	}

	/**
	* Callback for usort($navbar)
	*
	* @param array $item1 the first item to compare
	* @param array $item2 the second item to compare
	* @return int result of comparision
	*/
	function sort_navbar($item1, $item2)
	{
		$a =& $item1['order'];
		$b =& $item2['order'];

		if ($a == $b)
		{
			return strcmp($item1['text'], $item2['text']);
		}
		return ($a < $b) ? -1 : 1;
	}

	/**
	* Organise the navbar properly
	*
	* @param array $navbar the navbar items
	* @return array the organised navbar
	*/
	function prepare_navbar(&$navbar)
	{
		if ( isset($navbar['admin']) )
		{
			$navbar['admin']['children'] = execMethod('phpgwapi.menu.get', 'admin');
		}
		uasort($navbar, 'sort_navbar');
	}
