<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socompleted_reservation extends booking_socommon
	{
		const CUSTOMER_TYPE_EXTERNAL='external';
		const CUSTOMER_TYPE_INTERNAL='internal';
		
		protected static $customerTypes = array(
			self::CUSTOMER_TYPE_EXTERNAL,
			self::CUSTOMER_TYPE_INTERNAL
		);
		
		protected 
			$customer_id,
			$resource_so,
			$season_so;
		
		function __construct()
		{
			$this->season_so = CreateObject('booking.soseason');
			$this->resource_so = CreateObject('booking.soresource');
			$this->customer_id = CreateObject('booking.customer_identifier');
			
			parent::__construct('bb_completed_reservation', 
				array(
					'id' 						=> array('type' => 'int'),
					'reservation_type' 	=> array('type' => 'string', 'required' => True, 'nullable' => False),
					'reservation_id' 		=> array('type' => 'int', 'required' => True, 'nullable' => False),
					'season_id' 			=> array('type' => 'int'),
					'cost'					=> array('type' => 'decimal',   'required' => true),
					'from_'					=> array('type' => 'timestamp', 'required'=> true),
					'to_'						=> array('type' => 'timestamp', 'required'=> true),
					'organization_id'    => array('type' => 'int'),
					'customer_type' 			=> array('type' => 'string', 'nullable' => False),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_organization_number' => array('type' => 'string', 'precision' => '9', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'customer_ssn' 			=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN')), 
					'exported' 				=> array('type' => 'int'),
					'description'			=> array('type' => 'string', 'required' => True, 'nullable' => False),
					'article_description' => array('type' => 'string', 'required' => True, 'nullable' => False, 'precision' => 35),
					'building_id'			=> array('type' => 'string', 'required' => True),
					'building_name'		=> array('type' => 'string', 'required' => True),
					'season_name'	=> array('type' => 'string', 'query' => true,
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'organization_name'	=> array('type' => 'string', 'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'resources' => array('type' => 'int', 'required' => True,
						  'manytomany' => array(
							'table' => 'bb_completed_reservation_resource',
							'key' => 'completed_reservation_id',
							'column' => 'resource_id'
					)),
				)
			);
		}
		
		/**
		 * Implement in subclasses to perform custom validation.
		 */
		protected function doValidate($entity, booking_errorstack $errors)
		{
			if (!in_array($entity['customer_type'], $this->get_customer_types())) {
				$errors['customer_type'] = 'Invalid customer type';
			}
		}
		
		public function get_customer_types()
		{
			return self::$customerTypes;
		}
		
		function _get_conditions($query, $filters)
		{	
			//Removes season_name from filters if the season_id is already included in the filters
			if (isset($filters['season_name']) AND isset($filters['season_id'])) {
				unset($filters['season_name']);
			}
			
			//Removes building_name from filters if the building_id is already included in the filters
			if (isset($filters['building_name']) AND isset($filters['building_id'])) {
				unset($filters['building_name']);
			}
			
			$where_clauses = (isset($filters['where']) ? (array)$filters['where'] : array());
			
			if (isset($filters['season_id'])) {
				
				$season_id = $this->marshal_field_value('season_id', $filters['season_id']);
				unset($filters['season_id']);
				
				$where_clauses[] =  
					"(%%table%%.season_id = $season_id OR (%%table%%.reservation_type = 'event' AND %%table%%.season_id IS NULL) ".
					"AND %%table%%.reservation_type != 'event' OR ".
					"(%%table%%.reservation_type = 'event' AND %%table%%.reservation_id IN ".
						"(SELECT event_id FROM bb_event_resource er JOIN bb_season_resource sr ON er.resource_id = sr.resource_id AND sr.season_id = $season_id) ".
					"))";
			}
			
			if (count($where_clauses) > O) {
				$filters['where'][] = join($where_clauses, ' AND ');
			}
			
			return parent::_get_conditions($query, $filters);
		}
		
		public function create_from($type, $reservation) {
			$entity = array(
				'reservation_type' 	=> $type, 
				'reservation_id' 		=> $reservation['id'],
				'cost' 					=> $reservation['cost'],
				'from_' 					=> $reservation['from_'],
				'to_' 					=> $reservation['to_'],
				'customer_type' 		=> 'external',
				'resources' 			=> $reservation['resources'],
				'season_id'				=> isset($reservation['season_id']) ? $reservation['season_id'] : null,
			);
			
			//echo "$type\n";
			//print_r($reservation);
			
			$method = "initialize_completed_{$type}";
			$this->$method($reservation, $entity);
			$this->set_description($type, $reservation, $entity);
			$this->add($entity);
		}
		
		private function copy_customer_identifier(array $from, array &$to) {
			$this->customer_id->copy_between($from, $to);
		}
		
		protected function set_description($type, &$reservation, &$entity) {
			$building = $this->get_building($type, $reservation);
			$entity['article_description'] = $building['name'] . ': ' . implode(', ', $this->get_resource_names($reservation['resources']));
			
			if (mb_strlen($entity['article_description']) > 35) {
				$entity['article_description'] = mb_substr($entity['article_description'], 0, 32).'...'; 
			}
			
			$entity['description'] = mb_substr($entity['from_'], 0, -3) .' - '. mb_substr($entity['to_'], 0, -3);
			$entity['building_name'] = $building['name'];
			$entity['building_id'] = $building['id'];
		}
		
		public function get_building($type, &$reservation) {
			switch ($type) {
				case 'booking':
				case 'allocation':
					return $this->get_building_for_season($reservation['season_id']);
				case 'event':
					return count($reservation['resources']) > 0 ? $this->get_building_for_resource($reservation['resources'][0]) : '';
			}
			
			return '';
		}
		
		protected function get_building_for_season($season_id) {
			static $cache = array();
			if (!isset($cache[$season_id])) {
				$season = $this->season_so->read_single($season_id);
				$cache[$season_id] = array('id' => $season['building_id'], 'name' => $season['building_name']);
			}
			
			return $cache[$season_id];
		}
		
		protected function get_building_for_resource($resource_id) {
			static $cache = array();
			if (!isset($cache[$resource_id])) {
				$resource = $this->resource_so->read_single($resource_id);
				$cache[$resource_id] = array('id' => $resource['building_id'], 'name' => $resource['building_name']);
			}
			
			return $cache[$resource_id];
		}
		
		public function get_resource_names($resources) {
			static $cache = array();
			
			$names = array();
			$uncached = array();
			
			foreach ($resources as $id) {
				if ($name = $this->get_cached_resource_name($id, $cache)) {
					$names[$id] = $name;
				} else {
					$uncached[] = $id;
				}
			}
			
			if (count($uncached) > 0) {
				$found_resources = $this->resource_so->read(array(
					'filters' => array('id' => $uncached),
					'results' => count($uncached),
				));
				
				if (is_array($found_resources) && isset($found_resources['results']) && is_array($found_resources['results'])) {
					//Add to returned names and insert into name cache
					foreach ($found_resources['results'] as $resource) {
						$names[$resource['id']] = $cache[$resource['id']] = $resource['name'];
					}
				}
			}
			
			return $names;
		}
		
		protected function get_cached_resource_name($resource_id, &$cache) {
			return isset($cache[$resource_id]) ? $cache[$resource_id] : null;
		}
		
		protected function set_organization(&$entity, &$organization) {
			$entity['organization_id'] = $organization['id'];
			$entity['customer_organization_number'] = $organization['organization_number'];
		}
		
		protected function initialize_completed_booking(&$booking, &$entity) {
			static $sogroup, $soorg;
			static $cache = array();
			
			!$sogroup AND $sogroup = CreateObject('booking.sogroup');
			!$soorg AND $soorg = CreateObject('booking.soorganization');
			
			if (isset($cache[$booking['group_id']])) {
				$org = $cache[$booking['group_id']];
			} else {
				$group = $sogroup->read_single($booking['group_id']);
				$org = $soorg->read_single($group['organization_id']);
				$cache[$booking['group_id']] = $org;
			}

			$this->set_organization($entity, $org);
			$this->copy_customer_identifier($org, $entity);
		}
		
		protected function initialize_completed_allocation(&$allocation, &$entity) {
			static $soorg;
			static $cache = array();
			
			!$soorg AND $soorg = CreateObject('booking.soorganization');
			if (isset($cache[$allocation['organization_id']])) {
				$org = $cache[$allocation['organization_id']];
			} else {
				$org = $soorg->read_single($allocation['organization_id']);
				$cache[$allocation['organization_id']] = $org;
			}
			
			$this->set_organization($entity, $org);
			$this->copy_customer_identifier($org, $entity);
		}
		
		protected function initialize_completed_event(&$event, &$entity) {
			$this->copy_customer_identifier($event, $entity);
		}
		
		public function update_exported_state_of(&$reservations, $with_export_id) {
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $reservations));
			$sql = "UPDATE $table_name SET exported = $with_export_id WHERE {$table_name}.id IN ($ids);";
			return $db->query($sql, __LINE__, __FILE__);
		}
	}