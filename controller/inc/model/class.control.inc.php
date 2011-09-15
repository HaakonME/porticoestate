<?php
	include_class('controller', 'model', 'inc/model/');

	class controller_control extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $description;
		protected $start_date;
		protected $end_date;
		protected $repeat_day;
		protected $repeat_type;
		protected $repeat_interval;
		protected $enabled;
				
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_title() { return $this->title; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}
		
		public function get_end_date() { return $this->end_date; }
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}
		
		public function get_start_date() { return $this->start_date; }
		
		public function set_repeat_day($repeat_day)
		{
			$this->repeat_day = $repeat_day;
		}
		
		public function get_repeat_day() { return $this->repeat_day; }
		
		public function set_repeat_type($repeat_type)
		{
			$this->repeat_type = $repeat_type;
		}
					
		public function get_repeat_type() { return $this->repeat_type; }
		
		public function set_repeat_interval($repeat_interval)
		{
			$this->repeat_interval = $repeat_interval;
		}
		
		public function get_repeat_interval() { return $this->repeat_interval; }
		
		public function set_enabled($enabled)
		{
			$this->enabled = $enabled;
		}
		
		public function get_enabled() { return $this->enabled; }
				
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol');
			}
			
			return self::$so;
		}
	}
?>