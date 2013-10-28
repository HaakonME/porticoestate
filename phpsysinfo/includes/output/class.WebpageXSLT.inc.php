<?php 
/**
 * start page for webaccess
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Web
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * generate a static webpage with xslt trasformation of the xml
 *
 * @category  PHP
 * @package   PSI_Web
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class WebpageXSLT extends WebpageXML implements PSI_Interface_Output
{
    /**
     * call the parent constructor
     */
    public function __construct()
    {
        parent::__construct(false, null);
    }
    
    /**
     * generate the static page
     *
     * @return void
     */
    public function run()
    {
        CommonFunctions::checkForExtensions(array('xsl'));
        $xmlfile = $this->getXMLString();
        $xslfile = "phpsysinfo.xslt";
        $domxml = new DOMDocument();
        $domxml->loadXML($xmlfile);
        $domxsl = new DOMDocument();
        $domxsl->load($xslfile);
        $xsltproc = new XSLTProcessor;
        $xsltproc->importStyleSheet($domxsl);
        echo $xsltproc->transformToXML($domxml);
    }
}
