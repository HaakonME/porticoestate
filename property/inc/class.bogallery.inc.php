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
			$this->mime_magic 	= createObject('phpgwapi.mime_magic');
			$this->interlink	= CreateObject('property.interlink');

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
			$cat_id				= urldecode(phpgw::get_var('cat_id', 'string'));
			$location_id		= phpgw::get_var('location_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int');
			$user_id			= phpgw::get_var('user_id', 'int');
			$mime_type			= urldecode(phpgw::get_var('mime_type'));
			$start_date			= urldecode(phpgw::get_var('start_date', 'string'));
			$end_date			= urldecode(phpgw::get_var('end_date', 'string'));


			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->location_id	= isset($_REQUEST['location_id'])  ? $location_id :  $this->location_id;
			$this->user_id		= isset($_REQUEST['user_id'])  ? $user_id :  $this->user_id;
			$this->allrows		= isset($allrows) ? $allrows : false;
			$this->mime_type	= $mime_type ? $mime_type : '';

			$this->start_date	= isset($_REQUEST['start_date']) 	? $start_date		: $this->start_date;
			$this->end_date		= isset($_REQUEST['end_date'])		? $end_date			: $this->end_date;

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
			$start_date	= phpgwapi_datetime::date_to_timestamp($this->start_date);
			$end_date	= phpgwapi_datetime::date_to_timestamp($this->end_date);

			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'location_id' => $this->location_id, 'user_id' => $this->user_id,
											'mime_type' => $this->mime_type, 'start_date' => $start_date, 'end_date' => $end_date,
											'cat_id' => $this->cat_id, 'dry_run'=>$dry_run));

			static $locations = array();
			static $urls = array();
			$dateformat	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach($values as &$entry)
			{
				if(!$entry['mime_type'])
				{
					$entry['mime_type'] = $mime_magic->filename2mime($entry['name']);
				}

				$entry['date']	= $GLOBALS['phpgw']->common->show_date(strtotime($entry['created']),$dateformat);

				$directory = explode('/', $entry['directory']);

				switch ($directory[2])
				{
					case 'agreement':
						$entry['location'] = '.agreement';
						$entry['location_item_id'] = $directory[3];
						break;
					case 'document':
						$entry['location'] = '.document';
						$entry['location_item_id'] = $directory[4];
						break;
					case 'fmticket':
						$entry['location'] = '.ticket';
						$entry['location_item_id'] = $directory[3];
						break;
					case 'request':
						$entry['location'] = '.project.request';
						$entry['location_item_id'] = $directory[4];
						break;
					case 'service_agreement':
						$entry['location'] = '.s_agreement';
						$entry['location_item_id'] = $directory[3];
						break;
					case 'workorder':
						$entry['location'] = '.project.workorder';
						$entry['location_item_id'] = $directory[3];
						break;
					default:
						$entry['location'] = '.' . str_replace('_', '.', $directory[2]);
			//			$entity_info = explode('_', $directory[2]);						
						$entry['location_item_id'] = $directory[4];

				}

				$entry['url'] = $this->interlink->get_relation_link($entry['location'], $entry['location_item_id']);

				$entry['location_name'] = $this->interlink->get_location_name($entry['location']);
				$entry['document_url'] = $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uigallery.view_file',
											'file'		=> urlencode("{$entry['directory']}/{$entry['name']}")
										));
				$entry['user'] = $GLOBALS['phpgw']->accounts->get($entry['createdby_id'])->__toString();
			}
//_debug_array($values);
			$this->total_records = $this->so->total_records;

			return $values;
		}
		
		public function get_filetypes()
		{
			$values = $this->so->get_filetypes();

			$map = array_flip($this->mime_magic->mime_extension_map);

			$filetypes = array();
			foreach($values as $mime_type)
			{
				$filetypes[] = array
				(
					'id' => urlencode($mime_type),
					'name' => $map[$mime_type]
				);
			}
			return $filetypes;
		}

		public function get_gallery_location()
		{
			$values = $this->so->get_gallery_location();

			$_locations = array();
			$locations = array();
			foreach($values as $entry)
			{
				$directory = explode('/', $entry);

				if(isset($directory[2]) && !isset($directory[3]))
				{
					switch ($directory[2])
					{
						case 'agreement':
							$location = '.agreement';
							break;
						case 'document':
							$location = '.document';
							break;
						case 'fmticket':
							$location = '.ticket';
							break;
						case 'request':
							$location = '.project.request';
							break;
						case 'service_agreement':
							$location = '.s_agreement';
							break;
						case 'workorder':
							$location = '.project.workorder';
							break;
						default:
							$location = '.' . str_replace('_', '.', $directory[2]);
					}

					$locations[] = array
					(
						'id' => urlencode($entry),
						'name' => $this->interlink->get_location_name($location)
					);
				}
			}
			return $locations;
		}
	}
