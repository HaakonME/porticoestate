<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.sobilling');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.soinvoice');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'billing', 'inc/model/');

class rental_uibilling extends rental_uicommon
{
	
	public $public_functions = array
	(
		'index'	=> true,
		'query'	=> true
	);
	
	public function index()
	{
		if(!$this->isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}
		
		// No messages so far
		$errorMsg = null;
		$infoMsg = null;
		$step = null; // Used for overriding the user's selection and choose where to go by code
		// Step 3 - the billing job
		if(phpgw::get_var('step') == '2' && phpgw::get_var('next') != null) // User clicked next on step 2
		{
			$contract_ids = phpgw::get_var('contract'); // Ids of the contracts to bill
			if($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) // User submitted contracts to bill
			{
				$billing_start_timestamps = array(); // Billing start timestamps for each of the contracts
				foreach($contract_ids as $contract_id)
				{
					$billing_start_timestamps[] = strtotime(phpgw::get_var('bill_start_date_' . $contract_id . '_hidden'));
				}
				$billing_job = rental_sobilling::get_instance()->create_billing(isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) : 2, phpgw::get_var('contract_type'), phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, $billing_start_timestamps);
				$data = array
				(
					'billing_job' => $billing_job,
					'contract_type' => phpgw::get_var('contract_type'),
					'billing_term' => phpgw::get_var('billing_term'),
					'year' => phpgw::get_var('year'),
					'month' => phpgw::get_var('month')
				);
				$this->render('billing_step3.php', $data);
			}
			else
			{
				$errorMsg = lang('No contracts were selected.');
				$step = 2; // Go back to step 2
			}
		}
		// Step 2
		else if($step == 2 || (phpgw::get_var('step') == '1' && phpgw::get_var('next') != null) || phpgw::get_var('step') == '3' && phpgw::get_var('previous') != null) // User clicked next on step 1 or previous on step 3
		{
			$filters = array('contracts_for_billing' => true, 'contract_type' => phpgw::get_var('contract_type'), 'billing_term_id' => phpgw::get_var('billing_term'), 'year' => phpgw::get_var('year'), 'month' => phpgw::get_var('month'));
			$contracts = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$data = array
			(
				'contracts' => $contracts,
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => phpgw::get_var('billing_term'),
				'year' => phpgw::get_var('year'),
				'month' => phpgw::get_var('month'),
				'error' => $errorMsg
			);
			$this->render('billing_step2.php', $data);
		}
		// Step 1 - billing job list
		else
		{
			$data = array
			(
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => phpgw::get_var('billing_term'),
				'year' => phpgw::get_var('year'),
				'month' => phpgw::get_var('month'),
			);
			$this->render('billing_step1.php', $data);
		}
	}
	
	public function query()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		//Retrieve the type of query and perform type specific logic
		$query_type = phpgw::get_var('type');
		switch($query_type)
		{
			case 'all_billings':
				$filters = array();
				$result_objects = rental_sobilling::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sobilling::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'invoices':
				$filters = array('billing_id' => phpgw::get_var('billing_id'));
				$result_objects = rental_soinvoice::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soinvoice::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
			if(isset($result))
			{
				if($result->has_permission(PHPGW_ACL_READ))
				{
					// ... add a serialized result
					$rows[] = $result->serialize();
				}
			}
		}
		
		// ... add result data
		$result_data = array('results' => $rows, 'total_records' => $object_count);
		
		//Add action column to each row in result table
		array_walk(
			$result_data['results'],
			array($this, 'add_actions'), 
			array()
		);

		return $this->yui_results($result_data, 'total_records', 'results');
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

			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.view', 'id' => $value['id'])));
			$value['labels'][] = lang('show');
		}

}
?>