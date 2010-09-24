<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiaccount_code_dimension extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
		);
		
		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('booking::settings::account_code_dimensions');
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
						$config->value($dim, trim($value));
					}
					else
					{
						unset($config->config_data[$dim]);
					}
				}
				$config->save_repository();
			}
			//echo '<pre>'; print_r($config->config_data); exit;
			
			self::render_template('account_code_dimension', array('config_data' =>$config->config_data));
		}
	}
