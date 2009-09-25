<?php
	phpgw::import_class('booking.uiorganization');

	class bookingfrontend_uiorganization extends booking_uiorganization
	{
		public $public_functions = array
			(
			 'show'			=>	true,
			 'edit'         =>  true,
			 'index'        =>  true,
			 'building_users' => true,
			);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}
		
		protected function indexing()
		{
			return parent::index_json();
		}
		
		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$organization['organizations_link'] = self::link(array('menuaction' => $this->module.'.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => $this->module.'.uiorganization.edit', 'id' => $organization['id']));
			$organization['start'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index', 'type' => "organization"));
			$auth_forward = "?redirect_menuaction={$this->module}.uiorganization.show&redirect_id={$organization['id']}";

			// BEGIN EVIL HACK
			$auth_forward .= '&orgnr='.$organization['organization_number'];
			// END EVIL HACK

			$bouser = CreateObject('bookingfrontend.bouser');
			$organization['login_link'] = 'login.php'.$auth_forward;
			$organization['logoff_link'] = 'logoff.php'.$auth_forward;
			if ($bouser->is_organization_admin($organization['id'])) $organization['logged_on'] = true;
			self::render_template('organization', array('organization' => $organization));
		}
	}
