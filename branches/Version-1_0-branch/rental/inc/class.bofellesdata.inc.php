<?php
    class rental_bofellesdata {

    	// Instance variable
	    protected static $bo;
	    protected $connected = false;
	    protected $status;
		
    	/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$bo == null) {
				self::$bo = CreateObject('rental.bofellesdata');
			}
			return self::$bo;
		}
		
		public function get_db()
		{

			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();

//			$db = createObject('phpgwapi.db', null, null, true);
			$db = createObject('property.db_oci8');

			$db->debug = !!$config->config_data['external_db_debug'];
			$db->Host = $config->config_data['external_db_host'];
			$db->Type = $config->config_data['external_db_type'];
			$db->Database = $config->config_data['external_db_name'];
			$db->User = $config->config_data['external_db_user'];
			$db->Password = $config->config_data['external_db_password'];

			try
			{
				$db->connect();
				$connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}
			return $db;
		}
		
		public function responsibility_id_exist($responsibility_id)
		{
			if(isset($responsibility_id))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				$joins = "LEFT JOIN V_ANSVAR ON (V_ANSVAR.RESULTATENHET = V_ORG_ENHET.RESULTATENHET)";
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table $joins WHERE V_ANSVAR.ANSVAR = '$responsibility_id' AND V_ORG_ENHET.ORG_NIVAA = 4";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{	
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);				
				}
			}
			return false;
		}
		
		
		
  		public function result_unit_exist($result_unit, $level)
		{
			if(isset($result_unit) && is_numeric($result_unit))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.RESULTATENHET = $result_unit";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);
				}
			}
			return false;
		}
		
		public function org_unit_exist($org_unit_id, $level)
		{
			if(isset($org_unit_id) && is_numeric($org_unit_id))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.ORG_ENHET_ID = $org_unit_id";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);				
				}
			}
			return false;
		}
		
		public function get_result_unit($org_unit_id)
		{   
	        //Must traverse down u hierarchy
			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$joins = "LEFT JOIN V_ORG_KNYTNING ON (V_ORG_KNYTNING.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_KNYTNING.ORG_ENHET_ID = {$org_unit_id}";
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);			
	        
			if($db->next_record())
			{
				$level = (int)$db->f('ORG_NIVAA');
				if($level == 4)
				{
					return array(
							"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
							"ORG_NAME" => $db->f('ORG_NAVN'),
							"UNIT_ID" => $db->f('RESULTATENHET')
						);
				}
			}   
		}
		
		public function get_result_units()
		{
			
			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$sql = "SELECT $columns FROM $tables WHERE V_ORG_ENHET.ORG_NIVAA = 4 ORDER BY V_ORG_ENHET.RESULTATENHET ASC";
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);			
	        
			$result_units = array();
			while($db->next_record())
			{
				$result_units[] = array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"UNIT_ID" => $db->f('RESULTATENHET')
					);
			}
			return $result_units;
		}
		
		public function is_connected()
		{
			return $this->connected;
		}
		
		public function get_status()
		{
			return $this->status;
		}
    }
		
