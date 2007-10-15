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
 	* @version $Id: class.bojob.inc.php,v 1.16 2006/12/27 10:38:35 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bojob
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

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

		function hrm_bojob($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject($this->currentapp.'.sojob');
			$this->socommon = CreateObject($this->currentapp.'.socommon');

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
				$GLOBALS['phpgw']->session->appsession('session_data','phpgw_hrm_job',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','phpgw_hrm_job');

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

		function read_single_job($id)
		{
			return $this->so->read_single_job($id);
		}

		function read_qualification($job_id)
		{
			$qualification_list = $this->so->read_qualification(array('job_id'=>$job_id,'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			for ($i=0;$i<count($qualification_list);$i++)
			{
				if ($qualification_list[$i]['level'] > 0)
				{
					$space = '--> ';
					$spaceset = str_repeat($space,$qualification_list[$i]['level']);
					$qualification_list[$i]['name'] = $spaceset . $qualification_list[$i]['name'];
				}
			}

			return $qualification_list;
		}

		function read_qualification_type()
		{
			$qualification_list = $this->so->read_qualification_type(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $qualification_list;
		}

		function read_single_qualification($id)
		{
			$values =$this->so->read_single_qualification($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}

		function read_single_task($id)
		{
			$values =$this->so->read_single_task($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}

		function read_single_qualification_type($id)
		{
			$values =$this->so->read_single_qualification_type($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}


		function read_task($job_id)
		{
			$task_list = $this->so->read_task(array('job_id'=>$job_id,'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			for ($i=0;$i<count($task_list);$i++)
			{
				if ($task_list[$i]['level'] > 0)
				{
					$space = '--> ';
					$spaceset = str_repeat($space,$task_list[$i]['level']);
					$task_list[$i]['name'] = $spaceset . $task_list[$i]['name'];
				}
			}

			return $task_list;
		}


		function save_job($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_job($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_job($values);
			}
			return $receipt;
		}

		function save_task($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_task($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_task($values);
			}
			return $receipt;
		}

		function save_qualification($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['quali_id'] != '')
				{
					$receipt = $this->so->edit_qualification($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_qualification($values);
			}

			return $receipt;
		}

		function save_qualification_type($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['quali_type_id'] != '')
				{
					$receipt = $this->so->edit_qualification_type($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_qualification_type($values);
			}

			return $receipt;
		}

		function delete_task($id='')
		{
			$this->so->delete_task($id);
		}

		function delete_qualification($job_id,$id)
		{
			$this->so->delete_qualification($job_id,$id);
		}

		function delete_job($id)
		{
			$this->so->delete_job($id);
		}

		function reset_job_type_hierarchy()
		{
			$this->so->reset_job_type_hierarchy();
		}

		function select_job_list($selected='')
		{
			$jobs= $this->so->select_job_list();
			while (is_array($jobs) && list(,$job) = each($jobs))
			{
				if ($job['level'] > 0)
				{
					$space = '--';
					$spaceset = str_repeat($space,$job['level']);
					$job['name'] = $spaceset . $job['name'];
				}

				$sel_job = '';

				if ($job['id']==$selected)
				{
					$sel_job = 'selected';
				}

				$job_list[] = array
				(
					'id'		=> $job['id'],
					'name'		=> $job['name'],
					'selected'	=> $sel_job
				);
			}

			for ($i=0;$i<count($job_list);$i++)
			{
				if ($job_list[$i]['selected'] != 'selected')
				{
					unset($job_list[$i]['selected']);
				}
			}

			return $job_list;
		}

		function select_task_list($selected='',$id='', $job_id='')
		{
			$tasks= $this->so->select_task_list($id,$job_id);
			while (is_array($tasks) && list(,$task) = each($tasks))
			{
				if ($task['level'] > 0)
				{
					$space = '--';
					$spaceset = str_repeat($space,$task['level']);
					$task['name'] = $spaceset . $task['name'];
				}

				$sel_task = '';

				if ($task['id']==$selected)
				{
					$sel_task = 'selected';
				}

				$task_list[] = array
				(
					'id'		=> $task['id'],
					'name'		=> $task['name'],
					'selected'	=> $sel_task
				);
			}

			for ($i=0;$i<count($task_list);$i++)
			{
				if ($task_list[$i]['selected'] != 'selected')
				{
					unset($task_list[$i]['selected']);
				}
			}

			return $task_list;
		}

		function select_qualification_list($job_id,$quali_id='')
		{
			$qualification_list = $this->so->select_qualification_list($job_id,$quali_id);
			return $qualification_list;
		}
		
		function resort_value($data)
		{
			$this->so->resort_value($data);
		}

	}
