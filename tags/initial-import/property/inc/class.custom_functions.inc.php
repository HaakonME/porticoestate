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
	* @subpackage core
 	* @version $Id: class.custom_functions.inc.php,v 1.10 2007/02/27 10:11:29 sigurdne Exp $
	*/

	/**
	 * This is a class used to gain access to custom classes stored in /inc/cron to be run as cron jobs
	 * or from the admin interface.
	 * usage (example): /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default forward_mail_as_sms user=<username> cellphone=<phonenumber>
	 * @package property
	 */

	class property_custom_functions
	{

		var $public_functions = array(
			'index' => True
		);

		function property_custom_functions ()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;


			$GLOBALS['phpgw_info']['flags']['currentapp']	=	'property';

			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->config		= CreateObject('phpgwapi.config',$this->currentapp);

		}

		/**
		 * @param mixed $data
		 * If $data is an array - then the process is run as cron - and will look for $data['function'] to
		 * determine which custom class to load
		 */

		function index($data='')
		{
			if(is_array($data))
			{
				$function = $data['function'];
			}
			else
			{
				$data = unserialize(urldecode(get_var('data',array('POST','GET'))));
				if(!isset($data['function']))
				{
					$data['function'] = get_var('function',array('POST','GET'));
				}
			}

			include_once(PHPGW_SERVER_ROOT.'/'.'property'.'/inc/cron/' . $data['function'] . '.php');
			$custom = new $data['function'];
			$custom->pre_run($data);
		}


	}
?>
