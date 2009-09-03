<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_bocompleted_reservation extends booking_bocommon
	{
		protected static $customer_field_prefix = 'payee_';
		
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
			//payee_organization_number or payee_ssn
			
			if ($entity['reservation_type'] == 'event') {
				return array('ssn', 'organization_number');
			} else {
				if ($entity['payee_type'] == 'organization') {
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
		
		protected function build_default_read_params()
		{
			$params = parent::build_default_read_params();
			if ($filter_to = phpgw::get_var('filter_to', 'string', 'GET', null)) {
				$params['where'] = sprintf("to_ <= '%s 23:59:59'", $GLOBALS['phpgw']->db->db_addslashes($filter_to));
			}
			
			return $params;
		}
	}