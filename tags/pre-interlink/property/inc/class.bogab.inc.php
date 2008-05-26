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
	* @subpackage location
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bogab
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
		);

		function property_bogab($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->solocation 	= CreateObject('property.solocation');
			$this->config		= CreateObject('phpgwapi.config');
			$this->config->read_repository();
			$this->gab_insert_level = (isset($this->config->config_data['gab_insert_level'])?$this->config->config_data['gab_insert_level']:3);

			$this->so 		= CreateObject('property.sogab',$this->gab_insert_level);

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(!empty($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','gab',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','gab');

			//_debug_array($data);

			$this->start	= isset($data['start']) ? $data['start'] : '';
			$this->query	= isset($data['query']) ? $data['query'] : '';
			$this->filter	= isset($data['filter']) ? $data['filter'] : '';
			$this->sort		= isset($data['sort']) ? $data['sort'] : '';
			$this->order	= isset($data['order']) ? $data['order'] : '';
			$this->cat_id	= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->allrows	= isset($data['allrows']) ? $data['allrows'] : '';
		}


		function read($loc1='',$gaards_nr='',$bruksnr='',$feste_nr='',$seksjons_nr='',$address='',$check_payments = '',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = true;
			}
			
			$gab = $this->so->read(array('start' => $this->start,'sort' => $this->sort,'order' => $this->order,'allrows'=>$this->allrows,
											'cat_id' => $this->cat_id,'loc1' => $loc1,
											'gaards_nr' => $gaards_nr,'bruksnr' => $bruksnr,'feste_nr' => $feste_nr,
											'seksjons_nr' => $seksjons_nr,'address' => $address,'check_payments' => $check_payments));
			$this->total_records = $this->so->total_records;
			$this->payment_date = $this->so->payment_date;
			return $gab;
		}

		function read_detail($gab_id='')
		{
			$gab = $this->so->read_detail(array('start' => $this->start,'sort' => $this->sort,'order' => $this->order,
											'cat_id' => $this->cat_id,'gab_id' => $gab_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;


			for ($i=0; $i<count($gab); $i++)
			{
				$location_data=$this->solocation->read_single($gab[$i]['location_code']);

				for ($j=0;$j<count($cols_extra);$j++)
				{
					$gab[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
				}
			}

			return $gab;
		}

		function read_single($gab_id='',$location_code='')
		{
			$gab = $this->so->read_single($gab_id,$location_code);

			if($gab['location_code'])
			{
				$gab['location_data'] =$this->solocation->read_single($gab['location_code']);
			}

			return $gab;
		}


		function save($values)
		{
			if(!$values['location_code'])
			{
				while (is_array($values['location']) && list(,$value) = each($values['location']))
				{
					if($value)
					{
						$location[] = $value;
					}
				}

				$values['location_code']=implode("-", $location);
			}

			if ($values['action']=='edit')
			{
				$receipt = $this->so->edit($values);
			}
			else
			{
				$receipt = $this->so->add($values);
			}

			$receipt['location_code']=$values['location_code'];
			return $receipt;
		}

		function delete($gab_id='',$location_code='')
		{
			$this->so->delete($gab_id,$location_code);
		}
	}

