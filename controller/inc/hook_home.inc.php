<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
				
	$location_array = array();
	$component_short_desc = array();
	
	$so_check_list = CreateObject('controller.socheck_list');
	$so_control = CreateObject('controller.socontrol');
	
	$config	= CreateObject('phpgwapi.config','controller');
	$config->read();
	$limit_no_of_planned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls']:5);
	$limit_no_of_assigned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'] : (isset($config->config_data['no_of_assigned_controls']) && $config->config_data['no_of_assigned_controls'] > 0 ? $config->config_data['no_of_assigned_controls']:10);

	$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
	$styling  = "\n".'<!-- BEGIN checklist info -->'."\n ";
	$styling .= "<style>"; 
	$styling .= " .home_portal_content a { color:#0066CC;text-decoration: none;text-transform: uppercase;} .home_portal{margin:20px 20px 0 10px;} "; 
	$styling .= " .home-box { background: none repeat scroll 0 0 #EDF5FF; border-color: #DBE5EF; border-radius: 4px; margin: 5px 20px 20px;}";
	$styling .= " .home-box .home_portal { margin: 0;border: 1px solid #DEEAF8;}";
	$styling .= " .home_portal_content { padding:5px 10px;}";
	$styling .= " .home_portal_title h2 { overflow:hidden;clear:left;font-size: 13px;font-weight: bold;text-transform:uppercase; background: #DEEAF8; margin: 0; padding: 2px 10px; color: #1C3C6F;}";
	$styling .= " .property_tickets .home_portal_title h2 { font-size: 20px; padding: 5px 10px;}";
	$styling .= " .home_portal_content ul li { clear: left; overflow: hidden; padding: 3px 0;}";
	$styling .= " .home_portal .title { width:300px;margin:0 20px 0 0;}"; 
	$styling .= " .home_portal .control-area { width:200px;}";
	$styling .= " .home_portal .control { width:300px;}";
	$styling .= " .home_portal .date { width:300px;}";
	$styling .= " .home_portal li div { display: block;float:left;cursor: pointer;vertical-align: middle;}";
	$styling .= " .home_portal_title h2 div{ display:block;float:left;cursor: pointer;vertical-align: middle;}";
	$styling .= "  h2.heading { font-size: 22px; font-weight: normal;margin: 0 0 0 20px;}";
	$styling .= "  h4.expand_trigger img { vertical-align:middle;margin-right:3px; }";
	$styling .= "  h4.expand_trigger span { vertical-align:middle; }";
	$styling .= "  .expand_list{ display:none; }";
	$styling .= "</style>"; 
	$styling .= "\n".'<!-- END checklist info -->'."\n";
	echo $styling;
	
	echo "<script src='controller/js/controller/jquery.js'></script>";
	
	$script = "<script>";
	$script .= "$(document).ready(function(){"; 
  $script .= " $('.expand_trigger').live('click', function() {"; 
	$script .= " var liTag = $(this).closest('li'); ";
	$script .= " var expandList = $(liTag).find('.expand_list'); ";
	$script .= " if( !$(expandList).hasClass('active') ){ $(expandList).show(); $(expandList).addClass('active');  ";
	$script .= " $(liTag).find('img').attr('src', 'controller/images/arrow_down.png');} ";
	$script .= " else{ $(expandList).hide(); $(expandList).removeClass('active');  ";
	$script .= " $(liTag).find('img').attr('src', 'controller/images/arrow_right.png');} ";
	$script .= " return false; "; 
	$script .= " })";
	$script .= " })";
	$script .= "</script>";
	echo $script;
			
	// Fetches my properties
	$criteria = array
	(
		'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
		'type_id' => 1, // Nivå i bygningsregisteret 1:eiendom
		'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
		'allrows' => false
	);

	$location_finder = new location_finder();
	$my_properties = $location_finder->get_responsibilities( $criteria );

	// Fetches my buildings
	$criteria = array
	(
		'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
		'type_id' => 2, // Nivå i bygningsregisteret 1:eiendom
		'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
		'allrows' => false
	);

	$location_finder = new location_finder();
	$my_buildings = $location_finder->get_responsibilities( $criteria );
	
	$my_locations = array_merge($my_properties, $my_buildings);
	
	
	
	/* =======================================  UNDONE ASSIGNED CONTROLS FOR CURRENT USER  ================================= */
	
	$my_controls = array();
	$repeat_type = null;
	
	// from date is set to 3 months back in time
	$from_date_ts =  mktime(0, 0, 0, date("n")-3, date("j"), date("Y") );
	$to_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y") );
	
	// Fetches controls current user is responsible for 3 months back in time
	foreach($my_locations as $location)
	{
		$controls = array();
		$components_with_controls_array = array();
		$location_code = $location["location_code"];

		$controls_at_location = $so_control->get_controls_by_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"] );
				    
		$level = count(explode('-', $location_code));

		if($level == 1){
			// Fetches all controls for the components for a location within time period
			$filter = "bim_item.location_code = '$location_code' ";
			$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);	
		}else
		{
			// Fetches all controls for the components for a location within time period
			$filter = "bim_item.location_code LIKE '$location_code%' ";
			$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);	
		}

		if( count($controls_at_location) > 0 )
		{
			// Saves location code, location type and an array containing controls at locations
			$my_controls[] = array( $location_code, 'location', $controls_at_location );
		}

		if( count($components_with_controls_array) > 0 )
		{
			foreach($components_with_controls_array as $component)
			{
				// Saves location code, location type, an array containing controls at locations and component object 
				$my_controls[] = array( $location_code, 'component', $component['controls_array'], $component );
			}
		}
	}
	
	$my_undone_controls = array();

	// Generates an array containing undone controls
	foreach($my_controls as $container_arr)
	{	
		$location_code = $container_arr[0];
		$control_type = $container_arr[1];
		$controls = $container_arr[2];
				
		foreach($controls as $my_control)
		{
			if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
			{
				// DAILY CONTROLS: Fetch undone controls one week back in time
				$from_date_ts =  mktime(0, 0, 0, date("n"), date("j")-7, date("Y") );
			}
			else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK)
			{
				// WEEKLY CONTROLS: Fetch undone controls one month back in time
				$from_date_ts =  mktime(0, 0, 0, date("n")-1, date("j"), date("Y") ); 
			}
			else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH)
			{
				// MONTHLY CONTROLS: Fetch undone controls three months back in time
				$from_date_ts =  mktime(0, 0, 0, date("n")-3, date("j"), date("Y") ); 
			}
			else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR)
			{
				// YEARLY CONTROLS: Fetch undone controls one year back in time
				$from_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y")-1 );
			}
						
			$date_generator = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
			$deadline_dates_for_control = $date_generator->get_dates();

			$check_list_array = array();
			foreach($deadline_dates_for_control as $deadline_ts)
			{
				$check_list = null;
				
				if($control_type == "location")
				{
					$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location"	);
				}
				else if($control_type == "component")
				{
					$component = $container_arr[3];
					
					$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component"	);
				}

				$control_id = $my_control['id'];
				
				if($check_list == null & $control_type == "location")
				{   
					$my_undone_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control, "location", $location_code );   
				}
				else if($check_list == null & $control_type == "component")
				{
					$component = $container_arr[3];
					$my_undone_controls[$deadline_ts][]= array("add", $deadline_ts, $my_control, "component", $component['location_id'], $component['id'] );
				}
				else if($check_list->get_status() == controller_check_list::STATUS_NOT_DONE)
				{
					$my_undone_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control, $check_list->get_id(), $location_code );
				}
			}
		}
	}
		
	$portalbox0 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "<div class='date heading'>Fristdato</div><div class='control heading'>Tittel på kontroll</div><div class='title heading'>Lokasjonsnavn</div><div class='control-area heading'>Kontrollområde</div>",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	// Sorts my_undone_controls by deadline date
	ksort($my_undone_controls);
	
	foreach($my_undone_controls as $date_ts => $controls_on_date)
	{
			if(count( $controls_on_date) > 1 )
			{
				$portalbox0->data[] = array(		  	
						'text' => "<h4 class='expand_trigger' style='font-size: 12px;color:#031647;background: #D0DEF4;padding:2px 4px;margin:0;'><img height='12' src='controller/images/arrow_right.png' /><span style='display:inline-block;width:805px'>Frist: "  . date($dateformat, $date_ts) .  "</span><span style='display:inline-block;width:200px;'>Antall kontroller: " .  count($controls_on_date) . "</span></h4><ul class='expand_list'>"
					);
			}
		
		foreach($controls_on_date as $my_undone_control)
		{
			$check_list_status = $my_undone_control[0];
			$deadline_ts = $my_undone_control[1];
			$my_control = $my_undone_control[2];
			
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));
		        
			foreach($control_areas['cat_list'] as $area)
			{
				if($area['cat_id'] == $my_control["control_area_id"])
				{
					$control_area_name = $area['name'];
				}
			}
			
			$date_str = date($dateformat, $deadline_ts);
			
			if($check_list_status == "add")
			{
				$check_list_type = $my_undone_control[3];
				
				if($check_list_type == "location")
				{
					$location_code = $my_undone_control[4];
					if(!isset($location_array[$location_code]) || !$location_array[$location_code])
					{
						$location_array[$location_code] = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
					}
					$location_name = $location_array[$location_code]["loc1_name"];
					
					if(count( $controls_on_date) > 1 )
					{
						$portalbox0->data[] = array(		  	
							'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></li>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts))
						);
					}
					else
					{
						$portalbox0->data[] = array(		  	
							'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts))
						);
					}
					
				}
				else if($check_list_type == "component")
				{
					$location_id = $my_undone_control[4];
					$component_id = $my_undone_control[5];
					
					if(!isset($component_short_desc[$location_id][$component_id]))
					{
						$component_short_desc[$location_id][$component_id] = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
					}
	
					if($component_short_desc[$location_id][$component_id])
					{
						$short_desc_arr = $component_short_desc[$location_id][$component_id];
					}
	    		
					if(count( $controls_on_date) > 1 )
					{
						$portalbox0->data[] = array(		  	
							'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div></li>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts))
						);
					}
					else
					{
						$portalbox0->data[] = array(		  	
							'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts))
						);
					}
				}	
			}
			else if($check_list_status == "edit")
			{
				$check_list_id = $my_undone_control[3];
				$location_code = $my_undone_control[4];
					
				if(!isset($location_array[$location_code]) || !$location_array[$location_code])
				{
					$location_array[$location_code] = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				}
				$location_name = $location_array[$location_code]["loc1_name"];
			
				if(count( $controls_on_date) > 1 )
				{
					$portalbox0->data[] = array(		  	
						'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></li>",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id))
					);
				}	
				else
				{
					$portalbox0->data[] = array(		  	
						'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div>",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id))
					);
				}
			}
		}
		
		if(count( $controls_on_date) > 1 )
		{
			$portalbox0->data[] = array(		  	
					'text' => "</ul>"
				);
		}
	}
	
	echo "\n".'<!-- BEGIN checklist info -->'."\n <h2 class='heading'>Mine glemte kontroller</h2><div class='home-box'>".$portalbox0->draw()."</div>\n".'<!-- END checklist info -->'."\n";

	
	/* =======================================  PLANNED CONTROLS FOR CURRENT USER  ================================= */

	$repeat_type = null;
	$controls_for_location_array = array();
	foreach($my_locations as $location)
	{

		$controls = array();
		$controls_loc = $so_control->get_controls_by_location($location["location_code"], $from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"] );
		$controls_comp = $so_control->get_controls_for_components_by_location($location["location_code"], $from_date_ts, $to_date_ts, $repeat_type, $location["role_id"] );
	    
		foreach($controls_loc as $cl)
		{
			$controls[] = $cl;
		}
	    
		foreach($controls_comp as $cc)
	  {
			$controls[] = $cc;
		}
	    
		$controls_for_location_array[] = array($location["location_code"], $controls);
	}

	$controls_array = array();
	$control_dates = array();
	foreach($controls_for_location_array as $control_arr)
	{
		$current_location = $control_arr[0];
		$controls_for_loc_array = $control_arr[1];
		foreach($controls_for_loc_array as $control)
		{
			$date_generator = new date_generator($control["start_date"], $control["end_date"], $from_date_ts, $to_date_ts, $control["repeat_type"], $control["repeat_interval"]);
			$controls_array[] = array($current_location, $control, $date_generator->get_dates());
		}
	}

	$portalbox1 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "<div class='date heading'>Fristdato</div><div class='control heading'>Tittel på kontroll</div><div class='title heading'>Lokasjonsnavn</div><div class='control-area heading'>Kontrollområde</div>",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$category_name = array(); // caching
	
	$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
	$cats->supress_info	= true;
	$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

	$portalbox1->data = array();
	$portalbox1_data = array();
	foreach ($controls_array as $control_instance)
	{
		$curr_location = $control_instance[0];
		$current_control = $control_instance[1];
		$check_lists = $so_check_list->get_planned_check_lists_for_control($current_control["id"], $curr_location, $current_control['location_id'], $current_control['component_id']);

		if(!isset($location_array[$curr_location]) || !$location_array[$curr_location])
		{
			$location_array[$curr_location] = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		}
		$location_name = $location_array[$curr_location]["loc1_name"];

		if(isset($current_control['component_id']) && $current_control['component_id'])
		{
//_debug_array($current_control);
			if(!isset($component_short_desc[$current_control['location_id']][$current_control['component_id']]))
			{
				$component_short_desc[$current_control['location_id']][$current_control['component_id']] = execMethod('property.soentity.get_short_description', array('location_id' => $current_control['location_id'], 'id' => $current_control['component_id']));
			}
			
			if($component_short_desc[$current_control['location_id']][$current_control['component_id']])
			{
				$location_name .= "::{$component_short_desc[$current_control['location_id']][$current_control['component_id']]}";
			}
		}

		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control["control_area_id"])
			{
				$control_area_name = $area['name'];
			}
		}
		foreach($check_lists as $check_list)
		{
			$next_date = "Planlagt: " . date($dateformat, $check_list->get_planned_date());
			$portalbox1_data[] = array(
				$check_list->get_planned_date(), array(
		//			'text' => "<div class='control'>{$current_control["title"]}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div><div class='date'>{$next_date}</div>",
					'text' => "<div class='date'>{$next_date}</div><div class='control'>{$current_control["title"]}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div>",
					'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list->get_id()))
			));
		}
	}
	//sort data by planned date for check list
	sort($portalbox1_data);
	//$limit = 5;
	$tmp = 0;
	foreach($portalbox1_data as $check_list_dates)
	{
		if($tmp < $limit_no_of_planned)
		{
			$portalbox1->data[] = $check_list_dates[1];
		}
		$tmp++;
	}
	echo "\n".'<!-- BEGIN checklist info -->'."\n<h2 class='heading'>Mine planlagte kontroller</h2><div class='home-box'>".$portalbox1->draw()."</div>\n".'<!-- END checklist info -->'."\n";

	
	
	
	/* ================================  CONTROLS ASSIGNED TO CURRENT USER  ================================= */
	
	$portalbox2 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "<div class='date heading'>Fristdato</div><div class='control heading'>Tittel på kontroll</div><div class='title heading'>Lokasjonsnavn</div><div class='control-area heading'>Kontrollområde</div>",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));
	
	$my_controls = array();
	$repeat_type = null;
	
	$from_date_ts =  strtotime("now");
	$to_date_ts = mktime(0, 0, 0, date("n")+1, date("j"), date("Y") );
	
	// Fetches controls current user is responsible for
	$my_controls = array();
	foreach($my_locations as $location)
	{
		$controls = array();
		$components_with_controls_array = array();
		$location_code = $location["location_code"];
						
		$controls_loc = $so_control->get_controls_by_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"] );
				    
		$level = count(explode('-', $location_code));

		if($level == 1)
		{
			// Fetches all controls for the components for a location within time period
			$filter = "bim_item.location_code = '$location_code' ";
			$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);	
		}
		else
		{
			// Fetches all controls for the components for a location within time period
			$filter = "bim_item.location_code LIKE '$location_code%' ";
			$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);	
		}

		if( count($controls_loc) > 0 )
		{
			$my_controls[] = array( $location_code, 'location', $controls_loc );
		}

		if( count($components_with_controls_array) > 0 )
		{
			foreach($components_with_controls_array as $component)
			{
		    	$my_controls[] = array( $location_code, 'component', $component['controls_array'], $component );
			}
		}
	}
	
	$my_assigned_controls = array();

	$from_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y") );
	
	// Generates an array with undone controls 
	
	foreach($my_controls as $container_arr)
	{	
		$location_code = $container_arr[0];
		$control_type = $container_arr[1];
		$controls = $container_arr[2];
				
		foreach($controls as $my_control)
		{
			if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
			{
				// Daily control: Todate in one week
				$to_date_ts =  mktime(0, 0, 0, date("n"), date("j")+7, date("Y") );
			}
			else if(($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK) 
						| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH) 
						| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR))
			{
				// Daily, monthly yearly control: Todate in one month
				$to_date_ts =  mktime(0, 0, 0, date("n")+1, date("j"), date("Y") ); 
			}

			$date_generator = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
			$deadline_dates_for_control = $date_generator->get_dates();

			$check_list_array = array();
			foreach($deadline_dates_for_control as $deadline_ts)
			{
				$check_list = null;
				
				if($control_type == "location")
				{
					$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location"	);
				}
				else if($control_type == "component")
				{
					$component = $container_arr[3];
					$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component"	);
				}
				
				if($check_list == null)
				{      
					if($control_type == "location")
					{
						$my_assigned_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control, "location", $location_code );
					}
					else if($control_type == "component")
					{
						$component = $container_arr[3];
						$my_assigned_controls[$deadline_ts][] =  array("add", $deadline_ts, $my_control, "component", $component['location_id'], $component['id'] );
		      }
				}
				else if($check_list->get_status() == controller_check_list::STATUS_NOT_DONE)
				{
					$my_assigned_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control, $check_list->get_id(), $location_code );
				}
			}
		}
	}

	// Sorts my_undone_controls by deadline date
	$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
	$cats->supress_info	= true;
	$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

	foreach($my_assigned_controls as $date_ts => $assigned_controls_on_date)
	{
		if(count( $assigned_controls_on_date) > 1 )
		{
			$portalbox2->data[] = array(		  	
//					'text' => "<h4 class='expand_trigger' style='font-size: 12px;color:#031647;background: #D0DEF4;padding:2px 4px;margin:0;'><img height='12' src='controller/images/arrow_right.png' /><span style='display:inline-block;width:805px'>Antall kontroller: " .  count($assigned_controls_on_date) . "</span><span style='display:inline-block;width:200px;'>" . date($dateformat, $date_ts) . "</span></h4><ul class='expand_list'>"
					'text' => "<h4 class='expand_trigger' style='font-size: 12px;color:#031647;background: #D0DEF4;padding:2px 4px;margin:0;'><img height='12' src='controller/images/arrow_right.png' /><span style='display:inline-block;width:805px'>Frist: "  . date($dateformat, $date_ts) .  "</span><span style='display:inline-block;width:200px;'>Antall kontroller: " .  count($assigned_controls_on_date) . "</span></h4><ul class='expand_list'>"
				);
		}
		
		foreach($assigned_controls_on_date as $my_assigned_control)
		{
			$check_list_status = $my_assigned_control[0];
			$deadline_ts = $my_assigned_control[1];
			$my_control = $my_assigned_control[2];
			
		    reset($control_areas['cat_list']);    
			
			foreach($control_areas['cat_list'] as $area)
			{
				if($area['cat_id'] == $my_control["control_area_id"])
				{
					$control_area_name = $area['name'];
				}
			}
			
			$date_str = date($dateformat, $deadline_ts);
			
			if($check_list_status == "add")
			{
				$check_list_type = $my_assigned_control[3];
				
				if($check_list_type == "location")
				{
					$location_code = $my_assigned_control[4];

					if(!isset($location_array[$location_code]) || !$location_array[$location_code])
					{
						$location_array[$location_code] = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
					}
					$location_name = $location_array[$location_code]["loc1_name"];
			
					
				if(count( $assigned_controls_on_date) > 1 )
					{
						$portalbox2->data[] = array(		  	
					//		'text' => "<li><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div></li>",
							'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></li>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts))
						);
					}
					else
					{
						$portalbox2->data[] = array(		  	
					//		'text' => "<div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div>",
							'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts))
						);
					}
				}
				else if($check_list_type == "component")
				{
					$location_id = $my_assigned_control[4];
					$component_id = $my_assigned_control[5];
					
					if(!isset($component_short_desc[$location_id][$component_id]))
					{
						$component_short_desc[$location_id][$component_id] = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
					}
	
					if($component_short_desc[$location_id][$component_id])
					{
						$short_desc_arr = $component_short_desc[$location_id][$component_id];
					}

					if(count( $assigned_controls_on_date) > 1 )
					{
						$portalbox2->data[] = array(		  	
			//				'text' => "<li><div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div></li>",
							'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div></li>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts))
						);
					}
					else
					{
						$portalbox2->data[] = array(		  	
			//				'text' => "<div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div>",
							'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_desc_arr}</div><div class='control-area'>{$control_area_name}</div>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts))
						);
					}
				}	
			}
			else if($check_list_status == "edit")
			{
				$check_list_id = $my_assigned_control[3];
				$location_code = $my_assigned_control[4];
					
				if(!isset($location_array[$location_code]) || !$location_array[$location_code])
				{
					$location_array[$location_code] = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				}
				$location_name = $location_array[$location_code]["loc1_name"];

				if(count( $assigned_controls_on_date ) > 1 )
				{
					$portalbox2->data[] = array(		  	
					//	'text' => "<li><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div></li>",
						'text' => "<li><div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></li>",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id))
					);
				}	
				else
				{
					$portalbox2->data[] = array(		  	
						//'text' => "<div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div><div class='date'>Fristdato {$date_str}</div>",
						'text' => "<div class='date'>Fristdato {$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div>",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id))
					);
				}
			}
		}
		
		if(count( $assigned_controls_on_date ) > 1 )
		{
			$portalbox2->data[] = array(		  	
					'text' => "</ul>"
				);
		}
	}

	echo "\n".'<!-- BEGIN checklist info -->'."\n <h2 class='heading'>Mine tildelte kontroller</h2><div class='home-box'>".$portalbox2->draw()."</div>\n".'<!-- END checklist info -->'."\n";
