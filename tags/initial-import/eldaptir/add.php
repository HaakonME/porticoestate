<?php
	/**************************************************************************\
	* phpGroupWare - - eLDAPtir - LDAP Administration                          *
	* http://www.phpgroupware.org                                              *
	* Written by Miles Lott <milosch@phpgroupware.org>                         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: add.php,v 1.9 2001/11/19 16:08:46 milosch Exp $ */

	$dn = $HTTP_GET_VARS['dn'];
	$ou = $HTTP_GET_VARS['ou'];
	$submit = $HTTP_POST_VARS['submit'];

	$GLOBALS['phpgw_info']['flags'] = array(
		'enable_nextmatchs_class' => True,
		'currentapp'              => 'eldaptir',
		'parent_page'             => 'viewou.php'
	);
	include('../header.inc.php');

	$servers = servers();
	$server_type = $servers[$server_id]['type'];
	$ldapobj = CreateObject('eldaptir.ldap',$servers[$server_id]);
	$ldapobj->DEBUG = 1;

	if ($submit && $dn)
	{
		// This doesn't touch LDAP yet, just displays what might occur
		echo "<br>Form values:";
		$thisdn = urldecode($dn);
		$entry  = $ldapobj->read($thisdn);
		while (list($key,$objectclass) = each($entry[0]['objectclass']))
		{
			$object = strtolower($objectclass);
			if($ldapobj->$object)
			{
				echo "<br><br>Checking: ".$object;
				while(list($attrib,$req) = @each($ldapobj->$object))
				{
					$lattrib = strtolower($attrib);		
					if($ldapobj->DEBUG)
					{
						echo "<br>". $object."[".$lattrib."] = "
							. ${$object}[$lattrib];
					}

					if (${$object}[$lattrib] && !$ldapobj->entry[$lattrib])
					{
						$ldapobj->entry[$lattrib] = ${$object}[$lattrib];
					}
				}
			}
		}
		if($ldapobj->DEBUG) { echo "<br><br>Entry values:"; }
		while (list($key,$val) = each($entry[0]))
		{
			if($ldapobj->clean($val))
			{
				if (is_array($val))
				{
					while(list($v) = each($val))
					{
						if($ldapobj->clean($v))
						{
							if($ldapobj->DEBUG) { echo '<br>arr '.$v.': '.$entry[0][$v][0]."\n"; }
						}
					}
				}
				else
				{
					if($ldapobj->DEBUG) { echo '<br>str '.$val.': '.$entry[0][$val][0]."\n"; }
				}
			}
		}
		//$GLOBALS['phpgw']->common->phpgw_footer();
		//exit;
		//...
		$ldapobj->add($thisdn);
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->exit();
	}

	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file(array('add' => 'add.tpl'));
	$GLOBALS['phpgw']->template->set_block('add','header','header');
	$GLOBALS['phpgw']->template->set_block('add','row','row');
	$GLOBALS['phpgw']->template->set_block('add','footer','footer');

	$GLOBALS['phpgw']->template->set_var('title','<a href="'.$GLOBALS['phpgw']->link('/eldaptir','server_id='.$server_id).'">'.lang('eldaptir')."</a>\n");
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('tr_color1',$GLOBALS['phpgw_info']['theme']['row_on']);
	$GLOBALS['phpgw']->template->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
	$GLOBALS['phpgw']->template->set_var('lang_addobj',lang('Add').' '.lang('Objectclass'));
	$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Submit'));
	$GLOBALS['phpgw']->template->set_var('lang_dn',lang('dn'));
	$GLOBALS['phpgw']->template->set_var('lang_obj',lang('Objectclass'));
	$GLOBALS['phpgw']->template->set_var('lang_attr',lang('Attribute'));
	$GLOBALS['phpgw']->template->set_var('lang_value',lang('Value'));
	$GLOBALS['phpgw']->template->set_var('lang_rule',lang('Rule'));
	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/eldaptir/add.php','server_id='.$server_id.'&ou='.$ou));
	//$GLOBALS['phpgw']->template->set_var('addobj_url',$GLOBALS['phpgw']->link('/eldaptir/add.php','ou='.$ou.'&dn='.$dn.'&server_id='.$server_id));
	// following needs to work properly first
	$GLOBALS['phpgw']->template->set_var('addobj_url',$GLOBALS['phpgw']->link('/eldaptir/addobj.php','dn='.$dn));
	$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name ="dn" value="'.$dn.'">');
	$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/eldaptir/viewou.php','ou='.$ou.'&nisMapName='.$nisMapName.'&server_id='.$server_id));

	if ($addobj)
	{
		while(list($key,$oc) = each($ldapobj->objectclasses))
		{
			eval("if \(\$\$oc=='on'\) { \$newoc \.= \$oc\.';'; }");
		}
		$addto = explode(';',$newoc);
		$ldapobj->form_addobj($dn,$addto);
	}

	$userData = $ldapobj->create($ou,$nisMapName);
	$thisdn   = '<input size="30" name="dn" value="'.$ldapobj->objkey.'=,ou='.$ou.','.$ldapobj->base.'">';
	$GLOBALS['phpgw']->template->set_var('dn',$thisdn);
	$GLOBALS['phpgw']->template->pparse('out','header');

	while (list($key,$objectclass) = each($userData[0]['objectclass']))
	{
		if (gettype($objectclass) == 'string')
		{
			$object = strtolower($objectclass);
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
			$GLOBALS['phpgw']->template->set_var('objectclass',$object);
			$GLOBALS['phpgw']->template->set_var('row_name','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('row_value','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('row_rule','&nbsp;');
			$GLOBALS['phpgw']->template->parse('rows','row',True);
			$GLOBALS['phpgw']->template->pparse('out','row');
			if (is_array($ldapobj->$object))
			{
				@reset($ldapobj->$object);
				while(list($attrib,$req) = each($ldapobj->$object))
				{
					$lattrib = strtolower($attrib);
					if ($req) { $required = lang('required'); }
					else { $required = lang('optional'); }
					if ($userData[0][$lattrib][0] || $userData[0][$attrib][0])
					{
						if ($userData[0][$attrib])
						{
							$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
							$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
							$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
							$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
							$GLOBALS['phpgw']->template->set_var('row_value','<input size="30" name="'.$object.'['.$attrib.']" value="'.$userData[0][$attrib][0].'">');
							$GLOBALS['phpgw']->template->set_var('row_rule',$required);
							$GLOBALS['phpgw']->template->parse('rows','row',True);
						}
						else
						{
							$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
							$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
							$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
							$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
							$GLOBALS['phpgw']->template->set_var('row_value','<input size="30" name="'.$object.'['.$lattrib.']" value="'.$userData[0][$lattrib][0].'">');
							$GLOBALS['phpgw']->template->set_var('row_rule',$required);
							$GLOBALS['phpgw']->template->parse('rows','row',True);
						}
					}
					else
					{
						$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
						$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
						$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
						$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
						$GLOBALS['phpgw']->template->set_var('row_value','<input size="30" name="'.$object.'['.$attrib.']" value="">');
						$GLOBALS['phpgw']->template->set_var('row_rule',$required);
						$GLOBALS['phpgw']->template->parse('rows','row',True);
					}
					$GLOBALS['phpgw']->template->pparse('out','row');
				}
			}
		}
	}

	$GLOBALS['phpgw']->template->pparse('out','footer');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
