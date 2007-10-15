<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage custom
 	* @version $Id: class.uicustom.inc.php,v 1.5 2006/12/27 10:39:15 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_uicustom
	{
		var $public_functions = array(
			'index'			=> True,
			'add'			=> True,
			'add_yes'		=> True,
			'edit'			=> True,
			'edit_yes'		=> True,
			'delete'		=> True,
			
			);


		function sms_uicustom()
		{

			$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
		//	$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
		//	$this->bo				= CreateObject($this->currentapp.'.boconfig',true);
			$this->bocommon				= CreateObject($this->currentapp.'.bocommon');
			$this->menu				= CreateObject($this->currentapp.'.menu');
			$this->sms				= CreateObject($this->currentapp.'.sms');
			$this->acl				= CreateObject('phpgwapi.acl');
			$this->acl_location 			= '.custom';
			$this->menu->sub			=$this->acl_location;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			
			$this->db 				= clone($GLOBALS['phpgw']->db);
			$this->db2 				= clone($GLOBALS['phpgw']->db);
		}

		function index()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
			
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('List/Edit/Delete SMS customs');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
	
			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$add_data = array('menuaction'	=> $this->currentapp.'.uicustom.add');
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS custom ]</a>
			    <p>
			";
/*			if (!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
			    $query_user_only = "WHERE uid='" . $this->account ."'";
			}
*/
			$sql = "SELECT * FROM phpgw_sms_featcustom $query_user_only ORDER BY custom_code";
			$this->db->query($sql,__LINE__,__FILE__);	
			while ($this->db->next_record())
			{
				$owner = $GLOBALS['phpgw']->accounts->id2name($this->db->f('uid'));
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicustom.edit' , 'custom_id'=> $this->db->f('custom_id'))) . ">e</a>] ";
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicustom.delete', 'custom_id'=> $this->db->f('custom_id'))) . ">x</a>] ";
			    $content .= "<b>Code:</b> " . $this->db->f('custom_code') . " &nbsp;&nbsp;<b>User:</b> $owner<br><b>URL:</b><br>" . stripslashes($this->db->f('custom_url')) . "<br><br>";
			}

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS custom ]</a>
			    <p>
			";

				$done_data = array(
				'menuaction'	=> $this->currentapp.'.uisms.index');
				
				$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

				$content .= "
				    <p><li>
				    <a href=\"$done_url\">Back</a>
				    <p>
				";

			echo $content;	
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
			
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Add SMS custom');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
			$custom_code	= get_var('custom_code',array('POST','GET'));
			$custom_url	= get_var('custom_url',array('POST','GET'));

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uicustom.add_yes',
				'autoreply_id' => $autoreply_id
				);
				
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS custom code: <input type=text size=10 maxlength=10 name=custom_code value=\"$custom_code\">
			    <p>Pass these parameter to custom URL field:
			    <p>##SMSDATETIME## replaced by SMS incoming date/time
			    <p>##SMSSENDER## replaced by sender number
			    <p>##CUSTOMCODE## replaced by custom code 
			    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
			    <p>SMS custom URL: <input type=text size=60 maxlength=200 name=custom_url value=\"$custom_url\">
			    <p><input type=submit class=button value=Add>
			    </form>
			";

			$done_data = array('menuaction'	=> $this->currentapp.'.uicustom.index');
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

			$custom_code	= strtoupper(get_var('custom_code',array('POST','GET')));
			$custom_url	= get_var('custom_url',array('POST','GET'));

			$uid = $this->account;
			$target = 'add';

			if ($custom_code && $custom_url)
			{
			    if ($this->sms->checkavailablecode($custom_code))
				{
					$custom_url = $this->db->db_addslashes($custom_url);

					$sql = "INSERT INTO phpgw_sms_featcustom (uid,custom_code,custom_url) VALUES ('$uid','$custom_code','$custom_url')";
					$this->db->transaction_begin();

					$this->db->query($sql,__LINE__,__FILE__);

					$new_uid = $this->db->get_last_insert_id(phpgw_sms_featcustom,'custom_id');

					$this->db->transaction_commit();
					
					if ($new_uid)
					{
			    	    $error_string = "SMS custom code `$custom_code` has been added";
					}
					else
					{
			    	    $error_string = "Fail to add SMS custom code `$custom_code`";
					}
			    }
			    else
			    {
					$error_string = "SMS code `$custom_code` already exists, reserved or use by other feature!";
			    }
			}
			else
			{
			    $error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uicustom.' . $target,
				'err' => urlencode($error_string)
				);

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
		}


		function edit()
		{	
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}
		
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Edit SMS custom');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(get_var('err',array('POST','GET')));
			$custom_id	= get_var('custom_id',array('POST','GET'));
			$custom_code	= get_var('custom_code',array('POST','GET'));
			$custom_url	= get_var('custom_url',array('POST','GET'));

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$sql = "SELECT * FROM phpgw_sms_featcustom WHERE custom_id='$custom_id'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_code = $this->db->f('custom_code');

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uicustom.edit_yes',
				'custom_id' => $custom_id,
				'custom_code' => $custom_code,
				);
				
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$custom_url = stripslashes($this->db->f('custom_url'));

		//	PHPGW_SERVER_ROOT . SEP . 'sms' . SEP . 'bin';
		//	$custom_url = str_replace($feat_custom_path['bin'],'',$custom_url);

			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS custom code: <b>$custom_code</b>
			    <p>Pass these parameter to custom URL field:
			    <p>##SMSDATETIME## replaced by SMS incoming date/time
			    <p>##SMSSENDER## replaced by sender number
			    <p>##CUSTOMCODE## replaced by custom code 
			    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
			    <p>SMS custom URL: <input type=text size=60 name=custom_url value=\"$custom_url\">
			    <p><input type=submit class=button value=Save>
			    </form>
			";

			$done_data = array('menuaction'	=> $this->currentapp.'.uicustom.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function edit_yes()
		{
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT))
			{
				$links = $this->menu->links();
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
				$this->bocommon->no_access($links);
				return;
			}

			$custom_id	= get_var('custom_id',array('POST','GET'));
			$custom_code	= get_var('custom_code',array('POST','GET'));
			$custom_url	= get_var('custom_url',array('POST','GET'));

			$uid = $this->account;
			$target = 'edit';

			if ($custom_id && $custom_code && $custom_url)
			{

				$custom_url = $this->db->db_addslashes($custom_url);

				$sql = "UPDATE phpgw_sms_featcustom SET custom_url='$custom_url' WHERE custom_code='$custom_code'";
				$this->db->transaction_begin();
				$this->db->query($sql,__LINE__,__FILE__);
				if ($this->db->affected_rows()>0)
				{
					$error_string = "SMS custom code `$custom_code` has been saved";
				}
				else
				{
			   	    $error_string = "Fail to save SMS custom code `$custom_code`";
				}
				$this->db->transaction_commit();
			}
			else
			{
			    $error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction'	=> $this->currentapp.'.uicustom.' . $target,
				'custom_id' => $custom_id,
				'err' => urlencode($error_string)
				);

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
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

			$custom_id	= get_var('custom_id',array('POST','GET'));
			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uicustom.index'
			);

			if (get_var('confirm',array('POST')))
			{
			//	$this->bo->delete_type($autoreply_id);

				$sql = "SELECT custom_code FROM phpgw_sms_featcustom WHERE custom_id='$custom_id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$custom_code = $this->db->f('custom_code');

				if ($custom_code)
				{
					$sql = "DELETE FROM phpgw_sms_featcustom WHERE custom_code='$custom_code'";
					$this->db->transaction_begin();
					$this->db->query($sql,__LINE__,__FILE__);
					if ($this->db->affected_rows())
	    			{
						$error_string = "SMS custom code `$custom_code` has been deleted!";
					}
					else
					{
						$error_string = "Fail to delete SMS custom code `$custom_code`";
					
					}

	    			$this->db->transaction_commit();
				}
					
				$link_data['err'] = urlencode($error_string);

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.delete', 'custom_id'=> $custom_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$function_msg	= lang('delete SMS custom code');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
?>
