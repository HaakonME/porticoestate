<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sobuilding extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_building', 
				array(
					'id' => array('type' => 'int'),
					'name' => array('type' => 'string', 'query' => true, 'required' => true),
					'homepage' => array('type' => 'string'),
					'description' => array('type' => 'string'),
					'phone' => array('type' => 'string'),
					'email' => array('type' => 'string'),
					'street' 		=> array('type' => 'string', 'query' => true),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string', 'query' => true),
					'city' 			=> array('type' => 'string', 'query' => true),
					'active' => array('type' => 'int')
				)
			);
		}
		
		/**
		 * Returns buildings used by the organization with the specified id
		 * within the last 300 days.
		 *
		 * @param int $organization_id
		 * @param array $params Parameters to pass to socommon->read
		 *
		 * @return array (in socommon->read format)
		 */
		function find_buildings_used_by($organization_id, $params = array()) {				
			if (!isset($params['filters'])) { $params['filters'] = array(); }
			if (!isset($params['filters']['where'])) { $params['filters']['where'] = array(); }
			
			$params['filters']['where'][] = '%%table%%.id IN ('.
				'SELECT r.building_id FROM bb_allocation_resource ar '.
				'JOIN bb_resource r ON ar.resource_id = r.id '. 
				'JOIN bb_allocation a ON a.id = ar.allocation_id AND (a.from_ - \'now\'::timestamp < \'300 days\') AND a.organization_id = '.$this->_marshal($organization_id, 'int').' '.
			')';
			
			return $this->read($params);
		}
	}
