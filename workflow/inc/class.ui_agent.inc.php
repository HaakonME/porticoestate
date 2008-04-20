<?php
	/**************************************************************************\
	* phpGroupWare Workflow - Agents Connector - interface layer                 *
	* ------------------------------------------------------------------------ *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published           *
	* by the Free Software Foundation; either version 2 of the License, or     *
	* any later version.                                                       *
	\**************************************************************************/

	/* $Id: class.ui_agent.inc.php 19830 2005-11-14 19:37:58Z regis_glc $ */


	/**
	 *  * Agents abstraction library. interface layer
	 *  *
	 *  * This allows the Workflow Engine to connect to various agents
	 *  * Agents are external elements for the workflow. It could be
	 *  * email systems, filesystems, calendars, what you want.
	 *  * Use this class to make childrens like, for example in the
	 *  * class.ui_agent_mail_smtp.inc.php for the mail_smtp susbsytem
	 *  *
	 *  * @package workflow
	 *  * @author regis.leroy@glconseil.com
	 *  * GPL
	  */

	require_once(dirname(__FILE__) . '/' . 'class.workflow.inc.php');

	class ui_agent extends workflow
	{
		// local error storage
		var $error=Array();
		// store the POST or GET content concerning the agent
		var $agent_values = Array();
		// $interactivity for runtime mode
		var $interactivity = false;

		// concerning Child classes constructors ---------------------------------------------
		// bo_agent object which have to be set in your child class to the right bo_agent child
		var $bo_agent = null;
		// the type of the agent, on agent of this type for one activity, no more
		var $agent_type = '';
		// -----------------------------------------------------------------------------------

		function ui_agent()
		{
			parent::workflow();
		}

		/**
		 *  Function which must be called (internally) at runtime
		 * * The agent MUST know if he is runned in an interactive activity or not.
		 * * For example on non-interactive activities the agents musn't scan the POST content
		 */
		function setInteractivity($bool)
		{
			$this->interactivity = $bool;
		}

		 //! return errors recorded by this object
		 /**
		  * * You should always call this function after failed operations on a workflow object to obtain messages
		 *  * @param $as_array if true the result will be send as an array of errors or an empty array. Else, if you do not give any parameter
		 *  * or give a false parameter you will obtain a single string which can be empty or will contain error messages with <br /> html tags.
		  */
		 function get_error($as_array=false)
		 {
		 	$this->error[] = $this->bo_agent->get_error();
		 	if ($as_array)
			{
			 	return $this->error;
			}
			$result_str = implode('<br />',$this->error);
			$this->error= Array();
			return $result_str;
			}

		/**
		 * * Factory: load the agent values stored somewhere via the agent bo object
		*  * @param $agent_id is the agent id
		*  * @return false if the agent cannot be loaded, true else
		 */
		function load($agent_id)
		{
			return ( (isset($this->bo_agent)) && ($this->bo_agent->load($agent_id)));
		}

		/**
		 * * save the agent values somewhere via the agent bo object
		*  * @param $datas is an array containing comlumns => value pairs
		*  * @return false if the agent was not previously loaded or if the save fail, true else
		 */
		function save(&$datas)
		{
			if (!(isset($this->bo_agent)))
			{
				return false;
			}
			else
			{
				$ok = $this->bo_agent->set($datas);
				if ($ok) $ok = $this->bo_agent->save();
				return $ok;
			}
		}

		/**
		 * * Function called at runtime to permit associtaion with the instance and the activity
		 * * we store references to theses objects and we tell the ui object if we are in interactive
		 * * mode or not.
		 */
		function runtime(&$instance, &$activity)
		{
			$this->bo_agent->runtime($instance,$activity);
			$this->setInteractivity($activity->isInteractive());
		}

		/**
		*  * this function show the shared part of all agents when showing
		*  *
		*  * configuration in the admin activity form
		*  * do not forget to call parent::showAdminActivityOptions ($template_block_name)
		*  * in the child if you want to display this shared part
		*  * @return
		 */
		function showAdminActivityOptions ($template_block_name)
		{
			$admin_name = 'admin_agent_shared';
			$this->t->set_file($admin_name, $admin_name . '.tpl');
			$this->t->set_var(array(
				'agent_description'	=> $this->bo_agent->getDescription(),
				'agent_title'		=> $this->bo_agent->getTitle(),
				'agent_help'		=> $this->bo_agent->getHelp(),
			));
			$this->translate_template($admin_name);
												$this->t->parse($template_block_name, $admin_name);
		}

		/**
		*  * Function called by the running object (run_activity) after the activity_pre code
		*  *
		*  * and before the user code. This code is runned only if the $GLOBALS['workflow']['__leave_activity']
		*  * IS NOT set (i.e.: the user is not cancelling his form in case of interactive activity)
		*  * WARNING : on interactive queries the user code is parsed several times and this function is called
		*  * each time you reach the begining of the code, this means at least the first time when you show the form
		*  * and every time you loop on the form + the last time when you complete the code (if the user did not cancel).
		*  * @return true or false, if false the $this->error array should contains error messages
		 */
		function run_activity_pre()
		{
			return true;
		}

		/**
		*  * Function called by the running object (run_activity) after the activity_pre code
		*  *
		*  * and before the user code. This code is runned only if the $GLOBALS['workflow']['__leave_activity']
		*  * IS set (i.e.: the user is cancelling his form in case of interactive activity)
		*  * @return true or false, if false the $this->error array should contains error messages
		 */
		function run_leaving_activity_pre()
		{
			return true;
		}

		/**
		*  * Function called by the running object (run_activity) after the user code
		*  *
		*  * and after the activity_pos code. This code is runned only if the $GLOBALS['__activity_completed']
		*  * IS NOT set (i.e.: the user is not yet completing the activity)
		*  * WARNING : on automatic (non-interactive) activities this code is NEVER called. Non-interactive
		*  * activities are completed after the end of the user code and there is no way to re-parse this
		*  * user code after completion.
		*  * @return true or false, if false the $this->error array should contains error messages
		 */
		function run_activity_completed_pos()
		{
			return true;
		}

		/**
		*  * Function called by the running object (run_activity) after the user code
		*  *
		*  * and after the activity_pos code. This code is runned only if the $GLOBALS['__activity_completed']
		*  * IS set (i.e.: the user has completing the activity)
		*  * WARNING : on interactive queries the user code is parsed several times and this function is called
		*  * each time you reach the end of the code without completing, this means at least the first time
		*  * and every time you loop on the form.
		*  * @return true or false, if false the $this->error array should contains error messages
		 */
		function run_activity_pos()
		{
			return true;
		}

		/**
		 *  retrieve infos set by the user in interactive forms
		 * * ans store it with the bo_agent object
		 */
		function retrieve_form_settings()
		{
			if ($this->interactivity)
			{
				$res = Array();
				$this->agent_values = get_var('wf_agent_'.$this->agent_type, array('POST','GET'),$value);
				foreach ($this->bo_agent->get(2) as $name => $value)
				{
					$res[$name] = (isset($this->agents_values[$name]))? $this->agents_values[$name] : $value;
				}
				//store theses values in the bo_object(without saving the values)
				//htmlentites will be made by the bo's set function
				$this->bo_agent->set($res);
			}
		}

	}
?>
