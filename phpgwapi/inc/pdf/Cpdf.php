<?php
@include_once('include/TTFsubset.php');

/**
 * Create pdf documents without additional modules
 * Note that the companion class Document_CezPdf can be used to extend this class and
 * simplify the creation of documents.
 * <pre>
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see http://www.gnu.org/licenses/
 * </pre>
 *
 * **Document object methods**
 *
 * There is about one object method for each type of object in the pdf document<br>
 * Each function has the same call list ($id,$action,$options).<br>
 * <pre>
 * $id = the object ID of the object, or what it is to be if it is being created
 * $action = a string specifying the action to be performed, though ALL must support:
 *   'new' - create the object with the id $id
 *   'out' - produce the output for the pdf object
 * $options = optional, a string or array containing the various parameters for the object
 * </pre>
 * These, in conjunction with the output function are the ONLY way for output to be produced
 * within the pdf 'file'.
 *
 * @category Documents
 * @package	 Cpdf
 * @version  $Id: Cpdf.php 274 2014-03-21 12:25:04Z ole1986 $
 * @author   Wayne Munro (inactive) <pdf@ros.co.nz>
 * @author   Lars Olesen <lars@legestue.net>
 * @author   Sune Jensen <sj@sunet.dk>
 * @author   Ole Koeckemann <ole1986@users.sourceforge.net>
 * @copyright 2007 - 2014 The authors
 * @license  GNU General Public License v3
 * @link http://pdf-php.sf.net
 */ 
class pdf__ extends pdf___
{
    /**
     * PDF version 
     * This value may vary dependent on which methods and/or features are used.
     * For instance setEncryption may cause the pdf version to increase to $this->pdfversion = 1.4
     * 
     * Minimum 1.3
     * @var string $pdfversion default is 1.3
     */
    protected $pdfversion = 1.3;
    /**
     * allow the programmer to output debug messages on several places<br>
     * 'none' = no debug output at all
     * 'error_log' = use error_log
     * 'variable' = store in a variable called $this->messages
     * @var string $DEBUG Default is error_log
     */
    public $DEBUG = 'error_log';
    
    /**
     * Set the debug level
     * E_USER_ERROR = only errors
     * E_USER_WARNING = errors and warning
     * E_USER_NOTICE =  nearly everything
     *
     * @var int $DEBUGLEVEL Default E_USER_WARNING
     */
    public $DEBUGLEVEL = E_USER_WARNING;
    /**
     * Reversed char string to allow arabic or Hebrew
     * @todo incomplete implementation
     */
    public $rtl = false;
    
    /**
     * flag to validate the output and if output method has be executed
     * This option is not really in use but is set to true in checkAllHere method
     * @var bool
     */
    protected $valid = false;
    
    /**
     * temporary path used for image and font caching.
     * Need to get changed when using XAMPP
     * @var string
     */
    public $tempPath = '/tmp';
    /**
     * the current number of pdf objects in the document
     * @var integer
     */
    protected $numObj=0;

    /**
     * contains pdf objects ready for the final assembly
     * @var array
     */
    protected $objects = array();

    /**
     * set to true allows object being hashed. Primary used for images
     * @var bool
     */
    public $hashed = true;
    /**
     * Object hash array used to free pdf from redundancies
     * @var array
     */
    private $objectHash = array();
    
    /**
      * the objectId (number within the objects array) of the document catalog
      * @var integer
      */
    private $catalogId;
    
    /**
     * default encoding for NON-UNICODE text
     @var string default encoding is IS0-8859-1
     */
    public $targetEncoding = 'ISO-8859-1';
    /**
     * set this to true allows TTF font being parsed as unicode in PDF output.
     * This also converts all text output into utf16_be
     * @var boolean default is false
     */
    public $isUnicode = false;
    
    /**
     * define the tags being allowed in any text input, like addText or addTextWrap (default: bold, italic and links)
     * @var string
     */
     public $allowedTags = 'b|strong|i|uline|alink:?.*?|ilink:?.*?|color:?.*?';
     
    /**
     * used to either embed or not embed the ttf/pfb font program
     * @var boolean default embed the font program
     */
    protected $embedFont = true;
    
    /**
     * font cache timeout in seconds
     * @var integer default is 86400 which is 1 day
     */
    public $cacheTimeout = 86400;
    
    /**
     * stores the font family information for either core fonts or any other TTF font program.
     * Once the font family is defined, directives like bold and italic
     * @var array
     */
    private $fontFamilies = array(
            'Helvetica' => array(
                    'b'=>'Helvetica-Bold',
                    'i'=>'Helvetica-Oblique',
                    'bi'=>'Helvetica-BoldOblique',
                    'ib'=>'Helvetica-BoldOblique',
                ),
            'Courier' => array(
                    'b'=>'Courier-Bold',
                    'i'=>'Courier-Oblique',
                    'bi'=>'Courier-BoldOblique',
                    'ib'=>'Courier-BoldOblique',
                ),
            'Times-Roman' => array(
                    'b'=>'Times-Bold',
                    'i'=>'Times-Italic',
                    'bi'=>'Times-BoldItalic',
                    'ib'=>'Times-BoldItalic',
                )
    );

    /**
     * all CoreFonts available in PDF by default.
     * This array is used check if TTF font need to get attached and/or is unicode
     * @var array
     */
    private $coreFonts = array('courier', 'courier-bold', 'courier-oblique', 'courier-boldoblique',
    'helvetica', 'helvetica-bold', 'helvetica-oblique', 'helvetica-boldoblique',
    'times-roman', 'times-bold', 'times-italic', 'times-bolditalic',
    'symbol', 'zapfdingbats');

    /**
     * array carrying information about the fonts that the system currently knows about
     * used to ensure that a font is not loaded twice, among other things
     * @var array
     */
    protected $fonts = array();
    
    /**
     * font path location
     * @since 0.12-rc8
     */
    public $fontPath = './';
    
    /**
      * a record of the current font
      * @var string
      */
    protected $currentFont='';

    /**
     * the current base font
     * @var string
     */
    protected $currentBaseFont='';

    /**
      * the number of the current font within the font array
      * @var integer
      */
    protected $currentFontNum=0;

    /**
     * no clue for what this is used
     * @var integer
     */
    private $currentNode;

    /**
      * object number of the current page
      * @var integer
      */
    protected $currentPage;

    /**
      * object number of the currently active contents block
      * @var integer
      */
    protected $currentContents;

    /**
      * number of fonts within the system
      * @var integer
      */
    protected $numFonts = 0;

    /**
     * current colour for fill operations, defaults to inactive value, all three components should be between 0 and 1 inclusive when active
     */
    protected $currentColour = array('r' => -1, 'g' => -1, 'b' => -1);

    /**
     * current colour for stroke operations (lines etc.)
     */
    protected $currentStrokeColour = array('r' => -1, 'g' => -1, 'b' => -1);

    /**
      * current style that lines are drawn in
      */
    protected $currentLineStyle='';

    /**
      * an array which is used to save the state of the document, mainly the colours and styles
      * it is used to temporarily change to another state, the change back to what it was before
      */
    private $stateStack = array();

    /**
     * number of elements within the state stack
     */
    private $nStateStack = 0;

    /**
     * number of page objects within the document
     */
    protected $numPages=0;

    /**
     * object Id storage stack
     */
    protected $stack = array();

    /**
     * number of elements within the object Id storage stack
     */
    private $nStack=0;

    /**
     * an array which contains information about the objects which are not firmly attached to pages
     * these have been added with the addObject function
     */
    private $looseObjects=array();

    /**
     * array contains infomation about how the loose objects are to be added to the document
     */
    private $addLooseObjects=array();

    /**
      * the objectId of the information object for the document
      * this contains authorship, title etc.
      */
    private $infoObject=0;

    /**
      * number of images being tracked within the document
      */
    private $numImages=0;

    /**
      * some additional options while generation
      * currently used for compression only
      * Default: 'compression' => -1 which will set gzcompress to the default level of 6
      */
    public $options=array('compression'=>-1);

    /**
      * the objectId of the first page of the document
      */
    private $firstPageId;

    /**
      * used to track the last used value of the inter-word spacing, this is so that it is known
      * when the spacing is changed.
      */
    private $wordSpaceAdjust=0;

    /**
      * tracks the status of the current font style, like bold or italic
      */
    private $currentTextState = '';

    /**
     * messages are stored here during processing, these can be selected afterwards to give some useful debug information
     */
    public $messages='';

    /**
     * the encryption array for the document encryption is stored here
     */
    private $arc4='';

    /**
     * the object Id of the encryption information
     */
    private $arc4_objnum=0;

    /**
     * the file identifier, used to uniquely identify a pdf document
     */
    public $fileIdentifier;

    /**
     * Set the encryption mode
     * 0 = no encryption
     * 1 = RC40bit
     * 2 = RC128bit (since PDF Version 1.4)
     */
     private $encryptionMode = 0;
    /**
     * the encryption key for the encryption of all the document content (structure is not encrypted)
     *
     * @var string
     */
    private $encryptionKey='';
    
    /**
     * encryption padding fetched from the Adobe PDF reference
     */
    private $encryptionPad;
    
    /**
     * array which forms a stack to keep track of nested callback functions
     *
     * @var array
     */
    private $callback = array();

    /**
     * the number of callback functions in the callback array
     *
     * @var integer
     */
    private $nCallback = 0;

    /**
     * store label->id pairs for named destinations, these will be used to replace internal links
     * done this way so that destinations can be defined after the location that links to them
     *
     * @var array
     */
    private $destinations = array();

    /**
     * store the stack for the transaction commands, each item in here is a record of the values of all the
     * variables within the class, so that the user can rollback at will (from each 'start' command)
     * note that this includes the objects array, so these can be large.
     *
     * @var string
     */
    protected $checkpoint = '';

    /**
     * Constructor - start with a new PDF document
     *
     * @param array $pageSize Array of 4 numbers, defining the bottom left and upper right corner of the page. first two are normally zero.
     * @param bool $isUnicode
     * @return void
     */
    public function __construct($pageSize = array(0, 0, 612, 792), $isUnicode = false)
    {
        $this->isUnicode = $isUnicode;
        // set the hardcoded encryption pad
        $this->encryptionPad = chr(0x28).chr(0xBF).chr(0x4E).chr(0x5E).chr(0x4E).chr(0x75).chr(0x8A).chr(0x41).chr(0x64).chr(0x00).chr(0x4E).chr(0x56).chr(0xFF).chr(0xFA).chr(0x01).chr(0x08).chr(0x2E).chr(0x2E).chr(0x00).chr(0xB6).chr(0xD0).chr(0x68).chr(0x3E).chr(0x80).chr(0x2F).chr(0x0C).chr(0xA9).chr(0xFE).chr(0x64).chr(0x53).chr(0x69).chr(0x7A);
        
        $this->newDocument($pageSize);

        if ( in_array('Windows-1252', mb_list_encodings()) ) {
              $this->targetEncoding = 'Windows-1252';
        }
        // use the md5 to have a unique identifier for all documents created with R&OS pdf class
        $this->fileIdentifier = md5('ROSPDF'.time());
        
        // set the default font path to [...]/src/fonts
        $this->fontPath = dirname(__FILE__).'/fonts';
    }

    /**
     * destination object, used to specify the location for the user to jump to, presently on opening
     */
    private function o_destination($id,$action,$options='')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch($action){
            case 'new':
                 $this->objects[$id]=array('t'=>'destination','info'=>array());
                 $tmp = '';
                 switch ($options['type']){
                     case 'XYZ':
                     case 'FitR':
                         $tmp =  ' '.$options['p3'].$tmp;
                     case 'FitH':
                     case 'FitV':
                     case 'FitBH':
                     case 'FitBV':
                         $tmp =  ' '.$options['p1'].' '.$options['p2'].$tmp;
                     case 'Fit':
                     case 'FitB':
                         $tmp =  $options['type'].$tmp;
                         $this->objects[$id]['info']['string']=$tmp;
                         $this->objects[$id]['info']['page']=$options['page'];
                 }
                 break;
            case 'out':
                $tmp = $o['info'];
                $res="\n".$id." 0 obj\n".'['.$tmp['page'].' 0 R /'.$tmp['string']."]\nendobj";
                return $res;
                break;
        }
    }

    /**
     * sets the viewer preferences
     */
    private function o_viewerPreferences($id,$action,$options='')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'viewerPreferences','info'=>array());
                break;
            case 'add':
                foreach($options as $k=>$v){
                    switch ($k){
                        case 'HideToolbar':
                        case 'HideMenubar':
                        case 'HideWindowUI':
                        case 'FitWindow':
                        case 'CenterWindow':
                        case 'DisplayDocTitle': // since PDF 1.4
                        case 'NonFullScreenPageMode':
                        case 'Direction':
                            $o['info'][$k]=$v;
                            break;
                    }
                }
                break;
            case 'out':
                $res="\n".$id." 0 obj\n".'<< ';
                foreach($o['info'] as $k=>$v){
                    $res.="\n/".$k.' '.$v;
                }
                $res.="\n>>\n";
                return $res;
                break;
        }
    }

    /**
     * define the document catalog, the overall controller for the document
     */
    private function o_catalog($id, $action, $options = '')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'catalog','info'=>array());
                $this->catalogId=$id;
            break;
            case 'outlines':
            case 'pages':
            case 'openHere':
                $o['info'][$action]=$options;
                break;
            case 'viewerPreferences':
                if (!isset($o['info']['viewerPreferences'])){
                    $this->numObj++;
                    $this->o_viewerPreferences($this->numObj,'new');
                    $o['info']['viewerPreferences']=$this->numObj;
                }
                $vp = $o['info']['viewerPreferences'];
                $this->o_viewerPreferences($vp,'add',$options);
                break;
            case 'out':
                $res="\n".$id." 0 obj\n".'<< /Type /Catalog';
                foreach($o['info'] as $k=>$v){
                    switch($k){
                        case 'outlines':
                            $res.=' /Outlines '.$v.' 0 R';
                            break;
                        case 'pages':
                            $res.=' /Pages '.$v.' 0 R';
                            break;
                        case 'viewerPreferences':
                            $res.=' /ViewerPreferences '.$o['info']['viewerPreferences'].' 0 R';
                            break;
                        case 'openHere':
                            $res.=' /OpenAction '.$o['info']['openHere'].' 0 R';
                            break;
                    }
                }
                $res.=" >>\nendobj";
                return $res;
                break;
        }
    }

    /**
     * object which is a parent to the pages in the document
     */
    private function o_pages($id,$action,$options='')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'pages','info'=>array());
                $this->o_catalog($this->catalogId,'pages',$id);
                break;
            case 'page':
                if (!is_array($options)){
                    // then it will just be the id of the new page
                    $o['info']['pages'][]=$options;
                } else {
                    // then it should be an array having 'id','rid','pos', where rid=the page to which this one will be placed relative
                    // and pos is either 'before' or 'after', saying where this page will fit.
                    if (isset($options['id']) && isset($options['rid']) && isset($options['pos'])){
                        $i = array_search($options['rid'],$o['info']['pages']);
                        if (isset($o['info']['pages'][$i]) && $o['info']['pages'][$i]==$options['rid']){
                            // then there is a match make a space
                            switch ($options['pos']){
                                case 'before':
                                    $k = $i;
                                    break;
                                case 'after':
                                    $k=$i+1;
                                    break;
                                default:
                                    $k=-1;
                                    break;
                            }
                            if ($k>=0){
                                for ($j=count($o['info']['pages'])-1;$j>=$k;$j--){
                                    $o['info']['pages'][$j+1]=$o['info']['pages'][$j];
                                }
                                $o['info']['pages'][$k]=$options['id'];
                            }
                        }
                    }
                }
                break;
            case 'procset':
                $o['info']['procset']=$options;
                break;
            case 'mediaBox':
                $o['info']['mediaBox']=$options; // which should be an array of 4 numbers
                break;
            case 'font':
                $o['info']['fonts'][]=array('objNum'=>$options['objNum'],'fontNum'=>$options['fontNum']);
                break;
            case 'xObject':
                $o['info']['xObjects'][]=array('objNum'=>$options['objNum'],'label'=>$options['label']);
                break;
            case 'out':
                if (count($o['info']['pages'])){
                    $res="\n".$id." 0 obj\n<< /Type /Pages /Kids [";
                    foreach($o['info']['pages'] as $k=>$v){
                        $res.=$v." 0 R ";
                    }
                    $res.="] /Count ".count($this->objects[$id]['info']['pages']);
                    if ((isset($o['info']['fonts']) && count($o['info']['fonts'])) || isset($o['info']['procset'])){
                        $res.=" /Resources <<";
                        if (isset($o['info']['procset'])){
                            $res.=" /ProcSet ".$o['info']['procset'];
                        }
                        if (isset($o['info']['fonts']) && count($o['info']['fonts'])){
                            $res.=" /Font << ";
                            foreach($o['info']['fonts'] as $finfo){
                                $res.=" /F".$finfo['fontNum']." ".$finfo['objNum']." 0 R";
                            }
                            $res.=" >>";
                        }
                        if (isset($o['info']['xObjects']) && count($o['info']['xObjects'])){
                            $res.=" /XObject << ";
                            foreach($o['info']['xObjects'] as $finfo){
                                $res.=" /".$finfo['label']." ".$finfo['objNum']." 0 R";
                            }
                            $res.=" >>";
                        }
                        $res.=" >>";
                        if (isset($o['info']['mediaBox'])){
                            $tmp=$o['info']['mediaBox'];
                            $res.=" /MediaBox [".sprintf('%.3F',$tmp[0]).' '.sprintf('%.3F',$tmp[1]).' '.sprintf('%.3F',$tmp[2]).' '.sprintf('%.3F',$tmp[3]).']';
                        }
                    }
                    $res.=" >>\nendobj";
                } else {
                    $res="\n".$id." 0 obj\n<< /Type /Pages\n/Count 0\n>>\nendobj";
                }
                return $res;
                break;
         }
    }
    
    /**
     * defines the outlines in the doc, empty for now
     */
    private function o_outlines($id,$action,$options='')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'outlines','info'=>array('outlines'=>array()));
                $this->o_catalog($this->catalogId,'outlines',$id);
                break;
            case 'outline':
                $o['info']['outlines'][]=$options;
                break;
            case 'out':
                if (count($o['info']['outlines'])){
                    $res="\n".$id." 0 obj\n<< /Type /Outlines /Kids [";
                    foreach($o['info']['outlines'] as $k=>$v){
                        $res.=$v." 0 R ";
                    }
                    $res.="] /Count ".count($o['info']['outlines'])." >>\nendobj";
                } else {
                    $res="\n".$id." 0 obj\n<< /Type /Outlines /Count 0 >>\nendobj";
                }
                return $res;
                break;
        }
    }

    /**
     * an object to hold the font description
     */
    private function o_font($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'font','info'=>array('name'=>$options['name'], 'fontFileName'=> $options['fontFileName'], 'SubType'=>'Type1'));
                
                $fontFileName = &$options['fontFileName'];
                
                $fontNum=$this->numFonts;
                $this->objects[$id]['info']['fontNum']=$fontNum;
                // deal with the encoding and the differences
                if (isset($options['differences'])){
                    // then we'll need an encoding dictionary
                    $this->numObj++;
                    $this->o_fontEncoding($this->numObj,'new',$options);
                    $this->objects[$id]['info']['encodingDictionary']=$this->numObj;
                } else if (isset($options['encoding'])){
                    // we can specify encoding here
                    switch($options['encoding']){
                        case 'WinAnsiEncoding':
                        case 'MacRomanEncoding':
                        case 'MacExpertEncoding':
                            $this->objects[$id]['info']['encoding']=$options['encoding'];
                            break;
                        case 'none':
                            break;
                        default:
                            $this->objects[$id]['info']['encoding']='WinAnsiEncoding';
                            break;
                    }
                } else {
                    $this->objects[$id]['info']['encoding']='WinAnsiEncoding';
                }
                
                if ($this->fonts[$fontFileName]['isUnicode']) {
                    // For Unicode fonts, we need to incorporate font data into
                    // sub-sections that are linked from the primary font section.
                    // Look at o_fontGIDtoCID and o_fontDescendentCID functions
                    // for more informaiton.
                    //
                    // All of this code is adapted from the excellent changes made to
                    // transform FPDF to TCPDF (http://tcpdf.sourceforge.net/)
                    $toUnicodeId = ++$this->numObj;
                    $this->o_contents($toUnicodeId, 'new', 'raw');
                    $this->objects[$id]['info']['toUnicode'] = $toUnicodeId;

                    $stream = "/CIDInit /ProcSet findresource begin\n12 dict begin\nbegincmap\n/CIDSystemInfo <</Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def\n/CMapName /Adobe-Identity-UCS def\n/CMapType 2 def\n1 begincodespacerange\n<0000> <FFFF>\nendcodespacerange\n1 beginbfrange\n<0000> <FFFF> <0000>\nendbfrange\nendcmap\nCMapName currentdict /CMap defineresource pop\nend\nend\n";

                    $res = "<</Length " . mb_strlen($stream, '8bit') . " >>\n";
                    $res .= "stream\n" . $stream . "\nendstream";

                    $this->objects[$toUnicodeId]['c'] = $res;

                    $cidFontId = ++$this->numObj;
                    $this->o_fontDescendentCID($cidFontId, 'new', $options);
                    $this->objects[$id]['info']['cidFont'] = $cidFontId;
                }
                // also tell the pages node about the new font
                $this->o_pages($this->currentNode,'font',array('fontNum'=>$fontNum,'objNum'=>$id));
                break;
            case 'add':
                foreach ($options as $k=>$v){
                    switch ($k){
                        case 'BaseFont':
                            $o['info']['name'] = $v;
                            break;
                        case 'FirstChar':
                        case 'LastChar':
                        case 'Widths':
                        case 'FontDescriptor':
                        case 'SubType':
                            $this->debug('o_font '.$k." : ".$v, E_USER_NOTICE);
                            $o['info'][$k] = $v;
                            break;
                    }
                }
                
                // pass values down to descendent font
                  if (isset($o['info']['cidFont'])) {
                    $this->o_fontDescendentCID($o['info']['cidFont'], 'add', $options);
                  }
                break;
            case 'out':
                $fontFileName = &$o['info']['fontFileName'];
                // when font program is embedded and its not a coreFont, attach the font either as subset or completely
                if($this->embedFont && !in_array(strtolower($o['info']['name']), $this->coreFonts)){
                    // when TrueType font is used
                    if(isset($this->objects[$o['info']['FontDescriptor']]['info']['FontFile2'])){
                        // find font program id for TTF fonts (FontFile2)
                        $pfbid = $this->objects[$o['info']['FontDescriptor']]['info']['FontFile2'];
                        // if subsetting is set
                        if($this->fonts[$fontFileName]['isSubset'] && $this->fonts[$fontFileName]['isUnicode']){
                            $this->debug('subset font for ' . $fontFileName, E_USER_NOTICE);
                            $subsetFontName = "AAAAAD+" . $o['info']['name'];
                            $o['info']['name'] = $subsetFontName;
                            // find descendant font
                            $this->objects[$o['info']['cidFont']]['info']['name'] = $subsetFontName;
                            // find font descriptor
                            $this->objects[$o['info']['FontDescriptor']]['info']['FontName'] = $subsetFontName;
                            
                            // use TTF subset script from http://www.4real.gr/technical-documents-ttf-subset.html
                            $t = new TTFsubset();
                            // combine all used characters as string
                            $s = implode('',array_keys($this->fonts[$fontFileName]['subset']));
                            // submit the string to TTFsubset class to return the subset (as binary)
                            $data = $t->doSubset($this->fontPath.'/'.$fontFileName . '.ttf', $s, null);
                            // $data is the new (subset) of the font font
                            //file_put_contents('/tmp/'.$o['info']['name'] . '.ttf', $data);
                            
                            $newcidwidth = array();
                            $cidwidth = &$this->fonts[$fontFileName]['CIDWidths'];
                            foreach($t->TTFchars as $TTFchar){
                                if(!empty($TTFchar->charCode) && isset($cidwidth[$TTFchar->charCode])){
                                    $newcidwidth[$TTFchar->charCode] = $cidwidth[$TTFchar->charCode];
                                }
                            }
                            $cidwidth = $newcidwidth;
                        } else {
                            $data = file_get_contents($this->fontPath.'/'.$fontFileName. '.ttf');
                        }
                    
                        // TODO: cache the subset
                        
                        $l1 = strlen($data);
                        $this->objects[$pfbid]['c'].= $data;
                        $this->o_contents($pfbid,'add',array('Length1'=>$l1));
                    } else if(isset($this->objects[$o['info']['FontDescriptor']]['info']['FontFile'])) {
                        // find FontFile id - used for PFB fonts
                        $pfbid = $this->objects[$o['info']['FontDescriptor']]['info']['FontFile'];
                        $data = file_get_contents($this->fontPath.'/'.$fontFileName. '.pfb');
                        $l1 = strpos($data,'eexec')+6;
                        $l2 = strpos($data,'00000000')-$l1;
                        $l3 = strlen($data)-$l2-$l1;
                        $this->o_contents($pfbid,'add',array('Length1'=>$l1,'Length2'=>$l2,'Length3'=>$l3));
                    } else {
                        $this->debug("Failed to select the correct font program", E_USER_WARNING);
                    }
                }
                
                if ($this->fonts[$fontFileName]['isUnicode']) {
                    // For Unicode fonts, we need to incorporate font data into
                    // sub-sections that are linked from the primary font section.
                    // Look at o_fontGIDtoCID and o_fontDescendentCID functions
                    // for more informaiton.
                    //
                    // All of this code is adapted from the excellent changes made to
                    // transform FPDF to TCPDF (http://tcpdf.sourceforge.net/)

                    $res = "\n$id 0 obj\n<</Type /Font /Subtype /Type0 /BaseFont /".$o['info']['name']."";
                    // The horizontal identity mapping for 2-byte CIDs; may be used
                    // with CIDFonts using any Registry, Ordering, and Supplement values.
                       $res.= " /Encoding /Identity-H /DescendantFonts [".$o['info']['cidFont']." 0 R] /ToUnicode ".$o['info']['toUnicode']." 0 R >>\n";
                    $res.= "endobj";
                } else {
                    $res="\n".$id." 0 obj\n<< /Type /Font /Subtype /".$o['info']['SubType']." ";
                    $res.="/Name /F".$o['info']['fontNum']." ";
                    $res.="/BaseFont /".$o['info']['name']." ";
                    if (isset($o['info']['encodingDictionary'])){
                        // then place a reference to the dictionary
                        $res.="/Encoding ".$o['info']['encodingDictionary']." 0 R ";
                    } else if (isset($o['info']['encoding'])){
                        // use the specified encoding
                        $res.="/Encoding /".$o['info']['encoding']." ";
                    }
                    if (isset($o['info']['FirstChar'])){
                        $res.="/FirstChar ".$o['info']['FirstChar']." ";
                    }
                    if (isset($o['info']['LastChar'])){
                        $res.="/LastChar ".$o['info']['LastChar']." ";
                    }
                    if (isset($o['info']['Widths'])){
                        $res.="/Widths ".$o['info']['Widths']." 0 R ";
                    }
                    if (isset($o['info']['FontDescriptor'])){
                        $res.="/FontDescriptor ".$o['info']['FontDescriptor']." 0 R ";
                    }
                    $res.=">>\nendobj";
                }
                return $res;
                break;
        }
    }

    /**
     * a font descriptor, needed for including additional fonts
     */
    private function o_fontDescriptor($id, $action, $options = '')
    {
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                $this->objects[$id]=array('t'=>'fontDescriptor','info'=>$options);
                break;
            case 'out':
                $res="\n".$id." 0 obj\n<< /Type /FontDescriptor ";
                foreach ($o['info'] as $label => $value){
                    switch ($label){
                        case 'Ascent':
                        case 'CapHeight':
                        case 'Descent':
                        case 'Flags':
                        case 'ItalicAngle':
                        case 'StemV':
                        case 'AvgWidth':
                        case 'Leading':
                        case 'MaxWidth':
                        case 'MissingWidth':
                        case 'StemH':
                        case 'XHeight':
                        case 'CharSet':
                            if (strlen($value)){
                                $res.='/'.$label.' '.$value." ";
                            }
                            break;
                        case 'FontFile':
                        case 'FontFile2':
                        case 'FontFile3':
                            $res.='/'.$label.' '.$value." 0 R ";
                            break;
                        case 'FontBBox':
                            $res.='/'.$label.' ['.$value[0].' '.$value[1].' '.$value[2].' '.$value[3]."] ";
                            break;
                        case 'FontName':
                            $res.='/'.$label.' /'.$value." ";
                            break;
                    }
                }
                $res.=">>\nendobj";
                return $res;
                break;
        }
    }

    /**
     * the font encoding
     */
    private function o_fontEncoding($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
            case 'new':
                // the options array should contain 'differences' and maybe 'encoding'
                $this->objects[$id]=array('t'=>'fontEncoding','info'=>$options);
                break;
            case 'out':
                $res="\n".$id." 0 obj\n<< /Type /Encoding ";
                if (!isset($o['info']['encoding'])){
                    $o['info']['encoding']='WinAnsiEncoding';
                }
                if ($o['info']['encoding']!='none'){
                    $res.="/BaseEncoding /".$o['info']['encoding']." ";
                }
                $res.="/Differences [";
                $onum=-100;
                foreach($o['info']['differences'] as $num=>$label){
                    if ($num!=$onum+1){
                        // we cannot make use of consecutive numbering
                        $res.= " ".$num." /".$label;
                    } else {
                        $res.= " /".$label;
                    }
                    $onum=$num;
                }
                $res.="] >>\nendobj";
                return $res;
                break;
        }
    }

    /**
     * a descendent cid font, needed for unicode fonts
     */
    private function o_fontDescendentCID($id, $action, $options = '') {
        if ($action !== 'new') {
          $o = & $this->objects[$id];
        }

        switch ($action) {
        case 'new':
          $this->objects[$id] = array('t' => 'fontDescendentCID', 'info' => $options);
          // and a CID to GID map
          if($this->embedFont){
              $cidToGidMapId = ++$this->numObj;
              $this->o_fontGIDtoCIDMap($cidToGidMapId, 'new', $options);
              $this->objects[$id]['info']['cidToGidMap'] = $cidToGidMapId;
          }
          break;

        case 'add':
          foreach ($options as $k => $v) {
            switch ($k) {
            case 'BaseFont':
              $o['info']['name'] = $v;
              break;

            case 'FirstChar':
            case 'LastChar':
            case 'MissingWidth':
            case 'FontDescriptor':
            case 'SubType':
              $this->debug("o_fontDescendentCID $k : $v", E_USER_NOTICE);
              $o['info'][$k] = $v;
              break;
            }
          }

          // pass values down to cid to gid map
          if($this->embedFont){
              $this->o_fontGIDtoCIDMap($o['info']['cidToGidMap'], 'add', $options);
          }
          break;

        case 'out':
          $fontFileName = &$o['info']['fontFileName'];
          $res = "\n$id 0 obj\n";
          $res.= "<</Type /Font /Subtype /CIDFontType2 /BaseFont /".$o['info']['name']." /CIDSystemInfo << /Registry (Adobe) /Ordering (Identity) /Supplement 0 >>";
          if (isset($o['info']['FontDescriptor'])) {
            $res.= " /FontDescriptor ".$o['info']['FontDescriptor']." 0 R";
          }

          if (isset($o['info']['MissingWidth'])) {
            $res.= " /DW ".$o['info']['MissingWidth']."";
          }

          if (isset($fontFileName) && isset($this->fonts[$fontFileName]['CIDWidths'])) {
            $cid_widths = &$this->fonts[$fontFileName]['CIDWidths'];
            $res.= ' /W [';
            reset($cid_widths);
			$opened = false;
			while (list($k,$v) = each($cid_widths)) {
				list($nextk, $nextv) = each($cid_widths);
				//echo "\n$k ($v) == $nextk ($nextv)";
				if(($k + 1) == $nextk){
					if(!$opened){
						$res.= " $k [$v";
						$opened = true;
					} else if($opened) {
						$res.= ' '.$v;
					}
					prev($cid_widths);
				} else {
					if($opened){
						$res.=" $v]";
					} else {
						$res.= " $k [$v]";
					}
					
					$opened = false;
					prev($cid_widths);
				}
			}
			
			if(isset($nextk) && isset($nextv)){
				if($opened){
					$res.= "]";
				}
				$res.= " $nextk [$nextv]";
			}
            
            $res.= ' ]';
          }
          
          if($this->embedFont){
              $res.= " /CIDToGIDMap ".$o['info']['cidToGidMap']." 0 R";
          }
          $res.= "  >>\n";
          $res.= "endobj";

          return $res;
        }
    }

    /**
      * a font glyph to character map, needed for unicode fonts
      */
    private function o_fontGIDtoCIDMap($id, $action, $options = '') {
        if ($action !== 'new') {
          $o = & $this->objects[$id];
        }

        switch ($action) {
        case 'new':
          $this->objects[$id] = array('t' => 'fontGIDtoCIDMap', 'info' => $options);
          break;

        case 'out':
          $res = "\n$id 0 obj\n";
          $fontFileName = &$o['info']['fontFileName'];
          $tmp = $this->fonts[$fontFileName]['CIDtoGID'] = base64_decode($this->fonts[$fontFileName]['CIDtoGID']);
          
          if (isset($o['raw'])) {
            $res.= $tmp;
          } else {
            $res.= "<<";
            if (function_exists('gzcompress') && $this->options['compression']) {
              // then implement ZLIB based compression on this content stream
              $tmp = gzcompress($tmp, $this->options['compression']);
              $res.= " /Filter /FlateDecode";
            }
            
            $res.= " /Length ".mb_strlen($tmp, '8bit') .">>\nstream\n$tmp\nendstream";
          }

          $res.= "\nendobj";
          return $res;
        }
    }

    /**
     * define the document information
     */
    private function o_info($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
        case 'new':
            $this->infoObject=$id;
            $date='D:'.date('YmdHis')."-00'00";
            $this->objects[$id]=array('t'=>'info','info'=>array('Creator'=>'R&OS php pdf class, http://pdf-php.sf.net/','CreationDate'=>$date));
            break;
        case 'Title':
        case 'Author':
        case 'Subject':
        case 'Keywords':
        case 'Creator':
        case 'Producer':
        case 'CreationDate':
        case 'ModDate':
        case 'Trapped':
            $o['info'][$action]=$options;
            break;
        case 'out':
            if ($this->encryptionMode > 0){
                $this->encryptInit($id);
            }
            $res="\n".$id." 0 obj\n<< ";
            foreach ($o['info']  as $k=>$v){
                $res.='/'.$k.' (';
                if ($this->encryptionMode > 0){
                    $res.=$this->filterText($this->ARC4($v), true, false);
                } else {
                    $res.=$this->filterText($v, true, false);
                }
                $res.=") ";
            }
            $res.=">>\nendobj";
            return $res;
            break;
        }
    }

    /**
     * an action object, used to link to URLS initially
     * In version >= 0.12.2 internal and external links are handled in o_annotation directly
     * Additional actions, like SubmitForm, ResetForm, ImportData, Javascript will be part of
     * o_actions. Unless we also do not handle them similar to Links.
     *
     */
    private function o_action($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
        case 'new':
            if (is_array($options)){
                $this->objects[$id]=array('t'=>'action','info'=>$options,'type'=>$options['type']);
            } else {
                // then assume a URI action
                $this->objects[$id]=array('t'=>'action','info'=>$options,'type'=>'URI');
            }
            break;
        case 'out':
            if ($this->encryptionMode > 0){
                $this->encryptInit($id);
            }
            $res="\n".$id." 0 obj\n<< /Type /Action";
            switch($o['type']){
            case 'ilink':
                // there will be an 'label' setting, this is the name of the destination
                $res.=" /S /GoTo /D ".$this->destinations[(string)$o['info']['label']]." 0 R";
                break;
            case 'URI':
                $res.=" /S /URI /URI (";
                if ($this->encryptionMode > 0){
                    $res.=$this->filterText($this->ARC4($o['info']), true, false);
                } else {
                    $res.=$this->filterText($o['info'], true, false);
                }
                $res.=")";
                break;
            }
            $res.=" >>\nendobj";
            return $res;
            break;
        }
    }

    /**
     * an annotation object, this will add an annotation to the current page.
     * initially will support just link annotations
     */
    private function o_annotation($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
        case 'new':
            // add the annotation to the current page
            $pageId = $this->currentPage;
            $this->o_page($pageId,'annot',$id);
            // and add the action object which is going to be required
            switch($options['type']){
            case 'link':
                $this->objects[$id]=array('t'=>'annotation','info'=>$options);
                //$this->numObj++;
                //$this->o_action($this->numObj,'new',$options['url']);
                //$this->objects[$id]['info']['actionId']=$this->numObj;
                break;
            case 'ilink':
                // this is to a named internal link
                $label = $options['label'];
                $this->objects[$id]=array('t'=>'annotation','info'=>$options);
                //$this->numObj++;
                //$this->o_action($this->numObj,'new',array('type'=>'ilink','label'=>$label));
                //$this->objects[$id]['info']['actionId']=$this->numObj;
                break;
            case 'text':
                $this->objects[$id]=array('t'=>'annotation','info'=>$options);
                break;
            }
            break;
        case 'out':
            $res="\n".$id." 0 obj\n<< /Type /Annot";
            switch($o['info']['type']){
                case 'link':
                    $res.= " /Subtype /Link";
                    $res.=" /A << /S /URI /URI (".$o['info']['url'].") >>";
                    $res.=" /Border [0 0 0]";
                    $res.=" /H /I";
                    break;
                case 'ilink':
                    $res.= " /Subtype /Link";
                    if(isset($this->destinations[(string)$o['info']['label']])){
                        $res.=" /A << /S /GoTo /D ".$this->destinations[(string)$o['info']['label']]." 0 R >>";
                    }
                    $res.=" /Border [0 0 0]";
                    $res.=" /H /I";
                    break;
                case 'text':
                    $res.= " /Subtype /Text";
                    $res.= " /T (".$this->filterText($o['info']['title'], false, false).") /Contents (".$o['info']['content'].")";
                break;
            }
            
            $res.=" /Rect [ ";
            foreach($o['info']['rect'] as $v){
                $res.= sprintf("%.4F ",$v);
            }
            $res.="]";
            $res.=" >>\nendobj";
            return $res;
            break;
        }
    }

    /**
     * a page object, it also creates a contents object to hold its contents
     */
    private function o_page($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
        case 'new':
            $this->numPages++;
            $this->objects[$id]=array('t'=>'page','info'=>array('parent'=>$this->currentNode,'pageNum'=>$this->numPages));
            if (is_array($options)){
                // then this must be a page insertion, array shoudl contain 'rid','pos'=[before|after]
                $options['id']=$id;
                $this->o_pages($this->currentNode,'page',$options);
            } else {
                $this->o_pages($this->currentNode,'page',$id);
            }
            $this->currentPage=$id;
            // make a contents object to go with this page
            $this->numObj++;
            $this->o_contents($this->numObj,'new',$id);
            $this->currentContents=$this->numObj;
            $this->objects[$id]['info']['contents']=array();
            $this->objects[$id]['info']['contents'][]=$this->numObj;
            $match = ($this->numPages%2 ? 'odd' : 'even');
            foreach($this->addLooseObjects as $oId=>$target){
                if ($target=='all' || $match==$target){
                    $this->objects[$id]['info']['contents'][]=$oId;
                }
            }
            break;
        case 'content':
            $o['info']['contents'][]=$options;
            break;
        case 'annot':
            // add an annotation to this page
            if (!isset($o['info']['annot'])){
                $o['info']['annot']=array();
            }
            // $options should contain the id of the annotation dictionary
            $o['info']['annot'][]=$options;
            break;
        case 'out':
            $res="\n".$id." 0 obj\n<< /Type /Page";
            $res.=" /Parent ".$o['info']['parent']." 0 R";
            if (isset($o['info']['annot'])){
                $res.=" /Annots [";
                foreach($o['info']['annot'] as $aId){
                    $res.=" ".$aId." 0 R";
                }
                $res.=" ]";
            }
            $count = count($o['info']['contents']);
            if ($count==1){
                $res.=" /Contents ".$o['info']['contents'][0]." 0 R";
            } else if ($count>1){
                $res.=" /Contents [ ";
                foreach ($o['info']['contents'] as $cId){
                    $res.=$cId." 0 R ";
                }
                $res.="]";
            }
            $res.=" >>\nendobj";
            return $res;
            break;
        }
    }

    /**
     * the contents objects hold all of the content which appears on pages
     */
    private function o_contents($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch ($action){
        case 'new':
            $this->objects[$id]=array('t'=>'contents','c'=>'','info'=>array());
            if (strlen($options) && intval($options)){
                // then this contents is the primary for a page
                $this->objects[$id]['onPage']=$options;
            } else if ($options=='raw'){
                // then this page contains some other type of system object
                $this->objects[$id]['raw']=1;
            }
            break;
        case 'add':
            // add more options to the decleration
            foreach ($options as $k=>$v){
                $o['info'][$k]=$v;
            }
        case 'out':
            $tmp=$o['c'];
            $res= "\n".$id." 0 obj\n";
            if (isset($this->objects[$id]['raw'])){
                $res.=$tmp;
            } else {
                $res.= "<<";
                if (function_exists('gzcompress') && $this->options['compression']){
                    // then implement ZLIB based compression on this content stream
                    $res.=" /Filter /FlateDecode";
                    $tmp = gzcompress($tmp, $this->options['compression']);
                }
                if ($this->encryptionMode > 0){
                    $this->encryptInit($id);
                   $tmp = $this->ARC4($tmp);
                }
                foreach($o['info'] as $k=>$v){
                    $res .= " /".$k.' '.$v;
                }
                $res.=" /Length ".strlen($tmp)." >> stream\n".$tmp."\nendstream";
            }
            $res.="\nendobj";
            return $res;
            break;
        }
    }

    /**
     * an image object, will be an XObject in the document, includes description and data
     */
    private function o_image($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch($action){
        case 'new':
            // make the new object
            $this->objects[$id]=array('t'=>'image','data'=>$options['data'],'info'=>array());
            $this->objects[$id]['info']['Type']='/XObject';
            $this->objects[$id]['info']['Subtype']='/Image';
            $this->objects[$id]['info']['Width']=$options['iw'];
            $this->objects[$id]['info']['Height']=$options['ih'];
            if (!isset($options['type']) || $options['type']=='jpg'){
                if (!isset($options['channels'])){
                    $options['channels']=3;
                }
                switch($options['channels']){
                case 1:
                    $this->objects[$id]['info']['ColorSpace']='/DeviceGray';
                    break;
                default:
                    $this->objects[$id]['info']['ColorSpace']='/DeviceRGB';
                    break;
                }
                $this->objects[$id]['info']['Filter']='/DCTDecode';
                $this->objects[$id]['info']['BitsPerComponent']=8;
            } else if ($options['type']=='png'){
                if (strlen($options['pdata'])){
                    $this->numObj++;
                    $this->objects[$this->numObj]=array('t'=>'image','c'=>'','info'=>array());
                    $this->objects[$this->numObj]['info'] = array('Type'=>'/XObject', 'Subtype'=>'/Image', 'Width'=> $options['iw'], 'Height'=> $options['ih'], 'ColorSpace'=>'/DeviceGray', 'BitsPerComponent'=>'8', 'DecodeParms'=>'<< /Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns '.$options['iw'].' >>');
                    $this->objects[$this->numObj]['data']=$options['pdata'];
                    if (isset($options['transparency'])){
                        switch($options['transparency']['type']){
                        case 'indexed':
                            // temporary no transparency for indexed PNG images
                            //$tmp=' [ '.$options['transparency']['data'].' '.$options['transparency']['data'].'] ';
                            //$this->objects[$id]['info']['Mask'] = $tmp;
                            
                            $this->objects[$id]['info']['ColorSpace'] = ' [ /Indexed /DeviceRGB '.(strlen($options['pdata'])/3-1).' '.$this->numObj.' 0 R ]';
                            break;
                        case 'alpha':
                            $this->objects[$this->numObj]['info']['Filter'] = '/FlateDecode';
                            $this->objects[$id]['info']['SMask'] = $this->numObj.' 0 R';
                            $this->objects[$id]['info']['ColorSpace'] = '/'.$options['color'];
                            break;
                        }
                    }
                } else {
                    $this->objects[$id]['info']['ColorSpace']='/'.$options['color'];
                }
                $this->objects[$id]['info']['BitsPerComponent']=$options['bitsPerComponent'];
                $this->objects[$id]['info']['Filter']='/FlateDecode';
                $this->objects[$id]['data'] = $options['data'];
                $this->objects[$id]['info']['DecodeParms']='<< /Predictor 15 /Colors '.$options['ncolor'].' /Columns '.$options['iw'].' /BitsPerComponent '.$options['bitsPerComponent'].'>>';
            }
            // assign it a place in the named resource dictionary as an external object, according to
            // the label passed in with it.
            $this->o_pages($this->currentNode,'xObject',array('label'=>$options['label'],'objNum'=>$id));
            break;
        case 'out':
            $tmp=$o['data'];
            $res= "\n".$id." 0 obj\n<<";
            foreach($o['info'] as $k=>$v){
                $res.=" /".$k.' '.$v;
            }
            if ($this->encryptionMode > 0){
                $this->encryptInit($id);
                $tmp = $this->ARC4($tmp);
            }
            $res.=" /Length ".strlen($tmp)." >> stream\n".$tmp."\nendstream\nendobj";
            return $res;
            break;
        }
    }

    /**
     * encryption object.
     */
    private function o_encryption($id,$action,$options=''){
        if ($action!='new'){
            $o =& $this->objects[$id];
        }
        switch($action){
        case 'new':
            // make the new object
            $this->objects[$id]=array('t'=>'encryption','info'=>$options);
            $this->arc4_objnum=$id;
            
            // Pad or truncate the owner password
            $owner = substr($options['owner'].$this->encryptionPad,0,32);
            $user = substr($options['user'].$this->encryptionPad,0,32);
            
            $this->debug("o_encryption: user password (".$options['user'].") / owner password (".$options['owner'].")");
            
            // convert permission set into binary string
            $permissions = sprintf("%c%c%c%c", ($options['p'] & 255),  (($options['p'] >> 8) & 255) , (($options['p'] >> 16) & 255),  (($options['p'] >> 24) & 255));
            
            // Algo 3.3 Owner Password being set into /O Dictionary
            $this->objects[$id]['info']['O'] = $this->encryptOwner($owner, $user); 
            
            // Algo 3.5 User Password - START
            $this->objects[$id]['info']['U'] = $this->encryptUser($user, $this->objects[$id]['info']['O'], $permissions);
            // encryption key is set in encryptUser function
            break;
        case 'out':
            $res= "\n".$id." 0 obj\n<<";
            $res.=' /Filter /Standard';
            if($this->encryptionMode > 1){ // RC4 128bit encryption
                $res.=' /V 2';
                $res.=' /R 3';
                $res.=' /Length 128';
            } else { // RC4 40bit encryption
                $res.=' /V 1';
                $res.=' /R 2';
            }
            // use hex string instead of char code - char codes can make troubles (E.g. CR or LF)
            $res.=' /O <'.$this->strToHex($o['info']['O']).'>';
            $res.=' /U <'.$this->strToHex($o['info']['U']).'>';
            // and the p-value needs to be converted to account for the twos-complement approach
            //$o['info']['p'] = (($o['info']['p'] ^ 0xFFFFFFFF)+1)*-1;
            $res.=' /P '.($o['info']['p']);
            $res.=" >>\nendobj";
            return $res;
            break;
        }
    }
    
    /**
     * owner part of the encryption
     * @param $owner - owner password plus padding
     * @param $user - user password plus padding
     */
    private function encryptOwner($owner, $user){
        $keylength = 5;
        if($this->encryptionMode > 1){
            $keylength = 16;
        }
        
        $ownerHash = $this->md5_16($owner); // PDF 1.4 - repeat this 50 times in revision 3
        if($this->encryptionMode > 1) { // if it is the RC4 128bit encryption
            for($i = 0; $i < 50; $i++){
                $ownerHash = $this->md5_16($ownerHash);
            }
        }
        
        $ownerKey = substr($ownerHash,0,$keylength); // PDF 1.4 - Create the encryption key (IMPORTANT: need to check Length)
        
        $this->ARC4_init($ownerKey); // 5 bytes of the encryption key (hashed 50 times)
        $ovalue=$this->ARC4($user); // PDF 1.4 - Encrypt the padded user password using RC4
        
        if($this->encryptionMode > 1){
            $len = strlen($ownerKey);
            for($i = 1;$i<=19; ++$i){
                $ek = '';
                for($j=0; $j < $len; $j++){
                    $ek .= chr( ord($ownerKey[$j]) ^ $i );
                }
                $this->ARC4_init($ek);
                $ovalue = $this->ARC4($ovalue);
            }
        }
        return $ovalue;
    }
    
    /**
     * 
     * user part of the encryption
     * @param $user - user password plus padding
     * @param $ownerDict - encrypted owner entry
     * @param $permissions - permission set (print, copy, modify, ...)
     */
    function encryptUser($user,$ownerDict, $permissions){
        $keylength = 5;
        if($this->encryptionMode > 1){
            $keylength = 16;
        }
        // make hash with user, encrypted owner, permission set and fileIdentifier
        $hash = $this->md5_16($user.$ownerDict.$permissions.$this->hexToStr($this->fileIdentifier));
        
        // loop thru the hash process when it is revision 3 of encryption routine (usually RC4 128bit)
        if($this->encryptionMode > 1) {
            for ($i = 0; $i < 50; ++$i) {
                $hash = $this->md5_16(substr($hash, 0, $keylength)); // use only length of encryption key from the previous hash
            }
        }
        
        $this->encryptionKey = substr($hash,0,$keylength); // PDF 1.4 - Create the encryption key (IMPORTANT: need to check Length)
        
        if($this->encryptionMode > 1){ // if it is the RC4 128bit encryption
            // make a md5 hash from padding string (hardcoded by Adobe) and the fileIdenfier
            $userHash = $this->md5_16($this->encryptionPad.$this->hexToStr($this->fileIdentifier));
            
            // encrypt the hash from the previous method by using the encryptionKey
            $this->ARC4_init($this->encryptionKey);
            $uvalue=$this->ARC4($userHash);
            
            $len = strlen($this->encryptionKey);
            for($i = 1;$i<=19; ++$i){
                $ek = '';
                for($j=0; $j< $len; $j++){
                    $ek .= chr( ord($this->encryptionKey[$j]) ^ $i );
                }
                $this->ARC4_init($ek);
                $uvalue = $this->ARC4($uvalue);
            }
            $uvalue .= substr($this->encryptionPad,0,16);
        }else{ // if it is the RC4 40bit encryption
            $this->ARC4_init($this->encryptionKey);
            $uvalue=$this->ARC4($this->encryptionPad);
        }
        return $uvalue;
    }
    
    /**
     * internal method to convert string to hexstring (used for owner and user dictionary)
     * @param $string - any string value
     */
    private function strToHex($string)
    {
        $hex = '';
        for ($i=0; $i < strlen($string); $i++)
            $hex .= sprintf("%02x",ord($string[$i]));
        return $hex;
    }
    
    private function hexToStr($hex)
    {
        $str = '';
        for($i=0;$i<strlen($hex);$i+=2)
        $str .= chr(hexdec(substr($hex,$i,2)));
        return $str;
      }

    /**
     * calculate the 16 byte version of the 128 bit md5 digest of the string
     */
    private function md5_16($string){
        $tmp = md5($string);
        $out = pack("H*", $tmp);
        return $out;
    }

    /**
     * initialize the encryption for processing a particular object
     */
    private function encryptInit($id){
        $tmp = $this->encryptionKey;
        $hex = dechex($id);
        if (strlen($hex)<6){
            $hex = substr('000000',0,6-strlen($hex)).$hex;
        }
        $tmp.= chr(hexdec(substr($hex,4,2))).chr(hexdec(substr($hex,2,2))).chr(hexdec(substr($hex,0,2))).chr(0).chr(0);
        $key = $this->md5_16($tmp);
        if($this->encryptionMode > 1){
            $this->ARC4_init(substr($key,0,16)); // use max 16 bytes for RC4 128bit encryption key
        } else {
            $this->ARC4_init(substr($key,0,10)); // use (n + 5 bytes) for RC4 40bit encryption key
        }
    }

    /**
     * initialize the ARC4 encryption
     */
    private function ARC4_init($key=''){
        $this->arc4 = '';
        // setup the control array
        if (strlen($key)==0){
            return;
        }
        $k = '';
        while(strlen($k)<256){
            $k.=$key;
        }
        $k=substr($k,0,256);
        for ($i=0;$i<256;$i++){
            $this->arc4 .= chr($i);
        }
        $j=0;
        for ($i=0;$i<256;$i++){
            $t = $this->arc4[$i];
            $j = ($j + ord($t) + ord($k[$i]))%256;
            $this->arc4[$i]=$this->arc4[$j];
            $this->arc4[$j]=$t;
        }
    }

    /**
     * ARC4 encrypt a text string
     */
    private function ARC4($text){
        $len=strlen($text);
        $a=0;
        $b=0;
        $c = $this->arc4;
        $out='';
        for ($i=0;$i<$len;$i++){
            $a = ($a+1)%256;
            $t= $c[$a];
            $b = ($b+ord($t))%256;
            $c[$a]=$c[$b];
            $c[$b]=$t;
            $k = ord($c[(ord($c[$a])+ord($c[$b]))%256]);
            $out.=chr(ord($text[$i]) ^ $k);
        }
        return $out;
    }
    
    public function addComment($title, $text, $x, $y){
        $this->numObj++;
        $info = array('type'=>'text','title'=>$title, 'content'=>$text,'rect'=>array($x,$y,$x,$y));
        $this->o_annotation($this->numObj,'new',$info);
    }
    
    /**
     * add a link in the document to an external URL
     * @param string $url URL address
     * @param float $x0 bottom-left position in a rectangle
     * @param float $y0 top-left position in a rectangle
     * @param float $x0 bottom-right position in a rectangle
     * @param float $x0 top-right position in a rectangle
     */
    public function addLink($url,$x0,$y0,$x1,$y1){
        $this->numObj++;
        $info = array('type'=>'link','url'=>$url,'rect'=>array($x0,$y0,$x1,$y1));
        $this->o_annotation($this->numObj,'new',$info);
    }

    /**
     * add a link in the document to an internal destination (ie. within the document)
     * @param string $label label name of the destination
     * @param float $x0 bottom-left position in a rectangle
     * @param float $y0 top-left position in a rectangle
     * @param float $x0 bottom-right position in a rectangle
     * @param float $x0 top-right position in a rectangle
     */
    public function addInternalLink($label,$x0,$y0,$x1,$y1){
        $this->numObj++;
        $info = array('type'=>'ilink','label'=>$label,'rect'=>array($x0,$y0,$x1,$y1));
        $this->o_annotation($this->numObj,'new',$info);
    }

    /**
     * set the encryption of the document
     * can be used to turn it on and/or set the passwords which it will have.
     * also the functions that the user will have are set here, such as print, modify, add
     */
    public function setEncryption($userPass = '',$ownerPass = '',$pc = array(), $mode = 1){
        if($mode > 1){
            // increase the pdf version to support 128bit encryption
            if($this->pdfversion < 1.4) $this->pdfversion = 1.4;
            $p=bindec('01111111111111111111000011000000'); // revision 3 is using bit 3 - 6 AND 9 - 12
        }else{
            $mode = 1; // make sure at least the 40bit encryption is set
            $p=bindec('01111111111111111111111111000000'); // while revision 2 is using bit 3 - 6 only
        }
        
        $options = array(
            'print'=>4
            ,'modify'=>8
            ,'copy'=>16
            ,'add'=>32
            ,'fill'=>256
            ,'extract'=>512
            ,'assemble'=>1024
            ,'represent'=>2048
        );
        foreach($pc as $k=>$v){
            if ($v && isset($options[$k])){
                $p+=$options[$k];
            } else if (isset($options[$v])){
                $p+=$options[$v];
            }
        }
        
        // set the encryption mode to either RC4 40bit or RC4 128bit
        $this->encryptionMode = $mode;
        
        // implement encryption on the document
        if ($this->arc4_objnum == 0){
            // then the block does not exist already, add it.
            $this->numObj++;
            if (strlen($ownerPass)==0){
                $ownerPass=$userPass;
            }
            $this->o_encryption($this->numObj,'new',array('user'=>$userPass,'owner'=>$ownerPass,'p'=>$p));
        }
    }

    /**
     * should be used for internal checks, not implemented as yet
     */
    function checkAllHere() {
        // set the validation flag to true when everything is ok.
        // currently it only checks if output function has been called
        $this->valid = true;
    }

    /**
     * intialize a new document
     * if this is called on an existing document results may be unpredictable, but the existing document would be lost at minimum
     * this function is called automatically by the constructor function
     *
     */
    protected function newDocument($pageSize=array(0,0,612,792)){
        $this->numObj=0;
        $this->objects = array();

        $this->numObj++;
        $this->o_catalog($this->numObj,'new');

        $this->numObj++;
        $this->o_outlines($this->numObj,'new');

        $this->numObj++;
        $this->o_pages($this->numObj,'new');

        $this->o_pages($this->numObj,'mediaBox',$pageSize);
        $this->currentNode = 3;

        $this->o_pages($this->numObj, 'procset', '[/PDF/TEXT/ImageB/ImageC/ImageI]');

        $this->numObj++;
        $this->o_info($this->numObj,'new');

        $this->numObj++;
        $this->o_page($this->numObj,'new');

        // need to store the first page id as there is no way to get it to the user during
        // startup
        $this->firstPageId = $this->currentContents;
    }

    /**
     * open the font file and return a php structure containing it.
     * first check if this one has been done before and saved in a form more suited to php
     * note that if a php serialized version does not exist it will try and make one, but will
     * require write access to the directory to do it... it is MUCH faster to have these serialized
     * files.
     *
     * @param string $font Font name (can contain both path and extension)
     *
     * @return bool true on success, false on error
     */
    protected function openFont($font) {
        // $font should only contain the font name
        $fullFontPath = $this->fontPath.'/'.$font;
        
        $this->debug('openFont: '.$fullFontPath.' / IsUnicode: '.$this->isUnicode);
        // PATCH #13 - isUnicode cachedFile (font) problem | thank you jafjaf
        if ($this->isUnicode){
            $cachedFile = 'cached'.$font.'unicode.php';
        } else {
            $cachedFile = 'cached'.$font.'.php';
        }
        
        // use the temp folder to read/write cached font data
        if (file_exists($this->tempPath.'/'.$cachedFile)) {
            $cacheDate = filemtime($this->tempPath.'/'.$cachedFile);
            if(($cacheDate + $this->cacheTimeout) >= time() ) {
                $this->debug('openFont: font cache found in '.$this->tempPath.'/'.$cachedFile);
                $this->fonts[$font] = require($this->tempPath.'/'.$cachedFile);
                if (isset($this->fonts[$font]['_version_']) && $this->fonts[$font]['_version_'] == 3) {
                    // cache is valid - but without checking for a valid font path
                   return true;
                }
            }
        }
        
        // if no cache is found, parse the font file and rebuild the cache
        $this->debug('openFont: rebuilding font cache '.$cachedFile, E_USER_NOTICE);
        if(file_exists($fullFontPath.'.ttf') && class_exists('TTF')){
            $ttf = new TTF(file_get_contents($fullFontPath.'.ttf'));
            
            $head = $ttf->unmarshalHead();
            $uname = $ttf->unmarshalName();
            $hhea = $ttf->unmarshalHhea();
            $post = $ttf->unmarshalPost(true);
            $maxp = $ttf->unmarshalMAXP();
            $cmap = $ttf->unmarshalCmap();
            
            $cachedFont = array(
                'isUnicode' => $this->isUnicode, 
                'ItalicAngle' => $post['italicAngle'],
                'UnderlineThickness' => $post['underlineThickness'],
                'UnderlinePosition' => $post['underlinePosition'],
                'IsFixedPitch' => ($post['isFixedPitch'] == 0)? false : true,
                'Ascender' => $hhea['ascender'],
                'Descender' => $hhea['descender'],
                'LineGap' => $hhea['lineGap'],
                'FontName' => $font
            );
            
            foreach($uname['nameRecords'] as $v){
                if($v['nameID'] == 1 && $v['languageID'] == 0){
                    // fetch FontFamily from Default language (en?)
                    $cachedFont['FamilyName'] = preg_replace('/\x00/','',$v['value']);
                } else if($v['nameID'] == 2 && $v['languageID'] == 0){
                    // fetch font weight from Default language (en?)
                    $cachedFont['Weight'] = preg_replace('/\x00/','',$v['value']);
                } else if($v['nameID'] == 3 && $v['languageID'] == 0){
                    // fetch Unique font name from Default language (en?)
                    $cachedFont['UniqueName'] = preg_replace('/\x00/','',$v['value']);
                } else if($v['nameID'] == 4 && $v['languageID'] == 0){
                    // fetch font name (full style) from Default language (en?)
                    $cachedFont['FullName'] = preg_replace('/\x00/','',$v['value']);
                } else if($v['nameID'] == 5 && $v['languageID'] == 0){
                    // fetch version from Default language (en?)
                    $cachedFont['Version'] = preg_replace('/\x00/','',$v['value']);
                }
            }
            
            // calculate the bounding box properly by using 'units per em' property
            $cachedFont['FontBBox'] = array(
                                        intval($head['xMin'] / ($head['unitsPerEm'] / 1000)),
                                        intval($head['yMin'] / ($head['unitsPerEm'] / 1000)), 
                                        intval($head['xMax'] / ($head['unitsPerEm'] / 1000)), 
                                        intval($head['yMax'] / ($head['unitsPerEm'] / 1000))
                                    );
            $cachedFont['UnitsPerEm'] = $head['unitsPerEm'];
            
            $encodingTable = array();
            
            $hmetrics = $ttf->unmarshalHmtx($hhea['numberOfHMetrics'],$maxp['numGlyphs']);
            
            // get format 6 or format 4 as primary cmap table map glyph with character
            foreach($cmap['tables'] as $v){
                if(isset($v['format']) && $v['format'] == "4"){
                    $encodingTable = $v;
                    break;
                }
            }
            
            if($encodingTable['format'] == '4') {
                $glyphsIndices = range(1, $maxp['numGlyphs']);
                $charToGlyph = array();
                
                $segCount = $encodingTable['segCount'];
                $endCountArray = $encodingTable['endCountArray'];
                $startCountArray = $encodingTable['startCountArray'];
                $idDeltaArray = $encodingTable['idDeltaArray'];
                $idRangeOffsetArray = $encodingTable['idRangeOffsetArray'];
                $glyphIdArray = $encodingTable['glyphIdArray'];
                
                for ($seg = 0; $seg < $segCount; $seg++) {
                $endCount = $endCountArray[$seg];
                $startCount = $startCountArray[$seg];
                $idDelta = $idDeltaArray[$seg];
                $idRangeOffset = $idRangeOffsetArray[$seg];
                    for ($charCode = $startCount; $charCode <= $endCount; $charCode++) {
                        if ($idRangeOffset != 0) {
                        $j = $charCode - $startCount + $seg + $idRangeOffset / 2 - $segCount;
                        $gid0 = $glyphIdArray[$j];
                        } else {
                        $gid0 = $idDelta + $charCode;
                        }
                        $gid0 %= 65536;
                        if (in_array($gid0, $glyphsIndices)) {
                            $charToGlyph[sprintf("%d", $charCode)] = $gid0;
                        }
                    }
                }
				
				if($this->isUnicode)
					$cidtogid = str_pad('', 256*256*2, "\x00");
				
                $cachedFont['C'] = array();
                foreach($charToGlyph as $char => $glyphIndex){
                    if(!empty($char)){
                        $m = TTF::getHMetrics($hmetrics, $hhea['numberOfHMetrics'], $glyphIndex);
                        // calculate the correct char width by dividing it with 'units per em'
                        $cachedFont['C'][$char] = intval($m[0] / ($head['unitsPerEm'] / 1000)); 
                        if($this->isUnicode){
                            if($char >= 0){
                                if ($char >= 0 && $char < 0xFFFF && $glyphIndex) {
                                    $cidtogid[$char*2] = chr($glyphIndex >> 8);
                                    $cidtogid[$char*2 + 1] = chr($glyphIndex & 0xFF);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->debug('openFont: font file does not contain format 4 cmap', E_USER_WARNING);
            }
            
			if(isset($cidtogid))
            	$cachedFont['CIDtoGID'] = base64_encode($cidtogid);
            
        } else if(file_exists($fullFontPath.'.afm')){
            // use the core font program
            $cachedFont = array('isUnicode' => false );
            
            $file = file($fullFontPath.'.afm');
            foreach ($file as $row) {
                $row=trim($row);
                $pos=strpos($row,' ');
                if ($pos) {
                    // then there must be some keyword
                    $key = substr($row,0,$pos);
                    switch ($key) {
                    case 'FontName':
                    case 'FullName':
                    case 'FamilyName':
                    case 'Weight':
                    case 'ItalicAngle':
                    case 'IsFixedPitch':
                    case 'CharacterSet':
                    case 'UnderlinePosition':
                    case 'UnderlineThickness':
                    case 'Version':
                    case 'EncodingScheme':
                    case 'CapHeight':
                    case 'XHeight':
                    case 'Ascender':
                    case 'Descender':
                    case 'StdHW':
                    case 'StdVW':
                    case 'StartCharMetrics':
                        $cachedFont[$key]=trim(substr($row,$pos));
                        break;
                    case 'FontBBox':
                        $cachedFont[$key]=explode(' ',trim(substr($row,$pos)));
                        break;
                    case 'C':
                        // C 39 ; WX 222 ; N quoteright ; B 53 463 157 718 ;
                        // use preg_match instead to improve performace
                        // IMPORTANT: if "L i fi ; L l fl ;" is required preg_match must be amended
                        $r = preg_match('/C (-?\d+) ; WX (-?\d+) ; N (\w+) ; B (-?\d+) (-?\d+) (-?\d+) (-?\d+) ;/', $row, $m);
                        if($r == 1){
                            //$dtmp = array('C'=> $m[1],'WX'=> $m[2], 'N' => $m[3], 'B' => array($m[4], $m[5], $m[6], $m[7]));
                            $c = (int)$m[1];
                            $n = $m[3];
                            $width = floatval($m[2]);
            
                            if($c >= 0){
                                if ($c != hexdec($n)) {
                                    $cachedFont['codeToName'][$c] = $n;
                                  }
                                $cachedFont['C'][$c] = $width;
                                $cachedFont['C'][$n] = $width;
                            }else{
                                $cachedFont['C'][$n] = $width;
                            }
                            
                            if (!isset($cachedFont['MissingWidth']) && $c == -1 && $n === '.notdef') {
                                  $cachedFont['MissingWidth'] = $width;
                            }
                        }
                        break;
                    }
                }
            }
        } else {
            $this->debug(sprintf('openFont: no font file found for "%s" IsUnicode: %b', $font, $this->isUnicode), E_USER_ERROR);
            return false;
        }
        
        $cachedFont['_version_']=3;
        // store the data in as cached file and in $this->fonts array
        $this->fonts[$font]=$cachedFont;
        $fp = fopen($this->tempPath.'/'.$cachedFile,'w'); // use the temp folder to write cached font data
        fwrite($fp,'<?php /* R&OS php pdf class font cache file */ return '.var_export($cachedFont,true).'; ?>');
        fclose($fp);
        
        return true;
    }

    /**
     * if the font is not loaded then load it and make the required object
     * else just make it the current font
     * the encoding array can contain 'encoding'=> 'none','WinAnsiEncoding','MacRomanEncoding' or 'MacExpertEncoding'
     * note that encoding='none' will need to be used for symbolic fonts
     * and 'differences' => an array of mappings between numbers 0->255 and character names.
     *
     * @param string  $fontName Name of the font incl. path
     * @param string  $encoding Which encoding to use
     * @param integer $set used to force set the selected font
     * @param bool $subsetFont allow font subsetting
     * @return void
     */
    public function selectFont($fontName, $encoding = '', $set = 1, $subsetFont = false)
    {
        if($subsetFont && !class_exists('TTFsubset')){
            $this->debug("TTFsubset class not found. Falling back to complete font program", E_USER_WARNING);
            $subsetFont = false;
        }
        
        // old font selection containing full path
        $pos = strrpos($fontName, '/');
        if ($pos !== false) {
            $fontName = substr($fontName, $pos + 1);
        }
        
        // file extension found
        $pos = strrpos($fontName, '.');
        if($pos){
            $ext = substr($fontName, $pos + 1);
            $fontName = substr($fontName, 0, $pos);
        } else {
            // default extension is ttf
            $ext = 'ttf';
        }
        
        if (!isset($this->fonts[$fontName])){
            // check and load the font file, on no errors $ok = true
            $ok = $this->openFont($fontName);
            if(!$ok){
                $fontName = 'Helvetica';
                if(!isset($this->fonts[$fontName])){
                    $this->debug("Error while loading coreFont - check \$pdf->fontPath and/or define one coreFont as fallback", E_USER_ERROR);
                    die;
                }
            } else if(isset($this->fonts[$fontName])) {
                $this->numObj++;
                $this->numFonts++;
                
                $font = &$this->fonts[$fontName];
                $options = array('name' => $fontName, 'fontFileName'=>$fontName); // orgFontName is necessary when font subsetting is used
                
                if (is_array($encoding)){
                    // then encoding and differences might be set
                    if (isset($encoding['encoding'])){
                        $options['encoding'] = $encoding['encoding'];
                    }
                    if (isset($encoding['differences'])){
                        $options['differences'] = $encoding['differences'];
                    }
                } else if (strlen($encoding)){
                    // then perhaps only the encoding has been set
                    $options['encoding'] = $encoding;
                }
                $fontObj = $this->numObj;
                $this->o_font($fontObj, 'new', $options);
                $font['fontNum'] = $this->numFonts;
                // if this is a '.afm' font, and there is a '.pfa' file to go with it (as there
                // should be for all non-basic fonts), then load it into an object and put the
                // references into the font object
                
                $fbtype = '';
                if (file_exists($this->fontPath.'/'.$fontName.'.pfb')){
                    $fbtype = 'pfb';
                } else if (file_exists($this->fontPath.'/'.$fontName.'.ttf')){
                    $fbtype = 'ttf';
                }
                
                if ($fbtype){
                    $adobeFontName = $font['FontName'];
                    $this->debug('selectFont: adding font "'.$fontName.'" to pdf');
                    // find the array of fond widths, and put that into an object.
                    $firstChar = -1;
                    $lastChar = 0;
                    $widths = array();
                    $cid_widths = array();
                    
					if(!$font['isUnicode']){
						for($i = 0; $i < 255; $i++){
							if (isset($options['differences']) && isset($options['differences'][$i])){
								// set the correct width of the diffence by using its name
								$widths[] = $font['C'][$options['differences'][$i]];
							} else if(isset($font['C'][$i]))
								$widths[] = $font['C'][$i];
							else
								$widths[] = 0;
						}
						$firstChar = 0;
						$lastChar = 255;
					}
					
                    // also need to adjust the widths for the differences array
                    /*if (isset($options['differences'])){
                        foreach ($options['differences'] as $charNum => $charName){
                            if ($charNum>$lastChar){
                                for($i = $lastChar + 1; $i <= $charNum; $i++) {
                                    $widths[]=0;
                                }
                                $lastChar = $charNum;
                            }
                            if (isset($font['C'][$charName])){
                                $widths[$charNum-$firstChar]=$font['C'][$charName];
                                if($font['isUnicode']){
                                    $cid_widths[$charName] = $font['C'][$charName];
                                }
                            }
                        }
                    }*/
                    
                    if($font['isUnicode']){
                        $font['CIDWidths'] = $font['C'];
                    }
                    $this->debug('selectFont: FirstChar='.$firstChar);
                    $this->debug('selectFont: LastChar='.$lastChar);
                    
                    $widthid = -1;
                    
                    if(!$font['isUnicode']){
                        $this->numObj++;
                        $this->o_contents($this->numObj, 'new', 'raw');
                        $this->objects[$this->numObj]['c'].='['.implode(' ', $widths).']';
                        $widthid = $this->numObj;
                    }
                    
                    $missing_width = 500;
                    $stemV = 70;
                    
                    if (isset($font['MissingWidth'])) {
                        $missing_width = $font['MissingWidth'];
                      }
                      if (isset($font['StdVW'])) {
                        $stemV = $font['StdVW'];
                      } else if (isset($font['Weight']) && preg_match('!(bold|black)!i', $font['Weight'])) {
                        $stemV = 120;
                      }

                    // create the font descriptor
                    $fontDescriptorId = ++$this->numObj;
                    
                    // determine flags (more than a little flakey, hopefully will not matter much)
                    $flags=0;
                    if ($font['ItalicAngle']!=0){
                        $flags+=pow(2,6);
                    }
                    if ($font['IsFixedPitch']=='true'){
                        $flags+=1;
                    }
                    $flags+=pow(2,5); // assume non-sybolic

                    $list = array('Ascent'=>'Ascender','CapHeight'=>'CapHeight','Descent'=>'Descender','FontBBox'=>'FontBBox','ItalicAngle'=>'ItalicAngle');
                    $fdopt = array(
                        'Flags' => $flags,
                        'FontName' => $adobeFontName,
                        'StemV' => $stemV
                    );
                    foreach($list as $k=>$v){
                        if (isset($font[$v])){
                            $fdopt[$k]=$font[$v];
                        }
                    }
                    
                    // setup the basic properties for o_font output
                    $tmp = array('BaseFont'=>$adobeFontName,'Widths'=>$widthid
                                      ,'FirstChar'=>$firstChar,'LastChar'=>$lastChar
                                      ,'FontDescriptor'=>$fontDescriptorId);
                    
                    // binary content of pfb or ttf file
                    $pfbid = ++$this->numObj;
                    
                    // embed the font program
                    // to allow font subsets embedding fonts is proceed in o_font 'output'
                    if($this->embedFont){
                        if ($fbtype=='pfb'){
                            $fdopt['FontFile']=$pfbid;
                        } else if ($fbtype=='ttf'){
                            $fdopt['FontFile2']=$pfbid;
                            $tmp['SubType']='TrueType'; // Declare basic font as TrueType
                        }
                        $this->o_fontDescriptor($fontDescriptorId,'new',$fdopt);
                        $this->o_contents($pfbid,'new');
                    }
                    
                    $this->debug('selectFont: adding extra info to font.('.$fontObj.')');
                    foreach($tmp as $fk=>$fv){
                        $this->debug($fk." : ".$fv);
                    }
                    $this->o_font($fontObj,'add',$tmp);

                } else if(!in_array(strtolower($fontName), $this->coreFonts)) {
                    $this->debug('selectFont: No pfb/ttf file found for "'.$fontName.'"', E_USER_WARNING);
                }

                // also set the differences here, note that this means that these will take effect only the
                // first time that a font is selected, else they are ignored
                if (isset($options['differences'])){
                    $font['differences']=$options['differences'];
                }
            }
        }
        
        $this->fonts[$fontName]['isSubset'] = $subsetFont;
        if(!isset($this->fonts[$fontName]['subset'])) {
            $this->fonts[$fontName]['subset'] = array();
        }
        
        if ($set && isset($this->fonts[$fontName])){
            // so if for some reason the font was not set in the last one then it will not be selected
            $this->currentBaseFont=$fontName;
            // the next line means that if a new font is selected, then the current text state will be
            // applied to it as well.
            $this->setCurrentFont();
        }
        //return $this->currentFontNum;
    }

    /**
     * sets up the current font, based on the font families, and the current text state
     * note that this system is quite flexible, a <b><i> font can be completely different to a
     * <i><b> font, and even <b><b> will have to be defined within the family to have meaning
     * This function is to be called whenever the currentTextState is changed, it will update
     * the currentFont setting to whatever the appropriatte family one is.
     * If the user calls selectFont themselves then that will reset the currentBaseFont, and the currentFont
     * This function will change the currentFont to whatever it should be, but will not change the
     * currentBaseFont.
     *
     */
    protected function setCurrentFont(){
        if (strlen($this->currentBaseFont)==0){
            // then assume an initial font
            $this->selectFont('Helvetica');
        }
        
        $cf = $this->currentBaseFont;
        
        if (strlen($this->currentTextState)
            && isset($this->fontFamilies[$cf])
            && isset($this->fontFamilies[$cf][$this->currentTextState])){
            // then we are in some state or another
            // and this font has a family, and the current setting exists within it
            // select the font, then return it
            $nf = $this->fontFamilies[$cf][$this->currentTextState];
            // PATCH #14 - subset file fix when using font family | thank you johannes
            $isSubset = false;
            if (isset($this->fonts[$this->currentBaseFont]['isSubset'])) $isSubset = $this->fonts[$this->currentBaseFont]['isSubset'];
            $this->selectFont($nf,'', 0, $isSubset);
            $this->currentFont = $nf;
            $this->currentFontNum = $this->fonts[$nf]['fontNum'];
        } else {
            // the this font must not have the right family member for the current state
            // simply assume the base font
            $this->currentFont = $cf;
            $this->currentFontNum = $this->fonts[$cf]['fontNum'];
        }
    }

	/**
	 * get the current font name being used
	 * @since 0.12-rc12
	 * @param bool $withStyle force to receive the style font name, instead of the base font
	 * @return string current font name
	 */
	public function getCurrentFont($withStyle = false){
		if($withStyle){
			return $this->currentFont;
		}
		return $this->currentBaseFont;
	}
	
    /**
     * function for the user to find out what the ID is of the first page that was created during
     * startup - useful if they wish to add something to it later.
     */
    protected function getFirstPageId(){
        return $this->firstPageId;
    }

    /**
     * add content to the currently active object
     */
    protected function addContent($content){
        $this->objects[$this->currentContents]['c'].=$content;
    }

    /**
     * sets the colour for fill operations
     */
    public function setColor($r,$g,$b,$force=0){
        if ($r>=0 && ($force || $r!=$this->currentColour['r'] || $g!=$this->currentColour['g'] || $b!=$this->currentColour['b'])){
            $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$r).' '.sprintf('%.3F',$g).' '.sprintf('%.3F',$b).' rg';
            $this->currentColour=array('r'=>$r,'g'=>$g,'b'=>$b);
        }
    }

    /**
     * sets the colour for stroke operations
     */
    public function setStrokeColor($r,$g,$b,$force=0){
        if ($r>=0 && ($force || $r!=$this->currentStrokeColour['r'] || $g!=$this->currentStrokeColour['g'] || $b!=$this->currentStrokeColour['b'])){
            $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$r).' '.sprintf('%.3F',$g).' '.sprintf('%.3F',$b).' RG';
            $this->currentStrokeColour=array('r'=>$r,'g'=>$g,'b'=>$b);
        }
    }

    /**
     * draw a line from one set of coordinates to another
     */
    public function line($x1,$y1,$x2,$y2){
        $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$x1).' '.sprintf('%.3F',$y1).' m '.sprintf('%.3F',$x2).' '.sprintf('%.3F',$y2).' l S';
    }

    /**
     * draw a bezier curve based on 4 control points
     */
    public function curve($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3){
        // in the current line style, draw a bezier curve from (x0,y0) to (x3,y3) using the other two points
        // as the control points for the curve.
        $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$x0).' '.sprintf('%.3F',$y0).' m '.sprintf('%.3F',$x1).' '.sprintf('%.3F',$y1);
        $this->objects[$this->currentContents]['c'].= ' '.sprintf('%.3F',$x2).' '.sprintf('%.3F',$y2).' '.sprintf('%.3F',$x3).' '.sprintf('%.3F',$y3).' c S';
    }

    /**
     * draw a part of an ellipse
     */
    public function partEllipse($x0,$y0,$astart,$afinish,$r1,$r2=0,$angle=0,$nSeg=8){
        $this->ellipse($x0,$y0,$r1,$r2,$angle,$nSeg,$astart,$afinish,0);
    }

    /**
     * draw a filled ellipse
     */
    public function filledEllipse($x0,$y0,$r1,$r2=0,$angle=0,$nSeg=8,$astart=0,$afinish=360){
        return $this->ellipse($x0,$y0,$r1,$r2=0,$angle,$nSeg,$astart,$afinish,1,1);
    }

    /**
     * draw an ellipse
     * note that the part and filled ellipse are just special cases of this function
     *
     * draws an ellipse in the current line style
     * centered at $x0,$y0, radii $r1,$r2
     * if $r2 is not set, then a circle is drawn
     * nSeg is not allowed to be less than 2, as this will simply draw a line (and will even draw a
     * pretty crappy shape at 2, as we are approximating with bezier curves.
     */
    public function ellipse($x0,$y0,$r1,$r2=0,$angle=0,$nSeg=8,$astart=0,$afinish=360,$close=1,$fill=0){
        if ($r1==0){
            return;
        }
        if ($r2==0){
            $r2=$r1;
        }
        if ($nSeg<2){
            $nSeg=2;
        }

        $astart = deg2rad((float)$astart);
        $afinish = deg2rad((float)$afinish);
        $totalAngle =$afinish-$astart;

        $dt = $totalAngle/$nSeg;
        $dtm = $dt/3;

        if ($angle != 0){
            $a = -1*deg2rad((float)$angle);
            $tmp = "\n q ";
            $tmp .= sprintf('%.3F',cos($a)).' '.sprintf('%.3F',(-1.0*sin($a))).' '.sprintf('%.3F',sin($a)).' '.sprintf('%.3F',cos($a)).' ';
            $tmp .= sprintf('%.3F',$x0).' '.sprintf('%.3F',$y0).' cm';
            $this->objects[$this->currentContents]['c'].= $tmp;
            $x0=0;
            $y0=0;
        }

        $t1 = $astart;
        $a0 = $x0+$r1*cos($t1);
        $b0 = $y0+$r2*sin($t1);
        $c0 = -$r1*sin($t1);
        $d0 = $r2*cos($t1);

        $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$a0).' '.sprintf('%.3F',$b0).' m ';
        for ($i=1;$i<=$nSeg;$i++){
            // draw this bit of the total curve
            $t1 = $i*$dt+$astart;
            $a1 = $x0+$r1*cos($t1);
            $b1 = $y0+$r2*sin($t1);
            $c1 = -$r1*sin($t1);
            $d1 = $r2*cos($t1);
            $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',($a0+$c0*$dtm)).' '.sprintf('%.3F',($b0+$d0*$dtm));
            $this->objects[$this->currentContents]['c'].= ' '.sprintf('%.3F',($a1-$c1*$dtm)).' '.sprintf('%.3F',($b1-$d1*$dtm)).' '.sprintf('%.3F',$a1).' '.sprintf('%.3F',$b1).' c';
            $a0=$a1;
            $b0=$b1;
            $c0=$c1;
            $d0=$d1;
        }
        if ($fill){
            $this->objects[$this->currentContents]['c'].=' f';
        } else {
            if ($close){
                $this->objects[$this->currentContents]['c'].=' s'; // small 's' signifies closing the path as well
            } else {
                $this->objects[$this->currentContents]['c'].=' S';
            }
        }
        if ($angle !=0){
            $this->objects[$this->currentContents]['c'].=' Q';
        }
    }

    /**
     * this sets the line drawing style.
     * width, is the thickness of the line in user units
     * cap is the type of cap to put on the line, values can be 'butt','round','square'
     *    where the diffference between 'square' and 'butt' is that 'square' projects a flat end past the
     *    end of the line.
     * join can be 'miter', 'round', 'bevel'
     * dash is an array which sets the dash pattern, is a series of length values, which are the lengths of the
     *   on and off dashes.
     *   (2) represents 2 on, 2 off, 2 on , 2 off ...
     *   (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
     * phase is a modifier on the dash pattern which is used to shift the point at which the pattern starts.
     */
    public function setLineStyle($width=1,$cap='',$join='',$dash='',$phase=0){

        // this is quite inefficient in that it sets all the parameters whenever 1 is changed, but will fix another day
        $string = '';
        if ($width>0){
            $string.= $width.' w';
        }
        $ca = array('butt'=>0,'round'=>1,'square'=>2);
        if (isset($ca[$cap])){
            $string.= ' '.$ca[$cap].' J';
        }
        $ja = array('miter'=>0,'round'=>1,'bevel'=>2);
        if (isset($ja[$join])){
            $string.= ' '.$ja[$join].' j';
        }
        if (is_array($dash)){
            $string.= ' [';
            foreach ($dash as $len){
                $string.=' '.$len;
            }
            $string.= ' ] '.$phase.' d';
        }
        $this->currentLineStyle = $string;
        $this->objects[$this->currentContents]['c'].="\n".$string;
    }

    /**
     * draw a polygon, the syntax for this is similar to the GD polygon command
     */
    public function polygon($p,$np,$f=0){
        $this->objects[$this->currentContents]['c'].="\n";
        $this->objects[$this->currentContents]['c'].=sprintf('%.3F',$p[0]).' '.sprintf('%.3F',$p[1]).' m ';
        for ($i=2;$i<$np*2;$i=$i+2){
            $this->objects[$this->currentContents]['c'].= sprintf('%.3F',$p[$i]).' '.sprintf('%.3F',$p[$i+1]).' l ';
        }
        if ($f==1){
            $this->objects[$this->currentContents]['c'].=' f';
        } else {
            $this->objects[$this->currentContents]['c'].=' S';
        }
    }

    /**
     * a filled rectangle, note that it is the width and height of the rectangle which are the secondary paramaters, not
     * the coordinates of the upper-right corner
     */
    public function filledRectangle($x1,$y1,$width,$height){
        $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$x1).' '.sprintf('%.3F',$y1).' '.sprintf('%.3F',$width).' '.sprintf('%.3F',$height).' re f';
    }

    /**
     * draw a rectangle, note that it is the width and height of the rectangle which are the secondary paramaters, not
     * the coordinates of the upper-right corner
     */
    public function rectangle($x1,$y1,$width,$height){
        $this->objects[$this->currentContents]['c'].="\n".sprintf('%.3F',$x1).' '.sprintf('%.3F',$y1).' '.sprintf('%.3F',$width).' '.sprintf('%.3F',$height).' re S';
    }

    /**
     * add a new page to the document
     * this also makes the new page the current active object
     */
    public function newPage($insert=0,$id=0,$pos='after'){

        // if there is a state saved, then go up the stack closing them
        // then on the new page, re-open them with the right setings

        if ($this->nStateStack){
            for ($i=$this->nStateStack;$i>=1;$i--){
                $this->restoreState($i);
            }
        }

        $this->numObj++;
        if ($insert){
            // the id from the ezPdf class is the od of the contents of the page, not the page object itself
            // query that object to find the parent
            $rid = $this->objects[$id]['onPage'];
            $opt= array('rid'=>$rid,'pos'=>$pos);
            $this->o_page($this->numObj,'new',$opt);
        } else {
            $this->o_page($this->numObj,'new');
        }
        // if there is a stack saved, then put that onto the page
        if ($this->nStateStack){
            for ($i=1;$i<=$this->nStateStack;$i++){
                $this->saveState($i);
            }
        }
        // and if there has been a stroke or fill colour set, then transfer them
        if ($this->currentColour['r']>=0){
            $this->setColor($this->currentColour['r'],$this->currentColour['g'],$this->currentColour['b'],1);
        }
        if ($this->currentStrokeColour['r']>=0){
            $this->setStrokeColor($this->currentStrokeColour['r'],$this->currentStrokeColour['g'],$this->currentStrokeColour['b'],1);
        }

        // if there is a line style set, then put this in too
        if (strlen($this->currentLineStyle)){
            $this->objects[$this->currentContents]['c'].="\n".$this->currentLineStyle;
        }

        // the call to the o_page object set currentContents to the present page, so this can be returned as the page id
        return $this->currentContents;
    }

	/**
     * return the pdf stream as a string returned from the function
     * This method is protect to force user to use ezOutput from Cezpdf.php
     */
    function output($debug=0){
        if ($debug){
            // turn compression off
            $this->options['compression']=0;
        }

        if ($this->arc4_objnum){
            $this->ARC4_init($this->encryptionKey);
        }
        
        if($this->valid){
            $this->debug('The output method has been executed again', E_USER_WARNING);
        }

        $this->checkAllHere();

        $xref=array();
        // set the pdf version dynamically, depended on the objects being used
        $content="%PDF-".sprintf('%.1F', $this->pdfversion)."\n%\xe2\xe3\xcf\xd3";
        $pos=strlen($content);
        foreach($this->objects as $k=>$v){
            $tmp='o_'.$v['t'];
            $cont=$this->$tmp($k,'out');
            $content.=$cont;
            $xref[]=$pos;
            $pos+=strlen($cont);
        }
        ++$pos;
        $content.="\nxref\n0 ".(count($xref)+1)."\n0000000000 65535 f \n";
        foreach($xref as $p){
            $content.=substr('0000000000',0,10-strlen($p+1)).($p+1)." 00000 n \n";
        }
        $content.="trailer\n<< /Size ".(count($xref)+1)." /Root 1 0 R /Info ".$this->infoObject." 0 R";
        // if encryption has been applied to this document then add the marker for this dictionary
        if ($this->arc4_objnum > 0){
            $content .= " /Encrypt ".$this->arc4_objnum." 0 R";
        }
        if ($this->fileIdentifier){
            $content .= " /ID [<".$this->fileIdentifier."><".$this->fileIdentifier.">]";
        }
        $content .= " >>\nstartxref\n".$pos."\n%%EOF\n";
        return $content;
    }

    /**
     * output the pdf code, streaming it to the browser
     * the relevant headers are set so that hopefully the browser will recognise it
     * this method is protected to force user to use ezStream method from Cezpdf.php
     */
    protected function stream($options=''){
        // setting the options allows the adjustment of the headers
        // values at the moment are:
        // 'Content-Disposition'=>'filename'  - sets the filename, though not too sure how well this will
        //        work as in my trial the browser seems to use the filename of the php file with .pdf on the end
        // 'Accept-Ranges'=>1 or 0 - if this is not set to 1, then this header is not included, off by default
        //    this header seems to have caused some problems despite tha fact that it is supposed to solve
        //    them, so I am leaving it off by default.
        // 'compress'=> 1 or 0 - apply content stream compression, this is on (1) by default
        // 'download'=> 1 or 0 - provide download dialog
        if (!is_array($options)){
            $options=array();
        }
        if ( isset($options['compress']) && $options['compress']==0){
            $tmp = $this->output(1);
        } else {
            $tmp = $this->output();
        }
		
        ob_start();
        echo $tmp;
		
		$length = ob_get_length();
		
		header("Content-Type: application/pdf");
        header("Content-Length: ".$length);
        $fileName = (isset($options['Content-Disposition'])?$options['Content-Disposition']:'file.pdf');
        if(isset($options['download']) && $options['download'] == 1)
            $attached = 'attachment';
        else
            $attached = 'inline';
        header("Content-Disposition: $attached; filename=".$fileName);
        if (isset($options['Accept-Ranges']) && $options['Accept-Ranges']==1){
            header("Accept-Ranges: ".$length);
        }
		
		ob_end_flush();
    }

    /**
     * return the height in units of the current font in the given size
     */
    public function getFontHeight($size){
        if (!$this->numFonts){
            $this->selectFont('./fonts/Helvetica');
        }
        
        $font = &$this->fonts[$this->currentFont];
        // for the current font, and the given size, what is the height of the font in user units
        $h = $font['FontBBox'][3] - $font['FontBBox'][1];
        return $size*$h/1000;
    }

    /**
     * return the font decender, this will normally return a negative number
     * if you add this number to the baseline, you get the level of the bottom of the font
     * it is in the pdf user units
     */
    public function getFontDecender($size){
        // note that this will most likely return a negative value
        if (!$this->numFonts){
            $this->selectFont('./fonts/Helvetica');
        }
        $h = $this->fonts[$this->currentFont]['Descender'];
        return $size*$h/1000;
    }
   
    /**
     * filter the text, this is applied to all text just before being inserted into the pdf document
     * it escapes the various things that need to be escaped, and so on
     *
     */
    protected function filterText($text, $bom = true, $convert_encoding = true){
        $cf = $this->currentFont;
        if ($convert_encoding &&isset($this->fonts[$cf]) && $this->fonts[$cf]['isUnicode']) {
            $text = mb_convert_encoding($text, 'UTF-16BE','UTF-8');
            
            // store all used characters if subset font is set to true
            if($this->fonts[$cf]['isSubset']){
                for($i = 0; $i < mb_strlen($text,'UTF-16BE'); $i++)
                    $this->fonts[$cf]['subset'][mb_substr($text,$i, 1, 'UTF-16BE')] = true;
            }
        } else if(!$this->fonts[$cf]['isUnicode']) {
            $text = mb_convert_encoding($text, $this->targetEncoding, 'UTF-8');
            // store all used characters if subset font is set to true
            if($this->fonts[$cf]['isSubset']){
                for($i = 0; $i < strlen($text); $i++)
                    $this->fonts[$cf]['subset'][$text[$i]] = true;
            }
        }
        
        $text = strtr($text,  array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(8) => '\\b', chr(9) => '\\t', chr(10) => '\\n', chr(12) => '\\f' ,chr(13) => '\\r', '&lt;'=>'<', '&gt;'=>'>', '&amp;'=>'&') );

        if($this->rtl){
            $text = strrev($text);
        }

        return $text;
    }

    private function getDirectives(&$text, &$x, &$y, $width = 0, $size = 0, $justification = 'left',$angle = 0, &$wordSpaceAdjust = 0, $noCB = false){
        
        $regex = "/<\/?([cC]:|)(".$this->allowedTags.")\>/";
        $cb = array();
        $r = preg_match_all($regex, $text, $regs, PREG_OFFSET_CAPTURE);
        if($r){
            // backup current font style
            $store_currentTextState = $this->currentTextState;
        
            $nx = $x;
            $ny = $y;
            if($this->nCallback > 0){
                $cb[0] = $this->callback[1];
                $cb[0]['x'] = $x;
                $cb[0]['y'] = $y;
                $cb[0]['startTag'] = 0;
                $cb[0]['endTag'] = 0;
            }

            $restWidth = $width;
            reset($regs[0]);
            
            $prevEndTagIndex = 0;
            while(list($k,$curTag) = each($regs[0])){
                $curTagIndex = mb_strlen(substr($text, 0, $curTag[1]), 'UTF-8');
                $endTagIndex = $curTagIndex + strlen($curTag[0]);
                
                $tmpstr = mb_substr($text, $prevEndTagIndex, $curTagIndex - $prevEndTagIndex, 'UTF-8');
                $tmpstr = $this->filterText($tmpstr, false, false);
                $tmp = $this->getTextLength($size, $tmpstr, $restWidth, $angle, $wordSpaceAdjust);
                // if the text does not fit to $width, $tmp[2] contains the length to break the line
                if($tmp[2] > 0){
                    // position where the line break occurs
                    $lbpos = $prevEndTagIndex + $tmp[2];
                    
                    // adjust to position if justification is set
                    if($nx > $x){
                        $tmpw = ($width - $restWidth) + $tmp[0];
                        // force word wrap when something already written
                        /*$lbpos -= $tmp[2];
                        if($prevEndTagIndex == $lbpos){
                            $lastCallback = array_pop($cb);
                            
                            if(isset($lastCallback)){
                                $lbpos -= $lastCallback['endTag'] - $lastCallback['startTag'];
                                $tmp[0] = 0;
                                if(isset($lastCallback['nCallback']))
                                    $this->nCallback--;
                            }
                        }*/
                    } else{
                        $tmpw = $tmp[0];
                    }
                    $this->adjustWrapText($tmpstr, $tmpw, $width, $x, $wordSpaceAdjust, $justification);
                    // set position array by using the current break position minus offset
                    $cb[$lbpos] = array('x'=> ($nx + $tmp[0]), 'y'=> $ny + $tmp[1], 'f'=>'linebreak', 'p' => $tmp[3], 'width'=>$tmp[0]);
                    // restore previous stored font style 
                    $this->currentTextState = $store_currentTextState;
                    $this->setCurrentFont();
                    
                    return $cb;
                }
                
                $prevEndTagIndex = $endTagIndex;
                $restWidth -= $tmp[0];
                $nx += $tmp[0];
                
                $ny += $tmp[1];
                
                if(!empty($regs[1][$k][0])){
                    // these are custom callbacks (with parameters)
                    $pos = strpos($regs[2][$k][0], ':');
                    if($pos){
                        $func = substr($regs[2][$k][0], 0, $pos);
                        $parm = substr($regs[2][$k][0], $pos + 1);
                    } else {
                        $func = $regs[2][$k][0];
                        $parm = '';
                    }
                    // adjust the coordinates if justification is set
                    
                    // end tag for custom callbacks
                    if(substr($curTag[0], 0, 2) == "</"){
                        $cb[$curTagIndex] = array('x'=> $nx, 'y'=>$ny,'angle'=>$angle,'status'=>'end', 'f'=>$func, 'p'=>$parm,'nCallback'=>$this->nCallback, 'startTag' => $curTagIndex, 'endTag' => $endTagIndex );
                        if(!$noCB){
                            $this->nCallback--;
                            if ($this->nCallback<0){
                                $this->nCallback=0;
                            }
                        }
                    } else {
                        $noClose = ($regs[1][$k][0] == 'C:')? true: false;
                        $cb[$curTagIndex] = array('x'=> $nx, 'y'=>$ny, 'angle'=>$angle,'status'=>'start','p'=>$parm,'f'=>$func,'height'=>$this->getFontHeight($size),'descender'=>$this->getFontDecender($size), 
                                            'startTag' => $curTagIndex, 'endTag' => $endTagIndex , 'noClose' => $noClose);
                        if(!$noCB){
                            if(!$noClose){
                                $this->nCallback++;
                                $cb[$curTagIndex]['nCallback']=$this->nCallback;
                                $this->callback[$this->nCallback]=$cb[$curTagIndex];
                            }
                        }
                    }
                } else {
                    $parm = $regs[2][$k][0];
                    if(substr($curTag[0] ,0 , 2) == "</"){
                        $cb[$curTagIndex] = array('x'=> $nx, 'y'=>$ny, 'angle'=>$angle,'status'=>'end',  'p'=>$parm,'f'=>'defaultFormatting', 'startTag' => $curTagIndex, 'endTag' => $endTagIndex );
                    } else {
                        $cb[$curTagIndex] = array('x'=> $nx, 'y'=>$ny, 'angle'=>$angle,'status'=>'start','p'=>$parm,'f'=>'defaultFormatting', 'startTag' => $curTagIndex, 'endTag' => $endTagIndex );
                    }
                    
                    // do a dry formatting to fetch the correct text width
                    $this->defaultFormatting($cb[$curTagIndex]);
                    $this->setCurrentFont();
                }
            }
            
            $l = mb_strlen($text, 'UTF-8');
            
            if($prevEndTagIndex < $l){
                $tmpstr = mb_substr($text, $prevEndTagIndex, $l - $prevEndTagIndex, 'UTF-8');
                $tmp = $this->getTextLength($size, $tmpstr, $restWidth, $angle, $wordSpaceAdjust);
                // if the text does not fit to $width, $tmp[2] contains the length
                if($tmp[2] > 0){
                    // restore previous stored font style 
                    $this->currentTextState = $store_currentTextState;
                    $this->setCurrentFont();
                    
                    $tmpstr = mb_substr($text, 0, $prevEndTagIndex + $tmp[2], 'UTF-8');
                    // adjust to position if justification is set
                    $this->adjustWrapText($tmpstr, $width - ($restWidth - $tmp[0]), $width, $x, $wordSpaceAdjust, $justification);
                    // set position array by using the current break position minus offset
                    
                    $lbpos = $prevEndTagIndex + $tmp[2];
                    $cb[$lbpos] = array('x'=> ($nx + $tmp[0]), 'y'=> $ny + $tmp[1], 'f'=>'linebreak', 'p' => $tmp[3], 'width'=>$tmp[0]);
                    return $cb;
                } else {
                    $restWidth -= $tmp[0];
                }
            }
            
            // restore previous stored font style 
            $this->currentTextState = $store_currentTextState;
            $this->setCurrentFont();
            
            // adjust to position if justification is set
            $tmpx = $x;
            if($justification != 'full') {
                $this->adjustWrapText($tmpstr, $width - $restWidth, $width, $x, $wordSpaceAdjust, $justification);
            }
            
            foreach($cb as &$v){
                if($v['status'] == 'start'){
                    $v['x'] += $x - $tmpx;
                } else {
                    $v['x'] += $x - $tmpx;
                }
            }
        } else {
            $tmp = $this->getTextLength($size, $text, $width, $angle, $wordSpaceAdjust);
            // if the text does not fit to $width, $tmp[2] contains the length
            if($tmp[2] > 0){
                $tmpstr = mb_substr($text, 0, $tmp[2], 'UTF-8');
                // adjust to position if justification is set
                $this->adjustWrapText($tmpstr, $tmp[0], $width, $x, $wordSpaceAdjust, $justification);
                // set position array by using the current break position minus offset
                $lbpos = $tmp[2];
                $cb[$lbpos] = array('x'=> ($x + $tmp[0]), 'y'=> $y + $tmp[1], 'f'=>'linebreak', 'p' => $tmp[3], 'width'=>$tmp[0]);
                return $cb;
            } else if($justification != 'full') {
                $this->adjustWrapText($text, $tmp[0], $width, $x, $wordSpaceAdjust, $justification);
            }
        }
        return $cb;
    }
    
    protected function defaultFormatting($info){
        $tag = $info['p'];
        switch($tag){
            case 'strong':
                $tag = 'b';
            case 'i':
            case 'b':
                if($info['status'] == 'start'){
                    $this->currentTextState.= $tag;
                } else {
                    $p = strrpos($this->currentTextState, $tag);
                    if ($p !== false){
                        // then there is one to remove
                        $this->currentTextState = substr($this->currentTextState,0,$p).substr($this->currentTextState,$p+1);
                    }
                }
                break;
        }
    }

    /**
     * add text to the document, at a specified location, size and angle on the page
     */
    public function addText($x, $y, $size, $text, $width = 0, $justification = 'left', $angle = 0, $wordSpaceAdjust = 0, $test=0) {
        if($text == "") return '';
        
        if (!$this->numFonts) {
            $this->selectFont(dirname(__FILE__) . '/fonts/Helvetica');
        }
        // if there are any open callbacks, then they should be called, to show the start of the line
        if ($this->nCallback > 0){ 
            for ($i = $this->nCallback; $i > 0; $i--){
                // call each function
                $info = array('x'=>$x,'y'=>$y,'angle'=>$angle,'status'=>'sol','p'=>$this->callback[$i]['p'],'nCallback'=>$this->callback[$i]['nCallback'],'height'=>$this->callback[$i]['height'],'decender'=>$this->callback[$i]['decender']);
                $func = $this->callback[$i]['f'];
                $this->$func($info);
            }
        }
        
        $cf = $this->currentFont;
        
        // convert ISO-8859-1 into utf8 if necessary
        if(mb_detect_encoding($text) != 'UTF-8'){
            $text=utf8_encode($text);
        }
        // get length of its unicode string
        $len=mb_strlen($text,'UTF-8');
        
        $directives = $this->getDirectives($text, $x, $y, $width, $size, $justification, $angle, $wordSpaceAdjust);
        //print_r($directives);
        if ($angle == 0) {
          $this->addContent(sprintf("\nBT %.3F %.3F Td", $x, $y));
        } else {
          $a = deg2rad((float)$angle);
          $this->addContent(sprintf("\nBT %.3F %.3F %.3F %.3F %.3F %.3F Tm", cos($a), -sin($a), sin($a), cos($a), $x, $y));
        }
        
        // only single-byte character will work with word spacing according to PDF 1.3 reference (Chapter 5.2.2)
        if (!$this->fonts[$cf]['isUnicode'] && ($wordSpaceAdjust != 0 || $wordSpaceAdjust != $this->wordSpaceAdjust)) {
          $this->wordSpaceAdjust = $wordSpaceAdjust;
          $this->addContent(sprintf(" %.3F Tw", $wordSpaceAdjust));
        }
		
        $start=0;
        foreach($directives as  $pos => $directive){
            if($pos > $start){
                $part = mb_substr($text,$start,$pos-$start, 'UTF-8');
                $this->addContent(' /F'.$this->currentFontNum.' '.sprintf('%.1F',$size).' Tf ');
                
                $place_text = $this->filterText($part, false);
                
                if ($this->fonts[$cf]['isUnicode'] && $wordSpaceAdjust != 0) {
					$s = $this->fonts[$this->currentFont]['C'][32];
                	$space_scale = (1000 / $size) * $wordSpaceAdjust + $s;
                	$place_text = str_replace("\x00\x20", ') '.(-round($space_scale)).' (', $place_text);
                
                	$this->addContent(" [(".$place_text.")] TJ");
                } else {
                	$this->addContent(' ('.$place_text.') Tj');
                }
            }
            
            $func = $directive['f'];
            if($func == 'defaultFormatting'){ // default font style, bold, italic, ...
                $this->defaultFormatting($directive);
                $this->setCurrentFont();
            } else if($func == 'linebreak') { // line break
                $this->addContent(' ET');
                if ($this->nCallback > 0) { 
                    for ($j = $this->nCallback; $j > 0; $j--) {
                        $info = array(
                          'x' => $directive['x'],
                          'y' => $directive['y'],
                          'angle' => $angle,
                          'status' => 'eol',
                          'p'         => $this->callback[$j]['p'],
                          'nCallback' => $this->callback[$j]['nCallback'],
                          'height'    => $this->callback[$j]['height'],
                          'descender' => isset($this->callback[$j]['descender'])?$this->callback[$j]['descender']:null
                        );
                        $func = $this->callback[$j]['f'];
                        $this->$func($info);
                    }
                }
                return mb_substr($text,$pos + $directive['p'], $len, 'UTF-8');
            } else { // custom callbacks
                $this->addContent(' ET');
                $this->$func($directive);
                
                $xp = $directive['x'];
                $yp = $directive['y'];
                
                // restart the text object
                if ($angle==0){
                    $this->addContent("\n".'BT '.sprintf('%.3F',$xp).' '.sprintf('%.3F',$yp).' Td');
                } else {
                    $a = deg2rad((float)$angle);
                    $tmp = "\n".'BT ';
                    $tmp .= sprintf('%.3F',cos($a)).' '.sprintf('%.3F',(-1.0*sin($a))).' '.sprintf('%.3F',sin($a)).' '.sprintf('%.3F',cos($a)).' ';
                    $tmp .= sprintf('%.3F',$xp).' '.sprintf('%.3F',$yp).' Tm';
                    $this->addContent($tmp);
                }
                // only single-byte character will work with word spacing according to PDF 1.3 reference (Chapter 5.2.2)
                if (!$this->fonts[$cf]['isUnicode'] && ($wordSpaceAdjust!=0 || $wordSpaceAdjust != $this->wordSpaceAdjust)){
                    $this->wordSpaceAdjust=$wordSpaceAdjust;
                    $this->addContent(' '.sprintf('%.3F',$wordSpaceAdjust).' Tw');
                }
            }
            $start = $directive['endTag'];
        }
        
        if ($start < $len) {
          $part = mb_substr($text,$start, $len - $start,'UTF-8');
          $place_text = $this->filterText($part, false);
          // according to PDF 1.3 reference (Chapter 5.2.2) word spacing works only for single-byte codes
          // So for multiple-byte codes we have to do it manually
          if ($this->fonts[$cf]['isUnicode'] && $wordSpaceAdjust != 0) {
            $space_scale = 1000 / $size;
            $place_text = str_replace(' ', ' ) '.(-round($space_scale*$wordSpaceAdjust)).' (', $place_text);
          }
          
          $this->addContent(" /F$this->currentFontNum ".sprintf('%.1F Tf', $size));
          $this->addContent(" (".$place_text.") Tj");
        }

        $this->addContent(' ET');

        // if there are any open callbacks, then they should be called, to show the end of the line
        if ($this->nCallback > 0) { 
          for ($i = $this->nCallback; $i > 0; $i--) {
            // call each function
            $tmp = $this->getTextLength($size, $text, $width, $angle, $wordSpaceAdjust);
            //$tmp = $this->getTextPosition($x, $y, $angle, $size, $wordSpaceAdjust, $text);
            $info = array(
              'x' => $tmp[0] + $x,
              'y' => $tmp[1] + $y,
              'angle' => $angle,
              'status' => 'eol',
              'p'         => $this->callback[$i]['p'],
              'nCallback' => $this->callback[$i]['nCallback'],
              'height'    => $this->callback[$i]['height'],
              'descender' => $this->callback[$i]['descender']
            );
            $func = $this->callback[$i]['f'];
            $this->$func($info);
          }
        }
        // return text position of the last line break or 0 (if error -1)
        return '';
    }
    
    /*
     * unicode version of php ord to get the decimal of an utf-8 character
     */
    private function uniord($c)
    {
        // important condition to allow char "0" (zero) being converted to decimal
        if(strlen($c) <= 0) return false;
        $ord0 = ord($c{0}); if ($ord0>=0   && $ord0<=127) return $ord0;
        $ord1 = ord($c{1}); if ($ord0>=192 && $ord0<=223) return ($ord0-192)*64 + ($ord1-128);
        $ord2 = ord($c{2}); if ($ord0>=224 && $ord0<=239) return ($ord0-224)*4096 + ($ord1-128)*64 + ($ord2-128);
        $ord3 = ord($c{3}); if ($ord0>=240 && $ord0<=247) return ($ord0-240)*262144 + ($ord1-128)*4096 + ($ord2-128)*64 + ($ord3-128);
        return false;
    }
    
    /**
     * calculate how wide a given text string will be on a page, at a given size.
     * this can be called externally, but is alse used by the other class functions
     */
    public function getTextWidth($size,$text){
        $regex = "/<\/?([cC]:|)(".$this->allowedTags.")\>/";
        $text=preg_replace($regex, '', $text);
        $tmp = $this->getTextLength($size, $text);
        return $tmp[0];
    }
    
    private function getTextLength($size, $text, $maxWidth = 0, $angle = 0, $wa = 0){
        // Used to identify any space char for line breaks (either in Unicode or ANSI)
        $spaces = array(32,5760,6158,8192,8193,8194,8195,8196,8197,8198,8200,8201,8202,8203,8204,8205,8287,8288,12288);
        
        if (!$this->numFonts){
            $this->selectFont('./fonts/Helvetica');
        }
        
        // convert ISO-8859-1 into utf8 if necessary
        if(mb_detect_encoding($text) != 'UTF-8'){
            $text=utf8_encode($text);
        }
        
        
        $a = deg2rad((float)$angle);
        // get length of its unicode string
        $len=mb_strlen($text);
        $cf = $this->currentFont;
        $tw = $maxWidth/$size*1000;
        $break=0;
        $w=0;
        
        for ($i=0;$i< $len ;$i++){
            $c = mb_substr($text, $i, 1, 'UTF-8');
            $cOrd = $this->uniord($c);
            if($cOrd == 0){
                continue;
            }
            
            if (isset($this->fonts[$cf]['differences'][$cOrd])){
                // then this character is being replaced by another
                $cOrd2 = $this->fonts[$cf]['differences'][$cOrd];
            } else {
                $cOrd2 = $cOrd;
            }

            if (isset($this->fonts[$cf]['C'][$cOrd2])){
                $w+=$this->fonts[$cf]['C'][$cOrd2];
            }
            // word space adjust
            if($wa > 0 && in_array($cOrd2, $spaces)){
                $w += $wa;
            }
            
            if($maxWidth > 0 && (cos($a)*$w) > $tw){
                if ($break>0){
                    return array(cos($a)*$breakWidth, -sin($a)*$breakWidth, $break, 1);
                } else {
                    $ctmp = $cOrd;
                    if (isset($this->fonts[$cf]['differences'][$ctmp])){
                        $ctmp=$this->fonts[$cf]['differences'][$ctmp];
                    }
                    $tmpw=($w-$this->fonts[$cf]['C'][$ctmp])*$size/1000;
                    // just split before the current character
                    return array(cos($a)*$tmpw, -sin($a)*$tmpw, $i, 0);
                }
            }
            
            // find space or minus for a clean line break
            if(in_array($cOrd2, $spaces) && $maxWidth > 0){
            	$break=$i;
                $breakWidth = ($w-$this->fonts[$cf]['C'][$cOrd2])*$size/1000;
            } else if($cOrd2 == 45  && $maxWidth > 0){
            	$break=$i;
                $breakWidth = $w*$size/1000;
            }
        }
        
        $tmpw=$w*$size/1000;
        return array(cos($a)*$tmpw, -sin($a)*$tmpw, 0, 0);
    }

    /**
     * do a part of the calculation for sorting out the justification of the text
     *
     */
    private function adjustWrapText($text,$actual,$width,&$x,&$adjust,$justification){
        switch ($justification){
            case 'left':
                return;
                break;
            case 'right':
                $x+=$width-$actual;
                break;
            case 'center':
            case 'centre':
                $x+=($width-$actual)/2;
                break;
            case 'full':
                // count the number of words
                $words = explode(' ',$text);
                $nspaces=count($words) - 1;
                if ($nspaces>0){
                    $adjust = ($width-$actual)/$nspaces;
                } else {
                    $adjust=0;
                }
                break;
        }
    }
    
    /**
     * this will be called at a new page to return the state to what it was on the
     * end of the previous page, before the stack was closed down
     * This is to get around not being able to have open 'q' across pages
     */
    public function saveState($pageEnd=0){
        if ($pageEnd){
            // this will be called at a new page to return the state to what it was on the
            // end of the previous page, before the stack was closed down
            // This is to get around not being able to have open 'q' across pages
            $opt = $this->stateStack[$pageEnd]; // ok to use this as stack starts numbering at 1
            $this->setColor($opt['col']['r'],$opt['col']['g'],$opt['col']['b'],1);
            $this->setStrokeColor($opt['str']['r'],$opt['str']['g'],$opt['str']['b'],1);
            $this->objects[$this->currentContents]['c'].="\n".$opt['lin'];
            // $this->currentLineStyle = $opt['lin'];
        } else {
            $this->nStateStack++;
            $this->stateStack[$this->nStateStack]=array(
                'col'=>$this->currentColour
                ,'str'=>$this->currentStrokeColour
                ,'lin'=>$this->currentLineStyle
            );
        }
        $this->objects[$this->currentContents]['c'].="\nq";
    }

    /**
     * restore a previously saved state
     */
    public function restoreState($pageEnd=0){
        if (!$pageEnd){
            $n = $this->nStateStack;
            $this->currentColour = $this->stateStack[$n]['col'];
            $this->currentStrokeColour = $this->stateStack[$n]['str'];
            $this->objects[$this->currentContents]['c'].="\n".$this->stateStack[$n]['lin'];
            $this->currentLineStyle = $this->stateStack[$n]['lin'];
            unset($this->stateStack[$n]);
            $this->nStateStack--;
        }
        $this->objects[$this->currentContents]['c'].="\nQ";
    }

    /**
     * make a loose object, the output will go into this object, until it is closed, then will revert to
     * the current one.
     * this object will not appear until it is included within a page.
     * the function will return the object number
     */
    public function openObject(){
        $this->nStack++;
        $this->stack[$this->nStack]=array('c'=>$this->currentContents,'p'=>$this->currentPage);
        // add a new object of the content type, to hold the data flow
        $this->numObj++;
        $this->o_contents($this->numObj,'new');
        $this->currentContents=$this->numObj;
        $this->looseObjects[$this->numObj]=1;

        return $this->numObj;
    }

    /**
    * open an existing object for editing
    */
    public function reopenObject($id){
       $this->nStack++;
       $this->stack[$this->nStack]=array('c'=>$this->currentContents,'p'=>$this->currentPage);
       $this->currentContents=$id;
       // also if this object is the primary contents for a page, then set the current page to its parent
       if (isset($this->objects[$id]['onPage'])){
         $this->currentPage = $this->objects[$id]['onPage'];
       }
    }

    /**
     * close an object
     */
    public function closeObject(){
        // close the object, as long as there was one open in the first place, which will be indicated by
        // an objectId on the stack.
        if ($this->nStack>0){
            $this->currentContents=$this->stack[$this->nStack]['c'];
            $this->currentPage=$this->stack[$this->nStack]['p'];
            $this->nStack--;
            // easier to probably not worry about removing the old entries, they will be overwritten
            // if there are new ones.
        }
    }

    /**
     * stop an object from appearing on pages from this point on
     */
    public function stopObject($id){
        // if an object has been appearing on pages up to now, then stop it, this page will
        // be the last one that could contian it.
        if (isset($this->addLooseObjects[$id])){
            $this->addLooseObjects[$id]='';
        }
    }

    /**
     * after an object has been created, it wil only show if it has been added, using this function.
     */
    public function addObject($id,$options='add'){
        // add the specified object to the page
        if (isset($this->looseObjects[$id]) && $this->currentContents!=$id){
            // then it is a valid object, and it is not being added to itself
            switch($options){
            case 'all':
                // then this object is to be added to this page (done in the next block) and
                // all future new pages.
                $this->addLooseObjects[$id]='all';
            case 'add':
                if (isset($this->objects[$this->currentContents]['onPage'])){
                    // then the destination contents is the primary for the page
                    // (though this object is actually added to that page)
                    $this->o_page($this->objects[$this->currentContents]['onPage'],'content',$id);
                }
                break;
            case 'even':
                $this->addLooseObjects[$id]='even';
                $pageObjectId=$this->objects[$this->currentContents]['onPage'];
                if ($this->objects[$pageObjectId]['info']['pageNum']%2==0){
                    $this->addObject($id); // hacky huh :)
                }
                break;
            case 'odd':
                $this->addLooseObjects[$id]='odd';
                $pageObjectId=$this->objects[$this->currentContents]['onPage'];
                if ($this->objects[$pageObjectId]['info']['pageNum']%2==1){
                    $this->addObject($id); // hacky huh :)
                }
                break;
            case 'next':
                $this->addLooseObjects[$id]='all';
                break;
            case 'nexteven':
                $this->addLooseObjects[$id]='even';
                break;
            case 'nextodd':
                $this->addLooseObjects[$id]='odd';
                break;
            }
        }
    }

    /**
     * add content to the documents info object
     */
    public function addInfo($label,$value=0){
        // this will only work if the label is one of the valid ones.
        // modify this so that arrays can be passed as well.
        // if $label is an array then assume that it is key=>value pairs
        // else assume that they are both scalar, anything else will probably error
        if (is_array($label)){
            foreach ($label as $l=>$v){
                $this->o_info($this->infoObject,$l,$v);
            }
        } else {
            $this->o_info($this->infoObject,$label,$value);
        }
    }

    /**
     * set the viewer preferences of the document, it is up to the browser to obey these.
     */
    public function setPreferences($label,$value=0){
        // this will only work if the label is one of the valid ones.
        if (is_array($label)){
            foreach ($label as $l=>$v){
                $this->o_catalog($this->catalogId,'viewerPreferences',array($l=>$v));
            }
        } else {
            $this->o_catalog($this->catalogId,'viewerPreferences',array($label=>$value));
        }
    }

    /**
     * extract an integer from a position in a byte stream
     *
     */
    private function getBytes(&$data,$pos,$num){
        // return the integer represented by $num bytes from $pos within $data
        $ret=0;
        for ($i=0;$i<$num;$i++){
            $ret=$ret*256;
            $ret+=ord($data[$pos+$i]);
        }
        return $ret;
    }

    /**
     * reads the PNG chunk
     * @param $data - binary part of the png image
     */
    private function readPngChunks(&$data){
        $default = array('info'=> array(), 'transparency'=> null, 'idata'=> null, 'pdata'=> null, 'haveHeader'=> false);
        // set pointer
        $p = 8;
        $len = strlen($data);
        // cycle through the file, identifying chunks
        while ($p<$len){
            $chunkLen = $this->getBytes($data,$p,4);
            $chunkType = substr($data,$p+4,4);
            //error_log($chunkType. ' - '.$chunkLen);
            switch($chunkType){
                case 'IHDR':
                //this is where all the file information comes from
                $default['info']['width']=$this->getBytes($data,$p+8,4);
                $default['info']['height']=$this->getBytes($data,$p+12,4);
                $default['info']['bitDepth']=ord($data[$p+16]);
                $default['info']['colorType']=ord($data[$p+17]);
                $default['info']['compressionMethod']=ord($data[$p+18]);
                $default['info']['filterMethod']=ord($data[$p+19]);
                $default['info']['interlaceMethod']=ord($data[$p+20]);
                
                $this->debug('readPngChunks: ColorType is' . $default['info']['colorType'], E_USER_NOTICE);
                
                $default['haveHeader'] = true;
                
                if ($default['info']['compressionMethod']!=0){
                    $error = true;
                    $errormsg = "unsupported compression method";
                }
                if ($default['info']['filterMethod']!=0){
                    $error = true;
                    $errormsg = "unsupported filter method";
                }
                
                $default['transparency'] = array('type'=> null, 'data' => null);
                
                if ($default['info']['colorType'] == 3) { // indexed color, rbg
                    // corresponding to entries in the plte chunk
                    // Alpha for palette index 0: 1 byte
                    // Alpha for palette index 1: 1 byte
                    // ...etc...
        
                    // there will be one entry for each palette entry. up until the last non-opaque entry.
                    // set up an array, stretching over all palette entries which will be o (opaque) or 1 (transparent)
                    $default['transparency']['type']='indexed';
                    //$numPalette = strlen($default['pdata'])/3;
                    $trans=0;
                    for ($i=$chunkLen;$i>=0;$i--){
                        if (ord($data[$p+8+$i])==0){
                            $trans=$i;
                        }
                    }
                    $default['transparency']['data'] = $trans;
        
                } elseif($default['info']['colorType'] == 0) { // grayscale
                    // corresponding to entries in the plte chunk
                    // Gray: 2 bytes, range 0 .. (2^bitdepth)-1
        
                    // $transparency['grayscale']=$this->getBytes($data,$p+8,2); // g = grayscale
                    $default['transparency']['type']='indexed';
                    $default['transparency']['data'] = ord($data[$p+8+1]);
                } elseif($default['info']['colorType'] == 2) { // truecolor
                    // corresponding to entries in the plte chunk
                    // Red: 2 bytes, range 0 .. (2^bitdepth)-1
                    // Green: 2 bytes, range 0 .. (2^bitdepth)-1
                    // Blue: 2 bytes, range 0 .. (2^bitdepth)-1
                    $default['transparency']['r']=$this->getBytes($data,$p+8,2); // r from truecolor
                    $default['transparency']['g']=$this->getBytes($data,$p+10,2); // g from truecolor
                    $default['transparency']['b']=$this->getBytes($data,$p+12,2); // b from truecolor
                } else if($default['info']['colorType'] == 6 || $default['info']['colorType'] == 4) {
                    // set transparency type to "alpha" and proceed with it in $this->o_image later
                    $default['transparency']['type'] = 'alpha';
                    
                    $img = imagecreatefromstring($data);
                    
                    $imgalpha = imagecreate($default['info']['width'], $default['info']['height']);
                    // generate gray scale palette (0 -> 255)
                    for ($c = 0; $c < 256; ++$c) {
                        ImageColorAllocate($imgalpha, $c, $c, $c);
                    }
                    // extract alpha channel
                    for ($xpx = 0; $xpx < $default['info']['width']; ++$xpx) {
                        for ($ypx = 0; $ypx < $default['info']['height']; ++$ypx) {
                            $colorBits = imagecolorat($img, $xpx, $ypx);
                            $color = imagecolorsforindex($img, $colorBits);
                            $color['alpha'] = (((127 - $color['alpha']) / 127) * 255);
                            imagesetpixel($imgalpha, $xpx, $ypx, $color['alpha']);
                        }
                    }
                    $tmpfile_alpha=tempnam($this->tempPath,'ezImg');
                    
                    imagepng($imgalpha, $tmpfile_alpha);
                    imagedestroy($imgalpha);
                    
                    $alphaData = file_get_contents($tmpfile_alpha);
                    // nested method call to receive info on alpha image
                    $alphaImg = $this->readPngChunks($alphaData);
                    // use 'pdate' to fill alpha image as "palette". But it s the alpha channel
                    $default['pdata'] = $alphaImg['idata'];
                    
                    // generate true color image with no alpha channel
                    $tmpfile_tt=tempnam($this->tempPath,'ezImg');
                    
                    $imgplain = imagecreatetruecolor($default['info']['width'], $default['info']['height']);
                    imagecopy($imgplain, $img, 0, 0, 0, 0, $default['info']['width'], $default['info']['height']);
                    imagepng($imgplain, $tmpfile_tt);
                    imagedestroy($imgplain);
                    
                    $ttData = file_get_contents($tmpfile_tt);
                    $ttImg = $this->readPngChunks($ttData);
                    
                    $default['idata'] = $ttImg['idata'];
                    
                    // remove temp files 
                    unlink($tmpfile_alpha);
                    unlink($tmpfile_tt);
                    // return to addPngImage prematurely. IDAT has already been read and PLTE is not required
                    return $default;
                }
                break;
                case 'PLTE':
                    $default['pdata'] = substr($data,$p+8,$chunkLen); 
                break;
                case 'IDAT':
                    $default['idata'] .= substr($data,$p+8,$chunkLen);
                break;
                case 'tRNS': // this HEADER info is optional. More info: rfc2083 (http://tools.ietf.org/html/rfc2083)
                    // error_log('OPTIONAL HEADER -tRNS- exist:');
                    // this chunk can only occur once and it must occur after the PLTE chunk and before IDAT chunk
                    // KS End new code
                break;
                default:
                break;
            }
            $p += $chunkLen+12;
        }
        return $default;
    }
    
    /**
     * add a PNG image into the document, from a file
     * this should work with remote files
     */
    public function addPngFromFile($file,$x,$y,$w=0,$h=0){
        // read in a png file, interpret it, then add to the system
        $error = false;
        $errormsg = "";
        
        $this->debug('addPngFromFile: opening image ' . $file);
        
        $data = file_get_contents($file);
        
        if($data === false){
            $this->debug('addPngFromFile: trouble opening file ' . $file, E_USER_WARNING);
            return;
        }
        
        $header = chr(137).chr(80).chr(78).chr(71).chr(13).chr(10).chr(26).chr(10);
        if (substr($data,0,8)!=$header){
            $this->debug('addPngFromFile: Invalid PNG header for file: ' . $file, E_USER_WARNING);
            return;
        }
        
        $iChunk = $this->readPngChunks($data);
        
        if(!$iChunk['haveHeader']){
            $error = true;
            $errormsg = "information header is missing.";
        }
        if (isset($iChunk['info']['interlaceMethod']) && $iChunk['info']['interlaceMethod']){
            $error = true;
            $errormsg = "There appears to be no support for interlaced images in pdf.";
        }
        
        if ($iChunk['info']['bitDepth'] > 8){
            $error = true;
            $errormsg = "only bit depth of 8 or less is supported.";
        }

        if ($iChunk['info']['colorType'] == 1 || $iChunk['info']['colorType'] == 5 || $iChunk['info']['colorType']== 7){
            $error = true;
            $errormsg = 'Unsupported PNG color type: '.$iChunk['info']['colorType'];
        } else if(isset($iChunk['info'])) {
            switch ($iChunk['info']['colorType']){
                case 3:
                    $color = 'DeviceRGB';
                    $ncolor=1;
                break;
                case 6:
                case 2:
                    $color = 'DeviceRGB';
                    $ncolor=3;
                break;
                case 4:
                case 0:
                    $color = 'DeviceGray';
                    $ncolor=1;
                break;
            }
        }
            
        if ($error){
            $this->debug('addPngFromFile: '.$errormsg, E_USER_WARNING);
            return;
        }
        if ($w==0){
            $w=$h/$iChunk['info']['height']*$iChunk['info']['width'];
        }
        if ($h==0){
            $h=$w*$iChunk['info']['height']/$iChunk['info']['width'];
        }
        
        if($this->hashed){
            $oHash = md5($iChunk['idata']);
        }
        if(isset($oHash) && isset($this->objectHash[$oHash])){
            $label = $this->objectHash[$oHash];
        }else{
            $this->numImages++;
            $label='I'.$this->numImages;
            $this->numObj++;
            
            if(isset($oHash)){
                $this->objectHash[$oHash] = $label;
            }
            
            $options = array('label'=>$label,'data'=>$iChunk['idata'],'bitsPerComponent'=>$iChunk['info']['bitDepth'],'pdata'=>$iChunk['pdata']
                                      ,'iw'=>$iChunk['info']['width'],'ih'=>$iChunk['info']['height'],'type'=>'png','color'=>$color,'ncolor'=>$ncolor);
            if (isset($iChunk['transparency'])){
                $options['transparency']=$iChunk['transparency'];
            }
            $this->o_image($this->numObj,'new',$options);
        }
        
        $this->objects[$this->currentContents]['c'].="\nq ".sprintf('%.3F',$w)." 0 0 ".sprintf('%.3F',$h)." ".sprintf('%.3F',$x)." ".sprintf('%.3F',$y)." cm";
        $this->objects[$this->currentContents]['c'].=" /".$label.' Do';
        $this->objects[$this->currentContents]['c'].=" Q";
    }
    
    /**
     * add a JPEG image into the document, from a file
     */
    public function addJpegFromFile($img,$x,$y,$w=0,$h=0){
        // attempt to add a jpeg image straight from a file, using no GD commands
        // note that this function is unable to operate on a remote file.

        if (!file_exists($img)){
            return;
        }

        $tmp=getimagesize($img);
        $imageWidth=$tmp[0];
        $imageHeight=$tmp[1];

        if (isset($tmp['channels'])){
            $channels = $tmp['channels'];
        } else {
            $channels = 3;
        }

        if ($w<=0 && $h<=0){
            $w=$imageWidth;
        }
        if ($w==0){
            $w=$h/$imageHeight*$imageWidth;
        }
        if ($h==0){
            $h=$w*$imageHeight/$imageWidth;
        }

        $data = file_get_contents($img);

        $this->addJpegImage_common($data,$x,$y,$w,$h,$imageWidth,$imageHeight,$channels);
    }

    /**
     * read gif image from file, converts it into an JPEG (no transparancy) and display it
     * @param $img - file path ti gif image
     * @param $x - coord x
     * @param $y - y cord
     * @param $w - width
     * @param $h - height
     */
    public function addGifFromFile($img, $x, $y, $w=0, $h=0){
        if (!file_exists($img)){
            return;
        }
        
        if(!function_exists("imagecreatefromgif")){
            $this->debug('addGifFromFile: Missing GD function imageCreateFromGif', E_USER_ERROR);
            return;
        }
        
        $tmp=getimagesize($img);
        $imageWidth=$tmp[0];
        $imageHeight=$tmp[1];
        
        
        if ($w<=0 && $h<=0){
            $w=$imageWidth;
        }
        if ($w==0){
            $w=$h/$imageHeight*$imageWidth;
        }
        if ($h==0){
            $h=$w*$imageHeight/$imageWidth;
        }
        
        $imgres = imagecreatefromgif($img);
        $tmpName=tempnam($this->tempPath,'img');
        imagejpeg($imgres,$tmpName,90);
        
        $this->addJpegFromFile($tmpName,$x,$y,$w,$h);
    }
    
     /**
     * add an image into the document, from a GD object
     * this function is not all that reliable, and I would probably encourage people to use
     * the file based functions
     * @param $img - gd image resource
     * @param $x coord x
     * @param $y coord y
     * @param $w width
     * @param $h height
     * @param $quality image quality
     */
    protected function addImage(&$img,$x,$y,$w=0,$h=0,$quality=75){
        // add a new image into the current location, as an external object
        // add the image at $x,$y, and with width and height as defined by $w & $h

        // note that this will only work with full colour images and makes them jpg images for display
        // later versions could present lossless image formats if there is interest.

        // there seems to be some problem here in that images that have quality set above 75 do not appear
        // not too sure why this is, but in the meantime I have restricted this to 75.
        if ($quality>75){
            $quality=75;
        }

        // if the width or height are set to zero, then set the other one based on keeping the image
        // height/width ratio the same, if they are both zero, then give up :)
        $imageWidth=imagesx($img);
        $imageHeight=imagesy($img);

        if ($w<=0 && $h<=0){
            return;
        }
        if ($w==0){
            $w=$h/$imageHeight*$imageWidth;
        }
        if ($h==0){
            $h=$w*$imageHeight/$imageWidth;
        }

        $tmpName=tempnam($this->tempPath,'img');
        imagejpeg($img,$tmpName,$quality);
        
        $data = file_get_contents($tmpName);
        if($data === false) {
            $this->debug('addImage: trouble opening image resource', E_USER_WARNING);
        }
        unlink($tmpName);
        $this->addJpegImage_common($data,$x,$y,$w,$h,$imageWidth,$imageHeight);
    }

    /**
     * common code used by the two JPEG adding functions
     */
    private function addJpegImage_common(&$data,$x,$y,$w=0,$h=0,$imageWidth,$imageHeight,$channels=3){
        // note that this function is not to be called externally
        // it is just the common code between the GD and the file options
        if($this->hashed){
            $oHash = md5($data);
        }
        if(isset($oHash) && isset($this->objectHash[$oHash])){
            $label = $this->objectHash[$oHash];
        }else{
            $this->numImages++;
            $label='I'.$this->numImages;
            $this->numObj++;
            
            if(isset($oHash)){
                $this->objectHash[$oHash] = $label;
            }
            
            $this->o_image($this->numObj,'new',array('label'=>$label,'data'=>$data,'iw'=>$imageWidth,'ih'=>$imageHeight,'channels'=>$channels));
        }

        $this->objects[$this->currentContents]['c'].="\nq ".sprintf('%.3F',$w)." 0 0 ".sprintf('%.3F',$h)." ".sprintf('%.3F',$x)." ".sprintf('%.3F',$y)." cm";
        $this->objects[$this->currentContents]['c'].=" /".$label.' Do';
        $this->objects[$this->currentContents]['c'].=" Q";
    }

    /**
     * specify where the document should open when it first starts
     */
    public function openHere($style,$a=0,$b=0,$c=0){
        // this function will open the document at a specified page, in a specified style
        // the values for style, and the required paramters are:
        // 'XYZ'  left, top, zoom
        // 'Fit'
        // 'FitH' top
        // 'FitV' left
        // 'FitR' left,bottom,right
        // 'FitB'
        // 'FitBH' top
        // 'FitBV' left
        $this->numObj++;
        $this->o_destination($this->numObj,'new',array('page'=>$this->currentPage,'type'=>$style,'p1'=>$a,'p2'=>$b,'p3'=>$c));
        $id = $this->catalogId;
        $this->o_catalog($id,'openHere',$this->numObj);
    }

    /**
     * create a labelled destination within the document
     */
    public function addDestination($label,$style,$a=0,$b=0,$c=0){
        // associates the given label with the destination, it is done this way so that a destination can be specified after
        // it has been linked to
        // styles are the same as the 'openHere' function
        $this->numObj++;
        $this->o_destination($this->numObj,'new',array('page'=>$this->currentPage,'type'=>$style,'p1'=>$a,'p2'=>$b,'p3'=>$c));
        $id = $this->numObj;
        // store the label->idf relationship, note that this means that labels can be used only once
        $this->destinations["$label"]=$id;
    }

    /**
     * define font families, this is used to initialize the font families for the default fonts
     * and for the user to add new ones for their fonts. The default bahavious can be overridden should
     * that be desired.
     */
    public function setFontFamily($family, $options = ''){
        if (is_array($options)) {
            // the user is trying to set a font family
            // note that this can also be used to set the base ones to something else
            if (strlen($family)){
                $this->fontFamilies[$family] = $options;
            }
        }
    }

    /**
     * used to add messages for use in debugging
     */
    protected function debug($message, $error_type = E_USER_NOTICE)
    {
        if($error_type <= $this->DEBUGLEVEL){
            switch(strtolower($this->DEBUG)){
                default:
                case 'none':
                    break;
                case 'error_log':
                    error_log($message);
                    break;
                case 'variable':
                    $this->messages.=$message."\n";
                break;
            }
        }
    }

    /**
     * a few functions which should allow the document to be treated transactionally.
     *
     * @param string $action WHAT IS THIS?
     * @return void
     */
    public function transaction($action){
        switch ($action){
        case 'start':
            // store all the data away into the checkpoint variable
            $data = get_object_vars($this);
            $this->checkpoint = $data;
            unset($data);
            break;
        case 'commit':
            if (is_array($this->checkpoint) && isset($this->checkpoint['checkpoint'])){
                $tmp = $this->checkpoint['checkpoint'];
                $this->checkpoint = $tmp;
                unset($tmp);
            } else {
                $this->checkpoint='';
            }
            break;
        case 'rewind':
            // do not destroy the current checkpoint, but move us back to the state then, so that we can try again
            if (is_array($this->checkpoint)){
                // can only abort if were inside a checkpoint
                $tmp = $this->checkpoint;
                foreach ($tmp as $k=>$v){
                    if ($k != 'checkpoint'){
                        $this->$k=$v;
                    }
                }
                unset($tmp);
            }
            break;
        case 'abort':
            if (is_array($this->checkpoint)){
                // can only abort if were inside a checkpoint
                $tmp = $this->checkpoint;
                foreach ($tmp as $k=>$v){
                    $this->$k=$v;
                }
                unset($tmp);
            }
            break;
        }
    }
} // end of class
?>
