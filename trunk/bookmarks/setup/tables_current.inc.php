<?php
	/**
	* Bookmarks setup
	* @author totschnig
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @subpackage setup
	* @version $Id: tables_current.inc.php,v 1.8 2005/04/28 18:59:46 powerstat Exp $
	*/

	// This file should be generated for you. It should never be edited by hand

	$phpgw_baseline = array(
		'phpgw_bookmarks' => array(
			'fd' => array(
				'bm_id' => array('type' => 'auto','nullable' => False),
				'bm_owner' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'bm_access' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_url' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_desc' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_keywords' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_category' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'bm_rating' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'bm_info' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bm_visits' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('bm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
