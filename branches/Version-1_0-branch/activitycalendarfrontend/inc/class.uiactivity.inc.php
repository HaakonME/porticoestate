<?php
	phpgw::import_class('activitycalendar.uiactivities');
	phpgw::import_class('activitycalendar.soactivity');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.socontactperson');
	
	include_class('activitycalendar', 'activity', 'inc/model/');
	include_class('activitycalendar', 'group', 'inc/model/');
	include_class('activitycalendar', 'organization', 'inc/model/');
	include_class('activitycalendar', 'arena', 'inc/model/');

	class activitycalendarfrontend_uiactivity extends activitycalendar_uiactivities
	{
		private $so_activity;
		private $so_arena;
		private $so_organization;
		private $so_group;
		private $so_contact;
		
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
			'view'			=>	true,
			'index'			=>	true,
			'get_organization_groups'	=>	true,
			'get_address_search'	=> true,
			'edit_organization_values' => true,
			'get_organization_activities' => true
		);
		
		public function __construct()
		{
			parent::__construct();
			$this->so_activity = activitycalendar_soactivity::get_instance();
			$this->so_arena = activitycalendar_soarena::get_instance();
			$this->so_organization = activitycalendar_soorganization::get_instance();
			$this->so_group = activitycalendar_sogroup::get_instance();
			$this->so_contact = activitycalendar_socontactperson::get_instance();
		}
		
		/**
		 * Public method. Add new activity.
		 */
		public function add()
		{
			//$GLOBALS['phpgw']->redirect_link('/activitycalendarfrontend/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.edit', 'action' => 'new_activity'));
			$GLOBALS['phpgw']->js->validate_file( 'json', 'json', 'phpgwapi' );

			$categories = $this->so_activity->get_categories();
			$targets = $this->so_activity->get_targets();
			$offices = $this->so_activity->select_district_list();
			$districts = $this->so_activity->get_districts();
			$buildings = $this->so_arena->get_buildings();
			$arenas = $this->so_arena->get(null, null, 'arena.arena_name', true, null, null, null);
			$organizations = $this->so_organization->get(null, null, 'org.name', true, null, null, null);
			
			$activity = new activitycalendar_activity();
			
			$g_id = phpgw::get_var('group_id');
			$o_id = phpgw::get_var('organization_id');
			
			if(isset($_POST['step_1'])) //activity shall be registred on a new organization
			{
				if($o_id == "new_org")
				{
					$new_org = true;
					$this->render('activity_new.php', array
						(
							'activity' 	=> $activity,
							'new_organization' => true,
							'arenas' => $arenas,
							'buildings' => $buildings,
							'categories' => $categories,
							'targets' => $targets,
							'districts' => $districts,
							'offices' => $offices,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
				else
				{
					$new_org = false;
					$organization = $this->so_organization->get_single($o_id);
					$person_arr = $this->so_contact->get(null, null, null, null, null, null, array('organization_id' => $o_id));
					foreach($person_arr as $p)
					{
						//var_dump($p);
						$persons[] = $p;
					}
					
					$groups = $this->so_group->get(null, null, null, null, null, null, array('org_id' => $o_id));
					
					$activity->set_organization_id($o_id);
					$activity->set_description($organization->get_description());
					$activity->set_contact_persons($pers);
					
					$this->render('activity_new.php', array
						(
							'activity' 	=> $activity,
							'new_organization' => false,
							'organization' => $organization,
							'contact1' => $persons[0],
							'contact2' => $persons[1],
							'groups' => $groups,
							'arenas' => $arenas,
							'buildings' => $buildings,
							'categories' => $categories,
							'targets' => $targets,
							'districts' => $districts,
							'offices' => $offices,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
			}
			else if(isset($_POST['save_activity']))
			{
				//$persons = $this->so_organization->get_contacts($o_id);				
				//$organization = $this->so_organization->get_single($o_id);
				
				$get_org_from_local = false;
				$new_org = phpgw::get_var('new_org');
				if($new_org != null && $new_org == 'yes')
				{
					$get_org_from_local = true;
				}
				
				if($o_id == "new_org")
				{
					$activity->set_new_org(true);
					//add new organization to internal activitycalendar organization register
					$org_info['name'] = phpgw::get_var('orgname');
					$org_info['orgnr'] = phpgw::get_var('orgno');
					$org_info['homepage'] = phpgw::get_var('homepage');
					$org_info['phone'] = phpgw::get_var('phone');
					$org_info['email'] = phpgw::get_var('email');
					$org_info['description'] = phpgw::get_var('org_description');
					$org_info['street'] = phpgw::get_var('address') . ' ' . phpgw::get_var('number') . ', ' . phpgw::get_var('postaddress');
					//$org_info['zip'] = phpgw::get_var('postaddress');
					$org_info['district'] = phpgw::get_var('org_district'); 
					$org_info['status'] = "new";
					$o_id = $this->so_activity->add_organization_local($org_info);
					
					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('org_contact1_name');
					$contact1['phone'] = phpgw::get_var('org_contact1_phone');
					$contact1['mail'] = phpgw::get_var('org_contact1_email');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact1);
					
					$contact2 = array();
					$contact2['name'] = phpgw::get_var('org_contact2_name');
					$contact2['phone'] = phpgw::get_var('org_contact2_phone');
					$contact2['mail'] = phpgw::get_var('org_contact2_email');
					$contact2['org_id'] = $o_id;
					$contact2['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact2);
					
					//$persons = $this->so_organization->get_contacts_local_as_objects($o_id);
					//var_dump($persons);
					$person_arr = $this->so_contact->get_local_contact_persons($o_id);
					foreach($person_arr as $p)
					{
						//var_dump($p);
						$persons[] = $p;
					}
					$person_ids = $this->so_organization->get_contacts_local($o_id);
					$desc = phpgw::get_var('org_description');
					$organization = $this->so_organization->get_organization_local($o_id);
					$new_org = true;
					//var_dump($organization);
				}
				else if(is_numeric($o_id) && $o_id > 0)
				{
					if(isset($g_id) && $g_id == "new_group")
					{
						$group_info['name'] = phpgw::get_var('groupname');
						$group_info['organization_id'] = $o_id;
						$group_info['description'] = phpgw::get_var('group_description');
						$group_info['status'] = "new";
						$g_id = $this->so_activity->add_group_local($group_info);
						
						//add contact persons
						$contact1 = array();
						$contact1['name'] = phpgw::get_var('group_contact1_name');
						$contact1['phone'] = phpgw::get_var('group_contact1_phone');
						$contact1['mail'] = phpgw::get_var('group_contact1_email');
						$contact1['org_id'] = 0;
						$contact1['group_id'] = $g_id;
						$this->so_activity->add_contact_person_local($contact1);
						
						$contact2 = array();
						$contact2['name'] = phpgw::get_var('group_contact2_name');
						$contact2['phone'] = phpgw::get_var('group_contact2_phone');
						$contact2['mail'] = phpgw::get_var('group_contact2_email');
						$contact2['org_id'] = 0;
						$contact2['group_id'] = $g_id;
						$this->so_activity->add_contact_person_local($contact2);
						
						//$persons = $this->so_group->get_contacts_local_as_objects($g_id);
						$person_arr = $this->so_contact->get_local_contact_persons($g_id, true);
						foreach($person_arr as $p)
						{
							//var_dump($p);
							$persons[] = $p;
						}
						$desc = phpgw::get_var('group_description');
						$group = $this->so_group->get_group_local($g_id);
						$person_ids = $this->so_group->get_contacts_local($g_id);
						$organization = $this->so_organization->get_single($o_id);
					}
					else if(isset($g_id) && is_numeric($g_id) && $g_id > 0)
					{
						$person_ids = $this->so_group->get_contacts($g_id);
						$person_arr = $this->so_contact->get_local_contact_persons($g_id, true);
						foreach($person_arr as $p)
						{
							//var_dump($p);
							$persons[] = $p;
						}
						$desc = $this->so_group->get_description($g_id);
						$organization = $this->so_organization->get_single($o_id);
						$group = $this->so_group->get_single($g_id);
					}
					else if(isset($o_id) && is_numeric($o_id) && $o_id > 0)
					{
						if($get_org_from_local)
						{
							$person_arr = $this->so_contact->get_local_contact_persons($o_id);
							foreach($person_arr as $p)
							{
								//var_dump($p);
								$persons[] = $p;
							}
							$person_ids = $this->so_organization->get_contacts_local($o_id);
							$desc = $this->so_organization->get_description_local($o_id);
							$organization = $this->so_organization->get_organization_local($o_id);
						}
						else
						{
							$person_ids = $this->so_organization->get_contacts($o_id);
							$person_arr = $this->so_contact->get_local_contact_persons($o_id);
							foreach($person_arr as $p)
							{
								//var_dump($p);
								$persons[] = $p;
							}
							$desc = $this->so_organization->get_description($o_id);
							$organization = $this->so_organization->get_single($o_id);
						}
					}
				}
				
				if(strlen($desc) > 254)
				{
					$desc = substr($desc,0,254);
				}
				
				$arena_id = phpgw::get_var('arena_id');
				if($arena_id != null && $arena_id == 'new_arena')
				{				
					$arena = new activitycalendar_arena();
					$arena_name = phpgw::get_var('arena_name');
					$arena_address = phpgw::get_var('arena_address') . ' ' . phpgw::get_var('arena_number') . ', ' . phpgw::get_var('arena_postaddress');
	
					$arena->set_arena_name($arena_name);
					$arena->set_address($arena_address);
					$arena->set_active(true);
			
					// All is good, store arena
					if ($this->so_arena->store($arena)) {
						$arena_id = $arena->get_id();
					}
				}
				
				//... set all parameters
				$activity->set_title(phpgw::get_var('title'));
				$activity->set_organization_id($o_id);
				$activity->set_group_id($g_id);
				$activity->set_arena($arena_id);
				$activity->set_internal_arena(phpgw::get_var('internal_arena_id'));
				$district_array = phpgw::get_var('district');
				$activity->set_district(implode(",", $district_array));
				$activity->set_office(phpgw::get_var('office'));
				$activity->set_state(1);
				$activity->set_category(phpgw::get_var('category'));
				$target_array = phpgw::get_var('target');
				$activity->set_target(implode(",", $target_array));
				$activity->set_description($desc);
				$activity->set_time(phpgw::get_var('time'));
				$activity->set_contact_persons($person_ids);
				$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
				$activity->set_frontend(true);
				
				$target_ok = false;
				$district_ok = false;
				if($activity->get_target() && $activity->get_target() != '')
				{
					$target_ok = true;
				}
				if($activity->get_district() && $activity->get_district() != '')
				{
					$district_ok = true;
				}
				
				if($target_ok && $district_ok)
				{
					if($this->so_activity->store($activity)) // ... and then try to store the object
					{
						$message = lang('messages_saved_form');	
					}
					else
					{
						$error = lang('messages_form_error');
					}
					//$org_info_edit_url = self::link('/index.php' ,array('menuaction' => 'activitycalendarfrontend.uiactivity.edit_organization_values'));

					$GLOBALS['phpgw_info']['flags']['noframework'] = true;

					$this->render('activity.php', array
								(
									'activity' 	=> $activity,
									'organization' => $organization,
									'group' => $group,
									'contact1' => $persons[0],
									'contact2' => $persons[1],
									'arenas' => $arenas,
									'buildings' => $buildings,
									'categories' => $categories,
									'targets' => $targets,
									'districts' => $districts,
									'offices' => $offices,
									'message' => isset($message) ? $message : phpgw::get_var('message'),
									'error' => isset($error) ? $error : phpgw::get_var('error')
								)
					);
				}
				else
				{
					if(!$target_ok)
					{
						$error .= "<br/>" . lang('target_not_selected');
					}
					if(!$district_ok)
					{
						$error .= "<br/>" . lang('district_not_selected');
					}
					return $this->render('activity_new.php', array
						(
							'activity' 	=> $activity,
							'organizations' => $organizations,
							'organization' => $organization,
							'contact1' => $persons[0],
							'contact2' => $persons[1],
							'org_name' => $org_name,
							'new_org' => $new_org,
							'groups' => $groups,
							'arenas' => $arenas,
							'buildings' => $buildings,
							'categories' => $categories,
							'targets' => $targets,
							'districts' => $districts,
							'offices' => $offices,
							'editable' => true,
							'cancel_link' => $cancel_link,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)	
					);
				}
			}
			else
			{
				return $this->render('activity_new_step_1.php', array
						(
							'organizations' => $organizations
						)	
					);
			}
		}
		
		function view()
		{
			$errorMsgs = array();
			$infoMsgs = array();
			$activity = activitycalendar_soactivity::get_instance()->get_single((int)phpgw::get_var('id'));
			
			if($activity == null) // Not found
			{
				$errorMsgs[] = lang('Could not find specified activity.');
			}
	
			$data = array
			(
				'activity' => $activity,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$this->render('activity.php', $data);
			//self::render_template('activity_tmp', array('activity' => $activity, 'frontend'=>'true'));
		}

		function edit()
		{
			$GLOBALS['phpgw']->js->validate_file( 'json', 'json', 'phpgwapi' );

			$id = intval(phpgw::get_var('id', 'GET'));

			$categories = $this->so_activity->get_categories();
			$targets = $this->so_activity->get_targets();
			$offices = $this->so_activity->select_district_list();
			$districts = $this->so_activity->get_districts();
			$buildings = $this->so_arena->get_buildings();
			$arenas = $this->so_arena->get(null, null, 'arena.arena_name', true, null, null, null);
			
			if(isset($_POST['step_1'])) //change_request
			{
				$activity_id = phpgw::get_var('activity_id');
				$activity = $this->so_activity->get_single($activity_id);
				$org = $this->so_organization->get_single($activity->get_organization_id());
				
				
				//store update-request
				$activity->set_state(2);
				if($this->so_activity->store($activity))
				{
					$this->send_email_to_selection(array($activity));
					$message = lang('update_request_sent', $activity->get_title(), $org->get_name());
					return $this->render('activity_edit_step_1.php', array
						(
							'activities' => $activities,
							'message' => $message
						)	
					);
				}
			}
			else
			{
				$secret_param = phpgw::get_var('secret', 'GET');
				if(!isset($id) || $id == '')
				{
					//select activity to edit
					$activities = $this->so_activity->get(null, null, 'title', true, null, null, array('activity_state' => 3));
					$organizations = $this->so_organization->get(null, null, 'org.name', true, null, null, array('edit_from_frontend' => 'yes'));
					return $this->render('activity_edit_step_1.php', array
						(
							'activities' => $activities,
							'organizations' => $organizations
						)	
					);
				}
				if(!isset($secret_param) || $secret_param == ''){
					//select activity to edit
					$activities = $this->so_activity->get(null, null, 'title', true, null, null, array('activity_state' => 3));
					return $this->render('activity_edit_step_1.php', array
						(
							'activities' => $activities
						)	
					);
				}
				else
				{
					// Retrieve the activity object or create a new one
					if(isset($id) && $id > 0)
					{	
						$activity = $this->so_activity->get_single($id); 
					}
					else
					{
						$activities = $this->so_activity->get(null, null, 'title', true, null, null, array('activity_state' => 3));
						return $this->render('activity_edit_step_1.php', array
							(
								'activities' => $activities
							)	
						);
					}
					
					if($activity->get_secret() != phpgw::get_var('secret', 'GET'))
					{
						//select activity to edit
						$activities = $this->so_activity->get(null, null, 'title', true, null, null, array('activity_state' => 3));
						return $this->render('activity_edit_step_1.php', array
							(
								'activities' => $activities
							)	
						);
					}
					
					if($activity->get_group_id())
					{
						$person_arr = $this->so_contact->get_booking_contact_persons($activity->get_group_id(), true);
						foreach($person_arr as $p)
						{
							$persons[] = $p;
						}
						$desc = $this->so_group->get_description($activity->get_group_id());
						$group = $this->so_group->get_single($activity->get_group_id());
						$person_ids = $this->so_group->get_contacts($activity->get_group_id());
					}
					else if($activity->get_organization_id())
					{
						$person_arr = $this->so_contact->get_booking_contact_persons($activity->get_organization_id());
						foreach($person_arr as $p)
						{
							$persons_array[] = $p;
						}
						$desc = $this->so_organization->get_description($activity->get_organization_id());
						$person_ids = $this->so_organization->get_contacts($activity->get_organization_id());
					}
					if(strlen($desc) > 254)
					{
						$desc = substr($desc,0,254);
					}
					$organization = $this->so_organization->get_single($activity->get_organization_id());
					
					if(isset($_POST['save_activity'])) // The user has pressed the save button
					{
						if(isset($activity)) // If an activity object is created
						{
							$old_state = $activity->get_state();
							$new_state = phpgw::get_var('state');
							// ... set all parameters
							$activity->set_title(phpgw::get_var('title'));
							$activity->set_arena(phpgw::get_var('arena_id'));
							$activity->set_internal_arena(phpgw::get_var('internal_arena_id'));
							$district_array = phpgw::get_var('district');
							$activity->set_district(implode(",", $district_array));
							$activity->set_office(phpgw::get_var('office'));
							$activity->set_state(2);
							$activity->set_category(phpgw::get_var('category'));
							$target_array = phpgw::get_var('target');
							$activity->set_target(implode(",", $target_array));
							$activity->set_description($desc);
							$activity->set_time(phpgw::get_var('time'));
							$activity->set_contact_persons($persons);
							$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
							$activity->set_frontend(true);
		
							$target_ok = false;
							$district_ok = false;
							if($activity->get_target() && $activity->get_target() != '')
							{
								$target_ok = true;
							}
							if($activity->get_district() && $activity->get_district() != '')
							{
								$district_ok = true;
							}
							
							if($target_ok && $district_ok)
							{
								
								if($this->so_activity->store($activity)) // ... and then try to store the object
								{
									$message = lang('messages_saved_form');	
								}
								else
								{
									$error = lang('messages_form_error');
								}
				
								$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			
								$this->render('activity.php', array
											(
												'activity' 	=> $activity,
												'organization' => $organization,
												'group' => $group,
												'arenas' => $arenas,
												'buildings' => $buildings,
												'categories' => $categories,
												'targets' => $targets,
												'districts' => $districts,
												'offices' => $offices,
												'message' => isset($message) ? $message : phpgw::get_var('message'),
												'error' => isset($error) ? $error : phpgw::get_var('error')
											)
								);
							}
							else
							{
								if(!$target_ok)
								{
									$error .= "<br/>" . lang('target_not_selected');
								}
								if(!$district_ok)
								{
									$error .= "<br/>" . lang('district_not_selected');
								}
								return $this->render('activity_edit.php', array
									(
										'activity' 	=> $activity,
										'organization' => $organization,
										'org_name' => $org_name,
										'group' => $group,
										'arenas' => $arenas,
										'buildings' => $buildings,
										'categories' => $categories,
										'targets' => $targets,
										'districts' => $districts,
										'offices' => $offices,
										'editable' => true,
										'cancel_link' => $cancel_link,
										'message' => isset($message) ? $message : phpgw::get_var('message'),
										'error' => isset($error) ? $error : phpgw::get_var('error')
									)	
								);
							}
						}
					}
					else
					{
						$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			
						$this->render('activity_edit.php', array
									(
										'activity' 	=> $activity,
										'organization' => $organization,
										'group' => $group,
										'arenas' => $arenas,
										'buildings' => $buildings,
										'categories' => $categories,
										'targets' => $targets,
										'districts' => $districts,
										'offices' => $offices,
										'editable' => true,
										'message' => isset($message) ? $message : phpgw::get_var('message'),
										'error' => isset($error) ? $error : phpgw::get_var('error')
									)
						);
					}
				}
			}
		}
		
		function index()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendarfrontend.uiactivity.add'));
		}
		
		function get_organization_groups()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true; 
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true; 
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			
			$org_id = phpgw::get_var('orgid');
			$group_id = phpgw::get_var('groupid');
			$returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
			if($org_id)
			{
				$group_html[] = "<option value='new_group'>Ny gruppe</option>";
				$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, array('org_id' => $org_id));
				foreach ($groups as $group) {
					if(isset($group))
					{
						//$res_g = $group->serialize();
						$selected = "";
						if($group_id && $group_id > 0)
						{
							$gr_id = (int)$group_id; 
							if($gr_id == (int)$group->get_id())
							{
								$selected_group = " selected";
							}
						}
						$group_html[] = "<option value='" . $group->get_id() . "'". $selected_group . ">" . $group->get_name() . "</option>";
					}
				}
			    $html = implode(' ' , $group_html);
			    $returnHTML = $returnHTML . ' ' . $html;
			}
			
			
			return $returnHTML;
			//return "<option>Ingen gruppe valgt</option>";
		}
		
		/**
		 * Public method.
		 */
		function get_address_search()
		{
			$search_string = phpgw::get_var('search');
			//var_dump($search_string);
			return activitycalendar_soarena::get_instance()->get_address($search_string);
		}
		
		function edit_organization_values()
		{
			$org_id = phpgw::get_var('organization_id');
			if(isset($org_id))
			{
				if(isset($_POST['save_org'])) //save updated organization info
				{
					$organization = $this->so_organization->get_single($org_id);
					
					$org_info['name'] = phpgw::get_var('orgname');
					$org_info['orgnr'] = phpgw::get_var('orgno');
					$org_info['homepage'] = phpgw::get_var('homepage');
					$org_info['phone'] = phpgw::get_var('phone');
					$org_info['email'] = phpgw::get_var('email');
					$org_info['description'] = phpgw::get_var('org_description');
					$org_info['street'] = phpgw::get_var('address');
					//$org_info['zip'] = phpgw::get_var('postaddress');
					$org_info['district'] = $organization->get_district();
					$org_info['status'] = "change";
					$org_info['original_org_id'] = $org_id;
					$o_id = $this->so_activity->add_organization_local($org_info);
					
					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('org_contact1_name');
					$contact1['phone'] = phpgw::get_var('org_contact1_phone');
					$contact1['mail'] = phpgw::get_var('org_contact1_email');
					$contact1['org_id'] = $o_id;
					$contact1['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact1);
					
					$contact2 = array();
					$contact2['name'] = phpgw::get_var('org_contact2_name');
					$contact2['phone'] = phpgw::get_var('org_contact2_phone');
					$contact2['mail'] = phpgw::get_var('org_contact2_email');
					$contact2['org_id'] = $o_id;
					$contact2['group_id'] = 0;
					$this->so_activity->add_contact_person_local($contact2);
					
					$message = lang('change_request_ok', $org_info['name']);
					
					$this->render('organization_reciept.php', array
						(
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
					 
				}
				else
				{
					$organization = $this->so_organization->get_single($org_id);
					$person_arr = $this->so_contact->get(null, null, null, null, null, null, array('organization_id' => $org_id));
					foreach($person_arr as $p)
					{
						$persons[] = $p;
					}
					
					$this->render('organization_edit.php', array
						(
							'organization' => $organization,
							'contact1' => $persons[0],
							'contact2' => $persons[1],
							'districts' => $districts,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
			}
		}
		
		function edit_group_values()
		{
			$group_id = phpgw::get_var('group_id');
			if(isset($group_id))
			{
				if(isset($_POST['save_group'])) //save updated organization info
				{
					$group = $this->so_group->get_single($group_id);
					
					$group_info['name'] = phpgw::get_var('groupname');
					$group_info['organization_id'] = phpgw::get_var('orgid');
					$group_info['description'] = phpgw::get_var('org_description');
					$group_info['status'] = "change";
					$group_info['original_group_id'] = $group_id;
					$g_id = $this->so_activity->add_group_local($group_info);
					
					//add contact persons
					$contact1 = array();
					$contact1['name'] = phpgw::get_var('group_contact1_name');
					$contact1['phone'] = phpgw::get_var('group_contact1_phone');
					$contact1['mail'] = phpgw::get_var('group_contact1_email');
					$contact1['org_id'] = 0;
					$contact1['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact1);
					
					$contact2 = array();
					$contact2['name'] = phpgw::get_var('group_contact2_name');
					$contact2['phone'] = phpgw::get_var('group_contact2_phone');
					$contact2['mail'] = phpgw::get_var('group_contact2_email');
					$contact2['org_id'] = 0;
					$contact2['group_id'] = $g_id;
					$this->so_activity->add_contact_person_local($contact2);
					
					$message = lang('change_request_ok', $group_info['name']);
					
					$this->render('group_reciept.php', array
						(
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
					 
				}
				else
				{
					$group = $this->so_group->get_single($group_id);
					$person_arr = $this->so_contact->get(null, null, null, null, null, null, array('group_id' => $group_id));
					foreach($person_arr as $p)
					{
						$persons[] = $p;
					}
					
					$this->render('group_edit.php', array
						(
							'group' => $group,
							'contact1' => $persons[0],
							'contact2' => $persons[1],
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error')
						)
					);
				}
			}
		}
		
		public function get_organization_activities()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true; 
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true; 
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			
			$org_id = phpgw::get_var('orgid');
			$returnHTML = "<option value='0'>Ingen aktivitet valgt</option>";
			if($org_id)
			{
				$activities = $this->so_activity->get(null, null, 'title', true, null, null, array('activity_state' => 3, 'activity_org' => $org_id));
				foreach ($activities as $act) {
					if(isset($act))
					{
						//$res_g = $group->serialize();
						$activity_html[] = "<option value='" . $act->get_id() . "'>" . $act->get_title() . "</option>";
					}
				}
			    $html = implode(' ' , $activity_html);
			    $returnHTML = $returnHTML . ' ' . $html;
			}
			
			
			return $returnHTML;
		}
	}
