<?php
	$setup_info['activitycalendar']['name'] = 'activitycalendar';
	$setup_info['activitycalendar']['version'] = '0.1';
	$setup_info['activitycalendar']['app_order'] = 60;
	$setup_info['activitycalendar']['enable'] = 1;
	$setup_info['activitycalendar']['app_group']	= 'office';
	
	$setup_info['activitycalendar']['tables'] = array 
	(
		'activity_activity',
		'activity_arena'
	);

	$setup_info['activitycalendar']['description'] = 'Bergen kommune activitycalendar';

	$setup_info['activitycalendar']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'booking',
		'versions' => Array('0.2.00', '0.2.01','0.2.02','0.2.03','0.2.04','0.2.05')
	);

	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
/*	$setup_info['activitycalendar']['hooks'] = array
	(
		'menu'	=> 'activitycalendar.menu.get_menu',
		'config'
	);*/
?>
