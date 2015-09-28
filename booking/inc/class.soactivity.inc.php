<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soactivity extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_activity', 
				array(
					'id'	=>		array('type' => 'int'),
					'parent_id'	=>	array('type' => 'int', 'required' => false),
					'name'	=>		array('type' => 'string',	'query' => true, 'required' => true),
					'description'	=>	array('type' => 'string', 'query' => true),
					'active' => array('type' => 'int', 'required' => true)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
			# Detect and prevent loop creation
			$node_id = $entity['parent_id'];
			while($entity['id'] && $node_id)
			{
				if($node_id == $entity['id'])
				{
					$errors['parent_id'] = lang('Invalid parent activity');
					break;
				}
				$next = $this->read_single($node_id);
				$node_id = $next['parent_id'];
			}
			return $errors;
		}

		public function get_path($id)
		{

			$sql = "SELECT name, parent_id FROM bb_activity WHERE id =" . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$parent_id = $this->db->f('parent_id');

			$name = $this->db->f('name', true);

			$path = array(array('id' => $id,'name' => $name));

			if($parent_id)
			{
				$path = array_merge($this->get_path($parent_id), $path);
			}
			return $path;
		}

	}
