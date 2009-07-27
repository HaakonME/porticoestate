<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'composite', 'inc/model/');

class rental_socontract extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_contract',
		array
		(
					'id'	=> array('type' => 'int'),
					'date_start' => array('type' => 'date'),
					'date_end' => array('type' => 'date'),
					'title'	=> array('type' => 'string'),
					'composite_name' => array('type' => 'string'),
					'first_name' => array('type' => 'string'),
					'last_name' => array('type' => 'string'),
					'company_name' => array('type' => 'string'),
					'old_contract_id' => array('type' => 'string')
		));
	}
	
	protected function get_conditions($query, $filters,$search_option)
	{	
		$clauses = array('1=1');
		if($query)
		{
			$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
			$like_clauses = array();
			switch($search_option){
				case "id":
					$like_clauses[] = "contract.id = $query";
					$like_clauses[] = "contract.old_contract_id = $query";
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
					$like_clauses[] = "contract.id = $query";
					$like_clauses[] = "contract.old_contract_id = $query";
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
		
		if(isset($filters['party_id'])){
			$party_id  =   $filters['party_id'];
			$filter_clauses[] = "party.id = $party_id";
		}
					
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $filters['contract_type'];
			$filter_clauses[] = "contract.type_id = $type";
		}
		
		if(isset($filters['contract_status']) && $filters['contract_status'] != 'all'){
			
			$status_date = date('Y-m-d');
			$timestamp = mktime(0,0,0,date("m")+3,date("d"),date("y"));
			$dismissal_date = date('Y-m-d',$timestamp);
			
			if(isset($filters['status_date_hidden']) && $filters['status_date_hidden'] != "")
			{
				$status_date = $filters['status_date_hidden'];
				$dismissal_timestamp = strtotime(date("Y-m-d", strtotime($status_date)) . " +3 month");;
				$dismissal_date = date('Y-m-d',$dismissal_timestamp);
				//var_dump($dismissal_date);
			}
			
			switch($filters['contract_status']){
				case 'under_planning':
					$filter_clauses[] = "contract.date_start > '{$status_date}' OR contract.date_start IS NULL";
					break;
				case 'active':
					$filter_clauses[] = "contract.date_start <= '{$status_date}' AND ( contract.date_end >= '{$status_date}' OR contract.date_end IS NULL)";
					break;
				case 'under_dismissal':
					$filter_clauses[] = "contract.date_start <= '{$status_date}' AND contract.date_end >= '{$status_date}' AND contract.date_end <= '{$dismissal_date}'";
					break;
				case 'ended':
					$filter_clauses[] = "contract.date_end < '{$status_date}'" ;
					break;
			}
		}
			
		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
		
		return join(' AND ', $clauses);
	}
	

	/**
	 * Get a key/value array of contract type titles keyed by their id
	 * 
	 * @return array
	 */
	function get_contract_types(){
		$sql = "SELECT id,title FROM rental_contract_type";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$results[$this->db->f('id', true)] = $this->db->f('title', true);
		}
		
		return $results;
	}
	
	/**
	 * Get a key/value array of titles of billing term types keyed by their id
	 * 
	 * @return array
	 */
	function get_billing_terms()
	{
		$sql = "SELECT id, title FROM rental_billing_term";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$results[$this->db->f('id', true)] = $this->db->f('title', true);
		}
		
		return $results;
	}
	
	/**
	 * Get single contract
	 * 
	 * @param	$id	id of the contract to return
	 * @return a rental_contract
	 */
	function get_single($id)
	{
		$id = (int)$id;
		$sql_payer_id = "LEFT JOIN  (SELECT contract_id, party_id FROM rental_contract_party  WHERE is_payer = true) rcp ON (rental_contract.id = rcp.contract_id)";
		
	    $sql = "SELECT * FROM " . $this->table_name ." $sql_payer_id WHERE " . $this->table_name . ".id={$id}";
	
	    $this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
	
	    $contract = new rental_contract();
	
	    $this->db->next_record();

		$contract->set_id($this->unmarshal($this->db->f('id', true), 'int'));

		$date_start =  strtotime($this->unmarshal($this->db->f('date_start', true), 'date'));
   		$date_end = strtotime($this->unmarshal($this->db->f('date_end', true), 'date'));
	
		$date = new rental_contract_date($date_start, $date_end);
		$contract->set_contract_date($date);
		
		$billing_start_date = strtotime($this->unmarshal($this->db->f('billing_start_date', true), 'date'));
		$contract->set_billing_start_date($billing_start_date);
		$contract->set_type_id($this->unmarshal($this->db->f('type_id', true), 'int'));
		$contract->set_term_id($this->unmarshal($this->db->f('term_id', true), 'int'));
		$contract->set_account($this->unmarshal($this->db->f('account', true), 'string'));
		$contract->set_payer_id($this->unmarshal($this->db->f('party_id', true), 'int'));
			
      	return $contract;
	}
	
	/**
	 * Get a list of contract objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_cotract objects
	 */
	function get_contract_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{ 
		$distinct = "DISTINCT contract.id, ";
		$columns_for_list = 'contract.id, contract.date_start, contract.date_end, contract.old_contract_id, type.title, composite.name as composite_name, party.first_name, party.last_name, party.company_name';
		$tables = "rental_contract contract";
		$join_contract_type = 	' LEFT JOIN rental_contract_type type ON (type.id = contract.type_id)';
		$join_parties = 'LEFT JOIN rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON c_t.party_id = party.id';
		$join_composites = 		' LEFT JOIN rental_contract_composite c_c ON (contract.id = c_c.contract_id) LEFT JOIN rental_composite composite ON c_c.composite_id = composite.id';
		$joins = $join_contract_type.$join_parties.$join_composites;
		$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//var_dump("SELECT  $columns_for_list FROM $tables $joins WHERE $condition");
		//$this->db->limit_query("SELECT  $columns_for_list FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		
		/*$temp = 'LEFT OUTER JOIN (rental_contract_party JOIN rental_party ON (rental_contract_party.party_id = rental_party.id)) USING (id)';
		$temp1 = 'LEFT OUTER JOIN(SELECT rental_party.first_name, rental_party.last_name FROM rental_party INNER JOIN rental_contract_party ON rental_contract_party.party_id = rental_party.id)';
		$cols = 'rental_contract.id, rental_contract.date_start, rental_contract.date_end, rental_contract_type.title, rental';
		
		// Calculate total number of records
		$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');*/
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $columns_for_list FROM $tables $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $columns_for_list FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		}
		
		
		
		
		$results = array();
		
		while ($this->db->next_record())
		{
			$row = array();
			foreach($this->fields as $field => $fparams)
			{
      			$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
			}
			$results[] = $row;
		}
		
		
		$contracts = array();
		
		// Go through each returned row and create contract objects
		foreach ($results as $row) {
			$new_contract = true;
			$party_name = $row['first_name']." ".$row['last_name'];
			if($row['company_name'] != ''){
				if(trim($party_name) != ''){
					$party_name.= " (".$row['company_name'].")";
				} else {
					$party_name = $row['company_name'];
				}
			}
			
			foreach($contracts as $c) {
				if($row[id] == $c->get_id()){
					$new_contract = false;
					if($row['composite_name'] != ''){
						$c->set_composite_name($row['composite_name']);
					}
					if($row['company_name'] != '' || $row['first_name'] != '' || $row['last_name'] != ''){
						$c->set_party_name($party_name);
					}
					break;
				}		
			}
			if($new_contract) {
				$contract = new rental_contract($row['id']);
			$contract->set_contract_date(new rental_contract_date($row['date_start'],$row['date_end']));
			$contract->set_party_name($party_name);
			$contract->set_composite_name($row['composite_name']);
			$contract->set_old_contract_id($row['old_contract_id']);
			$contract->set_contract_type_title($row['title']);
			$contracts[] = $contract;
			}
		}
		return $contracts;
	}
	
	/**
	 * Returns all contracts for a specified composite.
	 * 
	 * @param $params array with parameters for the query
	 * @return array with 'total_records' and 'results'.
	 */
	public function get_contracts($id, $sort = null, $dir = null, $start = 0, $limit = 1000, $contract_status = null, $date = null)
	{
		// Params
		$id = (int)$id;
				
		// Default return data:
		$total_records = 0;
		$results = array();
		
		$contracts = array();
		
		if($id > 0) // Valid id
		{
			$tables = 'rental_contract';
			$joins = 'JOIN rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id)';
			$condition = 'rental_contract_composite.composite_id = '.$id;
			$current_date = date('Y-m-d');
			switch($contract_date)
			{
				case 'all':
					/* no-op */
					break;
				case 'not_started':
					$condition .= " AND rental_contract.date_start > '{$current_date}'";  
					break;
				case 'ended':
					$condition .= " AND rental_contract.date_end < '{$current_date}'";  
					break;
				case 'active':
				default:
					$condition .= " AND (rental_contract.date_start <= '{$current_date}' AND rental_contract.date_end >= '{$current_date}')";  
					break;
			}
			
			$order = '';
			
			if($sort != null) // We should sort results
			{
				$order = 'ORDER BY '.$sort.' '.($dir == 'desc' ? 'desc' : 'asc');
			}
			
			$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');
			
			$sql = "SELECT rental_contract.id, date_start, date_end FROM {$tables} {$joins} WHERE {$condition} {$order}";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			while($this->db->next_record())
			{
				$contract = new rental_contract($this->unmarshal($this->db->f('id', true), 'string'));
				
				$date_start =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_start', true), 'date')));
	     	$date_end = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_end', true), 'date')));
	     	
				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));
				
				// TODO: include party here whenever that db table is ready
				//$contract->set_party($party)
				
				$contracts[] = $contract;
			}
		}
		
		return $contracts;
		
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
	
	/**
	 * Get the composites belonging to a certain contract
	 * 
	 * @return A list of rental_composite objects
	 * @param string $contract_id
	 */
	public function get_composites_for_contract($contract_id)
	{
		$sql = "SELECT rental_composite.id FROM rental_composite
			LEFT JOIN rental_contract_composite ON (rental_composite.id = rental_contract_composite.composite_id)
			LEFT JOIN rental_contract ON (rental_contract_composite.contract_id = rental_contract.id)
			WHERE rental_contract.id = $contract_id";
		$this->db->query($sql);						
		$composites = array();
		$composite_so = rental_composite::get_so();
		while($this->db->next_record()) { 
			$composite_id = $this->unmarshal($this->db->f('id', true), 'int');
			$composites[] = $composite_so->get_single($composite_id);
		 }
		return $composites;
	}
	
	/**
	 * Get the composites belonging to a certain contract
	 * 
	 * @return A list of rental_composite objects
	 * @param string $contract_id
	 */
	public function get_available_composites_for_contract($contract_id)
	{
		$sql = "SELECT rental_composite.id FROM rental_composite
			LEFT JOIN rental_contract_composite ON (rental_composite.id = rental_contract_composite.composite_id)
			LEFT JOIN rental_contract ON (rental_contract_composite.contract_id = rental_contract.id)
			WHERE rental_contract.id != $contract_id";
		$this->db->query($sql);						
		$composites = array();
		$composite_so = rental_composite::get_so();
		while($this->db->next_record()) { 
			$composite_id = $this->unmarshal($this->db->f('id', true), 'int');
			$composites[] = $composite_so->get_single($composite_id);
		 }
		return $composites;
	}
	
	
	/**
	 * Get the parties involved in this contract
	 * 
	 * @param $contract_id the contract id
	 * @return A list of rental_party objects
	 */
	public function get_parties_for_contract($contract_id)
	{
		$sql = "SELECT party_id FROM rental_contract_party WHERE contract_id = $contract_id";
		$this->db->query($sql);
		$parties = array();
		$parties_so = rental_party::get_so();
		while($this->db->next_record()) {
			$party_id = $this->unmarshal($this->db->f('party_id', true), 'int');
			$parties[] = $parties_so->get_single($party_id);
		}
		return $parties;
	}
	
	/**
	 * Get the parties not involved in this contract
	 * 
	 * @param $contract_id the contract id
	 * @return  A list of rental_party objects
	 */
	public function get_available_parties_for_contract($contract_id)
	{
		$sql = "SELECT DISTINCT party_id FROM rental_contract_party WHERE contract_id != $contract_id";
		$this->db->query($sql);
		$parties = array();
		$parties_so = rental_party::get_so();
		while($this->db->next_record()) { 
			$party_id = $this->unmarshal($this->db->f('party_id', true), 'int'); 
			$parties[] = $parties_so->get_single($party_id);
		}
		return $parties;
	}
	
	/**
	 * Update the database values for an existing contract object.
	 * 
	 * @param $contract the contract to be updated
	 * @return result receipt from the db operation
	 */
	function update(rental_contract $contract)
	{
		$id = intval($contract->get_id());
		
		// TODO: Not all of these are mandatory, so include checks (@see add())
		// Build a db-friendly array of the contract object
		$values = array(
			"billing_start = " . $this->marshal(date('Y-m-d', $contract->get_billing_start_date()), 'date'),
			"type_id = " . $this->marshal($contract->get_type_id(), 'int'),
			"term_id = " . $this->marshal($contract->get_term_id(), 'int'),
			"account = " . $this->marshal($contract->get_account(), 'string')
		);
		
		if ($contract->get_contract_date()) {
			$values[] = "date_start = " . $this->marshal(date('Y-m-d', $contract->get_contract_date()->get_start_date()), 'date');
			$values[] = "date_end = " . $this->marshal(date('Y-m-d', $contract->get_contract_date()->get_end_date()), 'date');
		}

		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		return $receipt;
	}
	
	/**
	 * Add a new contract to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $contract the contract to be added
	 * @return array result receipt from the db operation
	 */
	function add(rental_contract &$contract)
	{
		// These are the columns we know we have or that are nullable
		$cols = array('type_id', 'term_id');
		
		// Start making a db-formatted list of values of the columns we have to have
		$values = array(
			$this->marshal($contract->get_type_id(), 'int'),
			$this->marshal($contract->get_term_id(), 'int')
		);
		
		// Check values that can be null before trying to add them to the db-pretty list
		if ($contract->get_account()) {
			$cols[] = 'account';
			$values[] = "'" . $this->marshal($contract->get_account(), 'string') . "'";
		}
		
		if ($contract->get_billing_start_date()) {
			$cols[] = 'billing_start';
			$values[] = $this->marshal(date('Y-m-d', $contract->get_billing_start_date()), 'date');
		}
		
		if ($contract->get_contract_date()) {
			$cols[] = 'date_start';
			$cols[] = 'date_end';
			$values[] = $this->marshal(date('Y-m-d', $contract->get_contract_date()->get_start_date()), 'date');
			$values[] = $this->marshal(date('Y-m-d', $contract->get_contract_date()->get_end_date()), 'date');
		}
		
		// Insert the new contract
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$contract->set_id($receipt['id']);
		
		return $receipt;
	}
	
	function add_party($contract_id, $party_id)
	{
		$q = "INSERT INTO rental_contract_party (contract_id, party_id) VALUES ($contract_id, $party_id)";
		$result = $this->db->query($q);
	}
	
	function remove_party($contract_id, $party_id)
	{
		$q = "DELETE FROM rental_contract_party WHERE contract_id = $contract_id AND party_id = $party_id";
		$result = $this->db->query($q);
	}
	
	function add_composite($contract_id, $composite_id)
	{
		$q = "INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES ($contract_id, $composite_id)";
		$result = $this->db->query($q);
	}
	
	function remove_composite($contract_id, $composite_id)
	{
		$q = "DELETE FROM rental_contract_composite WHERE contract_id = $contract_id AND composite_id = $composite_id";
		$result = $this->db->query($q);
	}
	
	function set_payer($contract_id, $party_id)
	{
		$pid =$this->marshal($party_id, 'int');
		$cid = $this->marshal($contract_id, 'int'); 
		$q = "UPDATE rental_contract_party SET is_payer = true WHERE party_id = ".$pid." AND contract_id = ".$cid;
		$result = $this->db->query($q);
		$q = "UPDATE rental_contract_party SET is_payer = false WHERE party_id != ".$pid." AND contract_id = ".$cid;
		$result = $this->db->query($q);
				
		
	}
	
}
?>
