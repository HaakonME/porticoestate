<?php
	/**
	* phpGroupWare - bim: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package bim
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 6982 2011-02-14 20:01:17Z sigurdne $
	*/

	/**
	* Update bim version from 0.9.17.500 to 0.9.17.501
	*/
	$test[] = '0.9.17.500';
	function bim_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type','location_id',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type','is_ifc',array('type' => 'int','precision' => 2,'default' => 1,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','p_location_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','p_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','location_code', array('type' => 'varchar','precision' => '20','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','address', array('type' => 'varchar','precision' => '150','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','entry_date', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','user_id', array('type' => 'int','precision' => '4','nullable' => True));
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}

	/**
	* Update bim version from 0.9.17.501 to 0.9.17.502
	*/
	$test[] = '0.9.17.501';
	function bim_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_item','guid',array('type' => 'varchar','precision' => '50','nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}

	/**
	* Update bim version from 0.9.17.502 to 0.9.17.503
	*/
	$test[] = '0.9.17.502';
	function bim_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','loc1', array('type' => 'varchar','precision' => '6','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_bim_item DROP CONSTRAINT fm_bim_item_pkey',__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_bim_item ADD CONSTRAINT fm_bim_item_pkey PRIMARY KEY(type,id)',__LINE__,__FILE__);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	
	/**
	* Update bim version from 0.9.17.503 to 0.9.17.504
	*/
	$test[] = '0.9.17.503';
	function bim_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_type','name',array('type' => 'varchar','precision' => '150','nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.504 to 0.9.17.505
	*/
	$test[] = '0.9.17.504';
	function bim_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','location_id', array('type' => 'int', 'precision' => 4,'nullable' => true));


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_bim_type",__LINE__,__FILE__);

		$values = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$types[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('id'),
				'location_id'	=> (int)$GLOBALS['phpgw_setup']->oProc->f('location_id'),
				'name' 			=> $GLOBALS['phpgw_setup']->oProc->f('name',true),
				'description'	=> $GLOBALS['phpgw_setup']->oProc->f('description',true)
			);
		}

		foreach ($types as $entry)
		{
			if(!$location_id = $entry['location_id'])
			{
				$location_id = $GLOBALS['phpgw']->locations->add($entry['name'], $entry['description'], 'bim');
			}

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_bim_item SET location_id = {$location_id} WHERE type = {$entry['id']}",__LINE__,__FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_item','location_id', array('type' => 'int', 'precision' => 4,'nullable' => false));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
