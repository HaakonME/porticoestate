<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage autoreply
 	* @version $Id: class.uiautoreply.inc.php,v 1.5 2006/12/27 10:39:15 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_uiautoreply
	{
		var $public_functions = array(
			'index'			=> True,
			'add'			=> True,
			'add_yes'		=> True,
			'add_scenario'		=> True,
			'add_scenario_yes'	=> True,
			'edit_scenario'		=> True,
			'edit_scenario_yes'	=> True,
			'manage'		=> True,
			'delete'		=> True,
			'delete_scenario'	=> true
			);

		function sms_uiautoreply()
		{
			$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject($this->currentapp.'.boautoreply',true);
			$this->bocommon				= CreateObject($this->currentapp.'.bocommon');
			$this->menu				= CreateObject($this->currentapp.'.menu');
			$this->sms				= CreateObject($this->currentapp.'.sms');
			$this->acl				= CreateObject('phpgwapi.acl');
			$this->acl_location 			= '.autoreply';
			$this->menu->sub			= '.autoreply';
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			
			$this->db 		= clone($GLOBALS['phpgw']->db);
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}


		function index()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$links = $this->menu->links();
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ))
			{

				$this->bocommon->no_access($links);
				return;
			}
			
			$GLOBALS['phpgw']->xslttpl->add_file(array('autoreply','nextmatchs','menu',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','sms_reply_receipt');

			$GLOBALS['phpgw']->session->appsession('session_data','sms_reply_receipt','');

			$autoreply_info = $this->bo->read();

			while (is_array($autoreply_info) && list(,$entry) = each($autoreply_info))
			{

				if($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.delete', 'autoreply_id'=> $entry['id']));
					$text_delete		= lang('delete');
					$lang_delete_text 	= lang('delete the autoreply code');
				}

				$content[] = array
				(
					'code'					=> $entry['code'],
					'user'					=> $GLOBALS['phpgw']->accounts->id2name($entry['uid']),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.manage', 'autoreply_id'=> $entry['id'])),
					'link_delete'				=> $link_delete,
//					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.view&', 'autoreply_id'=> $entry['id'])),
//					'lang_view_config_text'			=> lang('view the config'),
					'lang_edit_config_text'			=> lang('manage the autoreply code'),
//					'text_view'				=> lang('view'),
					'text_edit'				=> lang('manage'),
					'text_delete'				=> $text_delete,
					'lang_delete_text'			=> $lang_delete_text,
				);
	
				unset ($link_delete);
				unset ($text_delete);
				unset ($lang_delete_text);
			}


			$table_header[] = array
			(

				'sort_code'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'autoreply_code',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiautoreply.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_code'		=> lang('code'),
				'lang_delete'		=> lang('delete'),
				'lang_edit'		=> lang('manage'),
				'lang_view'		=> lang('view'),
				'lang_user'		=> lang('user'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiautoreply.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

//			if($this->acl->check($this->acl_location, PHPGW_ACL_ADD))
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a autoreply'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.add')),
				);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'						=> $links,
				'allow_allrows'					=> True,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($autoreply_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'table_add'					=> $table_add,
				'values'					=> $content
			);

			$appname					= lang('autoreplies');
			$function_msg					= lang('list SMS autoreplies');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}

		function add()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
			
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Add SMS autoreply');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
			$add_autoreply_code	= get_var('add_autoreply_code',array('POST','GET'));

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.add_yes',
				'autoreply_id'	=> $autoreply_id
				);
				
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);


			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS autoreply code: <input type=text size=10 maxlength=10 name=add_autoreply_code value=\"$add_autoreply_code\">
			    <p><input type=submit class=button value=Add>
			    </form>
			";


			$done_data = array('menuaction'	=> $this->currentapp.'.uiautoreply.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";


			echo $content;

		}

		function add_yes()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}

			$add_autoreply_code	= strtoupper(get_var('add_autoreply_code',array('POST','GET')));

			$uid = $this->account;
			$target = 'add';

			if ($add_autoreply_code)
			{
			    if ($this->sms->checkavailablecode($add_autoreply_code))
			    {
				$sql = "INSERT INTO phpgw_sms_featautoreply (uid,autoreply_code) VALUES ('$uid','$add_autoreply_code')";
				$this->db->transaction_begin();

				$this->db->query($sql,__LINE__,__FILE__);

				$new_uid = $this->db->get_last_insert_id(phpgw_sms_featautoreply,'autoreply_id');

				$this->db->transaction_commit();
					
				if ($new_uid)
				{
					$receipt['message'][]=array('msg'=>lang('SMS autoreply code %1 has been added', $add_autoreply_code));
					$GLOBALS['phpgw']->session->appsession('session_data','sms_reply_receipt',$receipt);
					$target = 'index';
				}
				else
				{
					$error_string = lang('Fail to add SMS autoreply code') . ' ' . $add_autoreply_code;
				}
			    }
			    else
			    {
				$error_string = lang('SMS code %1 already exists, reserved or use by other feature!',$add_autoreply_code);
			    }
			}
			else
			{
				$error_string = lang('You must fill all fields!');
			}
			
			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.' . $target,
				'err'		=> urlencode($error_string)
				);

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
		}


		function add_scenario()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
			
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Add SMS autoreply scenario');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$add_autoreply_scenario_result	= get_var('add_autoreply_scenario_result',array('POST','GET'));


			$sql = "SELECT * FROM phpgw_sms_featautoreply WHERE autoreply_id='$autoreply_id'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$autoreply_code = $this->db->f('autoreply_code');

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.add_scenario_yes',
				'autoreply_id'	=> $autoreply_id
				);
				
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);


			$content .= "
			    <p>
			    <p>SMS autoreply code: <b>$autoreply_code</b>
			    <p>
			    <form action=$add_url method=post>
			";
			for ($i=1;$i<=7;$i++)
			{
			    ${"add_autoreply_scenario_param".$i} = strtoupper(get_var("add_autoreply_scenario_param$i",array('POST','GET')));
			    $content .= "<p>SMS autoreply scenario param $i: <input type=text size=20 maxlength=20 name=add_autoreply_scenario_param$i value=\"".${"add_autoreply_scenario_param".$i}."\">\n";			    
			}

			$content .= "
		    	    <p>SMS autoreply scenario return: <input type=text size=60 maxlength=130 name=add_autoreply_scenario_result value=\"$add_autoreply_scenario_result\">
			    <p><input type=submit class=button value=Add>
			    </form>
			";

			$done_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.manage',
				'autoreply_id'	=> $autoreply_id);
				
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p><li>
			    <a href=\"$done_url\">Back</a>
			    <p>
			";
			echo $content;
		}

		function add_scenario_yes()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}

			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$add_autoreply_scenario_result	= get_var('add_autoreply_scenario_result',array('POST','GET'));

			$ok = 0;
			
			for ($i=1;$i<=7;$i++)
			{
			    ${"add_autoreply_scenario_param".$i} = strtoupper(get_var("add_autoreply_scenario_param$i",array('POST','GET')));
			    if(${"add_autoreply_scenario_param".$i})
			    {
			    	$ok++;
			    }

			}
			if ($add_autoreply_scenario_result && ($ok > 0))
			{
			    for ($i=1;$i<=7;$i++)
			    {
				$autoreply_scenario_param_list .= "autoreply_scenario_param$i,";
			    }
			    for ($i=1;$i<=7;$i++)
			    {
				$autoreply_scenario_code_param_entry .= "'".${"add_autoreply_scenario_param".$i}."',";
			    }
			    $sql = "
				INSERT INTO phpgw_sms_featautoreply_scenario 
				(autoreply_id,".$autoreply_scenario_param_list."autoreply_scenario_result) VALUES ('$autoreply_id',$autoreply_scenario_code_param_entry'$add_autoreply_scenario_result')";
			    
				$this->db->transaction_begin();
				$this->db->query($sql,__LINE__,__FILE__);
				$new_uid = $this->db->get_last_insert_id(phpgw_sms_featautoreply_scenario,'autoreply_scenario_id');
				$this->db->transaction_commit();
					
				if ($new_uid)
			    {
					$error_string = "SMS autoreply scenario has been added";
			    }
			    else
			    {
			        $error_string = "Fail to add SMS autoreply scenario";
			    }
			}
			else
			{
			    $error_string = "You must fill at least one field and the scenario result!";
			}
			$target = 'add_scenario';
			
			$add_data = array(
				'menuaction'			=> $this->currentapp.'.uiautoreply.' . $target,
				'autoreply_id' 			=> $autoreply_id,
				'err' 				=> urlencode($error_string),
				'add_autoreply_scenario_result' => $add_autoreply_scenario_result
				);

			for ($i=1;$i<=7;$i++)
			{
			    $add_data["add_autoreply_scenario_param".$i] = strtoupper(get_var("add_autoreply_scenario_param$i",array('POST')));
			}

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
		}


		function edit_scenario()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
			
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Edit SMS autoreply scenario');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$autoreply_scenario_id	= get_var('autoreply_scenario_id',array('POST','GET'));
			$add_autoreply_scenario_result	= get_var('add_autoreply_scenario_result',array('POST','GET'));

			$sql = "SELECT * FROM phpgw_sms_featautoreply WHERE autoreply_id='$autoreply_id'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$autoreply_code = $this->db->f('autoreply_code');

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$edit_data = array(
				'menuaction'		=> $this->currentapp.'.uiautoreply.edit_scenario_yes',
				'autoreply_id'		=> $autoreply_id,
				'autoreply_scenario_id'	=> $autoreply_scenario_id
				);
				
			$edit_url = $GLOBALS['phpgw']->link('/index.php',$edit_data);


			$content .= "
			    <p>
			    <p>SMS autoreply code: <b>$autoreply_code</b>
			    <p>
			    <form action=$edit_url method=post>
			";
			$sql = "SELECT * FROM phpgw_sms_featautoreply_scenario WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			for ($i=1;$i<=7;$i++)
			{
			    ${"edit_autoreply_scenario_param".$i} =  $this->db->f("autoreply_scenario_param$i");
			}
			for ($i=1;$i<=7;$i++)
			{
			    $content .= "<p>SMS autoreply scenario param $i: <input type=text size=20 maxlength=20 name=edit_autoreply_scenario_param$i value=\"".${"edit_autoreply_scenario_param".$i}."\">\n";
			}
			$edit_autoreply_scenario_result = $this->db->f('autoreply_scenario_result');
			$content .= "
		    	    <p>SMS autoreply scenario result: <input type=text size=60 maxlength=130 name=edit_autoreply_scenario_result value=\"$edit_autoreply_scenario_result\">
			    <p><input type=submit class=button value=\"Save\">
			    </form>
			";
			$done_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.manage',
				'autoreply_id'	=> $autoreply_id);
				
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p><li>
			    <a href=\"$done_url\">Back</a>
			    <p>
			";
			echo $content;
		}



		function edit_scenario_yes()
		{
		
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}

			$autoreply_scenario_id	= get_var('autoreply_scenario_id',array('POST','GET'));
			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$edit_autoreply_scenario_result	= get_var('edit_autoreply_scenario_result',array('POST','GET'));
			
			for ($i=1;$i<=7;$i++)
			{
			    ${"edit_autoreply_scenario_param".$i} = strtoupper(get_var("edit_autoreply_scenario_param$i",array('POST','GET')));
			    if(${"edit_autoreply_scenario_param".$i})
			    {
			    	$ok++;
			    }
			}

			if ($edit_autoreply_scenario_result  && ($ok > 0))
			{
			    for ($i=1;$i<=7;$i++)
			    {
					$autoreply_scenario_param_list .= "autoreply_scenario_param$i='".${"edit_autoreply_scenario_param".$i}."',";
			    }
			    $sql = "
				UPDATE phpgw_sms_featautoreply_scenario 
				SET ".$autoreply_scenario_param_list."autoreply_scenario_result='$edit_autoreply_scenario_result' 
				WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'
			    ";

				$this->db->transaction_begin();
				$this->db->query($sql,__LINE__,__FILE__);
				if ($this->db->affected_rows())
			    {
					$error_string = "SMS autoreply scenario has been edited";
			    }
			    else
			    {
			        $error_string = "Fail to edit SMS autoreply scenario";
			    }

				$this->db->transaction_commit();

			}
			else
			{
			    $error_string = "You must fill at least one field and the scenario result!";
			}

			$target = 'edit_scenario';
			
			$add_data = array(
				'menuaction'				=> $this->currentapp.'.uiautoreply.' . $target,
				'autoreply_id' 				=> $autoreply_id,
				'autoreply_scenario_id'			=> $autoreply_scenario_id,
				'err' 					=> urlencode($error_string),
				'edit_autoreply_scenario_result'	=> $edit_autoreply_scenario_result
				);

			for ($i=1;$i<=7;$i++)
			{
			    $add_data["edit_autoreply_scenario_param".$i] = strtoupper(get_var("edit_autoreply_scenario_param$i",array('POST')));
			}

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);

		}


		function manage()
		{		
			if(!$this->acl->check($this->acl_location, 16))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
					
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Manage SMS autoreply');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();


			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$err	= urldecode(get_var('err',array('POST','GET')));
	
/*			if (!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
			    $query_user_only = "AND uid='$uid'";
			}
*/
			$sql = "SELECT * FROM phpgw_sms_featautoreply WHERE autoreply_id='$autoreply_id' $query_user_only";
	
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$manage_autoreply_code = $this->db->f('autoreply_code');
			$o_uid = $this->db->f('uid');
			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.add_scenario',
				'autoreply_id'	=> $autoreply_id
				);
				
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$content .= "
			    <p>
			    <p>SMS autoreply code: <b>$manage_autoreply_code</b>
	    		<p>";

			$content .= "
			    <p>
			    <a href=\"$add_url\">[ Add SMS autoreply scenario ]</a>
			    <p>
			";

			$sql = "SELECT * FROM phpgw_sms_featautoreply_scenario WHERE autoreply_id='$autoreply_id' ORDER BY autoreply_scenario_param1";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
			    $owner = $GLOBALS['phpgw']->accounts->id2name($o_uid);
			    $list_of_param = "";
			    for ($i=1;$i<=7;$i++)
			    { 
					$list_of_param .= $this->db->f("autoreply_scenario_param$i")."&nbsp;";
			    }

				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiautoreply.edit_scenario', 'autoreply_id'=> $this->db->f('autoreply_id'), 'autoreply_scenario_id'=> $this->db->f('autoreply_scenario_id'))) . ">e</a>] ";
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiautoreply.delete_scenario', 'autoreply_id'=> $this->db->f('autoreply_id'), 'autoreply_scenario_id'=> $this->db->f('autoreply_scenario_id'))) . ">x</a>] ";
				$content .= " <b>Param:</b> ".$list_of_param."&nbsp;<br><b>Return:</b> ".$this->db->f('autoreply_scenario_result')."&nbsp;&nbsp;<b>User:</b> $owner<br><br>";
			}
			$content .= "
			    <p>
			    <a href=\"$add_url\">[ Add SMS autoreply scenario ]</a>
			    <p>
			";

			$done_data = array(
				'menuaction'	=> $this->currentapp.'.uiautoreply.index');
				
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p><li>
			    <a href=\"$done_url\">Back</a>
			    <p>
			";
			echo $content;
		
		}

		function delete()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_DELETE))
			{
				$links = $this->menu->links();
				$this->bocommon->no_access($links);
				return;
			}

			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));
			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uiautoreply.index',
				'autoreply_id' => $autoreply_id
			);

			if (get_var('confirm',array('POST')))
			{
			//	$this->bo->delete_type($autoreply_id);

				$sql = "SELECT autoreply_code FROM phpgw_sms_featautoreply WHERE autoreply_id='$autoreply_id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$code_name = $this->db->f('autoreply_code');

				if ($code_name)
				{
					$sql = "DELETE FROM phpgw_sms_featautoreply WHERE autoreply_code='$code_name'";
					$this->db->transaction_begin();
					$this->db->query($sql,__LINE__,__FILE__);
					if ($this->db->affected_rows())
	    				{
						$receipt['message'][]=array('msg'=>lang('SMS autoreply code %1 has been deleted!', $code_name));
						$error_string = "SMS autoreply code `$code_name` has been deleted!";
					}
					else
	    				{
						$receipt['message'][]=array('msg'=>lang('Fail to delete SMS autoreply code') . ' ' . $code_name);
						$error_string = "Fail to delete SMS autoreply code `$code_name`";
	    				}
	    				$this->db->transaction_commit();
				}
					
				$link_data['err'] = urlencode($error_string);
				$GLOBALS['phpgw']->session->appsession('session_data','sms_reply_receipt',$receipt);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}


			
			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.delete', 'autoreply_id'=> $autoreply_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('autoreply');
			$function_msg	= lang('delete autoreply');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_scenario()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_DELETE))
			{
				$links = $this->menu->links();
				$this->bocommon->no_access($links);
				return;
			}

			$autoreply_scenario_id	= get_var('autoreply_scenario_id',array('POST','GET'));
			$autoreply_id	= get_var('autoreply_id',array('POST','GET'));

			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uiautoreply.manage',
				'autoreply_id' => $autoreply_id
			);

			if (get_var('confirm',array('POST')))
			{
				$sql = "SELECT autoreply_scenario_result FROM phpgw_sms_featautoreply_scenario WHERE autoreply_scenario_id='$autoreply_scenario_id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$scenario_result = $this->db->f('autoreply_scenario_result');

				if ($scenario_result)
				{
				    $sql = "DELETE FROM phpgw_sms_featautoreply_scenario WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'";

					$this->db->transaction_begin();
					$this->db->query($sql,__LINE__,__FILE__);
					if ($this->db->affected_rows())
				    {
						$error_string = "SMS autoreply scenario result `$scenario_result` has been deleted!";
				    }
				    else
				    {
						$error_string = "Fail to delete SMS autoreply scenario result `$scenario_result`";
				    }
				}

				$link_data['err'] = urlencode($error_string);

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uiautoreply.delete_scenario', 'autoreply_id'=> $autoreply_id, 'autoreply_scenario_id'=> $autoreply_scenario_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('autoreply');
			$function_msg	= lang('delete autoreply');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

	}
?>
