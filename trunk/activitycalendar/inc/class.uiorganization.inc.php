<?php
phpgw::import_class('activitycalendar.uicommon');
phpgw::import_class('activitycalendar.soorganization');
phpgw::import_class('activitycalendar.sogroup');
phpgw::import_class('activitycalendar.soactivity');

include_class('activitycalendar', 'organization', 'inc/model/');
include_class('activitycalendar', 'group', 'inc/model/');

class activitycalendar_uiorganization extends activitycalendar_uicommon
{
	public $public_functions = array
	(
		'index'				=> true,
		'query'				=> true,
		'changed_organizations'	=>	true,
		'get_organization_groups' => true,
		'show'	=>	true,
		'edit'	=>	true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('activitycalendar::organizationList');
		$config	= CreateObject('phpgwapi.config','activitycalendar');
		$config->read();
	}
	
	public function index()
	{
		if(phpgw::get_var('phpgw_return_as') == 'json') {
			return $this->index_json();
		}
			
		$this->render('organization_list.php');
	}
	
	public function changed_organizations()
	{
		self::set_active_menu('activitycalendar::organizationList::changed_organizations');
		$this->render('organization_list_changed.php');
	}
	
	public function index_json()
	{
		$organizations = activitycalendar_soorganization::get_instance()->get(); //get organizations
		array_walk($organizations["results"], array($this, "_add_links"), "booking.uiorganization.show");

		foreach($organizations["results"] as &$organization) {

			$contact = (isset($organization['contacts']) && isset($organization['contacts'][0])) ? $organization['contacts'][0] : null;

			if ($contact) {
				$organization += array(
							"primary_contact_name"  => ($contact["name"])  ? $contact["name"] : '',
							"primary_contact_phone" => ($contact["phone"]) ? $contact["phone"] : '',
							"primary_contact_email" => ($contact["email"]) ? $contact["email"] : '',
				);
			}
		}

		return $this->yui_results($organizations);
	}
	
	public function edit()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
		$id = (int)phpgw::get_var('id');
		$type = phpgw::get_var('type');
		unset($org_info);
		unset($contact1);
		unset($contact2);
		if($type)
		{
			//var_dump($type);
			$so = activitycalendar_sogroup::get_instance();
			$group = $so->get(null, null, null, null, null, null, array('id' => $id, 'changed_groups' => 'true'));
			if(count($group_array) > 0){
				$keys = array_keys($group_array);
				$group = $group_array[$keys[0]];
			}
			if(isset($_POST['save_group'])) // The user has pressed the save button
			{
				$orgno = phpgw::get_var('orgno');
				$district = phpgw::get_var('org_district');
				$homepage = phpgw::get_var('homepage');
				$email = phpgw::get_var('email');
				$phone = phpgw::get_var('phone');
				$address = phpgw::get_var('address');
				$desc = phpgw::get_var('org_description');
			}
			else if(isset($_POST['store_group'])) // The user has pressed the store button
			{
				$orgno = phpgw::get_var('orgno');
				$district = phpgw::get_var('org_district');
				$homepage = phpgw::get_var('homepage');
				$email = phpgw::get_var('email');
				$phone = phpgw::get_var('phone');
				$address = phpgw::get_var('address');
				$desc = phpgw::get_var('org_description');
			}
			
			$data = array
			(
				'group' 	=> $group,
				'editable' => true,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			return $this->render('group.php', $data);
		}
		else
		{
			//var_dump('org');
			$so = activitycalendar_soorganization::get_instance();
			$so_activity = activitycalendar_soactivity::get_instance();
			$so_contact = activitycalendar_socontactperson::get_instance();
			$org_array = $so->get(null, null, null, null, null, null, array('id' => $id, 'changed_orgs' => 'true'));
			if(count($org_array)>0){
				$keys = array_keys($org_array);
				$org = $org_array[$keys[0]];
			}
			//var_dump($org);
			$districts = $so_activity->get_districts();
			
			if(isset($_POST['save_organization'])) // The user has pressed the save button
			{
				$org->set_organization_number(phpgw::get_var('orgno'));
				$org->set_district(phpgw::get_var('org_district'));
				$org->set_homepage(phpgw::get_var('homepage'));
				$org->set_email(phpgw::get_var('email'));
				$org->set_phone(phpgw::get_var('phone'));
				$org->set_address(phpgw::get_var('address'));
				$org->set_description(phpgw::get_var('org_description'));
				
				if($so->update_local_org($org))
				{
					$message = lang('messages_saved_form');	
				}
				else
				{
					$error = lang('messages_form_error');
				}
			}
			else if(isset($_POST['store_organization'])) // The user has pressed the store button
			{
				$orgno = phpgw::get_var('orgno');
				$district = phpgw::get_var('org_district');
				$homepage = phpgw::get_var('homepage');
				$email = phpgw::get_var('email');
				$phone = phpgw::get_var('phone');
				$address_tmp = phpgw::get_var('address');
				//phpgw::get_var('address') . ' ' . phpgw::get_var('number') . ', ' . phpgw::get_var('postaddress');
				$address_array = explode(",",$address_tmp);
				$desc = phpgw::get_var('org_description');
				
				$org_info = array();
				$org_info['name'] = $org->get_name(); //new
				$orgno_tmp = $orgno;
				if(strlen($orgno_tmp) > 9)
				{
					$orgno_tmp = NULL;
				}
				$org_info['orgnr'] = $orgno_tmp; 
				
				$org_info['homepage'] = $homepage;
				$org_info['phone'] = $phone;
				$org_info['email'] = $email;
				$org_info['description'] = $desc;
				$org_info['street'] = $address_array[0];
				$org_info['zip'] = $address_array[1];
				$org_info['activity_id'] = '';
				$org_info['district'] = $district;
				
				$new_org_id = $so->transfer_organization($org_info);
				if($new_org_id)
				{
					//update activity with new org id
					//add contact persons to booking
					$contact1 = array();
					$contact1['name'] = $contact1_name;
					$contact1['phone'] = $contact1_phone;
					$contact1['mail'] = $contact1_email;
					$contact1['org_id'] = $new_org_id;
					$so_activity->add_contact_person_org($contact1);
					
					$contact2 = array();
					$contact2['name'] = $contact2_name;
					$contact2['phone'] = $contact2_phone;
					$contact2['mail'] = $contact_mail_2;
					$contact2['org_id'] = $new_org_id;
					$so_activity->add_contact_person_org($contact2);
					$message = lang('messages_saved_form');	
					
					//get affected activities and update with new org id
					$update_activities = $so_activity->get_activities_for_update($new_org_id);
					foreach($update_activities as $act)
					{
						$act->set_organization_id($new_org_id);
						$act->set_new_org(false);
						$so_activity->store($act);
					}
					
					//set local organization as stored
					$org->set_change_type("added");
					$org->set_transferred(true);
					$so->update_local($org);
				}
				else
				{
					$error = lang('messages_form_error');
				}
				
			}
			
			$data = array
			(
				'organization' 	=> $org,
				'districts'	=>	$districts,
				'editable' => true,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			
			return $this->render('organization.php', $data);
		}
	}
	
	public function show()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
		$id = (int)phpgw::get_var('id');
		$type = phpgw::get_var('type');
		if($type)
		{
			//var_dump($type);
			$so = activitycalendar_sogroup::get_instance();
			$group_array = $so->get(null, null, null, null, null, null, array('id' => $id));
			if(count($group_array) > 0){
				$keys = array_keys($group_array);
				$group = $group_array[$keys[0]];
			}
			
			$data = array
			(
				'group' 	=> $group,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			return $this->render('group.php', $data);
		}
		else
		{
			//var_dump('org');
			$so = activitycalendar_soorganization::get_instance();
			$org_array = $so->get(null, null, null, null, null, null, array('id' => $id, 'changed_orgs' => 'true'));
			if(count($org_array)>0){
				$keys = array_keys($org_array);
				$org = $org_array[$keys[0]];
			}
			
			var_dump($org);
			
			$data = array
			(
				'organization' 	=> $org,
				'errorMsgs' => $errorMsgs,
				'infoMsgs' => $infoMsgs
			);
			
			return $this->render('organization.php', $data);
		}
	}
	
	

	/**
	 * (non-PHPdoc)
	 * @see rental/inc/rental_uicommon#query()
	 */
	public function query()
	{
		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else {
			$user_rows_per_page = 10;
		}
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort', 'string', 'GET', 'identifier');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		
		//Create an empty result set
		$parties = array();
		
		$exp_param 	= phpgw::get_var('export');
		$export = false;
		if(isset($exp_param)){
			$export=true;
			$num_of_objects = null;
		}
		
		//Retrieve the type of query and perform type specific logic
		$type = phpgw::get_var('type');
		$changed_org = false;
		$changed_group = false;
		switch($type)
		{
			case 'changed_organizations':
				$filters = array('changed_orgs' => 'true');
				$changed_org = true;
				break;
			case 'changed_groups':
				$filters = array('changed_groups' => 'true');
				$changed_group = true;
				break;
			default: // ... get all parties of a given type
				//$filters = array('party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
				break;
		}
		if($changed_group)
		{
			$result_objects = activitycalendar_sogroup::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = activitycalendar_sogroup::get_instance()->get_count($search_for, $search_type, $filters);
		}
		else
		{
			$result_objects = activitycalendar_soorganization::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = activitycalendar_soorganization::get_instance()->get_count($search_for, $search_type, $filters);
		}
				
		//var_dump($result_objects);
		// Create an empty row set
		$rows = array();
		foreach ($result_objects as $result) {
			if(isset($result))
			{
				$res = $result->serialize();
				$org_id = $result->get_id();
				//$rows[] = $result->serialize();
				$rows[] = $res;
				if(!$changed_group && !$changed_org)
				{
					$filter_group = array('org_id' => $org_id);
					$result_groups = activitycalendar_sogroup::get_instance()->get(null, null, $sort_field, $sort_ascending, $search_for, $search_type, $filter_group);
					foreach ($result_groups as $result_group) {
						if(isset($result_group))
						{
							$res_g = $result_group->serialize();
							$rows[] = $res_g;
						}
					}
				}
			}
		}
		// ... add result data
		$organization_data = array('results' => $rows, 'total_records' => $result_count);

		$editable = phpgw::get_var('editable') == 'true' ? true : false;

		if(!$export){
			array_walk(
				$organization_data['results'], 
				array($this, 'add_actions'), 
				array(													// Parameters (non-object pointers)
					$type												// [2] The type of query		
				)
			);
		}
		
		
		return $this->yui_results($organization_data, 'total_records', 'results');
	}
	
	public function get_organization_groups()
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = true; 
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true; 
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		
		$org_id = phpgw::get_var('orgid');
		$group_id = phpgw::get_var('groupid');
		$returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
		if($org_id)
		{
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
	 * Public method. Called when a user wants to view information about a party.
	 * @param HTTP::id	the party ID
	 */
	public function view()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
		// Get the contract part id
		$party_id = (int)phpgw::get_var('id');
		if(isset($party_id) && $party_id > 0)
		{
			$party = rental_soparty::get_instance()->get_single($party_id); 
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('invalid_request')));
			return;
		}
		
		if(isset($party) && $party->has_permission(PHPGW_ACL_READ))
		{
			return $this->render(
				'party.php', 
				array (
					'party' 	=> $party,
					'editable' => false,
					'cancel_link' => self::link(array('menuaction' => 'rental.uiparty.index', 'populate_form' => 'yes')),
				)
			);
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('permission_denied_view_party')));
		}
	}

	public function download_agresso(){
		$browser = CreateObject('phpgwapi.browser');
		$browser->content_header('export.txt','text/plain');
		print rental_soparty::get_instance()->get_export_data();
	}
	
	/**
	 * Add action links and labels for the context menu of the list items
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [composite_id, type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		//Defining new columns
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();

		$query_type = $params[0];
		
		switch($query_type)
		{
			case 'all_organizations':
				$value['ajax'][] = false;
				if($value['organization_id'] != '' && $value['organization_id'] != null){
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'booking.uigroup.show', 'id' => $value['id'])));
				}
				else
				{
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $value['id'])));
				}
				$value['labels'][] = lang('show');
				break;
				
			case 'changed_organizations':
				$value['ajax'][] = false;
				if($value['organization_id'] != '' && $value['organization_id'] != null){
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show', 'id' => $value['id'], 'type' => 'group')));
				}
				else
				{
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show', 'id' => $value['id'])));
				}
				$value['labels'][] = lang('show');
				$value['ajax'][] = false;
				if($value['organization_id'] != '' && $value['organization_id'] != null){
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.show', 'id' => $value['id'], 'type' => 'group')));
				}
				else
				{
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiorganization.edit', 'id' => $value['id'])));
				}
				$value['labels'][] = lang('edit');
				break;
		}
    }
}
?>