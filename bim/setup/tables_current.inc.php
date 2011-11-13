<?php
	/**
	* phpGroupWare - bim
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package bim
	* @subpackage setup
 	* @version $Id: tables_current.inc.php 6685 2010-12-20 14:44:13Z peturbjorn $
	*/

	$phpgw_baseline = array(
		'fm_bim_type' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'is_ifc' => array('type' => 'int','precision' => 2,'default' => 1,'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 64,'nullable' => False),
				'description' => array('type' => 'varchar', 'precision' => 512,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('name')
		),
		'fm_bim_model' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4,'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 128,'nullable' => False),
				'vfs_file_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'authorization_value' => array('type' => 'varchar', 'precision' => 200,'nullable' => true),
				'author' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
				'changedate' => array('type' => 'timestamp','nullable' => True),
				'description' => array('type' => 'varchar', 'precision' => 512,'nullable' => True),
				'organization' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
				'originatingsystem' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
				'preprocessor' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
				'valdate' => array('type' => 'timestamp','nullable' => True),
				'nativeschema' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('phpgw_vfs' => array('vfs_file_id' => 'file_id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_bim_item' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'type' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'guid' => array('type' => 'varchar', 'precision' => 24,'nullable' => False),
				'xml_representation' => array('type' => 'xml','nullable' => False),
				'model' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'p_location_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('fm_bim_type' => array('type' => 'id')),
//			'fk' => array('fm_bim_model' => array('model' => 'id'),
//							'fm_bim_type' => array('type' => 'id')),
			'ix' => array(),
			'uc' => array('guid')
		)
	);
