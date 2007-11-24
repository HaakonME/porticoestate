<?php
	/**
	* Addressbook - Setup
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @subpackage setup
	* @version $Id: tables_current.inc.php,v 1.10 2005/05/10 13:02:43 powerstat Exp $
	*/

	$phpgw_baseline = array(
		'phpgw_addressbook_servers' => array(
			'fd' => array(
				'name'    => array('type' => 'varchar', 'precision' => 64,  'nullable' => False),
				'basedn'  => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'search'  => array('type' => 'varchar', 'precision' => 32,  'nullable' => True),
				'attrs'   => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'enabled' => array('type' => 'int', 'precision' => 4)
			),
			'pk' => array('name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
