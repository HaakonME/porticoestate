<?php
	/**
	 * Class that represents a rental composite
	 *
	 */

	phpgw::import_class('rental.bocommon');
	
	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	
	class rental_composite extends rental_model
	{
		public static $so;
		
		protected $id;
		protected $name;
		protected $description;
		protected $is_active;
		protected $has_custom_address;
		// These are custom fields that may be set on the composite
		protected $custom_address_1;
		protected $custom_address_2;
		protected $custom_house_number;
		protected $custom_postcode;
		protected $custom_place;
		
		protected $units;
	
		/**
		 * Constructor.  Takes an optional ID.  If a composite is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = $id;
		}
		
		/**
		 * Return a single rental_composite object based on the provided id
		 * 
		 * @param $id rental composite id
		 * @return a rental_composite
		 */
		public static function get($id)
		{
			$so = self::get_so();
			
			return $so->get_single($id);
		}
		
		/**
		 * Return a list all of rental_composite objects that fits the provided arguments
		 * 
		 * @param $start		which index to start the list at
		 * @param $results	how many results to return
		 * @param $sort			sort column
		 * @param $dir			sort direction
		 * @param $query
		 * @param $search_option
		 * @param $filters
		 * @return a list of rental_composite objects
		 */
		public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$so = self::get_so();
			
			$composites = $so->get_composite_array($start, $results, $sort, $dir, $query, $search_option, $filters);
			
			return $composites;
		}
		
		/**
		 * Add a new rental composite object to the store.
		 * 
		 * @param $composite the new composite
		 * @return the status of the operation
		 */
		public static function add($composite)
		{
			$so = self::get_so();
			
			return $so->add($composite);
		}
		
		/**
		 * Adds a composite to the composite object. Note that this method is
		 * meant for populating the object and will not fetch anything from
		 * the database.
		 * @see add_new_unit().
		 * @param $unit to add to object.
		 */
		public function add_unit($new_unit)
		{
			if(!isset($this->units)) // No units are added yet
			{
				$this->units = array();
			}
			else // There are units
			{
				foreach ($this->units as $unit) {
					if ($unit->get_location_id() == $new_unit->get_location_id()) { // Unit already exists
						return;
					}
				}
			}
			// Unit doesn't already exist so we add it to the array
			$this->units[] = $new_unit;
		}
		
		/**
		 * Associate a rental unit to this composite.  Note that the composite is not updated
		 * in the database until store() is called.  This function checks for duplicates
		 * before adding the given unit. This function will fetch the belonging units from the
		 * db if necessary.
		 * 
		 * @param $new_unit the unit to associate
		 */
		public function add_new_unit($new_unit)
		{
			$units = $this->get_included_rental_units();
			
			$already_has_unit = false;
			
			foreach ($this->get_included_rental_units() as $unit) {
				if ($unit->get_location_id() == $new_unit->get_location_id()) {
					$already_has_unit == true;
				}
			}
			
			if (!$already_has_unit) {
				$this->units[] = $new_unit;
			}
		}
		
		/**
		 * Remove a given rental unit from this rental_composite. Note that the composite is not updated
		 * in the database until store() is called.
		 * 
		 * @param $unit_to_remove the rental_unit to remove
		 */
		public function remove_unit($unit_to_remove)
		{
			$units = $this->get_included_rental_units();
			
			foreach ($this->get_included_rental_units() as $index => $unit) {
				if ($unit->get_location_id() == $unit_to_remove->get_location_id()) {
					unset($this->rental_units[$index]);
				}
			}
		}
		
		/**
		 * Store the composite in the database.  If the composite has no ID it is assumed to be new and
		 * inserted for the first time.  The composite is then updated with the new insert id.
		 */
		public function store()
		{
			$so = self::get_so();
			
			if ($this->id) {
				// We can assume this composite came from the database since it has an ID. Update the existing row
				$so->update($this);
			} 
			else
			{
				// This object does not have an ID, so will be saved as a new DB row
				$so->add($this);
			}
		}
	
		/**
		 * Get the rental_unit objects associated with this composite
		 * 
		 * @param $sort the name of the column to sort by
		 * @param $dir the sort direction, 'asc' or 'desc'
		 * @param $start which row number to start returning results from
		 * @param $results how many results to return
		 * @return rental_unit[]
		 */
		public function get_units($sort = null, $dir = 'asc', $start = 0, $results = null)
		{
			if (!$this->units) {
				$this->units = rental_unit::get_units_for_composite($this->get_id(), $sort, $dir, $start, $results);
			}
			return $this->units; 
		}
		
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_is_active($is_active)
		{
			$this->is_active = (boolean)$is_active;
		}
		
		public function is_active() { return $this->is_active;	}
		
		public function set_name($name)
		{
			$this->name = $name;
		}
		
		public function get_name() { return $this->name; }
		
		public function set_has_custom_address($has_custom_address)
		{
			$this->has_custom_address = $has_custom_address;
		}
		
		public function has_custom_address() { return $this->has_custom_address; }
		
		public function set_custom_postcode($custom_postcode)
		{
			$this->custom_postcode = $custom_postcode;
		}
		
		public function set_custom_address_1($custom_address_1)
		{
			$this->custom_address_1 = $custom_address_1;
		}
	
		public function get_custom_address_1(){ return $this->custom_address_1; }
			
		public function set_custom_address_2($custom_address_2)
		{
			$this->custom_address_2 = $custom_address_2;
		}
	
		public function get_custom_address_2(){ return $this->custom_address_2; }
			
		public function get_custom_postcode() { return $this->custom_postcode; }
		
		public function set_custom_place($custom_place)
		{
			$this->custom_place = $custom_place;
		}
		
		public function set_custom_house_number($custom_house_number)
		{
			$this->custom_house_number = $custom_house_number;
		}
	
		public function get_custom_house_number(){ return $this->custom_house_number; }
		
		public function get_custom_place() { return $this->custom_place; }
		
		public function get_area_gros() {
			$area = 0;
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$area += $location->get_area_gros() ;
				}
			}
			return $area;
		}
		
		public function get_area_net() {
			$area = 0;
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$area += $location->get_area_net() ;
				}
			}
			return $area;

		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.socomposite');
			}
			
			return self::$so;
		}
		
		/**
		 * Return a string representation of the composite.
		 * 
		 * @return string
		 */
		function __toString()
		{
			$result  = '{';
			$result .= '"id":"' . $this->get_id() . '",';
			$result .= '"name":"' . $this->get_name() . '"';
			$result .= '}';
			return $result;
		}
		
		public function serialize()
		{
			$addresses = '';
			$location_codes = '';
			$gab_ids = '';
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$addresses .= $location->get_address_1() . "\n";
					$location_codes .= $location->get_location_code() . "\n";
					$gab_ids .= $location->get_gab_id() . "\n";
				}
			}
			if($this->has_custom_address())
			{
				$addresses = $this->get_custom_address_1() . ' ' . $this->get_custom_house_number();
			}
			return array(
				'id' => $this->get_id(),
				'location_code' => $location_codes,
				'description' => $this->get_description(),
				'is_active' => $this->is_active(),
				'name' => $this->get_name(),
				'address' => $addresses,
				'gab_id' => $gab_ids,
				'area_gros' => $this->get_area_gros(),
				'area_net' => $this->get_area_net()
			);
		} 
	}
?>