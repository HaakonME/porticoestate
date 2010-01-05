<?php 
/**
 * Basic OS Functions
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define which methods a os class for phpsysinfo must implement
 * to be recognized and fully work without errors, these are the methods which
 * are called from outside to include the information in the main application
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
interface PSI_Interface_OS
{
    /**
     * get a special encoding from os where phpsysinfo is running
     *
     * @return string
     */
    function getEncoding();
    
    /**
     * build the os information
     *
     * @return void
     */
    function build();
    
    /**
     * get the filled or unfilled (with default values) system object
     *
     * @return System
     */
    function getSys();
}
?>
