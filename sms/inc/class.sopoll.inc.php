<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage poll
 	* @version $Id: class.sopoll.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_sopoll
	{
		var $grants;
		var $db;
		var $db2;
		var $account;
		var $poll_data;

		function sms_sopoll()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('sms.bocommon');
			$this->db 		= clone($GLOBALS['phpgw']->db);
			$this->db2 		= clone($this->db);

			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('sms','.config');
			$this->join		= $this->db->join;
			$this->like		= $this->db->like;
		}



		function read($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query		= (isset($data['query'])?$data['query']:'');
				$sort		= (isset($data['sort'])?$data['sort']:'DESC');
				$order		= (isset($data['order'])?$data['order']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by poll_code asc';
			}

			$table = 'phpgw_sms_featpoll';

			$where= 'WHERE';
			$grants = $this->grants;
			
/*			if (is_array($grants))
			{
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where ( $table.uid IN(" . implode(',',$public_user_list) . "))";

				$where= 'AND';
			}
*/

			if($query)
			{
				$query = preg_replace("'",'',$query);
				$query = preg_replace('"','',$query);

				$querymethod = " $where poll_code $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

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
				$poll_info[] = array
				(
					'id'		=> $this->db->f('poll_id'),
					'uid'		=> $this->db->f('uid'),
					'code'		=> stripslashes($this->db->f('poll_code')),
					'title'		=> stripslashes($this->db->f('poll_title')),
					'enable'	=> $this->db->f('poll_enable'),
					'grants'	=> (int)$grants[$this->db->f('uid')]
				);
			}

			return $poll_info;
		}


		function read_single_poll($id)
		{
			$sql = 'SELECT * FROM phpgw_sms_featpoll WHERE poll_id=' . intval($id);
			$this->db->query($sql,__LINE__,__FILE__);
			$bin_path = PHPGW_SERVER_ROOT . SEP . 'sms' . SEP . 'bin';
			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['code']		= stripslashes($this->db->f('poll_code'));
				$values['exec']		= stripslashes(str_replace($bin_path,'',$this->db->f('poll_exec')));
				$values['type']		= $this->db->f('poll_type');
				$values['descr']	= stripslashes($this->db->f('poll_descr'));
			}
			return $values;
		}


		function add_poll($values)
		{
			$this->db->transaction_begin();

			$values['exec'] = PHPGW_SERVER_ROOT . SEP . 'sms' . SEP . 'bin/' . $values['exec'];
			$values['exec'] = str_replace("//","/",$values['exec']);
			$values['exec'] = str_replace("..",".",$values['exec']);
			$values['exec'] = $this->db->db_addslashes($values['exec']);
			$values['code'] = $this->db->db_addslashes($values['code']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values=array(
				$this->account,
				$values['code'],
				$values['exec'],
				$values['type'],
				$values['descr'],
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);
			
			$this->db->query("INSERT INTO phpgw_sms_featpoll (uid,poll_code,poll_exec,poll_type,poll_descr) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('SMS poll code %1 has been added',$values['code']));
			$receipt['poll_id']= $this->db->get_last_insert_id(phpgw_sms_featpoll,'poll_id');

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_poll($values)
		{
			$this->db->transaction_begin();

			$values['exec'] = PHPGW_SERVER_ROOT . SEP . 'sms' . SEP . 'bin/' . $values['exec'];
			$values['exec'] = str_replace("//","/",$values['exec']);
			$values['exec'] = str_replace("..",".",$values['exec']);
			$value_set['poll_type'] 	= $this->db->db_addslashes($values['type']);
			$value_set['poll_exec'] 	= $this->db->db_addslashes($values['exec']);
			$value_set['poll_code'] 	= $this->db->db_addslashes($values['code']);
			$value_set['poll_descr']	= $this->db->db_addslashes($values['descr']);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE phpgw_sms_featpoll set $value_set WHERE poll_id=" . $values['poll_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('SMS poll code %1 has been saved',$values['code']));

			$receipt['poll_id']= $values['poll_id'];
			return $receipt;
		}

		function delete_poll($id)
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM phpgw_sms_poll_value WHERE poll_id =' . intval($poll_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_poll_choice WHERE poll_id =' . intval($poll_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_poll_attrib WHERE poll_id =' . intval($poll_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_poll_poll WHERE id='  . intval($id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}
	}
