<?php
	/**
	* Setup
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage setup
	* @version $Id: tables_current.inc.php,v 1.72 2006/12/26 07:20:49 skwashd Exp $
	* @internal $Source: /sources/phpgwapi/phpgwapi/setup/tables_current.inc.php,v $
	*/

	$phpgw_baseline = array(
		'phpgw_config' => array(
			'fd' => array(
				'config_app' => array('type' => 'varchar','precision' => '50'),
				'config_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'config_value' => array('type' => 'text')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('config_app','config_name'),
			'uc' => array('config_name')
		),
		'phpgw_applications' => array(
			'fd' => array(
				'app_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'app_name' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'app_enabled' => array('type' => 'int','precision' => '4','nullable' => False),
				'app_order' => array('type' => 'int','precision' => '4','nullable' => False),
				'app_tables' => array('type' => 'text','nullable' => False),
				'app_version' => array('type' => 'varchar','precision' => '20','nullable' => False,'default' => '0.0')
			),
			'pk' => array('app_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('app_name')
		),
		'phpgw_acl' => array(
			'fd' => array(
				'acl_appname' => array('type' => 'varchar','precision' => '50'),
				'acl_location' => array('type' => 'varchar','precision' => '255'),
				'acl_account' => array('type' => 'int','precision' => '4'),
				'acl_rights' => array('type' => 'int','precision' => '4'),
				'acl_grantor' => array('type' => 'int','precision' => '4','nullable' => True),
				'acl_type' => array('type' => 'int','precision' => '2','nullable' => True,'default' => '0')
			),
			'pk' => array(),
			'ix' => array('acl_location','acl_account'),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_acl_location' => array(
			'fd' => array(
				'appname' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'id' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'allow_grant' => array('type' => 'int','precision' => '2','nullable' => True),
				'allow_c_attrib' => array('type' => 'int','precision' => '2','nullable' => True),
				'c_attrib_table' => array('type' => 'varchar','precision' => '25','nullable' => True)
			),
			'pk' => array('appname','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_accounts' => array(
			'fd' => array(
				'account_id' => array('type' => 'auto','nullable' => False),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => False),
				'account_firstname' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'account_lastname' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'account_permissions' => array('type' => 'text','nullable' => True),
				'account_groups' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'account_lastlogin' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lastloginfrom' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'account_lastpwd_change' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_status' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'A'),
				'account_expires' => array('type' => 'int','precision' => '4','nullable' => False),
				'account_type' => array('type' => 'char','precision' => '1','nullable' => True),
				'person_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_quota' => array('type' => 'int','precision' => '4','default' => '-1','nullable' => True)
			),
			'pk' => array('account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('account_lid')
		),
		'phpgw_preferences' => array(
			'fd' => array(
				'preference_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'preference_app' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'preference_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('preference_owner','preference_app'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sessions' => array(
			'fd' => array(
				'session_id' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'session_lid' => array('type' => 'varchar','precision' => '255'),
				'session_ip' => array('type' => 'varchar','precision' => '255'),
				'session_logintime' => array('type' => 'int','precision' => '4'),
				'session_dla' => array('type' => 'int','precision' => '4'),
				'session_action' => array('type' => 'varchar','precision' => '255'),
				'session_flags' => array('type' => 'char','precision' => '2')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('session_id')
		),
		'phpgw_app_sessions' => array(
			'fd' => array(
				'sessionid' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'loginid' => array('type' => 'varchar','precision' => '20'),
				'location' => array('type' => 'varchar','precision' => '255'),
				'app' => array('type' => 'varchar','precision' => '20'),
				'content' => array('type' => 'text'),
				'session_dla' => array('type' => 'int','precision' => '4')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_access_log' => array(
			'fd' => array(
				'sessionid' => array('type' => 'char','precision' => '32','nullable' => False),
				'loginid' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'ip' => array('type' => 'varchar','precision' => '50','nullable' => False,'default' => '::1'),
				'li' => array('type' => 'int','precision' => '4','nullable' => False),
				'lo' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '0'),
				'account_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hooks' => array(
			'fd' => array(
				'hook_id' => array('type' => 'auto','nullable' => False),
				'hook_appname' => array('type' => 'varchar','precision' => '255'),
				'hook_location' => array('type' => 'varchar','precision' => '255'),
				'hook_filename' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('hook_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_languages' => array(
			'fd' => array(
				'lang_id' => array('type' => 'varchar','precision' => '2','nullable' => False),
				'lang_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'available' => array('type' => 'char','precision' => '3','nullable' => False,'default' => 'No')
			),
			'pk' => array('lang_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_lang' => array(
			'fd' => array(
				'message_id' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => ''),
				'app_name' => array('type' => 'varchar','precision' => '25','nullable' => False,'default' => 'common'),
				'lang' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => ''),
				'content' => array('type' => 'text')
			),
			'pk' => array('message_id','app_name','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_nextid' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => True),
				'appname' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('appname')
		),
		'phpgw_categories' => array(
			'fd' => array(
				'cat_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'cat_main' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'cat_parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'cat_level' => array('type' => 'int','precision' => '2','default' => '0','nullable' => False),
				'cat_owner' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'cat_access' => array('type' => 'varchar','precision' => '7'),
				'cat_appname' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'cat_name' => array('type' => 'varchar','precision' => '150','nullable' => False),
				'cat_description' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'cat_data' => array('type' => 'text'),
				'last_mod' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_addressbook' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'lid' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'tid' => array('type' => 'char','precision' => '1','nullable' => True),
				'owner' => array('type' => 'int','precision' => '8','nullable' => True),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'cat_id' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'fn' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'n_family' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'n_given' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'n_middle' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'n_prefix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'n_suffix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'sound' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'bday' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'note' => array('type' => 'text','nullable' => True),
				'tz' => array('type' => 'varchar','precision' => '8','nullable' => True),
				'geo' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'url' => array('type' => 'varchar','precision' => '128','nullable' => True),
				'pubkey' => array('type' => 'text','nullable' => True),
				'org_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'org_unit' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'title' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_street' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_locality' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_region' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_postalcode' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_countryname' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_one_type' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'label' => array('type' => 'text','nullable' => True),
				'adr_two_street' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_two_locality' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_two_region' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_two_postalcode' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_two_countryname' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'adr_two_type' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'tel_work' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_home' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_voice' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_fax' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_msg' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_cell' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_pager' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_bbs' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_modem' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_car' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_isdn' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_video' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => '+1 (000) 000-0000'),
				'tel_prefer' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'email' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'email_type' => array('type' => 'varchar','precision' => '32','nullable' => False,'default' => 'INTERNET'),
				'email_home' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'email_home_type' => array('type' => 'varchar','precision' => '32','nullable' => False,'default' => 'INTERNET'),
				'last_mod' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_addressbook_extra' => array(
			'fd' => array(
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_owner' => array('type' => 'int','precision' => '8','nullable' => True),
				'contact_name' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'contact_value' => array('type' => 'text','nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_log' => array(
			'fd' => array(
				'log_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'log_date' => array('type' => 'timestamp','nullable' => False),
				'log_account_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'log_account_lid' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'log_app' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'log_severity' => array('type' => 'char','precision' => '1','nullable' => False),
				'log_file' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => ''),
				'log_line' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'log_msg' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_interserv' => array(
			'fd' => array(
				'server_id' => array('type' => 'auto','nullable' => False),
				'server_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'server_host' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'server_url' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'trust_level' => array('type' => 'int','precision' => '4'),
				'trust_rel' => array('type' => 'int','precision' => '4'),
				'username' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'password' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'admin_name' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'admin_email' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'server_mode' => array('type' => 'varchar','precision' => '16','nullable' => False,'default' => 'xmlrpc'),
				'server_security' => array('type' => 'varchar','precision' => '16','nullable' => True)
			),
			'pk' => array('server_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_vfs' => array(
			'fd' => array(
				'file_id' => array('type' => 'auto','nullable' => False),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'createdby_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'modifiedby_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'created' => array('type' => 'date','nullable' => False,'default' => '1970-01-01'),
				'modified' => array('type' => 'date','nullable' => True),
				'size' => array('type' => 'int','precision' => '4','nullable' => True),
				'mime_type' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'deleteable' => array('type' => 'char','precision' => '1','nullable' => True,'default' => 'Y'),
				'comment' => array('type' => 'text','nullable' => True),
				'app' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'directory' => array('type' => 'text','nullable' => True),
				'name' => array('type' => 'text','nullable' => False),
				'link_directory' => array('type' => 'text','nullable' => True),
				'link_name' => array('type' => 'text','nullable' => True),
				'version' => array('type' => 'varchar','precision' => '30','nullable' => False,'default' => '0.0.0.0'),
				'content' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('file_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_history_log' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_async' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'next' => array('type' => 'int','precision' => '4','nullable' => False),
				'times' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'method' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'data' => array('type' => 'text','nullable' => False),
				'account_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_contact' => array(
			'fd' => array(
				'contact_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'cat_id' => array('type' => 'varchar','precision' => '200','nullable' => True),
				'contact_type_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_id'),
			'fk' => array(),
			'ix' => array('owner','access','contact_type_id'),
			'uc' => array()
		),
		'phpgw_contact_person' => array(
			'fd' => array(
				'person_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'first_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'last_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'middle_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'prefix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'suffix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'birthday' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'pubkey' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'department' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'initials' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'sound' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'active' => array('type' => 'char','precision' => '1','nullable' => True,'default' => 'Y'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('person_id'),
			'uc' => array()
		),
		'phpgw_contact_org' => array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'active' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'Y'),
				'parent' => array('type' => 'int','precision' => '4','nullable' => True),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('org_id','active'),
			'uc' => array()
		),
		'phpgw_contact_org_person' => array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'person_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'addr_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('org_id','person_id'),
			'fk' => array(),
			'ix' => array('addr_id','person_id','org_id','preferred'),
			'uc' => array()
		),
		'phpgw_contact_addr' => array(
			'fd' => array(
				'contact_addr_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'addr_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'add1' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add2' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add3' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'city' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'state' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'postal_code' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'country' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'tz' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_addr_id'),
			'fk' => array(),
			'ix' => array('contact_id','addr_type_id','preferred'),
			'uc' => array()
		),
		'phpgw_contact_note' => array(
			'fd' => array(
				'contact_note_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_text' => array('type' => 'text','nullable' => False),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_note_id'),
			'fk' => array(),
			'ix' => array('contact_id','note_type_id'),
			'uc' => array()
		),
		'phpgw_contact_others' => array(
			'fd' => array(
				'other_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'contact_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'other_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'other_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('other_id'),
			'fk' => array(),
			'ix' => array('contact_id','contact_owner','other_name'),
			'uc' => array()
		),
		'phpgw_contact_comm' => array(
			'fd' => array(
				'comm_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'comm_descr_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'comm_data' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('comm_id'),
			'fk' => array(),
			'ix' => array('comm_data','preferred','comm_descr_id','contact_id'),
			'uc' => array()
		),
		'phpgw_contact_comm_descr' => array(
			'fd' => array(
				'comm_descr_id' => array('type' => 'auto','nullable' => False),
				'comm_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True)
			),
			'pk' => array('comm_descr_id'),
			'fk' => array(),
			'ix' => array('descr','comm_type_id'),
			'uc' => array()
		),
		'phpgw_contact_comm_type' => array(
			'fd' => array(
				'comm_type_id' => array('type' => 'auto','nullable' => False),
				'type' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'active' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'class' => array('type' => 'varchar','precision' => '30','nullable' => True)
			),
			'pk' => array('comm_type_id'),
			'fk' => array(),
			'ix' => array('type','active','class'),
			'uc' => array()
		),
		'phpgw_contact_types' => array(
			'fd' => array(
				'contact_type_id' => array('type' => 'auto','nullable' => False),
				'contact_type_descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'contact_type_table' => array('type' => 'varchar','precision' => '50','nullable' => True)
			),
			'pk' => array('contact_type_id'),
			'fk' => array(),
			'ix' => array('contact_type_descr'),
			'uc' => array()
		),
		'phpgw_contact_addr_type' => array(
			'fd' => array(
				'addr_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '50','nullable' => False)
			),
			'pk' => array('addr_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_contact_note_type' => array(
			'fd' => array(
				'note_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '30','nullable' => False)
			),
			'pk' => array('note_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cust_attribute' => array(
			'fd' => array(
				'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'column_name' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'input_text' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'statustext' => array('type' => 'varchar','precision' => '150','nullable' => False),
				'datatype' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'search' => array('type' => 'int','precision' => '2','nullable' => True),
				'history' => array('type' => 'int','precision' => '2','nullable' => True),
				'list' => array('type' => 'int','precision' => '4','nullable' => True),
				'attrib_sort' => array('type' => 'int','precision' => '4','nullable' => True),
				'size' => array('type' => 'int','precision' => '4','nullable' => True),
				'precision_' => array('type' => 'int','precision' => '4','nullable' => True),
				'scale' => array('type' => 'int','precision' => '4','nullable' => True),
				'default_value' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'nullable' => array('type' => 'varchar','precision' => '5','nullable' => True)
			),
			'pk' => array('appname','location','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cust_choice' => array(
			'fd' => array(
				'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('appname','location','attrib_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cust_function' => array(
			'fd' => array(
				'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'file_name ' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'active' => array('type' => 'int','precision' => '2','nullable' => True),
				'custom_sort' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('appname','location','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_mapping' => array(
			'fd' => array(
				'ext_user' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'auth_type' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'status' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'A'),
				'location' => array('type' => 'varchar','precision' => '200','nullable' => False),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('ext_user','location','auth_type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_mail_handler' => array(
			'fd' => array(
				'handler_id' => array('type' => 'auto','nullable' => False),
				'target_email' => array('type' => 'varchar','precision' => '75','nullable' => False),
				'handler' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'is_active' => array('type' => 'int','precision' => '4','nullable' => False),
				'lastmod' => array('type' => 'int','precision' => '8','nullable' => False),
				'lastmod_user' => array('type' => 'int','precision' => '8','nullable' => False)
			),
			'pk' => array('handler_id'),
			'fk' => array(),
			'ix' => array('target_email','is_active'),
			'uc' => array()
		)
	);
?>
