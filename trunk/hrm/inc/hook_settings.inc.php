<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage core
 	* @version $Id: hook_settings.inc.php,v 1.3 2006/02/06 13:29:16 sigurdne Exp $
	*/

	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];

	$yes_and_no = array(
		'True' => 'Yes',
		''     => 'No'
	);

