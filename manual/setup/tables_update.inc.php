<?php
	/**
	* phpGroupWare - Manual
	*
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package manual
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 18030 2007-03-09 12:17:39Z sigurdne $
	*/

	/**
	* Update manual version from 0.9.13.002 to 0.9.17.500
	*/

	$test[] = '0.9.13.002';
	function manual_upgrade0_9_13_002()
	{
		return $GLOBALS['setup_info']['manual']['currentver'] = '0.9.17.500';
	}

