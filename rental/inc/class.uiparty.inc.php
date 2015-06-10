<?php
//phpgw::import_class('rental.uicommon');

phpgw::import_class('phpgwapi.uicommon_jquery');
phpgw::import_class('phpgwapi.jquery');
	
phpgw::import_class('rental.soparty');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.sodocument');
phpgw::import_class('rental.bofellesdata');
include_class('rental', 'party', 'inc/model/');
include_class('rental', 'unit', 'inc/model/');
include_class('rental', 'location_hierarchy', 'inc/locations/');

//class rental_uiparty extends rental_uicommon
class rental_uiparty extends phpgwapi_uicommon_jquery
{
	public $public_functions = array
	(
			'add'				=> true,
			'edit'				=> true,
			'index'				=> true,
			'query'				=> true,
			'view'				=> true,
			'download'			=> true,
			'download_agresso'	=> true,
			'sync'				=> true,
			'update_all_org_enhet_id'	=> true,
			'syncronize_party'	=> true,
			'syncronize_party_name'	=> true,
			'create_user_based_on_email' => true,
			'get_synchronize_party_info' => true,
			'delete_party'		=> true
	);

	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('rental::parties');
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('parties');
	}

	private function _get_filters()
	{
		$filters = array();

		$search_option = array
		(
			array('id' => 'all', 'name' =>lang('all')),
			array('id' => 'name', 'name' =>lang('name')),
			array('id' => 'address', 'name' =>lang('address')),
			array('id' => 'identifier', 'name' =>lang('identifier')),
			array('id' => 'reskontro', 'name' =>lang('reskontro')),
			array('id' => 'result_unit_number', 'name' =>lang('result_unit_number')),
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'search_option',
						'text'   => lang('search option'),
						'list'   => $search_option
					);
		
		$types = rental_socontract::get_instance()->get_fields_of_responsibility();
		$party_types = array();
		array_unshift ($party_types, array('id'=>'all', 'name'=>lang('all')));
		foreach($types as $id => $label)
		{
			$party_types[] = array('id' => $id, 'name' =>lang($label));
		}
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'party_type',
						'text'   => lang('part_of_contract'),
						'list'   => $party_types
					);
		
		$status_option = array
		(
			array('id' => 'all', 'name' =>lang('not_available_nor_hidden')),
			array('id' => 'active', 'name' =>lang('available_for_pick')),
			array('id' => 'inactive', 'name' =>lang('hidden_for_pick')),
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'active',
						'text'   => lang('marked_as'),
						'list'   => $status_option
					);
		
		return $filters;
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
		/*
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort', 'string', 'GET', 'identifier');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');*/
		
		$search			= phpgw::get_var('search');
		$order			= phpgw::get_var('order');
		$draw			= phpgw::get_var('draw', 'int');
		$columns		= phpgw::get_var('columns');

		$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
		$num_of_objects	= phpgw::get_var('length', 'int', 'REQUEST', $user_rows_per_page);
		$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'identifier'; 
		$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
		// Form variables
		$search_for 	= $search['value'];
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
		
		//Retrieve a contract identifier and load corresponding contract
		$contract_id = phpgw::get_var('contract_id');
		if(isset($contract_id))
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
		}
		
		//Retrieve the type of query and perform type specific logic
		$type = phpgw::get_var('type');

		$config	= CreateObject('phpgwapi.config','rental');
		$config->read();
		$use_fellesdata = $config->config_data['use_fellesdata'];
		switch($type)
		{
			case 'included_parties': // ... get all parties incolved in the contract
				$filters = array('contract_id' => $contract_id);
				break;
			case 'not_included_parties': // ... get all parties not included in the contract
				$filters = array('not_contract_id' => $contract_id, 'party_type' => phpgw::get_var('party_type'));
				break;
			case 'sync_parties':
			case 'sync_parties_res_unit':
			case 'sync_parties_identifier':
			case 'sync_parties_org_unit':
				$filters = array('sync' => $type, 'party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
				if($use_fellesdata)
				{
					$bofelles = rental_bofellesdata::get_instance();
				}
				break;
			default: // ... get all parties of a given type
				phpgwapi_cache::session_set('rental', 'party_query', $search_for);
				phpgwapi_cache::session_set('rental', 'party_search_type', $search_type);
				phpgwapi_cache::session_set('rental', 'party_type', phpgw::get_var('party_type'));
				phpgwapi_cache::session_set('rental', 'party_status', phpgw::get_var('active'));
				$filters = array('party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
				break;
		}
		
		$result_objects = rental_soparty::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
		$result_count = rental_soparty::get_instance()->get_count($search_for, $search_type, $filters);
		
		// Create an empty row set
		$rows = array();
		foreach ($result_objects as $party) {
			if(isset($party))
			{
				$serialized = $party->serialize($contract);
				if($use_fellesdata){
					$sync_data = $party->get_sync_data();
					if($type == 'sync_parties')
					{
						$unit_name_and_id = $bofelles->responsibility_id_exist($sync_data['responsibility_id']);
					}
					else if($type == 'sync_parties_res_unit')
					{
						$unit_name_and_id = $bofelles->result_unit_exist($sync_data['result_unit_number']);
					}
					else if($type == 'sync_parties_identifier')
					{
						$unit_name_and_id = $bofelles->result_unit_exist($party->get_identifier());
					}
					else if($type == 'sync_parties_org_unit')
					{
						$unit_name_and_id = $bofelles->org_unit_exist($sync_data['org_enhet_id']);
					}
					
					if(isset($unit_name_and_id))
					{
						$unit_id = $unit_name_and_id['UNIT_ID'];
						$unit_name = $unit_name_and_id['UNIT_NAME'];
						
						if(isset($unit_id) && is_numeric($unit_id))
						{
							$serialized['org_unit_name'] =  isset($unit_name) ? $unit_name : lang('no_name');
							$serialized['org_unit_id'] = $unit_id;
						}

						// Fetches data from Fellesdata
						$org_unit_id = $sync_data['org_enhet_id'];
				
						$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
						$org_department = $bofelles->get_department_for_org_unit($org_unit_id);
				
						$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
						$org_email = $org_unit_with_leader['ORG_EMAIL'];
						$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];
						$dep_org_name = $org_department['DEP_ORG_NAME'];

						// Fields are displayed in syncronization table
						$serialized['org_unit_name'] = $org_name;
						$serialized['unit_leader'] = $unit_leader_fullname;
						$serialized['org_email'] = $org_email;
						$serialized['dep_org_name'] = $dep_org_name;		
					}
				}
				
				//check if party is a part of a contract
				$party_in_contract = rental_soparty::get_instance()->has_contract($party->get_id());
				$serialized['party_in_contract'] = $party_in_contract ? true : false;
				
				$rows[] = $serialized;
			}
		}
		// ... add result data
		$party_data = array('results' => $rows, 'total_records' => $result_count);

		$editable = phpgw::get_var('editable', 'bool');

		if(!$export){
			array_walk(
				$party_data['results'], 
				array($this, 'add_actions'), 
				array(													// Parameters (non-object pointers)
					$contract_id,										// [1] The contract id
					$type,												// [2] The type of query
					isset($contract) ? $contract->serialize() : null, 	// [3] Serialized contract
					$editable,											// [4] Editable flag
					$this->type_of_user									// [5] User role
				)
			);
		}
		
		$result_data    =   array('results' =>  $party_data['results']);
		$result_data['total_records']	= $result_count;
		$result_data['draw']    = $draw;

		return $this->jquery_results($result_data);
		//return $this->yui_results($party_data, 'total_records', 'results');
	}
	
	/*
	 * One time job for updating the parties with no org_enhet_id.  
	 * The org_enhet_id will be set according to the suggestions given in 
	 * the synchronize function in the rental model UI. 
	 * 
	 */
	public function update_all_org_enhet_id()
	{
		$config	= CreateObject('phpgwapi.config','rental');
		$config->read();

		$use_fellesdata = $config->config_data['use_fellesdata'];	
		if(!$use_fellesdata){
			return;
		}
		$bofelles = rental_bofellesdata::get_instance();
		
		$parties = rental_soparty::get_instance()->get();
		$result_count = rental_soparty::get_instance()->get_count();
		
		echo "Total number of parties: {$result_count}";
		
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			$count = 0;
			$count_result_unit_number = 0;
			$count_identifier = 0;
			$count_responsibility = 0;

			foreach ($parties as $party) {
				$unit_found = false;
				$fellesdata = NULL;

				if(isset($party)) {
					$sync_data = $party->get_sync_data();

					$fellesdata = $bofelles->result_unit_exist($sync_data['result_unit_number'],4);
					if ($fellesdata) {
						echo "Unit id found {$fellesdata['UNIT_ID']} by result unit number check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
						$count_result_unit_number++;
					} else {
						$fellesdata = $bofelles->result_unit_exist($party->get_identifier(),4);
						if ($fellesdata) {
							echo "Unit id found {$fellesdata['UNIT_ID']} by identifier check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
							$count_identifier++;
						} else {
							$fellesdata = $bofelles->responsibility_id_exist($sync_data['responsibility_id']);
							if ($fellesdata) {
								echo "Unit id found {$fellesdata['UNIT_ID']} by responsibility id check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
								$count_responsibility++;
							}
						}
					}

					if ($fellesdata && isset($fellesdata['UNIT_ID']) && is_numeric($fellesdata['UNIT_ID'])) {
						// We found a match, so store the new connection
						$party->set_org_enhet_id($fellesdata['UNIT_ID']);
					} else {
						// No match was found. Set the connection to NULL
						$party->set_org_enhet_id(NULL);
					}
					rental_soparty::get_instance()->store($party);
				}
			}

			echo "Number of parties found through result unit number {$count_result_unit_number}<br />";
			echo "Number of parties found through identifier {$count_identifier}<br />";
			echo "Number of parties found through responsibility id {$count_responsibility}<br />";
			echo "Number of parties that have been updated {$count}<br />";
		}
 	}
 	
	/**
	 * Add action links for the context menu of the list item
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [composite_id, type of query, contract editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();
		$value['alert'] = array();
	
		// Get parameters
		$contract_id = $params[0];
		$type = $params[1];
		$serialized_contract= $params[2];
		$editable = $params[3];
		$user_is = $params[4];
		
		// Depending on the type of query: set an ajax flag and define the action and label for each row
		switch($type)
		{
			case 'included_parties':
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');*/

				if($editable == true)
				{
					/*$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_party', 'party_id' => $value['id'], 'contract_id' => $params[0])));
					$value['labels'][] = lang('remove');*/

					if($value['id'] != $serialized_contract['payer_id']){
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.set_payer', 'party_id' => $value['id'], 'contract_id' => $params[0])));
						$value['labels'][] = lang('set_payer');
					}
				}
				break;
			case 'not_included_parties':
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
				if($editable == true)
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_party', 'party_id' => $value['id'], 'contract_id' => $params[0])));
					$value['labels'][] = lang('add');
				}*/
				break;
			default:
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');*/
					
				if($user_is[ADMINISTRATOR] || $user_is[EXECUTIVE_OFFICER])
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');*/
					
					if((isset($value['party_in_contract']) && $value['party_in_contract'] == false) && (!isset($value['org_enhet_id']) || $value['org_enhet_id'] == ''))
					{
						$value['ajax'][] = true;
						$value['alert'][] = true;
						
						$alertMessage_deleteParty = "Du er i ferd med å slette en kontraktspart.\n\n";
						$alertMessage_deleteParty .= "Operasjonen kan ikke angres.\n\n";
						$alertMessage_deleteParty .= "Vil du gjøre dette?";
						
						$value['alert'][] = $alertMessage_deleteParty;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.delete_party', 'id' => $value['id'])));
						$value['labels'][] = lang('delete');
					}
					
					if(isset($value['org_enhet_id']) && $value['org_enhet_id'] != '')
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'frontend.uihelpdesk.index', 'org_enhet_id' => $value['org_enhet_id'])));
						$value['labels'][] = lang('frontend_access');
					}
					
					if(isset($value['org_enhet_id']) && $value['org_enhet_id'] != '')
					{
						$value['ajax'][] = true;
						$value['alert'][] = true;
						
						$alertMessage = "Du er i ferd med å overskrive data med informasjon hentet fra Fellesdata.\n\n";
						$alertMessage .= "Følgende felt vil bli overskrevet: Foretak, Avdeling, Enhetsleder, Epost. \n\n";
						$alertMessage .= "Vil du gjøre dette?";
						
						$value['alert'][] = $alertMessage; 
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.syncronize_party', 'org_enhet_id' => $value['org_enhet_id'], 'party_id' => $value['id'])));
						$value['labels'][] = lang('syncronize_party');
					}
				}
				break;
		}
	}
	
	/**
	 * Public method. View all contracts.
	 */
	public function index()
	{
		$this->render('party_list.php');
	}

	/**
	 * Public method. Forwards the user to edit mode.
	 */
	public function add()
	{
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit'));
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

	/**
	 * Public method. Called when user wants to edit a contract party.
	 * @param HTTP::id	the party ID
	 */
	public function edit()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
		// Get the contract part id
		$party_id = (int)phpgw::get_var('id');
		
		
		// Retrieve the party object or create a new one if correct permissions
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			if(isset($party_id) && $party_id > 0)
			{	
				$party = rental_soparty::get_instance()->get_single($party_id); 
			}
			else
			{
				$party = new rental_party();
			}
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('permission_denied_edit')));
		}
		
		if(isset($_POST['save_party'])) // The user has pressed the save button
		{
			if(isset($party)) // If a party object is created
			{
				// ... set all parameters
				$party->set_identifier(phpgw::get_var('identifier'));
				$party->set_first_name(phpgw::get_var('firstname'));
				$party->set_last_name(phpgw::get_var('lastname'));
				$party->set_title(phpgw::get_var('title'));
				$party->set_company_name(phpgw::get_var('company_name'));
				$party->set_department(phpgw::get_var('department'));
				$party->set_address_1(phpgw::get_var('address1'));
				$party->set_address_2(phpgw::get_var('address2'));
				$party->set_postal_code(phpgw::get_var('postal_code'));
				$party->set_place(phpgw::get_var('place'));
				$party->set_phone(phpgw::get_var('phone'));
				$party->set_mobile_phone(phpgw::get_var('mobile_phone'));
				$party->set_fax(phpgw::get_var('fax'));
				$party->set_email(phpgw::get_var('email'));
				$party->set_url(phpgw::get_var('url'));
				$party->set_account_number(phpgw::get_var('account_number'));
				$party->set_reskontro(phpgw::get_var('reskontro'));
				$party->set_is_inactive(phpgw::get_var('is_inactive') == 'on' ? true : false);
				$party->set_comment(phpgw::get_var('comment'));
				//$party->set_location_id(phpgw::get_var('location_id'));
				$party->set_org_enhet_id(phpgw::get_var('org_enhet_id'));
				$party->set_unit_leader(phpgw::get_var('unit_leader'));
				
				if(rental_soparty::get_instance()->store($party)) // ... and then try to store the object
				{
					$message = lang('messages_saved_form');	
				}
				else
				{
					$error = lang('messages_form_error');
				}
			}
		}

		$config = CreateObject('phpgwapi.config','rental');
		$config->read();

		return $this->render('party.php', array
			(
				'party' 	=> $party,
				'editable' => true,
				'message' => isset($message) ? $message : phpgw::get_var('message'),
				'error' => isset($error) ? $error : phpgw::get_var('error'),
				'cancel_link' => self::link(array('menuaction' => 'rental.uiparty.index', 'populate_form' => 'yes')),
				'use_fellesdata' => $config->config_data['use_fellesdata']
			)	
		);
	}
	
	public function download_agresso(){
		$browser = CreateObject('phpgwapi.browser');
		$browser->content_header('export.txt','text/plain');
		print rental_soparty::get_instance()->get_export_data();
	}
	
	public function sync()
	{
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}
		
		$editable		= phpgw::get_var('editable', 'bool');
		$sync_job		= phpgw::get_var('sync', 'string', 'GET');
		$contract_id	= phpgw::get_var('contract_id');
		$user_is		= $this->type_of_user;
		
		switch($sync_job)
		{
			case 'resp_and_service':
				self::set_active_menu('rental::parties::sync::sync_resp_and_service');
				//$this->render('sync_party_list.php');
				$appname = lang('sync_parties_service_and_responsibiity');
				$type = 'sync_parties';
				$extra_cols = array(
					array("key" => "responsibility_id", "label" => lang('responsibility_id'), "sortable"=>false, "hidden"=>false),
					array("key" => "sync_message", "label" => lang('sync_message'), "sortable"=>false, "hidden"=>false),
					array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable"=>false, "hidden"=>false)
				);
				break;
			case 'res_unit_number':
				self::set_active_menu('rental::parties::sync::sync_res_units');
				//$this->render('sync_party_list_res_unit.php');
				$appname = lang('sync_parties_result_unit_number');
				$type = 'sync_parties_res_unit';
				$extra_cols = array(
					array("key" => "result_unit_number", "label" => lang('result_unit_number'), "sortable"=>false, "hidden"=>false),
					array("key" => "sync_message", "label" => lang('sync_message'), "sortable"=>false, "hidden"=>false),
					array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable"=>false, "hidden"=>false)
				);
				break;
			case 'identifier':
				self::set_active_menu('rental::parties::sync::sync_identifier');
				//$this->render('sync_party_list_identifier.php');
				$appname = lang('sync_parties_identifier');
				$type = 'sync_parties_identifier';
				$extra_cols = array(
					array("key" => "service_id", "label" => lang('service_id'), "sortable"=>false, "hidden"=>false),
					array("key" => "responsibility_id", "label" => lang('responsibility_id'), "sortable"=>false, "hidden"=>false),
					array("key" => "identifier", "label" => lang('identifier'), "sortable"=>false, "hidden"=>false),
					array("key" => "sync_message", "label" => lang('sync_message'), "sortable"=>false, "hidden"=>false),
					array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable"=>false, "hidden"=>false)
				);
				break;
			case 'org_unit':
				self::set_active_menu('rental::parties::sync::sync_org_unit');
				//$this->render('sync_party_list_org_id.php');
				$appname = lang('sync_parties_fellesdata_id');
				$type = 'sync_parties_org_unit';
				$extra_cols = array(
					array("key" => "org_unit_name", "label" => lang('sync_org_name_fellesdata'), "sortable"=>false, "hidden"=>false),
					array("key" => "dep_org_name", "label" => lang('sync_org_department_fellesdata'), "sortable"=>false, "hidden"=>false),
					array("key" => "unit_leader", "label" => lang('sync_org_unit_leader_fellesdata'), "sortable"=>false, "hidden"=>false),
					array("key" => "org_email", "label" => lang('sync_org_email_fellesdata'), "sortable"=>false, "hidden"=>false)
				);
				break;
		}
			
		$function_msg		= lang('list %1', $appname);

		$data = array(
			'datatable_name'	=> $function_msg,
			'form' => array(
				'toolbar' => array(
						'item' => array()
				)
			),
			'datatable' => array(
				'source'	=> self::link(array(
					'menuaction'	=> 'rental.uiparty.sync', 
					'editable'		=> ($editable) ? 1 : 0,
					'type'			=> $type,
					'phpgw_return_as' => 'json'
				)),
				'download'	=> self::link(array('menuaction' => 'rental.uiparty.download',
						'type'		=> $type,
						'export'    => true,
						'allrows'   => true
				)),
				'allrows'	=> true,
				'editor_action' => '',
				'field' => array(
					array(
						'key'		=> 'identifier', 
						'label'		=> lang('identifier'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'name', 
						'label'		=> lang('name'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'address', 
						'label'		=> lang('address'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					)
				)
			)
		);
				
		$filters = $this->_get_Filters();
		krsort($filters);
		foreach($filters as $filter){
			array_unshift($data['form']['toolbar']['item'], $filter);
		}
			
		foreach  ($extra_cols as $col)
		{
			array_push($data['datatable']['field'], $col);
		}
		
		$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'id',
						'source'	=> 'id'
					),
				)
			);
			
		$parameters2 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'id',
						'source'	=> 'party_id'
					),
				)
			);
		
		$data['datatable']['actions'][] = array
			(
				'my_name'		=> 'view',
				'text' 			=> lang('show'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'rental.uiparty.view'
				)),
				'parameters'	=> json_encode($parameters)
			);
				
		switch($type)
		{
			case 'included_parties':

				if($editable)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'remove',
							'text' 			=> lang('remove'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'rental.uicontract.remove_party',
								'contract_id'	=> $contract_id
							)),
							'parameters'	=> json_encode($parameters2)
						);
					/*$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_party', 'party_id' => $value['id'], 'contract_id' => $params[0])));
					$value['labels'][] = lang('remove');*/
				}
				break;
			case 'not_included_parties':

				if($editable)
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'rental.uicontract.add_party',
								'contract_id'	=> $contract_id
							)),
							'parameters'	=> json_encode($parameters2)
						);
					/*$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_party', 'party_id' => $value['id'], 'contract_id' => $params[0])));
					$value['labels'][] = lang('add');*/
				}
				break;
			default:
					
				if($user_is[ADMINISTRATOR] || $user_is[EXECUTIVE_OFFICER])
				{
					$data['datatable']['actions'][] = array
						(
							'my_name'		=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'rental.uiparty.edit'
							)),
							'parameters'	=> json_encode($parameters)
						);
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');*/
				}
				break;
		}
		
		self::render_template_xsl('datatable_jquery', $data);
	}
	
	public function syncronize_party()
	{
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			$party_id = phpgw::get_var('party_id');
			$org_unit_id = phpgw::get_var('org_enhet_id');

			if(isset($party_id) && $party_id > 0 && isset($org_unit_id) && $org_unit_id > 0)
			{	
				$config	= CreateObject('phpgwapi.config','rental');
				$config->read();
				
				$use_fellesdata = $config->config_data['use_fellesdata'];
				if(!$use_fellesdata){ 
					return;
				}
				
				$bofelles = rental_bofellesdata::get_instance();
				
				$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
				$org_department = $bofelles->get_department_for_org_unit($org_unit_id);
				
				$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
				$org_email = $org_unit_with_leader['ORG_EMAIL'];
				$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];
				$dep_org_name = $org_department['DEP_ORG_NAME'];
					
				$party = rental_soparty::get_instance()->get_single($party_id);
								
				if(!empty($dep_org_name) & $dep_org_name != '')
					$party->set_department($dep_org_name);
				
				if(!empty($unit_leader_fullname) & $unit_leader_fullname != '')
					$party->set_unit_leader($unit_leader_fullname);
					
				if(!empty($org_name) & $org_name != '')
					$party->set_company_name($org_name);

				if(!empty($org_email) & $org_email != '')
					$party->set_email($org_email);
					
				if(!empty($org_unit_id) & $org_unit_id != '')
					$party->set_org_enhet_id($org_unit_id);
	
				rental_soparty::get_instance()->store($party);
				
				$party = rental_soparty::get_instance()->get_single($party_id);
			}
		}
	}
	
	/**
	 * Public method. Called when a user wants to sync data with Fellesdata. 
	 * Returns a json string with the following fields: email, org_name, unit_leader_fullname and department
	 */
	public function get_synchronize_party_info()
	{
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			$org_unit_id = phpgw::get_var("org_enhet_id");
					
			if(isset($org_unit_id) && $org_unit_id > 0)
			{	
				$config	= CreateObject('phpgwapi.config','rental');
				$config->read();
				
				$use_fellesdata = $config->config_data['use_fellesdata'];
				if(!$use_fellesdata){ 
					return;
				}
				
				$bofelles = rental_bofellesdata::get_instance();
				
				$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
				$org_department = $bofelles->get_department_for_org_unit($org_unit_id);
				
				$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
				$org_email = $org_unit_with_leader['ORG_EMAIL'];
				$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];
				
				$dep_org_name = $org_department['DEP_ORG_NAME'];
									
				$jsonArr = array("email" => trim($org_email), "org_name" => trim($org_name), 
								 "unit_leader_fullname" => trim($unit_leader_fullname), "department" => trim($dep_org_name));
				
				return json_decode( json_encode($jsonArr) );		
			}	
		}
	}	
		
	/**
	 * Function to create Portico Estate users based on email, first- and lastname on contract parties.
	 */
	public function create_user_based_on_email()
	{	
		//Get the party identifier from the reuest
		$party_id = phpgw::get_var('id');
		
		//Access control: only executive officers and administrators can create such accounts
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			if(isset($party_id) && $party_id > 0)
			{
				//Load the party from the database
				$party = rental_soparty::get_instance()->get_single($party_id);
				$email = $party->get_email();
				
				//Validate the email
				$validator = CreateObject('phpgwapi.EmailAddressValidator');
				if(!$validator->check_email_address($email))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit','id' => $party_id, 'error' => lang('error_create_user_based_on_email_not_valid_address')));
				}
				if ($GLOBALS['phpgw']->accounts->exists($email) )
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit','id' => $party_id, 'error' => lang('error_create_user_based_on_email_account_exist')));
				}
				
				//Read group configuration
				$config	= CreateObject('phpgwapi.config','rental');
				$config->read();
				$renter_group = $config->config_data['create_user_based_on_email_group'];
				
				//Get namae and generate password
				$first_name = $party->get_first_name();
				$last_name = $party->get_last_name();
				$passwd = $GLOBALS['phpgw']->common->randomstring(6)."ABab1!"; 
				
				
				try {
					//Create account which never expires
					$account			= new phpgwapi_user();
					$account->lid		= $email;
					$account->firstname	= $first_name;
					$account->lastname	= $last_name;
					$account->passwd	= $passwd;
					$account->enabled	= true;
					$account->expires	= -1;
					$frontend_account	= $GLOBALS['phpgw']->accounts->create($account, array($renter_group), array(), array('frontend'));
					
					//Specify the accounts access to modules 
					$aclobj =& $GLOBALS['phpgw']->acl;
					$aclobj->set_account_id($frontend_account, true);
					$aclobj->add('frontend', '.', 1);
					$aclobj->add('frontend', 'run', 1);
					$aclobj->add('manual', '.', 1);
					$aclobj->add('manual', 'run', 1);
					$aclobj->add('preferences', 'changepassword',1);
					$aclobj->add('preferences', '.',1);
					$aclobj->add('preferences', 'run',1);
					$aclobj->save_repository();
					
					//Set the default module for the account
					$preferences = createObject('phpgwapi.preferences', $frontend_account);
					$preferences->add('common','default_app','frontend');
					$preferences->save_repository();
				
				} catch (Exception $e) {
					//Redirect with error message if something goes wrong
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit','id' => $party_id, 'error' => $e->getMessage()));
				}
		
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'] )
				{
					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}
					
					//Get addresses from module configuration
					$from = $config->config_data['from_email_setting'];
					$address = $config->config_data['http_address_for_external_users'];
					
					// Define email content
					$title = lang('email_create_user_based_on_email_title');
					$message = lang('email_create_user_based_on_email_message',$first_name,$last_name,$passwd, $address);
				
					//Send email
					$rcpt = $GLOBALS['phpgw']->send->msg('email',$email,$title,
						 stripslashes(nl2br($message)), '', '', '',
						 $from , 'System message',
						 'html', '', array() , false);
					
					//Redirect with sucess message if receipt is ok
					if($rcpt)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit','id' => $party_id, 'message' => lang('success_create_user_based_on_email')));
					}
				}
			}	
		}
		//Redirect to edit mode with error message if user reaches this point.
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit','id' => $party_id, 'error' => lang('error_create_user_based_on_email')));
	}
	
	public function delete_party()
	{
		$party_id = phpgw::get_var('id');
		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			if(isset($party_id) && $party_id > 0)
			{
				if(rental_soparty::get_instance()->delete_party($party_id)) // ... delete the party
				{
					$message = lang('messages_saved_form');	
				}
				else
				{
					$error = lang('messages_form_error');
				} 
			}
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('permission_denied_edit')));
		}
	}	
}
?>
