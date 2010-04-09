<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
 	* @version $Id$
	*/

	/*
	 * Import the datetime class for date processing
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_bogallery
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();
	
		function __construct($session=false)
		{
			$this->so 			= CreateObject('property.sogallery');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$location_id		= phpgw::get_var('location_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int');
			$user_id			= phpgw::get_var('user_id', 'int');

			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->location_id	= isset($_REQUEST['location_id'])  ? $location_id :  $this->location_id;
			$this->user_id		= isset($_REQUEST['user_id'])  ? $user_id :  $this->user_id;
			$this->allrows		= isset($allrows) ? $allrows : false;
		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','gallery',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','gallery');

	//		_debug_array($data);

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->allrows		= $data['allrows'];
			$this->location_id	= $data['location_id'];
			$this->user_id		= $data['user_id'];
		}

		public function read($dry_run='')
		{
			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'location_id' => $this->location_id, 'user_id' => $this->user_id, 'dry_run'=>$dry_run));

			static $locations = array();
			static $urls = array();
			$interlink	= CreateObject('property.interlink');
			$dateformat	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach($values as &$entry)
			{
				$entry['date']	= $GLOBALS['phpgw']->common->show_date($entry['schedule_time'],$dateformat);
				$entry['receipt_date']	= $GLOBALS['phpgw']->common->show_date($entry['receipt_date'],$dateformat);

				if($locations[$entry['location_id']])
				{
					 $location = $locations[$entry['location_id']];
				}
				else
				{
					$location = $GLOBALS['phpgw']->locations->get_name($entry['location_id']);
					$locations[$entry['location_id']] = $location;
				}

				if($urls[$entry['location_id']][$entry['location_item_id']])
				{
					$entry['url'] = $urls[$entry['location_id']][$entry['location_item_id']];
				}
				else
				{
					$entry['url'] = $interlink->get_relation_link($location['location'], $entry['location_item_id']);
					$urls[$entry['location_id']][$entry['location_item_id']] = $entry['url'];
				}
				$entry['location_name'] = $interlink->get_location_name($location['location']);
				$entry['location'] = $location['location'];

			}

			$this->total_records = $this->so->total_records;

			return $values;
		}
		
		public function get_gallery_location()
		{
			return $this->so->get_gallery_location();
		}
	}
