<?php
/**
* eTemplate - basic application development environment
* @copyright Copyright (C) 2002-2006 Free Software Foundation, Inc. http://www.fsf.org/
* @author Ralf Becker <ralf.becker@outdoortraining.de>
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package etemplate
* @version $Id: class.etemplate.inc.php,v 1.10 2007/02/13 12:59:32 sigurdne Exp $
*/

	if (!function_exists('get_var'))
	{
		include_once('get_var.php');
	}
	$ui = ''; // html UI, which UI to use, should come from api and be in $GLOBALS['phpgw']???
	if (isset($_ENV['DISPLAY']) && $_ENV['DISPLAY'] && isset($_SERVER['_']))
	{
		$ui = '_gtk';
	}
	include_once(PHPGW_INCLUDE_ROOT . "/etemplate/inc/class.uietemplate$ui.inc.php");
