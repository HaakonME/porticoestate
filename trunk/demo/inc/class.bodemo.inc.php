<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage demo
 	* @version $Id: class.bodemo.inc.php,v 1.8 2007/01/24 12:53:01 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package demo
	 */

	class demo_bodemo
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;

		var $public_functions = array
		(
			'read'			=> True,
			'read_single'	=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'	=> True
		);

		function demo_bodemo($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject($this->currentapp.'.sodemo');
			$this->custom 		= createObject('phpgwapi.custom_fields');
			$this->acl_location 	= '.demo_location';

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= get_var('start',array('POST','GET'));
			$query	= get_var('query',array('POST','GET'));
			$sort	= get_var('sort',array('POST','GET'));
			$order	= get_var('order',array('POST','GET'));
			$filter	= get_var('filter',array('POST','GET'));
			$cat_id	= get_var('cat_id',array('POST','GET'));
			$allrows= get_var('allrows',array('POST','GET'));

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if ($allrows)
			{
				$this->allrows = $allrows;
			}

			switch($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mssql':
					$this->dateformat 		= "M d Y";
					$this->datetimeformat 	= "M d Y g:iA";
					break;
				case 'mysql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
				case 'pgsql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
				case 'postgres':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','demo_app',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','demo_app');

			$this->start	= (isset($data['start'])?$data['start']:'');
			$this->query	= (isset($data['query'])?$data['query']:'');
			$this->filter	= (isset($data['filter'])?$data['filter']:'');
			$this->sort		= (isset($data['sort'])?$data['sort']:'');
			$this->order	= (isset($data['order'])?$data['order']:'');
			$this->cat_id	= (isset($data['cat_id'])?$data['cat_id']:'');
		}

		function check_perms($rights, $required)
		{
			return ($rights & $required);
		}

		function read()
		{
			$demo_info = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'cat_id'=>$this->cat_id,'allrows'=>$this->allrows,'filter'=>$this->filter));
			$this->total_records = $this->so->total_records;
			return $demo_info;
		}

		/**
		* Get list of records with dynamically allocated coulmns
		*
		* @return array Array with records.
		*/
		function read2()
		{
			$custom_attributes = $this->custom->get_attribs($this->currentapp, $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);
			
			$demo_info = $this->so->read2(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'cat_id'=>$this->cat_id,'allrows'=>$this->allrows,'filter'=>$this->filter,
											'custom_attributes'=>$custom_attributes));
			$this->total_records = $this->so->total_records;
			$this->uicols	= $this->so->uicols;
			return $demo_info;
		}

		function read_single($id='')
		{
			$values['attributes'] = $this->custom->get_attribs($this->currentapp, $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);
			
			if($id)
			{
				$values = $this->so->read_single($id,$values);
			}
			
			$values = $this->custom->prepare_attributes($values,$appname=$this->currentapp, $location=$this->acl_location);
			
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(isset($values['entry_date']) && $values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}

			return $values;
		}

		function save($values,$values_attribute='')
		{
			if(is_array($values_attribute))
			{
				for ($i=0;$i<count($values_attribute);$i++)
				{
					if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
					}
					if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
					}

					if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
					}
	
					if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = date($this->dateformat,$this->date_to_timestamp($values_attribute[$i]['value']));
					}
				}
			}


			if (isset($values['demo_id']) && $values['demo_id'])
			{
				$receipt = $this->so->edit($values,$values_attribute);
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute);
			}

			$custom_functions = $this->custom->read_custom_function(
				array
				(
					'appname'	=> $this->currentapp,
					'location'	=> $this->acl_location,
					'allrows'	=> true
				));

			if ( isset($custom_functions) && is_array($custom_functions) )
			{
				foreach($custom_functions as $entry)
				{
					if ( is_file(PHPGW_APP_INC . "/custom/{$entry['file_name']}") 
						&& $entry['active'] )
					{
						include_once(PHPGW_APP_INC . "/custom/{$entry['file_name']}");
					}
				}
			}

			return $receipt;
		}

		function delete($id)
		{
			$this->so->delete($id);
		}

		function select_category_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories = $this->so->select_category_list();

			$category_list = array();
			if ( is_array($categories) )
			{
				foreach ( $categories as $category )
				{
					if ( $category['id'] == $selected )
					{
						$category_list[] = array
						(
							'cat_id'	=> $category['id'],
							'name'		=> $category['name'],
							'selected'	=> 'selected'
						);
					}
					else
					{
						$category_list[] = array
						(
							'cat_id'	=> $category['id'],
							'name'		=> $category['name'],
						);
					}
				}
			}
			return $category_list;
		}

		/**
		* Preserve attribute values from post in case of an error
		*
		* @param array $values_attribute attribute definition and values from posting
		* @param array $values value set with 
		* @return array Array with attribute definition and values
		*/
		function preserve_attribute_values($values='',$values_attribute='')
		{
			return $this->custom->preserve_attribute_values($values,$values_attribute);
		}

		function date_array($datestr)
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$fields = split('[./-]',$datestr);
			foreach(split('[./-]',$dateformat) as $n => $field)
			{
				$date[$field] = intval($fields[$n]);

				if($field == 'M')
				{
					for($i=1; $i <=12; $i++)
					{
						if(date('M',mktime(0,0,0,$i,1,2000)) == $fields[$n])
						{
							$date['m'] = $i;
						}
					}
				}
			}

			$ret = array(
				'year'  => $date['Y'],
				'month' => $date['m'],
				'day'   => $date['d']
			);
			return $ret;
		}

		function date_to_timestamp($date='')
		{
			if (!$date)
			{
				return False;
			}

			$date_array	= $this->date_array($date);
			$date	= mktime (8,0,0,$date_array['month'],$date_array['day'],$date_array['year']);

			return $date;
		}

	}
