<?php

phpgw::import_class('activitycalendar.socommon');


class activitycalendar_soorganization extends activitycalendar_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('activitycalendar.soorganization');
		}
		return self::$so;
	}

	/**
	 * Generate SQL query
	 *
	 * @todo Add support for filter "party_type", meaning what type of contracts
	 * the party is involved in.
	 *
	 * @param string $sort_field
	 * @param boolean $ascending
	 * @param string $search_for
	 * @param string $search_type
	 * @param array $filters
	 * @param boolean $return_count
	 * @return string SQL
	 */
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');

		//Add columns to this array to include them in the query
		$columns = array();

		if($sort_field != null) {
			$dir = $ascending ? 'ASC' : 'DESC';
			/*if($sort_field == 'name')
			{
				$order = "ORDER BY organization.last_name {$dir}, party.first_name {$dir}";
			}
			else
			{
				if($sort_field == 'address')
				{
					$sort_field = 'party.address_1';
				}*/
				$order = "ORDER BY {$this->marshal($sort_field,'field')} $dir";
			//}
		}
/*		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "name":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					break;
				case "address":
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
					$like_clauses[] = "party.address_2 $this->like $like_pattern";
					$like_clauses[] = "party.postal_code $this->like $like_pattern";
					$like_clauses[] = "party.place $this->like $like_pattern";
					break;
				case "identifier":
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					break;
				case "reskontro":
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
				case "result_unit_number":
					$like_clauses[] = "party.result_unit_number $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
					$like_clauses[] = "party.address_2 $this->like $like_pattern";
					$like_clauses[] = "party.postal_code $this->like $like_pattern";
					$like_clauses[] = "party.place $this->like $like_pattern";
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					$like_clauses[] = "party.comment $this->like $like_pattern";
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}*/

		$filter_clauses = array();
		$filter_clauses[] = "show_in_portal";
/*
		// All parties with contracts of type X
		if(isset($filters['party_type']))
		{
			$party_type = $this->marshal($filters['party_type'],'int');
			if(isset($party_type) && $party_type > 0)
			{
				$filter_clauses[] = "contract.location_id = {$party_type}";
			}
		}
*/		
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(org.id)) AS count';
		}
		else
		{
			$columns[] = 'org.id AS org_id';
			$columns[] = 'org.name';
			$columns[] = 'org.homepage';
			$columns[] = 'org.phone';
			$columns[] = 'org.email';
			$columns[] = 'org.description';
			$columns[] = 'org.active';
			$columns[] = 'org.street';
			$columns[] = 'org.zip_code';
			$columns[] = 'org.city';
			$columns[] = 'org.district';
			$columns[] = 'org.organization_number';
			$columns[] = 'org.activity_id';
			$columns[] = 'org.customer_number';
			$columns[] = 'org.customer_identifier_type';
			$columns[] = 'org.customer_organization_number';
			$columns[] = 'org.customer_ssn';
			$columns[] = 'org.customer_internal';
			$columns[] = 'org.shortname';
			$columns[] = 'org.show_in_portal';
			
			$cols = implode(',',$columns);
		}

		$tables = "bb_organization org";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";

		$joins = $join_contracts;
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$party)
	{
		// Insert a new party
		$q ="INSERT INTO rental_party (is_inactive) VALUES (false)";
		$result = $this->db->query($q);

		if(isset($result)) {
			// Set the new party ID
			$party->set_id($this->db->get_last_insert_id('rental_party', 'id'));
			// Forward this request to the update method
			return $this->update($party);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($party)
	{
		$id = intval($party->get_id());
		
		
		$location_id = $this->marshal($party->get_location_id(), 'int');
		
		if($location_id)
		{
			$loc = $GLOBALS['phpgw']->locations->get_name($location_id);
			$name = $loc['location'];
			$level_identifier = result_unit::get_identifier_from_name($name);
		}
		
		$result_unit_number = $this->marshal($level_identifier, 'string');
		
		$values = array(
			'identifier = '		. $this->marshal($party->get_identifier(), 'string'),
			'first_name = '     . $this->marshal($party->get_first_name(), 'string'),
			'last_name =  '     . $this->marshal($party->get_last_name(), 'string'),
			'title = '          . $this->marshal($party->get_title(), 'string'),
			'company_name = '   . $this->marshal($party->get_company_name(), 'string'),
			'department = '     . $this->marshal($party->get_department(), 'string'),
			'address_1 = '      . $this->marshal($party->get_address_1(), 'string'),
			'address_2 = '      . $this->marshal($party->get_address_2(), 'string'),
			'postal_code = '    . $this->marshal($party->get_postal_code(), 'string'),
			'place = '          . $this->marshal($party->get_place(), 'string'),
			'phone = '          . $this->marshal($party->get_phone(), 'string'),
			'mobile_phone = '	. $this->marshal($party->get_mobile_phone(), 'string'),
			'fax = '            . $this->marshal($party->get_fax(), 'string'),
			'email = '          . $this->marshal($party->get_email(), 'string'),
			'url = '            . $this->marshal($party->get_url(), 'string'),
			'account_number = ' . $this->marshal($party->get_account_number(), 'string'),
			'reskontro = '      . $this->marshal($party->get_reskontro(), 'string'),
			'is_inactive = '    . $this->marshal(($party->is_inactive() ? 'true' : 'false'), 'bool'),
			'comment = '        . $this->marshal($party->get_comment(), 'string'),
			'org_enhet_id = '	. $this->marshal($party->get_org_enhet_id(), 'int'),
			'location_id = '	. $location_id,
			'result_unit_number = ' . $result_unit_number
		);
		
		$result = $this->db->query('UPDATE rental_party SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
			
		return isset($result);
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'party_id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'party', // alias
				'field'			=> 'id',
				'translated'	=> 'party_id'
			);
		}
		return $ret;
	}

	protected function populate(int $party_id, &$party)
	{

		if($party == null) {
			$party = new rental_party((int) $party_id);

			$party->set_account_number( $this->unmarshal($this->db->f('account_number'), 'string'));
			$party->set_address_1(      $this->unmarshal($this->db->f('address_1'), 'string'));
			$party->set_address_2(      $this->unmarshal($this->db->f('address_2'), 'string'));
			$party->set_comment(        $this->unmarshal($this->db->f('comment'), 'string'));
			$party->set_company_name(   $this->unmarshal($this->db->f('company_name'), 'string'));
			$party->set_department(     $this->unmarshal($this->db->f('department'), 'string'));
			$party->set_email(          $this->unmarshal($this->db->f('email'), 'string'));
			$party->set_fax(            $this->unmarshal($this->db->f('fax'), 'string'));
			$party->set_first_name(     $this->unmarshal($this->db->f('first_name'), 'string'));
			$party->set_is_inactive(    $this->unmarshal($this->db->f('is_inactive'), 'bool'));
			$party->set_last_name(      $this->unmarshal($this->db->f('last_name'), 'string'));
			$party->set_location_id(    $this->unmarshal($this->db->f('org_location_id'), 'int'));
			$party->set_identifier(		$this->unmarshal($this->db->f('identifier'), 'string'));
			$party->set_mobile_phone(	$this->unmarshal($this->db->f('mobile_phone'), 'string'));
			$party->set_place(          $this->unmarshal($this->db->f('place'), 'string'));
			$party->set_postal_code(    $this->unmarshal($this->db->f('postal_code'), 'string'));
			$party->set_reskontro(      $this->unmarshal($this->db->f('reskontro'), 'string'));
			$party->set_title(          $this->unmarshal($this->db->f('title'), 'string'));
			$party->set_url(            $this->unmarshal($this->db->f('url'), 'string'));
			$party->set_org_enhet_id(   $this->unmarshal($this->db->f('org_enhet_id'), 'string'));
			$sync_message = $party->set_sync_data(
				array(
					'responsibility_id' => $this->unmarshal($this->db->f('responsibility_id'), 'string'),
					'org_enhet_id' => $this->unmarshal($this->db->f('org_enhet_id'), 'string'),
					'result_unit_number' => $this->unmarshal($this->db->f('result_unit_number'), 'string'),
				)
			);
			if(isset($sync_message) && $sync_message != '')
			{
				$party->add_sync_problem($sync_message);
			}
		}
		return $party;
	}
	
	public function get_export_data()
	{
		$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, null);
		$exportable = new rental_agresso_cs15($parties);
		return $exportable->get_contents();
	}
	
	public function get_number_of_parties()
	{
		$q ="SELECT COUNT(id) FROM rental_party";
		$result = $this->db->query($q);
		$this->db->query($q, __LINE__, __FILE__);
		$this->db->next_record();
		return (int) $this->db->f('count',true);
	}
	
}
?>
