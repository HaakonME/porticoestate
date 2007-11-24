<?php
	/**
	* phpGroupWare EMail - http://phpGroupWare.org
	*
	* @author Angles <angles@phpgroupware.org>
	* @copyright Copyright (C) 2001-2004 Angelo Tony Puglisi
	* @copyright Portions Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage setup
	* @version $Id: tables_baseline.inc.php,v 1.1 2005/05/15 14:28:40 skwashd Exp $
	* @internal Based on AeroMail by Mark Cushman <mark@cushman.net>
	*/
	
	$phpgw_baseline = array(
			'phpgw_anglemail' => array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'text', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		)
	);
	
?>
