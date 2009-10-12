<?php
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');

	$javascripts = array();

	phpgwapi_yui::load_widget('dragdrop');
	phpgwapi_yui::load_widget('element');
	phpgwapi_yui::load_widget('container');
	phpgwapi_yui::load_widget('connection');
	phpgwapi_yui::load_widget('resize');
	phpgwapi_yui::load_widget('layout');

	phpgwapi_yui::load_widget('button');
	$stylesheets = array
		(
			"/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
			"/phpgwapi/js/yahoo/tabview/assets/skins/sam/tabview.css",
			"/phpgwapi/js/yahoo/resize/assets/skins/sam/resize.css",
			"/phpgwapi/js/yahoo/layout/assets/skins/sam/layout.css",
		);
	$stylesheets[] = "/phpgwapi/js/yahoo/menu/assets/skins/sam/menu.css";
	$stylesheets[] = "/phpgwapi/js/yahoo/button/assets/skins/sam/button.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/base.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/phpgwapi/templates/bkbooking/css/frontend.css";

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $javascript );
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}

	// Construct navbar_config by taking into account the current selected menu
	// The only problem with this loop is that leafnodes will be included
	$navbar_config = execMethod('phpgwapi.template_portico.retrieve_local', 'navbar_config');

	if( isset($GLOBALS['phpgw_info']['flags']['menu_selection']) )
	{
		if(!isset($navbar_config))
		{
			$navbar_config = array();
		}

		$current_selection = $GLOBALS['phpgw_info']['flags']['menu_selection'];

		while($current_selection)
		{
			$navbar_config["navbar::$current_selection"] = true;
			$current_selection = implode("::", explode("::", $current_selection, -1));
		}

		phpgwapi_template_portico::store_local('navbar_config', $navbar_config);
	}

	$_navbar_config			= json_encode($navbar_config);


	$app = lang($app);
	$tpl_vars = array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url'],
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'navbar_config' => $_navbar_config,
		'lbl_search'   	=> lang('Search'),
		'header_search_class'	=> ($_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
	);
	$bouser = CreateObject('bookingfrontend.bouser');
	if($bouser->is_logged_in())
	{
		$tpl_vars['login_text'] = lang('Logout');
		$tpl_vars['login_url'] = 'logout.php';
	}
	else
	{
		$tpl_vars['login_text'] = lang('Login');
		$tpl_vars['login_url'] = 'login.php?after='.urlencode($_SERVER['QUERY_STRING']);
	}

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
