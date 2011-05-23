<?php

	/**
	 * Update Activitycalendar from v 0.1 to 0.1.1
	 */

	$test[] = '0.1';
	function activitycalendar_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('activity_activity','target',array(
			'type' => 'varchar', 
			'precision' => '255'
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.1';
	function activitycalendar_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_arena','active',array(
			'type' => 'bool',
			'default' => 'true'
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.2';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
?>