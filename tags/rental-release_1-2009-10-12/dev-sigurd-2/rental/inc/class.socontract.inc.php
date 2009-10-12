<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'party', 'inc/model/');
include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');

class rental_socontract extends rental_socommon
{
	protected static $so;
	protected $fields_of_responsibility; // Used for caching the values
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return rental_socontract the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socontract');
		}
		return self::$so;
	}
	
	/**
	 * Filters:
	 * Contracts with party as contract party
	 * Contracts for executive officer
	 * Contracts last edited by user
	 * Contracts of type
	 * Contracts with this id (get single)
	 * Contracts with composite as contract composite
	 * Contracts with contract status
	 * Contracts for billing
	 * 
	 * @see rental/inc/rental_socommon#get_query($sort_field, $ascending, $search_for, $search_type, $filters, $return_count)
	 */
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{	
		$clauses = array('1=1');
		
		//Add columns to this array to include them in the query
		$columns = array();
		
		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == null || $sort_field == '')
		{
			$sort_field = 'contract.id';
		}
		$order = "ORDER BY {$sort_field} {$dir}";
		
		// Search for based on search type
		if($search_for)
		{
			$search_for = $this->marshal($search_for,'field');
			$like_pattern = "'%".$search_for."%'";
			$int_value_of_search = (int) $search_for;
			$like_clauses = array();
			switch($search_type){
				case "id":
					$like_clauses[] = "contract.id = $int_value_of_search";
					$like_clauses[] = "contract.old_contract_id $this->like $like_pattern";
					break;
				case "party_name":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					break;
				case "composite":
					$like_clauses[] = "composite.name $this->like $like_pattern";
					break;
				case "all":
					
					$like_clauses[] = "contract.id = $int_value_of_search";
					$like_clauses[] = "contract.old_contract_id $this->like $like_pattern";
					$like_clauses[] = "contract.comment $this->like $like_pattern";
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "composite.name $this->like $like_pattern";
					break;
			}
			
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
			
			
		}
		
		$filter_clauses = array();
		
		// Contracts with party as contract party
		if(isset($filters['party_id'])){
			$party_id  =   $this->marshal($filters['party_id'],'int');
			$filter_clauses[] = "party.id = $party_id";
		}
		
		// Contracts for this executive officer
		if(isset($filters['executive_officer'])){
			$account_id  =   $this->marshal($filters['executive_officer'],'int');
			$filter_clauses[] = "contract.executive_officer = $account_id";
		}

		// Contracts of type
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $this->marshal($filters['contract_type'],'field');
			$filter_clauses[] = "contract.location_id IN ($type)";
		}
		
		// Contracts with this id (filter for retrieveing a single contract)
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "contract.id = {$id}";
		}
		
		// All contracts with composite as contract composite
		if(isset($filters['composite_id']))
		{	
			$composite_id = $this->marshal($filters['composite_id'],'int');
			$filter_clauses[] = "composite.id = {$composite_id}";
		}
		
		/* 
		 * Contract status is defined by the dates in each contract compared to the target date (default today):
		 * - contracts under planning: 
		 * the start date is larger (in the future) than the target date, or start date is undefined
		 * - active contracts: 
		 * the start date is smaller (in the past) than the target date, and the end date is undefined (running) or 
		 * larger (fixed) than the target date
		 * - under dismissal: 
		 * the start date is smaller than the target date, 
		 * the end date is larger than the target date, and 
		 * the end date substracted the contract type notification period is smaller than the target date
		 * - ended:
		 * the end date is smaller than the target date
		 */
		if(isset($filters['contract_status']) && $filters['contract_status'] != 'all'){	
			
			if(isset($filters['status_date_hidden']) && $filters['status_date_hidden'] != "")
			{
				$ts_query = strtotime($this->marshal($filters['status_date_hidden'],'int')); // target timestamp specified by user
			} 
			else
			{
				$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			}
			switch($filters['contract_status']){
				case 'under_planning':
					$filter_clauses[] = "contract.date_start > {$ts_query} OR contract.date_start IS NULL";
					break;
				case 'active':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND ( contract.date_end >= {$ts_query} OR contract.date_end IS NULL)";
					break;
				case 'under_dismissal':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND contract.date_end >= {$ts_query} AND (contract.date_end - (type.notify_before * (24 * 60 * 60)))  <= {$ts_query}";
					break;
				case 'ended':
					$filter_clauses[] = "contract.date_end < {$ts_query}" ;
					break;
			}
		}
		
		/*
		 * Contracts for billing
		 */
		if(isset($filters['contracts_for_billing']))
		{
			$billing_term_id = (int)$filters['billing_term_id'];
			$sql = "SELECT months FROM rental_billing_term WHERE id = {$billing_term_id}";
			$result = $this->db->query($sql);
			if(!$result)
			{
				return;
			}
			if(!$this->db->next_record())
			{
				return;
			}
			$month = (int)$filters['month'];
			$year = (int)$filters['year'];
			$months = $this->unmarshal($this->db->f('months', true), 'int');
			$timestamp_end = strtotime("{$year}-{$month}-01"); // The first day in the month to bill for
			$timestamp_start = strtotime("-{$months} months", $timestamp_end); // The first day of the period to bill for
			$timestamp_end = strtotime('+1 month', $timestamp_end); // The first day in the month after the one to bill for
			$timestamp_start = strtotime("{$year}-{$month}-01");
			
			$filter_clauses[] = "contract.term_id = {$billing_term_id}";
			$filter_clauses[] = "date_start < $timestamp_end";
			$filter_clauses[] = "(date_end IS NULL OR date_end >= {$timestamp_start})";
			$filter_clauses[] = "billing_start <= {$timestamp_end}";
			
			$specific_ordering = 'invoice.timestamp_end DESC, contract.billing_start DESC, contract.date_start DESC, contract.date_end DESC';
			$order = $order ? $order.' '.$specific_ordering : "ORDER BY {$specific_ordering}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		$condition =  join(' AND ', $clauses);
		
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(contract.id)) AS count';
			$order = ''; // No ordering
		}
		else
		{
			// columns to retrieve
			$columns[] = 'contract.id AS contract_id';
			$columns[] = 'contract.date_start, contract.date_end, contract.old_contract_id, contract.executive_officer, contract.last_updated, contract.location_id, contract.billing_start, contract.service_id, contract.responsibility_id, contract.reference, contract.invoice_header, contract.project_id, billing.deleted, contract.account_in, contract.account_out, contract.term_id, contract.security_type, contract.security_amount, contract.comment';
			$columns[] = 'party.id AS party_id';
			$columns[] = 'party.first_name, party.last_name, party.company_name';
			$columns[] = 'c_t.is_payer';		
			$columns[] = 'composite.id AS composite_id';
			$columns[] = 'composite.name AS composite_name';
			$columns[] = 'type.title, type.notify_before';
			$columns[] = 'last_edited.edited_on';
			$columns[] = 'invoice.timestamp_end';	
			$cols = implode(',',$columns);
		}
		
		$tables = "rental_contract contract";
		$join_contract_type = 	$this->left_join.' rental_contract_responsibility type ON (type.location_id = contract.location_id)';
		$join_parties = $this->left_join.' rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON (c_t.party_id = party.id)';
		$join_composites = 		$this->left_join." rental_contract_composite c_c ON (contract.id = c_c.contract_id) {$this->left_join} rental_composite composite ON c_c.composite_id = composite.id";
		$join_last_edited = $this->left_join.' rental_contract_last_edited last_edited ON (contract.id = last_edited.contract_id)';
		$join_last_billed = "{$this->left_join} rental_invoice invoice ON (contract.id = invoice.contract_id) {$this->left_join} rental_billing billing ON (invoice.billing_id = billing.id)";
		$joins = $join_contract_type.' '.$join_parties.' '.$join_composites.' '.$join_last_edited.' '.$join_last_billed;

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	public function get_id_field_name(){
		return 'contract_id';
	}

	
	function populate(int $contract_id, &$contract)
	{ 
		
		if($contract == null ) // new contract
		{
			$contract_id = (int) $contract_id; 
			$contract = new rental_contract($contract_id);
			$contract->set_contract_date(new rental_contract_date
				(
					$this->unmarshal($this->db->f('date_start'),'int'),
					$this->unmarshal($this->db->f('date_end'),'int')
				)
			);
			$contract->set_billing_start_date($this->unmarshal($this->db->f('billing_start'),'int'));
			$contract->set_old_contract_id($this->unmarshal($this->db->f('old_contract_id'),'string'));
			$contract->set_contract_type_title($this->unmarshal($this->db->f('title'),'string'));
			$contract->set_comment($this->unmarshal($this->db->f('comment'),'string'));
			$contract->set_last_edited_by_current_user($this->unmarshal($this->db->f('edited_on'),'int'));
			$contract->set_location_id($this->unmarshal($this->db->f('location_id'),'int'));
			$contract->set_last_updated($this->unmarshal($this->db->f('last_updated'),'int'));
			$contract->set_service_id($this->unmarshal($this->db->f('service_id'),'string'));
			$contract->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'),'string'));
			$contract->set_reference($this->unmarshal($this->db->f('reference'),'string'));
			$contract->set_invoice_header($this->unmarshal($this->db->f('invoice_header'),'string'));
			$contract->set_account_in($this->unmarshal($this->db->f('account_in'),'string'));
			$contract->set_account_out($this->unmarshal($this->db->f('account_out'),'string'));
			$contract->set_project_id($this->unmarshal($this->db->f('project_id'),'string'));
			$contract->set_executive_officer_id($this->unmarshal($this->db->f('executive_officer'),'int'));
			$contract->set_term_id($this->unmarshal($this->db->f('term_id'),'int'));
			$contract->set_security_type($this->unmarshal($this->db->f('security_type'),'int'));
			$contract->set_security_amount($this->unmarshal($this->db->f('security_amount'),'string'));
		}
		
		$timestamp_end = $this->unmarshal($this->db->f('timestamp_end'),'int');
		$billing_deleted = $this->unmarshal($this->db->f('deleted'),'bool');
		if($timestamp_end && !$billing_deleted)
		{
			$contract->add_bill_timestamp($timestamp_end);
		}
		
		$total_price = $this->unmarshal($this->db->f('total_price'),'int');
		if($total_price)
		{
			$contract->set_total_price($total_price);
		}
		
		$party_id = $this->unmarshal($this->db->f('party_id', true), 'int');
		if($party_id)
		{
			$party = new rental_party($party_id);
			$party->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
			$party->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
			$party->set_company_name($this->unmarshal($this->db->f('company_name', true), 'string'));
			$is_payer = $this->unmarshal($this->db->f('is_payer', true), 'bool');
			if($is_payer)
			{
				$contract->set_payer_id($party_id);
			}
			$contract->add_party($party);
		}
		
		$composite_id = $this->unmarshal($this->db->f('composite_id', true), 'int');
		if($composite_id)
		{
			$composite = new rental_composite($composite_id);
			$composite->set_name($this->unmarshal($this->db->f('composite_name', true), 'string'));
			$contract->add_composite($composite);
		}
		return $contract;
	}
	
	/**
	 * Get a key/value array of contract type titles keyed by their id
	 * 
	 * @return array
	 */
	function get_fields_of_responsibility(){
		if($this->fields_of_responsibility == null)
		{
			$sql = "SELECT location_id,title FROM rental_contract_responsibility";
			$this->db->query($sql, __LINE__, __FILE__);
			$results = array();
			while($this->db->next_record()){
				$location_id = $this->db->f('location_id', true);
				$results[$location_id] = $this->db->f('title', true);
			}
			$this->fields_of_responsibility = $results;
		}
		return $this->fields_of_responsibility;
	}
	
	function get_default_account(int $location_id, bool $in){
		if(isset($location_id) && $location_id > 0)
		{
			if($in)
			{
				$col = 'account_in';
			}
			else
			{
				$col = 'account_out';
			}
			
			$sql = "SELECT {$col} FROM rental_contract_responsibility WHERE location_id = {$location_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f($col,true);
		}
		return '';
	}
	
	/**
	 * Returns the range of year there are contracts. That is, the array
	 * returned contains reversed chronologically all the years from the earliest start
	 * year of the contracts to next year. 
	 * 
	 * @return array of string values, never null.
	 */
	public function get_year_range()
	{
		$year_range = array();
		$sql = "SELECT date_start FROM rental_contract ORDER BY date_start ASC";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$first_year = (int)date('Y'); // First year in the array returned - we first set it to default this year
		if($this->db->next_record()){
			$date = $this->unmarshal($this->db->f('date_start', true), 'int');
			if($date != null && $date != '')
			{
				$first_contract_year = (int)date('Y', $date);
				if($first_contract_year < $first_year) // First contract year is before this year
				{
					$first_year = $first_contract_year;
				}
			}
		}
		$next_year = (int)date('Y', strtotime('+1 year'));
		for($year = $next_year; $year >= $first_year; $year--) // Runs through all years from next year to the first year we want
		{
			$year_range[] = $year;
		}
		
		return $year_range;
	}
	
	/**
	 * Update the database values for an existing contract object.
	 * 
	 * @param $contract the contract to be updated
	 * @return result receipt from the db operation
	 */
	function update($contract)
	{
		$id = intval($contract->get_id());
		
		$values = array();
		
		if($contract->get_term_id()) {
			$values[] = "term_id = " . $this->marshal($contract->get_term_id(), 'int');
		}
		
		if($contract->get_billing_start_date()) {
			$values[] = "billing_start = " . $this->marshal($contract->get_billing_start_date(), 'int');
		}
		
		if ($contract->get_contract_date()) {
			$values[] = "date_start = " . $this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = "date_end = " . $this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}
		
		$values[] = "security_type = '" . $this->marshal($contract->get_security_type(), 'int') . "'";
		$values[] = "security_amount = " . $this->marshal($contract->get_security_amount(), 'string');
		$values[] = "executive_officer = ". $this->marshal($contract->get_executive_officer_id(), 'int');
		$values[] = "comment = ". $this->marshal($contract->get_comment(), 'string');
		$values[] = "last_updated = ".strtotime('now');
		$values[] = "service_id = ". $this->marshal($contract->get_service_id(),'string');
		$values[] = "responsibility_id = ". $this->marshal($contract->get_responsibility_id(),'string');
		$values[] = "reference = ". $this->marshal($contract->get_reference(),'string');
		$values[] = "invoice_header = ". $this->marshal($contract->get_invoice_header(),'string');
		$values[] = "account_in = ".$this->marshal($contract->get_account_in(),'string');
		$values[] = "account_out = ".$this->marshal($contract->get_account_out(),'string');
		$values[] = "project_id = ".$this->marshal($contract->get_project_id(),'string');
		$values[] = "old_contract_id = ".$this->marshal($contract->get_old_contract_id(),'string');
		 
		$result = $this->db->query('UPDATE rental_contract SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		if(isset($result))
		{
			$this->last_edited_by($id);
		}
			
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		return $receipt;
	}
	
	/**
	 * This method marks the combination contract/user account with the current timestamp. It updates the record if the user has updated
	 * this contract before; inserts a new record if the user has never updated this contract. 
	 * 
	 * @param $contract_id
	 * @return true if the contract was marker, false otherwise
	 */
	private function last_edited_by($contract_id){
		$account_id = $GLOBALS['phpgw_info']['user']['account_id']; // current user
		$ts_now = strtotime('now');
		
		$sql_has_edited_before = "SELECT account_id FROM rental_contract_last_edited WHERE contract_id = $contract_id AND account_id = $account_id";
		$result = $this->db->query($sql_has_edited_before);
		
		if(isset($result))
		{
			if($this->db->next_record())
			{
				$sql = "UPDATE rental_contract_last_edited SET edited_on=$ts_now WHERE contract_id = $contract_id AND account_id = $account_id";
			} 
			else
			{
				$sql = "INSERT INTO rental_contract_last_edited VALUES ($contract_id,$account_id,$ts_now)";
			}
			$result = $this->db->query($sql);
			if(isset($result))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * This method markw the given contract with the current timestamp
	 * 
	 * @param $contract_id
	 * @return true if the contract was marked, false otherwise
	 */
	private function last_updated($contract_id){
		$ts_now = strtotime('now');
		$sql = "UPDATE rental_contract SET last_updated=$ts_now";
		$result = $this->db->query($sql);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Add a new contract to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $contract the contract to be added
	 * @return array result receipt from the db operation
	 */
	function add(&$contract)
	{
		// These are the columns we know we have or that are nullable
		$cols = array('location_id', 'term_id');
		
		// Start making a db-formatted list of values of the columns we have to have
		$values = array(
			$this->marshal($contract->get_location_id(), 'int'),
			$this->marshal($contract->get_term_id(), 'int')
		);
		
		// Check values that can be null before trying to add them to the db-pretty list
		if ($contract->get_billing_start_date()) {
			$cols[] = 'billing_start';
			$values[] = $this->marshal($contract->get_billing_start_date(), 'int');
		}
		
		if ($contract->get_contract_date()) {
			$cols[] = 'date_start';
			$cols[] = 'date_end';
			$values[] = $this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = $this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}
		
		if($contract->get_executive_officer_id()) {
			$cols[] = 'executive_officer';
			$values[] = $this->marshal($contract->get_executive_officer_id(), 'int');
		}
		
		$cols[] = 'created';
		$cols[] = 'created_by';
		$values[] = strtotime('now');
		$values[] = $GLOBALS['phpgw_info']['user']['account_id'];
		
		
		$cols[] = 'service_id';
		$cols[] = 'responsibility_id';
		$values[] = $this->marshal($contract->get_service_id(),'string');
		$values[] = $this->marshal($contract->get_responsibility_id(),'string');
		
		$cols[] = 'reference';
		$cols[] = 'invoice_header';
		$values[] = $this->marshal($contract->get_reference(),'string');
		$values[] = $this->marshal($contract->get_invoice_header(),'string');
		
		$cols[] = 'account_in';
		$cols[] = 'account_out';
		$values[] = $this->marshal($contract->get_account_in(),'string');
		$values[] = $this->marshal($contract->get_account_out(),'string');
		
		$cols[] = 'project_id';
		$values[] = $this->marshal($contract->get_project_id(),'string');
		
		$cols[] = 'old_contract_id';
		$values[] = $this->marshal($contract->get_old_contract_id(),'string');
		
		$cols[] = 'comment';
		$values[] = $this->marshal($contract->get_comment(),'string');
		
		if ($contract->get_security_type()) {
			$cols[] = 'security_type';
			$values[] = $this->marshal($contract->get_security_type(),'int');
			$cols[] = 'security_amount';
			$values[] = $this->marshal($contract->get_security_amount(),'string');
		}
		
		// Insert the new contract
		$q ="INSERT INTO rental_contract (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		
		$contract_id = $this->db->get_last_insert_id('rental_contract', 'id');
		$contract->set_id($contract_id);
		return $contract;
	}
	
	/**
	 * This method adds a party to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to add
	 * @return true if successful, false otherwise
	 */
	function add_party($contract_id, $party_id)
	{
		$q = "INSERT INTO rental_contract_party (contract_id, party_id) VALUES ($contract_id, $party_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method removes a party from a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to remove
	 * @return true if successful, false otherwise
	 */
	function remove_party($contract_id, $party_id)
	{
		$q = "DELETE FROM rental_contract_party WHERE contract_id = $contract_id AND party_id = $party_id";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method adds a composite to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $composite_id	the composite to add
	 * @return true if successful, false otherwise
	 */
	function add_composite($contract_id, $composite_id)
	{
		$q = "INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES ($contract_id, $composite_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method removes a composite from a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the composite to remove
	 * @return true if successful, false otherwise
	 */
	function remove_composite($contract_id, $composite_id)
	{
		$q = "DELETE FROM rental_contract_composite WHERE contract_id = {$contract_id} AND composite_id = {$composite_id}";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	
	
	/**
	 * This method sets a payer on a contract
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to be the payer
	 * @return true if successful, false otherwise
	 */
	function set_payer($contract_id, $party_id)
	{
		$pid =$this->marshal($party_id, 'int');
		$cid = $this->marshal($contract_id, 'int'); 
		$q = "UPDATE rental_contract_party SET is_payer = true WHERE party_id = ".$pid." AND contract_id = ".$cid;
		$result = $this->db->query($q);
		$q1 = "UPDATE rental_contract_party SET is_payer = false WHERE party_id != ".$pid." AND contract_id = ".$cid;
		$result1 = $this->db->query($q1);
		if($result && $result1)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
}
?>
