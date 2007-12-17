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
	* @subpackage admin
 	* @version $Id: class.boXport.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boXport
	{
		var $public_functions = array(
			'import' => True,
			'export' => True
		);

		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;

		var $use_session = False;

		function property_boXport ($session=False)
		{

			$GLOBALS['phpgw_info']['flags']['currentapp']	=	'property';
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];

			$this->config		= CreateObject('phpgwapi.config','property');

			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');

			if($start || $start == 0)  { $this->start = $start; }
			if($query)  { $this->query  = $query;  }
			if($sort)   { $this->sort   = $sort;   }
			if($order)  { $this->order  = $order;  }
			if($filter) { $this->filter = $filter; }
			$this->cat_id = $cat_id;
		}

		function save_sessiondata()
		{

			if ($this->use_session)
			{
				$data = array(
					'start'  => $this->start,
					'query'  => $this->query,
					'sort'   => $this->sort,
					'order'  => $this->order,
					'filter' => $this->filter,
					'cat_id' => $this->cat_id
				);
				if($this->debug) { echo '<br>Save:'; _debug_array($data); }
				$GLOBALS['phpgw']->session->appsession('session_data','export',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','export');
			if($this->debug) { echo '<br>Read:'; _debug_array($data); }

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
			$this->filter = $data['filter'];
			$this->cat_id = $data['cat_id'];
		}

		function select_import_conv($selected='')
		{
			$dir_handle = @opendir(PHPGW_APP_INC . SEP . 'import');
			$i=0; $myfilearray = '';
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . SEP . 'import' . SEP . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('_',' ',$myfilearray[$i]);
				$sel_file = '';
				if ($myfilearray[$i]==$selected)
				{
					$sel_file = 'selected';
				}

				$conv_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $sel_file
				);
			}

			for ($i=0;$i<count($conv_list);$i++)
			{
				if ($conv_list[$i]['selected'] != 'selected')
				{
					unset($conv_list[$i]['selected']);
				}
			}

			return $conv_list;

		}

		function select_export_conv($selected='')
		{
			$dir_handle = @opendir(PHPGW_APP_INC . SEP . 'export');
			$i=0; $myfilearray = '';
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . SEP . 'export' . SEP . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('_',' ',$myfilearray[$i]);
				$sel_file = '';
				if ($myfilearray[$i]==$selected)
				{
					$sel_file = 'selected';
				}

				$conv_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $sel_file
				);
			}

			for ($i=0;$i<count($conv_list);$i++)
			{
				if ($conv_list[$i]['selected'] != 'selected')
				{
					unset($conv_list[$i]['selected']);
				}
			}

			return $conv_list;

		}

		function select_rollback_file($selected='')
		{
			$this->config->read_repository();
			$file_catalog = $this->config->config_data['export_path'];

			$dir_handle = @opendir($file_catalog);
			$i=0; $myfilearray = '';
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file($file_catalog . SEP . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			@sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('_',' ',$myfilearray[$i]);
				$sel_file = '';
				if ($myfilearray[$i]==$selected)
				{
					$sel_file = 'selected';
				}

				$rollback_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $sel_file
				);
			}

			for ($i=0;$i<count($rollback_list);$i++)
			{
				if ($rollback_list[$i]['selected'] != 'selected')
				{
					unset($rollback_list[$i]['selected']);
				}
			}

			return $rollback_list;

		}

		function import($invoice_common,$download)
		{
			include (PHPGW_APP_INC . '/import/' . $invoice_common['conv_type']);
			$invoice = new import_conv;

			$buffer = $invoice->import($invoice_common,$download);
			if ($download)
			{
				$header = $invoice->header;
				$import = $invoice->import;
				$buffer= array(
						'table'		=> $buffer,
						'header'	=> $header,
						'import'	=> $import
					);
			}
			return $buffer;
		}

		function export($data)
		{
			$conv_type = $data['conv_type'];
			$download = $data['download'];
			$pre_transfer = $data['pre_transfer'];
			$force_period_year = $data['force_period_year'];

		 	include(PHPGW_SERVER_ROOT.'/'.'property'.'/inc/export/' . $conv_type);
			$invoice = new export_conv;

			$buffer = $invoice->overfor($download,$pre_transfer,$force_period_year);

			return $buffer;
		}

		function rollback($conv_type,$role_back_date,$rollback_file)
		{
			include (PHPGW_APP_INC . '/export/' . $conv_type);
			$invoice = new export_conv;
			$buffer = $invoice->RullTilbake($role_back_date,$rollback_file);
			return $buffer;

		}

		function export_cron($data)
		{
			if($data['enabled']==1)
			{
				$this->export($data);
			}
		}

	}
?>
