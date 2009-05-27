<?php
phpgw::import_class('rental.socommon');

class rental_sorentalcomposites extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_composite',
		array
		(
					'composite_id'	=> array('type' => 'int'),
					'description' => array('type' => 'string'),
					'is_active' => array('type', 'bool'),
 					'name'	=> array('type' => 'string'),
					'has_custom_address' => array('type' => 'bool'),
					'address_1'	=> array('type' => 'string'),
					'address_2'	=> array('type' => 'string'),
					'house_number' => array('type' => 'string'),
					'postcode' => array('type' => 'string'),
					'place' => array('type' => 'string'),
					'adresse1' => array('type' => 'string'),
					'adresse2' => array('type' => 'string'),
					'postnummer' => array('type' => 'int'),
					'poststed' => array('type' => 'string'),
					'gab_id' => array('type' => 'string')
		));
	}
	
	function _get_conditions($query, $filters,$search_option)
		{	
			$clauses = array('1=1');
			if($query)
			{
				
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				switch($search_option){
					case "id":
						$like_clauses[] = "rental_composite.composite_id = $query";
						break;
					case "name":
						$like_clauses[] = "rental_composite.name $this->like $like_pattern";
						break;
					case "address":
						$like_clauses[] = "fm_location1.adresse1 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
						break;
					case "gab":
						$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
						break;
					case "all":
						$like_clauses[] = "rental_composite.name $this->like $like_pattern";
						$like_clauses[] = "fm_location1.adresse1 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
						$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
						break;
				}
				
				
				
				
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
				
				
			}
			
			$filter_clauses = array();
			switch($filters['is_active']){
				case "active":
					$filter_clauses[] = "rental_composite.is_active = TRUE";
					break;
				case "non_active":
					$filter_clauses[] = "rental_composite.is_active = FALSE";
					break;
				case "both":
					break;
			}
				
			if(count($filter_clauses))
				{
					$clauses[] = join(' AND ', $filter_clauses);
				}
			
			return join(' AND ', $clauses);
		}

	/**
	 * We override the parent method to hook in more specialized queries for
	 * this part of the system. (The DISTINCT JOIN and FROM handling in the common class
	 * isn't as advanced as needed here.)
	 * 
	 * Return all entries matching $params. Valid parameters:
	 *
	 * - $params['start']: Search result offset
	 * - $params['results']: Number of results to return
	 * - $params['sort']: Field to sort by
	 * - $params['query']: LIKE-based query string
	 * - $params['filters']: Array of custom filters
	 *
	 * @return array('total_records'=>X, 'results'=array(...))
	 */
	function read($params)
	{
		$start = isset($params['start']) && $params['start'] ? $params['start'] : 0;
		$results = isset($params['results']) && $params['results'] ? $data['results'] : 1000;
		$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
		$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : '';
		$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
		$search_option = isset($params['search_option']) && $params['search_option'] ? $params['search_option'] : null;
		$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

		$condition = $this->_get_conditions($query, $filters,$search_option);
		
		$tables = "rental_composite";
		$joins = 'JOIN rental_unit ON (rental_composite.composite_id = rental_unit.composite_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1)';
		$distinct = 'distinct on(rental_composite.composite_id)';
		$cols = 'rental_composite.composite_id, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, fm_location1.adresse1, fm_gab_location.gab_id';
		
		// Calculate total number of records
		$this->db->query("SELECT COUNT(distinct rental_composite.composite_id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');

		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		// We interpret 'Eiendomsnavn' as the name of the composite object and not loc1_name or loc2_name. TODO: Is this okay?
		// TODO: Should we ask for and let the address field on fm_location2 override the address found fm_location1? Do we know that the nothing higher than level 2 locations are rented? (The same question goes for the name of the location if we are to use it.)
		// XXX: The address ordering doesn't take custom addresses in consideration.
		
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $tables $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		}
		
		$results = array();
		
		while ($this->db->next_record())
		{
			$row = array();
			foreach($this->fields as $field => $fparams)
			{
                    $row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
			}
			if($row['has_custom_address'] == '1') // There's a custom address
			{
				$row['adresse1'] = $row['address_1'].' '.$row['house_number'];
			}
			$row['gab_id'] = substr($row['gab_id'],4,5).' / '.substr($row['gab_id'],9,4);
			$results[] = $row;
		}
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
	
	/*
	 * Get single rental composite record by the given composite_id
	 */
	function read_single($id)
	{
		$distinct = 'distinct on(rental_composite.composite_id)';
		$cols = 'rental_composite.composite_id, rental_composite.name, rental_composite.description, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, rental_composite.is_active, rental_composite.postcode, rental_composite.place, fm_location1.adresse1, fm_location1.adresse2, fm_location1.postnummer, fm_location1.poststed, fm_gab_location.gab_id';
		$joins = 'JOIN rental_unit ON (rental_composite.composite_id = rental_unit.composite_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1)';
		
		$this->db->query("SELECT $cols FROM {$this->table_name} $joins WHERE rental_composite.composite_id=$id", __LINE__, __FILE__);
		
		$row = array();
		
		while ($this->db->next_record())
		{
			foreach($this->fields as $field => $fparams)
			{
     		$row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
			}
			/*
			if($row['has_custom_address'] == '1') // There's a custom address
			{
				$row['adresse1'] = $row['address_1'].' '.$row['house_number'];
				$row['adresse2'] = $row['address_2'];
				$row['postnummer'] = $row['postcode'];
				$row['poststed'] = $row['place'];
			}
			*/
			$row['gab_id'] = substr($row['gab_id'],4,5).' / '.substr($row['gab_id'],9,4);
		}
		
		$row['units'] = array();
		
		// Get all rental units belonging to this composite object
		$this->db->query("SELECT fm_locations.* FROM rental_unit JOIN fm_locations ON (rental_unit.location_id = fm_locations.id) WHERE composite_id = {$id}");
		
		$units = array();
		while ($this->db->next_record()) {
			$level = $this->_unmarshal($this->db->f('level', true), 'int');
			$location_code = $this->_unmarshal($this->db->f('location_code', true), 'string');
			$units[] = array('level' => $level, 'location_code' => $location_code);
		}
		
		$area = 0;
		
		// Go through each rental unit (location) that belongs to this composite and add up their areas
		foreach ($units as $unit) {
			$sql = '';
			$area_column = 'bta';
			
			// Properties doesn't have areas, so we check location level 2 to work out the areas of whole properties (level 1)
			if ($unit['level'] == 1) {
				$sql = "SELECT * FROM fm_location2 WHERE loc1 LIKE '{$unit['location_code']}'";
			} else {
				$sql = "SELECT * FROM fm_location{$unit['level']} WHERE location_code LIKE '{$unit['location_code']}'";
			}
			
			// On level 5 the area columns have different names
			if ($unit['level'] == 5) {
				$area_column = 'bruksareal';
			}
			
			$this->db->query($sql);
			while ($this->db->next_record()) {
				$area += $this->_unmarshal($this->db->f($area_column, true), 'float');
			}
		}
		
		$row['area'] = $area;
		
		return $row;
	}
	
	function update($entry)
	{
		$id = intval($entry['composite_id']);
		$cols = array();
		$values = array();
		$fields = array('composite_id', 'description', 'is_active', 'name', 'address_1', 'address_2', 'house_number', 'postcode', 'place', 'has_custom_address');
		
		foreach($fields as $field)
		{
			$params = $this->fields[$field];
			
			if($field == 'composite_id' || $params['join'] || $params['manytomany'])
			{
				continue;
			}
			$values[] = $field . "=" . $this->_marshal($entry[$field], $params['type']);
		}
		
		$cols = join(',', $cols);
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE composite_id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		return $receipt;
	}
}
?>