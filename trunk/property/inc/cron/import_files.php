<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id: import_files.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class import_files
	{
		var	$function_name = 'import_files';

		function import_files()
		{
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db     			= & $GLOBALS['phpgw']->db;
			$this->db2				= clone($this->db);
			$this->soadmin_location	= CreateObject('property.soadmin_location');

			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->left_join 		= " LEFT JOIN ";
			$this->saveto			= '/mnt/filer2/VaktPC_filer';
		//	$this->saveto			= '/tmp';
			$this->export_method = 'csv';
		//	$this->export_method = 'excel';
 		//	$this->export_method = 'xml';
 			$this->dateformat = 'd/m/Y';

		}

		function pre_run($data='')
		{
			if($data['enabled']==1)
			{
				$confirm	= True;
				$cron		= True;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}

			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=False);
			}
		}


		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=>$this->function_name,
				'execute'	=> $execute,
			);


			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));


			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Export info as files'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= lang('Export info as files');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$this->import_vaktprotokoll();

			if(!$cron)
			{
				$this->confirm($execute=False);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				implode(',',(array_keys($msgbox_data)))
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function import_vaktprotokoll()
		{
			$filename = 'Vakthendelser.xls';
			//require_once  PHPGW_APP_INC . SEP . 'excelreader' . SEP . 'reader.php';
			$data = CreateObject('phpgwapi.excelreader');
			
			$data->setOutputEncoding('CP1251');
			$data->read(PHPGW_API_INC . SEP . 'excelreader' . SEP . 'test' . SEP . $filename);

			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
				{
					echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
				}
				echo "\n";
			}
		}
	}
	
?>
