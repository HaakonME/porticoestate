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
		protected $repeat_type;
		protected $repeat_interval;
		protected $procedure_id;
		protected $enabled;
		protected $requirement_id;
		protected $costresponsibility_id;
		protected $responsibility_id;
		protected $equipment_id;
		protected $equipment_type_id;
		protected $location_code;
		protected $control_area_id;

		/*
		var $validate = array(
	    	'title' => array(
	       		'rule' => array('minLength', 0),
	   			'message' => 'Kontrollen må ha en tittel'
	   		));
		*/
		
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
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id() { return $this->procedure_id; }
		
		public function set_enabled($enabled)
		{
			$this->enabled = $enabled;
		}
		
		public function get_enabled() { return $this->enabled; }
		
		public function set_requirement_id($requirement_id)
		{
			$this->requirement_id = $requirement_id;
		}
		
		public function get_requirement_id() { return $this->requirement_id; }
		
		public function set_costresponsibility_id($costresponsibility_id)
		{
			$this->costresponsibility_id = $costresponsibility_id;
		}
		
		public function get_costresponsibility_id() { return $this->costresponsibility_id; }
		
		public function set_responsibility_id($responsibility_id)
		{
			$this->responsibility_id = $responsibility_id;
		}
		
		public function get_responsibility_id() { return $this->responsibility_id; }
		
		public function set_equipment_id($equipment_id)
		{
			$this->equipment_id = $equipment_id;
		}
		
		public function get_equipment_id() { return $this->equipment_id; }
		
		public function set_equipment_type_id($equipment_type_id)
		{
			$this->equipment_type_id = $equipment_type_id;
		}
		
		public function get_equipment_type_id() { return $this->equipment_type_id; }
		
		public function set_location_code($location_code)
		{
			$this->location_code = $location_code;
		}
		
		public function get_location_code() { return $this->location_code; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id() { return $this->control_area_id; }
		
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
		
		public function populate()
		{
				$this->set_title(phpgw::get_var('title','string'));
				$this->set_description(phpgw::get_var('description','html'));
				$this->set_start_date(strtotime( phpgw::get_var('start_date_hidden','string') ));
				$this->set_end_date(strtotime( phpgw::get_var('end_date_hidden','string') ));
				$this->set_procedure_id(phpgw::get_var('procedure_id','int'));
				$this->set_control_area_id(phpgw::get_var('control_area_id','int'));
		}
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'description' => $this->get_description(),
				'start_date' => $this->get_start_date(),
				'end_date' => $this->get_end_date(),
				'procedure_id' => $this->get_procedure_id(),
				'control_area_id' => $this->get_control_area_id()
				);
		}
	}
?>