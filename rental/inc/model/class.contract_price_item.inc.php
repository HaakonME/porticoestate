<?php
	/**
	 * Class that represents a price item in the price list
	 *
	 */
	
	phpgw::import_class('rental.bocommon');
	include_class('rental', 'price_item', 'inc/model/');
	
	class rental_contract_price_item extends rental_price_item
	{
		public static $so;
		
		protected $price_item_id;
		protected $contract_id;
		protected $area;
		protected $count;
		protected $total_price;
		protected $date_start;
		protected $date_end;
		
		/**
		 * Constructor.  Takes an optional ID.  If a price item is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this price item
		 */
		public function __construct($id)
		{
			parent::__construct($id);
			/*
			if ($id) {
				parent::__construct($price_item->get_id());
				$this->set_title($price_item->get_title());
				$this->set_agresso_id($price_item->get_agresso_id());
				$this->set_is_area($price_item->is_area());
				$this->set_price($price_item->get_price);
			} else {
				parent::__construct();
			}
			*/
		}
		
		public function get_price_item_id()
		{
			return $this->price_item_id;
		}
		
		public function set_price_item_id($id)
		{
			$this->price_item_id = $id;
		}
		
		public function get_contract_id()
		{
			return $this->contract_id;
		}
		
		public function set_contract_id($contract_id)
		{
			$this->contract_id = $contract_id;
		}
		
		public function get_area()
		{
			if (!$this->area)
				$this->area = 0;
				
			return $this->area;
		}
		
		public function set_area($area)
		{
			$this->area = $area;
		}
		
		public function get_count()
		{
			if (!$this->count)
				$this->count = 0;
				
			return $this->count;
		}
		
		public function set_count($count)
		{
			$this->count = $count;
		}
		
		public function get_total_price()
		{
			if (!$this->total_price)
				$this->total_price = 0;
			return $this->total_price;
		}
		
		public function set_total_price($total_price)
		{
			$this->total_price = $total_price;
		}
		
		public function get_date_start()
		{
			return $this->date_start;
		}
		
		public function set_date_start($date_start)
		{
			$this->date_start = $date_start;
		}
		
		public function get_date_end()
		{
			return $this->date_end;
		}
		
		public function set_date_end($date_end)
		{
			$this->date_end = $date_end;
		}
		
		/**
		 * Reset this contract price item to its original values from the price list
		 */
		public function reset()
		{
			$so = self::get_so();
			
			$original = $so->get_single($this->get_price_item_id());
			$this->set_agresso_id($original->get_agresso_id());
			$this->set_title($original->get_title());
			$this->set_price($original->get_price());
			
			$so->update_contract_price_item($this);
		}
		
		/**
		 * Convert this object to a hash representation
		 * 
		 * @see rental/inc/model/rental_model#serialize()
		 */
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'price_item_id' => $this->get_price_item_id(),
				'contract_id' => $this->get_contract_id(),
				'area' => $this->get_area(),
				'count' => $this->get_count(),
				'total_price' => $this->get_total_price(),
				'title' => $this->get_title(),
				'agresso_id' => $this->get_agresso_id(),
				'is_area' => $this->get_type_text(),
				'price' => $this->get_price()
			);
		}
		
		/**
		 * Return a single rental_price_item object based on the provided id
		 * 
		 * @param $id rental price item id
		 * @return rental_price_item
		 */
		public static function get($id)
		{
			$so = self::get_so();
			
			return $so->get_single_contract_price_item($id);
		}
	}