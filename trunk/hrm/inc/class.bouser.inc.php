<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id: class.bouser.inc.php,v 1.8 2006/12/27 10:38:35 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bouser
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $use_session;

		var $public_functions = array
		(
			'read'			=> True,
			'read_single'		=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'		=> True
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function hrm_bouser($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject($this->currentapp.'.souser');
			$this->bocommon 	= CreateObject($this->currentapp.'.bocommon');
			$this->grants = $this->so->grants;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= get_var('start',array('POST','GET'));
			$query	= get_var('query',array('POST','GET'));
			$sort	= get_var('sort',array('POST','GET'));
			$order	= get_var('order',array('POST','GET'));
			$filter	= get_var('filter',array('POST','GET'));
			$cat_id	= get_var('cat_id',array('POST','GET'));
			$allrows= get_var('allrows',array('POST','GET'));

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if ($allrows)
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','hr_user',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','hr_user');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}


		function read()
		{
			$account_info = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $account_info;
		}

		function read_single_training($id)
		{
			$values =$this->so->read_single_training($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if($values['start_date'])
			{
				$values['start_date']	= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			}
			if($values['end_date'])
			{
				$values['end_date']	= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
			}
			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}

			return $values;
		}

		function read_training($user_id)
		{
			$values = $this->so->read_training(array('user_id'=>$user_id, 'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			return $values;
		}


		function save($values,$action='')
		{
			$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);

			if ($action=='edit')
			{
				if ($values['training_id'] != '')
				{

					$receipt = $this->so->edit_training($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_training($values);
			}

			return $receipt;
		}

		function delete_training($user_id,$id)
		{
			$this->so->delete_training($user_id,$id);
		}

		function select_category_list($format='',$selected='')
		{

			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories= $this->so->select_category_list();

			while (is_array($categories) && list(,$category) = each($categories))
			{
				$sel_category = '';
				if ($category['id']==$selected)
				{
					$sel_category = 'selected';
				}

				$category_list[] = array
				(
					'cat_id'	=> $category['id'],
					'name'		=> $category['name'],
					'selected'	=> $sel_category
				);
			}

			for ($i=0;$i<count($category_list);$i++)
			{
				if ($category_list[$i]['selected'] != 'selected')
				{
					unset($category_list[$i]['selected']);
				}
			}

			return $category_list;
		}


		function select_place_list($selected='')
		{
			$places= $this->so->select_place_list();
			$place_list = $this->bocommon->select_list($selected,$places);
			return $place_list;
		}
	}
