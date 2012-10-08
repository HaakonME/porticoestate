<?php

$phpgw_baseline = array(
		'lg_project_type' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'lg_project' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'project_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'description' => array('type' => 'text', 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array('lg_project_type' => array('project_type_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		),
		'lg_activity' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'parent_activity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'description' => array('type' => 'text', 'nullable' => false),
						'project_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'start_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'end_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'responsible_user_id' => array('type' => 'int', 'precision' => 4, 'nullable'=> false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'update_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'update_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_project' => array('project_id' => 'id'),
						'lg_activity' => array('parent_activity_id' => 'id')
				),
				'ix' => array('name'),
				'uc' => array()
		),
		'lg_requirement' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'activity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'date_from' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'date_to' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array('lg_activity' => array('activity_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		),
		'lg_requirement_resource_type' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'resource_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'no_of_elements' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_requirement' => array('requirement_id' => 'id'),
						'fm_bim_type' => array('resource_type_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_requirement_resource_allocation' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'article_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'type' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_requirement' => array('requirement_id' => 'id'),
						'fm_bim_item' => array('article_id' => 'id', 'type' => 'type')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_bim_item_type_requirement' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'cust_attribute_id' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
						'project_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_project_type' => array('project_type_id' => 'id'),
						'phpgw_locations' => array('location_id' => 'location_id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_requirement_value' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'type_requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'cust_attribute_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'value' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array('lg_bim_item_type_requirement' => array('type_requirement_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		),
		'lg_calendar' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'type' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'tracking' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array('fm_bim_item' => array('item_id' => 'id', 'type' => 'type')),
				'ix' => array(),
				'uc' => array()
		)
);
