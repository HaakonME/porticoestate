<?php 
/**
 * start page for webaccess
 * redirect the user to the supported page type by the users webbrowser (js available or not)
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */



if (file_exists('../header.inc.php'))
{
	$phpgw_info['flags'] = array
	(
		'currentapp'			=> 'admin',
		'disable_Template_class'=> true,
		'noheader'				=> true,
		'nofooter'				=> true

	);

	include_once('../header.inc.php');

	if(! $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'phpsysinfo'))
	{
		exit;
	}
	$_GET['disp'] = 'dynamic';
}
else
{
	exit;
}


 /**
 * define the application root path on the webserver
 * @var string
 */
define('APP_ROOT', dirname(__FILE__));

/**
 * internal xml or external
 * external is needed when running in static mode
 *
 * @var boolean
 */
define('PSI_INTERNAL_XML', false);

if (version_compare("5.2", PHP_VERSION, ">")) {
    die("PHP 5.2 or greater is required!!!");
}

require_once APP_ROOT.'/includes/autoloader.inc.php';

// Load configuration
require_once APP_ROOT.'/config.php';

if (!defined('PSI_CONFIG_FILE') || !defined('PSI_DEBUG')) {
    $tpl = new Template("/templates/html/error_config.html");
    echo $tpl->fetch();
    die();
}

// redirect to page with and without javascript
$display = isset($_GET['disp']) ? $_GET['disp'] : strtolower(PSI_DEFAULT_DISPLAY_MODE);
switch ($display) {
case "static":
    $webpage = new WebpageXSLT();
    $webpage->run();
    break;
case "dynamic":
    $webpage = new Webpage();
    $webpage->run();
    break;
case "xml":
    $webpage = new WebpageXML(true, null);
    $webpage->run();
    break;
default:
    $tpl = new Template("/templates/html/index_all.html");
    echo $tpl->fetch();
    break;
}
