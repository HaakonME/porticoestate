<?php
	$setup_info['controller']['name'] = 'controller';
	$setup_info['controller']['version'] = '0.1';
	$setup_info['controller']['app_order'] = 100;
	$setup_info['controller']['enable'] = 1;
	$setup_info['controller']['app_group']	= 'office';

	$setup_info['controller']['description'] = 'Bergen kommune controller';

	$setup_info['controller']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['controller']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['controller']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);
	
	/* The hooks this app includes, needed for hooks registration */
	$setup_info['controller']['hooks'] = array
	(
		'menu'	=> 'controller.menu.get_menu',
		'config'
	);
	
	$setup_info['controller']['tables'] = array 
	(
		'controller_control',
		'controller_control_schedule',
		'controller_control_point_list',
		'controller_control_point',
		'controller_control_group',
		'controller_check_point',
		'controller_check_list'
	);
	
?>
