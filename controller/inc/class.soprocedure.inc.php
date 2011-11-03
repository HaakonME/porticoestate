<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'procedure', 'inc/model/');

class controller_soprocedure extends controller_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return controller_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('controller.soprocedure');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$procedure)
	{
		$cols = array(
				'title',
				'purpose',
				'responsibility',
				'description',
				'reference',
				'attachment',
				'start_date',
				'end_date',
				'procedure_id',
				'revision_no',
				'revision_date',
				'control_area_id'
		);
			
		$values = array(
			$this->marshal($procedure->get_title(), 'string'),
			$this->marshal($procedure->get_purpose(), 'string'),
			$this->marshal($procedure->get_responsibility(), 'string'),
			$this->marshal($procedure->get_description(), 'string'),
			$this->marshal($procedure->get_reference(), 'string'),
			$this->marshal($procedure->get_attachment(), 'string'),
			$this->marshal($procedure->get_start_date(), 'int'),
			$this->marshal($procedure->get_end_date(), 'int'),
			$this->marshal($procedure->get_procedure_id(), 'int'),
			$this->marshal($procedure->get_revision_no(), 'int'),
			$this->marshal($procedure->get_revision_date(), 'int'),
			$this->marshal($procedure->get_control_area_id(), 'int')
		);
		
		$result = $this->db->query('INSERT INTO controller_procedure (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
		
		if(isset($result)) {
			// Get the new procedure ID and return it
			return $this->db->get_last_insert_id('controller_procedure', 'id');
		}
		else
		{
			return 0;
		}
		
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */

	function update($procedure)
	{	
		$id = intval($procedure->get_id());
			
		$values = array(
			'title = ' . $this->marshal($procedure->get_title(), 'string'),
			'purpose = ' . $this->marshal($procedure->get_purpose(), 'string'),
			'responsibility = ' . $this->marshal($procedure->get_responsibility(), 'string'),
			'description = ' . $this->marshal($procedure->get_description(), 'string'),
			'reference = ' . $this->marshal($procedure->get_reference(), 'string'),
			'attachment = ' . $this->marshal($procedure->get_attachment(), 'string'),
			'start_date = ' . $this->marshal($procedure->get_start_date(), 'int'),
			'end_date = ' . $this->marshal($procedure->get_end_date(), 'int'),
			'procedure_id = ' . $this->marshal($procedure->get_procedure_id(), 'int'),
			'revision_no = ' . $this->marshal($procedure->get_revision_no(), 'int'),
			'revision_date = ' . $this->marshal($procedure->get_revision_date(), 'int'),
			'control_area_id = ' . $this->marshal($procedure->get_control_area_id(), 'int')
		);
		
		$result = $this->db->query('UPDATE controller_procedure SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		return isset($result);
	}
	
	/**
	 * Get single procedure
	 * 
	 * @param	$id	id of the procedure to return
	 * @return a controller_procedure
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$joins = " {$this->left_join} controller_control_area ON (p.control_area_id = controller_control_area.id)";
		$sql = "SELECT p.*, controller_control_area.title AS control_area_name FROM controller_procedure p {$joins} WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
		$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
		$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
		$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
		$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
		$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
		$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
		$procedure->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
		$procedure->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
		$procedure->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
		$procedure->set_revision_no($this->unmarshal($this->db->f('revision_no'), 'int'));
		$procedure->set_revision_date($this->unmarshal($this->db->f('revision_date'), 'int'));
		$procedure->set_control_area_id($this->unmarshal($this->db->f('control_aera_id', 'int')));
		$procedure->set_control_area_name($this->unmarshal($this->db->f('control_area_name', 'string')));
		
		return $procedure;
	}
	
	function get_procedures($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		
		$condition = "WHERE end_date IS NULL";
		$sql = "SELECT * FROM controller_procedure $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
			$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
			$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
			$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
			$procedure->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
			$procedure->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
			$procedure->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$procedure->set_revision_no($this->unmarshal($this->db->f('revision_no'), 'int'));
			$procedure->set_revision_date($this->unmarshal($this->db->f('revision_date'), 'int'));
			
			$results[] = $procedure;
		}
		
		return $results;
	}

	function get_procedures_as_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		
		$condition = "WHERE end_date IS NULL";
		$sql = "SELECT * FROM controller_procedure $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
			$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
			$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
			$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
			$procedure->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
			$procedure->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
			$procedure->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$procedure->set_revision_no($this->unmarshal($this->db->f('revision_no'), 'int'));
			$procedure->set_revision_date($this->unmarshal($this->db->f('revision_date'), 'int'));
			
			$results[] = $procedure->toArray();;
		}
		
		return $results;
	}
	
	function get_old_revisions($id)
	{
		$results = array();
		
		$joins = " {$this->left_join} controller_control_area ON (p.control_area_id = controller_control_area.id)";
		
		$sql = "SELECT p.*, controller_control_area.title AS control_area_name FROM controller_procedure p {$joins} WHERE procedure_id = {$id} ORDER BY end_date DESC";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
			$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
			$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
			$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
			$procedure->set_start_date(date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $this->unmarshal($this->db->f('start_date'), 'int')));
			$procedure->set_end_date(date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $this->unmarshal($this->db->f('end_date'), 'int')));
			$procedure->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$procedure->set_revision_no($this->unmarshal($this->db->f('revision_no'), 'int'));
			$procedure->set_revision_date(date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $this->unmarshal($this->db->f('revision_date'), 'int')));
			$procedure->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$procedure->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
			
			$results[] = $procedure->toArray();;
		}
		
		return $results;
	}
	
	function get_id_field_name($extended_info = false)
	{
		
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'procedure', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		
		return $ret;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');
		if($search_for)
		{
			$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
			$like_clauses = array();
			switch($search_type){
				default:
					$like_clauses[] = "controller_procedure.title $this->like $like_pattern";
					break;
			}
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		$filter_clauses[] = "controller_procedure.end_date IS NULL";
		/*
		switch($filters['is_active']){
			case "non_active":
				$filter_clauses[] = "NOT controller_procedure.end_date IS NULL";
				break;
			default:
				$filter_clauses[] = "controller_procedure.end_date IS NULL";
				break;
		}
		*/
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "controller_procedure.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		if(isset($filters['control_areas']))
		{
			$filter_clauses[] = "controller_procedure.control_area_id = {$this->marshal($filters['control_areas'], 'int')}";
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);
		
		$joins = " {$this->left_join} controller_control_area ON (controller_procedure.control_area_id = controller_control_area.id)";

		$tables = "controller_procedure";
		
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(controller_procedure.id)) AS count';
		}
		else
		{
			$cols .= "controller_procedure.id, controller_procedure.title, controller_procedure.purpose, controller_procedure.responsibility, controller_procedure.description, controller_procedure.reference, controller_procedure.attachment, controller_procedure.start_date, controller_procedure.end_date, controller_procedure.procedure_id, controller_procedure.revision_no, controller_procedure.revision_date, controller_control_area.title AS control_area_name ";
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

	    //var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");    
	    
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	function populate(int $procedure_id, &$procedure)
	{
		
		if($procedure == null) {
			$procedure = new controller_procedure((int) $procedure_id);

			$procedure->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$procedure->set_purpose($this->unmarshal($this->db->f('purpose'), 'string'));
			$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility'), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$procedure->set_reference($this->unmarshal($this->db->f('reference'), 'string'));
			$procedure->set_attachment($this->unmarshal($this->db->f('attachment'), 'string'));
			$procedure->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
			$procedure->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
			$procedure->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$procedure->set_revision_no($this->unmarshal($this->db->f('revision_no'), 'int'));
			$procedure->set_revision_date($this->unmarshal($this->db->f('revision_date'), 'int'));
			$procedure->set_control_area_id($this->unmarshal($this->db->f('control_aera_id', 'int')));
			$procedure->set_control_area_name($this->unmarshal($this->db->f('control_area_name', 'string')));
		}
		
		return $procedure;
	}
	
}
