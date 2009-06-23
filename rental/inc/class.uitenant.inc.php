<?php
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'tenant', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	
	class rental_uitenant extends rental_uicommon
	{	
		public $public_functions = array
		(
			'add'		=> true,
			'edit'		=> true,
			'index'		=> true,
			'query'		=> true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::tenant');
		}
		
		//Common method for JSON queries
		public function query()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(phpgw::get_var('id') && $type = phpgw::get_var('type'))
				{
					$id = phpgw::get_var('id');
					$type = phpgw::get_var('type');
					return $this->json_query($id,$type);	
				} 
				else 
				{
					return $this->json_query();
				}
			}
		}

		/**
		 * Return a JSON result of rental tenant related data
		 * 
		 * @param $tenant_id  rental tenant id
		 * @param $type	type of details
		 * @param $field_total the field name that holds the total number of records
		 * @param $field_result the field name that holds the query result
		 * @return 
		 */
		protected function json_query($tenant_id = null, $type = 'index', $field_total = 'total_records', $field_results = 'results')
		{	
		
			/*  HTTP get variables:
			 * 
			 * sort: column to sort
			 * dir: direction (ascending, descending)
			 * startIndex: the index to start from in result
			 * results: number of rows to return
			 * contract_status: filter for contract status
			 * contract_date: filter for contract dates
			 */
			switch($type)
			{
				case 'index':
					$rows = array();
					$tenants = rental_tenant::get_all(
										phpgw::get_var('startIndex'),
										phpgw::get_var('results'),
										phpgw::get_var('sort'),
										phpgw::get_var('dir'),
										phpgw::get_var('query'),
										phpgw::get_var('search_option'),
										array('tenant_type' => phpgw::get_var('tenant_type'))
										);
					foreach ($tenants as $tenant) {
						$rows[] = $this->get_tenant_hash($tenant);
					}
					$tenant_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				return $tenant_data;
			}
			
			//Add action column to each row in result table
			array_walk($tenant_data[$field_results], array($this, '_add_actions'), array($tenant_id,$type));
			return $this->yui_results($tenant_data, $field_total, $field_results);
		}
		
		/**
		 * Add action links for the context menu of the list item
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [composite_id, type of query]
		 */
		public function _add_actions(&$value, $key, $params)
		{
			switch($params[1])
			{
				case 'index':
					$value['actions'] = array(
						'view' => html_entity_decode(self::link(array('menuaction' => 'rental.uitenant.view', 'id' => $value['id']))),
						'edit' => html_entity_decode(self::link(array('menuaction' => 'rental.uitenant.edit', 'id' => $value['id'])))
					);
					break;
				case 'contracts':
					$value['actions'] = array(
						'view_contract' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id']))),
						'edit_contract' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])))
					);
					break;	
			}
			
		}
		
		
		///View all contracts
		public function index()
		{
			self::add_javascript('rental', 'rental', 'rental.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			$data = array
			(
				'dateFormat' 	=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
			);
			self::render_template('tenant_list',$data);
		}
		
		/**
		 * Adds a new tenant and forwards to edit mode for it.
		 * 
		 */
		public function add()
		{
			$receipt = rental_tenant::add();
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uitenant.edit', 'id' => $receipt['id'], 'message' => lang('rental_messages_new_tenant')));
		}
		
		/**
		 * Displays info about a tenant.
		 * 
		 */
		public function view() {
			$tenant_id = (int)phpgw::get_var('id');
			return $this -> viewedit(false, $tenant_id);
		}
		
		/**
		 * Edits a tenant.
		 */
		public function edit(){
			$tenant_id = (int)phpgw::get_var('id');
			return $this -> viewedit(true, $tenant_id);
		}
		
		/**
		 * View or edit tenant
		 * 
		 * @param $editable bool true renders fields editable, false renders fields disabled
		 * @param $tenant_id int with the tenant id	
		 */
		protected function viewedit($editable = false, $tenant_id)
		{
			$tenant_id = (int)$tenant_id;
			if($tenant_id > 0) // Id is set
			{
				$message = phpgw::get_var('message');
				$error = phpgw::get_var('error');
				
				self::add_javascript('rental', 'rental', 'rental.js');
				phpgwapi_yui::load_widget('datatable');
				phpgwapi_yui::load_widget('tabview');
				
				$tenant = $this->get_tenant_hash(rental_tenant::get($tenant_id));
				
				$tabs = array();
				
				foreach(array('rental_tenant_details'/*, 'rental_tenant_contracts', 'rental_tenant_comments', 'rental_tenant_documents'*/) as $tab) {
					$tabs[$tab] =  array('label' => lang($tab), 'link' => '#' . $tab);
				}
				
				phpgwapi_yui::tabview_setup('tenant_edit_tabview');

				$documents = array();
				
				$active_tab = phpgw::get_var('active_tab');
				if (($active_tab == null) || ($active_tab == '')) {
					$active_tab = 'rental_tenant_details';
				}
				
				$data = array
				(
					'tenant' 	=> $tenant,
					'tenant_id' => $tenant_id,
					'tabs'	=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
					'access' => $editable,
					'message' => $message,
					'error' => $error,
					'cancel_link' => self::link(array('menuaction' => 'rental.uitenant.index')),
					'dateFormat' => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
				);				
				self::render_template('tenant', $data);
			}
		}
		
		/**
		 * Convert a rental_contract object into a more XSL-friendly keyed array format
		 * 
		 * @param $composite rental_composite to be converted
		 * @return key=>value array of composite data
		 */
		protected function get_tenant_hash($tenant)
		{
			$name = $tenant->get_last_name();
			if($tenant->get_first_name() != '') // Firstname is set
			{
				if($name != '') // There's a lastname
				{
					$name .= ', '; // Append comma
				}
				$name .= $tenant->get_first_name(); // Append firstname
			}
			if($tenant->get_company_name() != '') // There's a company name
			{
				if($name != '') // We've already got a name
				{
					$name .= ' (' . $tenant->get_company_name() . ')'; // Append company name in parenthesis
				}
				else // No name
				{
					$name = $tenant->get_company_name(); // Set name to company
				}
			}
			return array(
				'id' => $tenant->get_id(),
				'name' => $name,
				'firstname' => $tenant->get_first_name(),
				'lastname' => $tenant->get_last_name(),
				'address' => $tenant->get_address_1() . ', ' . $tenant->get_address_2() . ', ' . $tenant->get_postal_code() . ', ' . $tenant->get_place(),
				'phone' => $tenant->get_phone()
			);
		}

	}
?>