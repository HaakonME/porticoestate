<?php
	$javascripts = array();
	$stylesheets = array();

	$javascripts[] = "/phpgwapi/js/json/json.js";

	phpgw::import_class('phpgwapi.jquery');
	phpgwapi_jquery::load_widget('core');

	phpgw::import_class('phpgwapi.yui');
	phpgwapi_yui::load_widget('button');
	phpgwapi_yui::load_widget('container');
	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;
	$javascripts[] = "/phpgwapi/js/yui3/yui/yui-min.js";
	$javascripts[] = "/phpgwapi/js/jquery/mmenu/src/js/jquery.mmenu.min.all.js";
	$javascripts[] = "/phpgwapi/templates/pure/js/mmenu.js";

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

	if( !$GLOBALS['phpgw_info']['flags']['noframework'] && !$GLOBALS['phpgw_info']['flags']['nonavbar'] )
	{
	}
	$stylesheets[] = "/phpgwapi/templates/pure/css/demo_mmenu.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/jquery/mmenu/src/css/jquery.mmenu.all.css";
//	$stylesheets[] = "/phpgwapi/js/jquery/mmenu/src/css/extensions/jquery.mmenu.widescreen.css"  media="all and (min-width: 900px)" />

//	$stylesheets[] = "/phpgwapi/templates/pure/css/side-menu.css";
//	$stylesheets[] = "/phpgwapi/templates/pure/css/baby-blue.css";


	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/phpgwapi/templates/pure/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
	//$stylesheets[] = "/{$app}/templates/base/css/base.css";
	//$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/{$app}/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
/*
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap'])
	{
		$stylesheets[] = "/phpgwapi/templates/base/css/yui_table_nowrap.css";
	}
*/
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

	$tpl_vars = array
	(
		'noheader'				=> isset($GLOBALS['phpgw_info']['flags']['noheader_xsl']) && $GLOBALS['phpgw_info']['flags']['noheader_xsl'] ? 'true' : 'false',
		'nofooter'				=> isset($GLOBALS['phpgw_info']['flags']['nofooter']) && $GLOBALS['phpgw_info']['flags']['nofooter'] ? 'true' : 'false',
		'css'					=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'			=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'				=> $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'			=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'browser_not_supported'	=> lang('browser not supported'),
		'str_base_url'			=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'			=> $GLOBALS['phpgw_info']['server']['webserver_url'],
		'win_on_events'			=> $GLOBALS['phpgw']->common->get_on_events(),
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);

	flush();

	echo "\t<body>";

	if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
//		echo '<div align = "left">';
		register_shutdown_function('parse_footer_end_noframe');
	}
	
	function parse_footer_end_noframe()
	{
		if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
		{
//			echo '</div>';
		}

		$footer = <<<HTML
		</body>
	</html>
HTML;
		echo $footer;
	}
