<?php
	/**
	* Addressbook Chooser
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: 
	*/
	include_once('projects/inc/class.pbaddbookaccount.inc.php');

	class pbaddbookaccount_projects extends pbaddbookaccount
	{

		//@function uijsaddressbook constructor
		function pbaddbookaccount_projects()
		{
			parent::pbaddbookaccount();
		}

		function parse_result($list, $saveEntries)
		{
			for($i = 0; $i < count($list); $i++)
			{
				if($saveEntries[$list[$i]][0]['id'])
				{
					switch($list[$i])
					{
						case "to":
						$javascript  = "opener.document.getElementById('".$_REQUEST['targettagto']."').value = '".$saveEntries[$list[$i]][0]['name']."';";
						$javascript .= "opener.document.getElementById('".$_REQUEST['targettagto']."id').value = '".$saveEntries[$list[$i]][0]['id']."';";
						$this->template->set_var('to', $javascript);
						break;
					}
				}
			}

			$this->template->set_file(array('result' => 'pbaddressbook_result.tpl'));
			$this->template->parse('out','result',true);
			$this->template->p('out');
			return true;
		}

	}
