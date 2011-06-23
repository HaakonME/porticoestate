<?php
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_group extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $name;
		protected $description;
		protected $organization_id;
		protected $show_in_portal;
		protected $shortname;
		
		/**
		 * Constructor.  Takes an optional ID.  If a organization is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this organization
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
		
		public function set_name($name)
		{
			$this->name = $name;
		}
		
		public function get_name() { return $this->name; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_organization_id($organization_id)
		{
			$this->organization_id = $organization_id;
		}
		
		public function get_organization_id() { return $this->organization_id; }
		
		public function set_show_in_portal($show_in_portal)
		{
			$this->show_in_portal = $show_in_portal;
		}
		
		public function get_show_in_portal() { return $this->show_in_portal; }
		
		public function set_shortname($shortname)
		{
			$this->shortname = $shortname;
		}
		
		public function get_shortname() { return $this->shortname; }
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'name' => $this->get_name(),
				'organization_id' => $this->get_organization_id(),
				'shortname' => $this->get_shortname(),
				'description' => $this->get_description(),
				'show_in_portal' => $this->get_show_in_portal()
			);
		}
		
	}
?>