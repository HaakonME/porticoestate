<?php
	/**
	* Filemanager
	*
	* @copyright Copyright (C) 2002-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id: index.php 17626 2006-11-29 15:32:21Z Caeies $
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> True,
		'nonavbar'		=> True,
		'currentapp'	=> 'filemanager'
	);

	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'filemanager.uifilemanager.index'));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
