<?php

	/**
	 * Types:
 	 * 'varchar','int','auto','blob','char','date','decimal','float','longtext','text','timestamp','bool'
 	 * 
 	 * Abbreviations:
 	 * 	fd = fields
 	 * 	pk = primary key
 	 * 	fk = foreign key
 	 * 	ix = index
 	 * 	uc = unique constraint
 	 * 
 	 */

	$phpgw_baseline = array(
		'rental_composite' => array(
				'fd' => array(
					'id' => 				array('type' => 'auto', 'nullable' => false),
					'name' => 				array('type' => 'varchar','precision' => '45','nullable' => false),
					'description' => 		array('type' => 'text'),
					'is_active' => 			array('type' => 'bool','nullable' => false,'default' => 'true'),
					'address_1' =>			array('type' => 'varchar','precision' => '255'),
					'address_2' =>			array('type' => 'varchar','precision' => '255'),
					'house_number' =>		array('type' => 'varchar','precision' => '255'),
					'postcode' =>			array('type' => 'varchar','precision' => '255'),
					'place' =>				array('type' => 'varchar','precision' => '255'),
					'has_custom_address' =>	array('type' => 'bool','nullable' => false,'default' => 'false'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'rental_unit' => array(
				'fd' => array(
					'composite_id' => 		array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'location_id' => 		array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'loc1' => 				array('type' => 'varchar', 'precision' => 50, 'nullable' => false, 'default' => '-1') // We need a default value as this table probably already contains data
				),
				'pk' => array('composite_id','location_id'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'id'),
					'fm_locations' => array( 'location_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'rental_permission' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(array('object_id', 'object_type'), array('object_type')),
			'uc' => array('subject_id', 'role', 'object_type', 'object_id'),
		),
		'rental_permission_root' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array('subject_id', 'role'),
		),
		'rental_document_composite' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"rental_composite" => array('owner_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		// Describes different contract types
		'rental_contract_type' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'title' => 			array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'description' => 	array('type' => 'text')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		// Describes different billing terms like '14 days', 'Monthly', etc.
		'rental_billing_term' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'title' => 			array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'runs_a_year' => 	array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		// A contract
		'rental_contract' => array(
			'fd' => array(
				'id' => 			array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'date_start' => 	array('type' => 'date'),
				'date_end' => 		array('type' => 'date'),
				'billing_start' => 	array('type' => 'date'),
				'type_id' =>	 	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'term_id' =>		array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'account' => 		array('type' => 'varchar', 'precision' => '255', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
					'rental_contract_type' => array('type_id' => 'id'),
					'rental_billing_term' => array('term_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// The connection between a contract and a composite. A composite can belong to several contracts (if they aren't active at the same time) and a contract can contain several composites.
		'rental_contract_composite' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'contract_id' =>	array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'composite_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
					'rental_contract' => array('contract_id' => 'id'),
					'rental_composite' => array('composite_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// A tenant
		'rental_tenant' => array(
				'fd' => array(
					'id' =>	array('type' => 'auto', 'nullable' => false),
					'agresso_id' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'personal_identification_number' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'first_name' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'last_name' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'type_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'is_active' =>	array('type' => 'bool','nullable' => false,'default' => 'false'),
					'title' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'company_name' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'department' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'address_1' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'address_2' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'postal_code' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'place' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'phone' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'fax' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'email' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'url' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'post_bank_account_number' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'account_number' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'reskontro' =>	array('type' => 'varchar','precision' => '45','nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		// Tenant to contract relationship
		'rental_contract_tenant' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'contract_id' =>	array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'tenant_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
					'rental_contract' => array('contract_id' => 'id'),
					'rental_tenant' => array('tenant_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		)
	);
