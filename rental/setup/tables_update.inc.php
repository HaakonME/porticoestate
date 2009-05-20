<?php
	$test[] = '0.0.1';
	function rental_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_composite', array(
				'fd' => array(
					'composite_id' => 		array('type' => 'auto', 'nullable' => false),
					'name' => 				array('type' => 'varchar','precision' => '45','nullable' => false),
					'description' => 		array('type' => 'text'),
					'is_active' => 			array('type' => 'bool','nullable' => false,'default' => 'true'),
					'address_1' =>			array('type' => 'varchar','precision' => '255'),
					'address_2' =>			array('type' => 'varchar','precision' => '255'),
					'house_number' =>		array('type' => 'varchar','precision' => '255'),
					'postcode' =>			array('type' => 'varchar','precision' => '255'),
					'place' =>				array('type' => 'varchar','precision' => '255'),
					'has_custom_address' =>	array('type' => 'bool','nullable' => false,'default' => 'false')
				),
				'pk' => array('composite_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_unit', array(
				'fd' => array(
					'composite_id' => 		array('type' => 'int', 'precision' => '50', 'nullable' => false),
					'location_id' => 		array('type' => 'int', 'precision' => '50', 'nullable' => false)
				),
				'pk' => array('composite_id','location_id'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'composite_id'),
					'fm_locations' => array( 'location_id' => 'id')
				
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.0.2';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}
?>