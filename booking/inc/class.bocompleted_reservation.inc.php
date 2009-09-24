<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('booking.bocompleted_reservation_export');
	
	class booking_bocompleted_reservation extends booking_bocommon
	{	
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.socompleted_reservation');
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
	}