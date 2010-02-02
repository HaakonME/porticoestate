<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sob_account
	{
		function __construct()
		{
			$this->account	= 	$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = 'fm_b_account';

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " where id $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$b_account[] = array
				(
					'id'	=> $this->db->f('id'),
					'descr'			=> $this->db->f('descr')
				);
			}
			return $b_account;
		}

		function read_single($id)
		{

			$table = 'fm_b_account';

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$b_account['id']		= $this->db->f('id');
				$b_account['descr']		= $this->db->f('descr');
				$b_account['cat_id']		= $this->db->f('category');
				$b_account['responsible']	= $this->db->f('responsible');

				return $b_account;
			}
		}

		function add($b_account)
		{
			$table = 'fm_b_account';

			$b_account['descr'] = $this->db->db_addslashes($b_account['descr']);

			$this->db->query("INSERT INTO $table (id, descr,category,responsible) "
				. "VALUES ('" . $b_account['id'] . "','" . $b_account['descr']. "','" .$b_account['cat_id'] . "','" . $b_account['responsible'] . "')",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('budget account %1 has been saved',$b_account['id']));
			return $receipt;
		}

		function edit($b_account)
		{

			$table = 'fm_b_account';

			$b_account['descr'] = $this->db->db_addslashes($b_account['descr']);

			$this->db->query("UPDATE $table set"
					. " descr='" . $b_account['descr'] . "',"
					. "responsible=" . $b_account['responsible'] . ","
					. "category=" . (int)$b_account['cat_id']
					. " WHERE id='" . $b_account['id']. "'",__LINE__,__FILE__);


			$receipt['message'][]=array('msg'=>lang('budget account %1 has been edited',$b_account['id']));
			return $receipt;
		}

		function delete($id)
		{
			$table = 'fm_b_account';

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}
	}

