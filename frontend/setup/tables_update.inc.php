<?php
	/**
	* phpGroupWare - frontend: a simplified tool for end users.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package frontend
	* @subpackage setup
 	* @version $Id$
	*/

	/**
	* Update frontend version from 0.1 to 0.9.17.500
	* Add locations as placeholders for functions and menues
	* 
	*/

	$test[] = '0.1';
	function frontend_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.', 'top', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.ticket', 'helpdesk', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.rental.contract', 'contract_internal', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.document.drawings', 'drawings', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.document.pictures', 'pictures', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.maintenance', 'maintenance', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.refurbishment', 'refurbishment', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.services', 'services', 'frontend', false);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['frontend']['currentver'] = '0.9.17.500';
			return $GLOBALS['setup_info']['frontend']['currentver'];
		}
	}
	
	$test[] = '0.2';
	function frontend_upgrade0_2()
	{
		$GLOBALS['phpgw']->locations->add('.rental.contract_in','contract_in','frontend', false);
	}
