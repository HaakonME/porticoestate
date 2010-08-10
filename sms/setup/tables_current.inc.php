<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage setup
	* @version $Id$
	*/


	$phpgw_baseline = array(
		'phpgw_sms_featautoreply' => array(
			'fd' => array(
				'autoreply_id' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'autoreply_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False)
			),
			'pk' => array('autoreply_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featautoreply_log' => array(
			'fd' => array(
				'autoreply_log_id' => array('type' => 'auto','nullable' => False),
				'sms_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'autoreply_log_datetime' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'autoreply_log_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'autoreply_log_request' => array('type' => 'varchar', 'precision' => 160,'nullable' => False)
			),
			'pk' => array('autoreply_log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featautoreply_scenario' => array(
			'fd' => array(
				'autoreply_scenario_id' => array('type' => 'auto','nullable' => False),
				'autoreply_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'autoreply_scenario_param1' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'autoreply_scenario_param2' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_param3' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_param4' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_param5' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_param6' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_param7' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
				'autoreply_scenario_result' => array('type' => 'varchar', 'precision' => 130,'nullable' => False)
			),
			'pk' => array('autoreply_scenario_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featboard' => array(
			'fd' => array(
				'board_id' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'board_code' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'board_forward_email' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'board_pref_template' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('board_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featcommand' => array(
			'fd' => array(
				'command_id' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'command_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'command_exec' => array('type' => 'text','nullable' => False),
				'command_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => true),
				'command_descr' => array('type' => 'text', 'nullable' => true)
			),
			'pk' => array('command_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featcommand_log' => array(
			'fd' => array(
				'command_log_id' => array('type' => 'auto','nullable' => False),
				'sms_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'command_log_datetime' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'command_log_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'command_log_exec' => array('type' => 'text','nullable' => False),
				'command_log_param' => array('type' => 'varchar', 'precision' => 150,'nullable' => True),
				'command_log_success' => array('type' => 'int', 'precision' => 2,'nullable' => True),
			),
			'pk' => array('command_log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featcustom' => array(
			'fd' => array(
				'custom_id' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'custom_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'custom_url' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('custom_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featcustom_log' => array(
			'fd' => array(
				'custom_log_id' => array('type' => 'auto','nullable' => False),
				'sms_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'custom_log_datetime' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'custom_log_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'custom_log_url' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('custom_log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featpoll' => array(
			'fd' => array(
				'poll_id' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'poll_title' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'poll_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'poll_enable' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('poll_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featpoll_choice' => array(
			'fd' => array(
				'choice_id' => array('type' => 'auto','nullable' => False),
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'choice_title' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'choice_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False)
			),
			'pk' => array('choice_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_featpoll_result' => array(
			'fd' => array(
				'result_id' => array('type' => 'auto','nullable' => False),
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'choice_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'poll_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False)
			),
			'pk' => array('result_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_gwmodclickatell_apidata' => array(
			'fd' => array(
				'apidata_id' => array('type' => 'auto','nullable' => False),
				'smslog_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'apimsgid' => array('type' => 'varchar', 'precision' => 100,'nullable' => False)
			),
			'pk' => array('apidata_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

/*		'phpgw_sms_gwmodclickatell_config' => array(
			'fd' => array(
				'cfg_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => True,'default' => 'gnokii'),
				'cfg_api_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_username' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_password' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_send_url' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_incoming_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_credit' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_gwmodgnokii_config' => array(
			'fd' => array(
				'cfg_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'cfg_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_gwmodkannel_config' => array(
			'fd' => array(
				'cfg_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_incoming_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_username' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_password' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_global_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_bearerbox_host' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_sendsms_port' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'cfg_phpgwsms_web' => array('type' => 'varchar', 'precision' => 250,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
*/
		'phpgw_sms_gwmodkannel_dlr' => array(
			'fd' => array(
				'kannel_dlr_id' => array('type' => 'auto','nullable' => False),
				'smslog_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'kannel_dlr_type' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
			),
			'pk' => array('kannel_dlr_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

/*		'phpgw_sms_gwmodtemplate_config' => array(
			'fd' => array(
				'cfg_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'cfg_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
*/
		'phpgw_sms_gwmoduplink' => array(
			'fd' => array(
				'up_id' => array('type' => 'auto','nullable' => False),
				'up_local_slid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'up_remote_slid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'up_status' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
			),
			'pk' => array('up_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
/*
		'phpgw_sms_gwmoduplink_config' => array(
			'fd' => array(
				'cfg_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_master' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_username' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_password' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'cfg_global_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_incoming_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tblconfig_main' => array(
			'fd' => array(
				'cfg_web_title' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_email_service' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_email_footer' => array('type' => 'varchar', 'precision' => 250,'nullable' => True),
				'cfg_gateway_module' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'cfg_gateway_number' => array('type' => 'varchar', 'precision' => 100,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
*/
		'phpgw_sms_tblsmsincoming' => array(
			'fd' => array(
				'in_id' => array('type' => 'auto','nullable' => False),
				'in_gateway' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'in_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'in_masked' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'in_code' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'in_msg' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
				'in_datetime' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('in_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tblsmsoutgoing' => array(
			'fd' => array(
				'smslog_id' => array('type' => 'auto','nullable' => False),
				'flag_deleted' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'p_gateway' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'p_src' => array('type' => 'varchar', 'precision' => 100,'nullable' => true),
				'p_dst' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'p_footer' => array('type' => 'varchar', 'precision' => 11,'nullable' => true),
				'p_msg' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'p_datetime' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'p_update' => array('type' => 'timestamp', 'precision' => 20,'nullable' => true),
				'p_status' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'p_gpid' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'p_credit' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'p_sms_type' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'unicode' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
			),
			'pk' => array('smslog_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tblsmstemplate' => array(
			'fd' => array(
				'tid' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				't_title' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				't_text' => array('type' => 'varchar', 'precision' => 130,'nullable' => False)
			),
			'pk' => array('tid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
/*
		'phpgw_sms_tbluser' => array(
			'fd' => array(
				'uid' => array('type' => 'auto','nullable' => False),
				'status' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'ticket' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'username' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'password' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'mobile' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'email' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'sender' => array('type' => 'varchar', 'precision' => 30,'nullable' => False),
				'dailysms' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'gender' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'age' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'address' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'city' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'state' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'country' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'birthday' => array('type' => 'varchar', 'precision' => 10,'nullable' => False,'default' => '0000-00-00'),
				'marital' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'education' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
				'zipcode' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'junktimestamp' => array('type' => 'varchar', 'precision' => 30,'nullable' => False)
			),
			'pk' => array('uid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
*/
		'phpgw_sms_tblusergroupphonebook' => array(
			'fd' => array(
				'gpid' => array('type' => 'auto','nullable' => False),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'gp_name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'gp_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False)
			),
			'pk' => array('gpid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tblusergroupphonebook_public' => array(
			'fd' => array(
				'gpidpublic' => array('type' => 'auto','nullable' => False),
				'gpid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'uid' => array('type' => 'varchar', 'precision' => 100,'nullable' => False)
			),
			'pk' => array('gpidpublic'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tbluserinbox' => array(
			'fd' => array(
				'in_id' => array('type' => 'auto','nullable' => False),
				'in_sender' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'in_uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'in_msg' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
				'in_datetime' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'in_hidden' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
			),
			'pk' => array('in_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_sms_tbluserphonebook' => array(
			'fd' => array(
				'pid' => array('type' => 'auto','nullable' => False),
				'gpid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'p_num' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'p_desc' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'p_email' => array('type' => 'varchar', 'precision' => 250,'nullable' => False)
			),
			'pk' => array('pid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

/*
		'phpgw_sms_tbluser_country' => array(
			'fd' => array(
				'country_id' => array('type' => 'auto','nullable' => False),
				'country_name' => array('type' => 'varchar', 'precision' => 200,'nullable' => False)
			),
			'pk' => array('country_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
*/
/*
		'phpgw_sms_config_type' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sms_config_attrib' => array(
			'fd' => array(
				'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'input_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
			),
			'pk' => array('type_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sms_config_choice' => array(
			'fd' => array(
				'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'value' => array('type' => 'varchar', 'precision' => 20,'nullable' => False)
			),
			'pk' => array('type_id','attrib_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('type_id','attrib_id','value')
		),
		'phpgw_sms_config_value' => array(
			'fd' => array(
				'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'value' => array('type' => 'varchar', 'precision' => 200,'nullable' => False)
			),
			'pk' => array('type_id','attrib_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('type_id','attrib_id','value')
		),
*/

	);
