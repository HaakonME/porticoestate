<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiapplication_settings extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
		);
		
		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('booking::settings::application_settings');
		}
		
		public function index()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				foreach($_POST as $dim => $value)
				{
					if (strlen(trim($value)) > 0)
					{
						$config->value($dim, phpgw::clean_value($value));
					}
					else
					{
						unset($config->config_data[$dim]);
					}
				}
				$config->save_repository();
			}
			$this->use_yui_editor();
			self::render_template('application_settings', array('config_data' =>$config->config_data));
		}
	}
