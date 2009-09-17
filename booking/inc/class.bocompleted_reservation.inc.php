<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('booking.bocompleted_reservation_export');
	
	class booking_bocompleted_reservation extends booking_bocommon
	{
		protected static $customer_field_prefix = 'customer_';
		
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.socompleted_reservation');
		}
		
		/**
		 * Returns the 'precedence rules' for a completed reservation's customer identifier number
		 *
		 * @param array $entity
		 * @return array with identifier types ordered by ascending precedence/priority
		 */
		public function get_customer_identifier_precedence(&$entity) {
			//customer_organization_number or customer_ssn
			
			if ($entity['reservation_type'] == 'event') {
				return array('ssn', 'organization_number');
			} else {
				if ($entity['customer_type'] == 'organization') {
					return array('organization_number', 'ssn');
				} else {
					return array('ssn', 'organization_number');
				}
			}
		}
		
		/**
		 * Returns the primary customer identifier type for the $entity
		 *
		 * @param array $entity
		 * @return string identifier type
		 */
		public function get_primary_customer_identifier_type(&$entity) {
			$prule = $this->get_customer_identifier_precedence($entity);
			return $prule[0];
		}
		
		/**
		 * Returns the active primary customer identifier type for the $entity
		 *
		 * @param array $entity
		 * @return string identifier type
		 */
		public function get_active_customer_identifier_type(&$entity) {
			$prule = $this->get_customer_identifier_precedence($entity);
			foreach($prule as $identifier_type) {
				if (isset($entity[self::$customer_field_prefix.$identifier_type])) {
					$identifier_value = trim($entity[self::$customer_field_prefix.$identifier_type]);
					if (!empty($identifier_value)) return $identifier_type;
				}
			}
			
			return null;
		}
		
		public function get_active_customer_identifier(&$entity) {
			if (!($active_identifier_type = $this->get_active_customer_identifier_type($entity))) { 
				return array('N/A' => null);
			}
			return array($active_identifier_type => $entity[self::$customer_field_prefix.$active_identifier_type]);
		}
		
		public function unset_show_all_completed_reservations()
		{
			unset($_SESSION['show_all_completed_reservations']);
		}
		
		public function show_all_completed_reservations() 
		{
			$_SESSION['show_all_completed_reservations'] = "1";
		}
		
		protected function build_default_read_params()
		{
			$params = parent::build_default_read_params();
			
			$where_clauses = array();
			
			//build_default_read_params will not automatically build a filter for the to_ field 
			//because it cannot match the name 'filter_to' to an existing field once the prefix 
			//'filter' is removed nor do we want it to, so we build that filter manually here:
			if ($filter_to = phpgw::get_var('filter_to', 'string', array('GET', 'POST'), null)) {
				$where_clauses[] = "%%table%%".sprintf(".to_ <= '%s 23:59:59'", $GLOBALS['phpgw']->db->db_addslashes($filter_to));
			}
			
			if(!isset($_SESSION['show_all_completed_reservations'])) {
				$params['filters']['exported'] = null;
			}
			
			if (count($where_clauses) > O) {
				$params['filters']['where'] = $where_clauses;
			}
			
			return $params;
		}
		
		function read_single($id)
		{
			$entity = parent::read_single($id);
			$active_identifier = $this->get_active_customer_identifier($entity);

			if (current($active_identifier)) {
				$entity['customer_identifier_type'] = key($active_identifier);
				$entity['customer_identifier'] = current($active_identifier);
			}
			
			return $entity;
		}
	}