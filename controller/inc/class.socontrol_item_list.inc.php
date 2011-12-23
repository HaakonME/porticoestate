<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/

	phpgw::import_class('controller.socommon');

	include_class('controller', 'control_item_list', 'inc/model/');

	class controller_socontrol_item_list extends controller_socommon
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
				self::$so = CreateObject('controller.socontrol_item_list');
			}
			return self::$so;
		}

		/**
		 * Function for adding a new activity to the database. Updates the activity object.
		 *
		 * @param activitycalendar_activity $activity the party to be added
		 * @return bool true if successful, false otherwise
		 */
		function add(&$control_item_list)
		{
			$cols = array(
					'control_id',
					'control_item_id',
			);

			$values = array(
				$this->marshal($control_item_list->get_control_id(), 'int'),
				$this->marshal($control_item_list->get_control_item_id(), 'int')
			);

			$result = $this->db->query( 'INSERT INTO controller_control_item_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
			//$result = $this->db->query($sql, __LINE__,__FILE__);

			if(isset($result)) {
				// return the new control item ID
				return $this->db->get_last_insert_id('controller_control_item_list', 'id');
				// Forward this request to the update method
				//return $this->update($control_item);
			}
			else
			{
				return 0;
			}
		}

		function update($control_item_list)
		{
			$id = intval($control_item_list->get_id());

			$values = array(
				'control_id = ' . $this->marshal($control_item_list->get_control_id(), 'int'),
				'control_item_id = '. $this->marshal($control_item_list->get_control_item_id(), 'int'),
				'order_nr = ' . $this->marshal($control_item_list->get_order_nr(), 'int')
			);

			$result = $this->db->query('UPDATE controller_control_item_list SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return isset($result);
		}

		/**
		 * Get single control_item_list
		 * 
		 * @param	$id	id of the control_item_list to return
		 * @return a controller_control_item_list
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$sql = "SELECT p.* FROM controller_control_item_list p WHERE p.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id', true), 'int'));
			$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
			$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
			$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr', true), 'int'));

			return $control_item_list;
		}

		function get_single_2($control_id, $control_item_id)
		{
			$sql = "SELECT cil.* FROM controller_control_item_list cil WHERE cil.control_id = " . $control_id . " AND cil.control_item_id = " . $control_item_id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id', true), 'int'));
			$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
			$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
			$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr', true), 'int'));

			return $control_item_list;
		}

		function delete($control_id, $control_item_id)
		{
			$result = $this->db->query("DELETE FROM controller_control_item_list WHERE control_id = $control_id AND control_item_id = $control_item_id", __LINE__,__FILE__);

			return isset($result);
		}

		function delete_control_items($control_id)
		{
			$result = $this->db->query("DELETE FROM controller_control_item_list WHERE control_id = $control_id");

			return isset($result);
		}

		function get_control_item_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array()){}

		function get_id_field_name($extended_info = false)
		{
		}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{

		}

		function get_control_items($control_group_id)
		{

		}

		function populate(int $control_item_id, &$control_item)
		{

		}

	}
