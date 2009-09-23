<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	phpgw::import_class('booking.sogroup');
	
	class booking_soorganization extends booking_socommon
	{	
		function __construct()
		{
			parent::__construct('bb_organization', 
				array(
					'id'				=> array('type' => 'int'),
					'organization_number' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'name'			=> array('type' => 'string', 'required' => True, 'query' => True),
					'homepage'		=> array('type' => 'string', 'required' => False, 'query' => True),
					'phone'			=> array('type' => 'string'),
					'email'			=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'description'	=> array('type' => 'string'),
					'street' 		=> array('type' => 'string'),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string'),
					'city' 			=> array('type' => 'string'),
					'active'			=> array('type' => 'int', 'required'=>true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_number'				 		=> array('type' => 'string', 'required' => False),
					'customer_ssn' 						=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'activity_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_activity',
							'fkey' => 'activity_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'contacts'		=> array('type' => 'string',
						'manytomany' => array(
							'table' => 'bb_organization_contact',
							'key' => 'organization_id',
							'column' => array('name',
							                  'ssn' => array('sf_validator' => createObject('booking.sfValidatorNorwegianSSN')), 
							                  'email' => array('sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% contains an invalid email'))),
							                  'phone')
						)
					),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

        function get_groups($organization_id)
        {
            static $groups = null;
            if ($groups === null) {
                $groups = new booking_sogroup();
            }
            $results = $groups->read(array("filters" => array("organization_id" => $organization_id)));
            return $results;
        }
	}

