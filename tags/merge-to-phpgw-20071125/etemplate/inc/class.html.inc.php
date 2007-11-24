<?php
/**
* eTemplate - basic application development environment
* @copyright Copyright (C) 2002-2006 Free Software Foundation, Inc. http://www.fsf.org/
* @author Ralf Becker <ralf.becker@outdoortraining.de>
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package etemplate
* @version $Id: class.html.inc.php,v 1.29 2007/02/12 14:45:37 sigurdne Exp $
*/

class html
{
	var $user_agent,$ua_version;	// 'mozilla','msie','konqueror'
	var $prefered_img_title;

	function html()
	{																// should be Ok for all HTML 4 compatible browsers
		if (!eregi('compatible; ([a-z_]+)[/ ]+([0-9.]+)',$_SERVER['HTTP_USER_AGENT'],$parts))
		{
			eregi('^([a-z_]+)/([0-9.]+)',$_SERVER['HTTP_USER_AGENT'],$parts);
		}
		list(,$this->user_agent,$this->ua_version) = $parts;
		$this->user_agent = strtolower($this->user_agent);
		
		$this->prefered_img_title = $this->user_agent == 'mozilla' && $this->ua_version < 5 ? 'ALT' : 'TITLE';
		//echo "<p>HTTP_USER_AGENT='$GLOBALS[HTTP_USER_AGENT]', UserAgent: '$this->user_agent', Version: '$this->ua_version', img_title: '$this->prefered_img_title'</p>\n";
	}

	/*
	* Function:		Allows to show and select one item from an array
	*	Parameters:		$name		string with name of the submitted var which holds the key of the selected item form array
	*						$key		key(s) of already selected item(s) from $arr, eg. '1' or '1,2' or array with keys
	*						$arr		array with items to select, eg. $arr = array ( 'y' => 'yes','n' => 'no','m' => 'maybe');
	*						$no_lang	if !$no_lang send items through lang()
	*						$options	additional options (e.g. 'multiple')
	* On submit		$XXX		is the key of the selected item (XXX is the content of $name)
	* Returns:			string to set for a template or to echo into html page
	*/
	function select($name, $key, $arr=0,$no_lang=0,$options='',$multiple=0)
	{
		// should be in class common.sbox
		if (!is_array($arr))
		{
			$arr = array('no','yes');
		}
		if (intval($multiple) > 0)
		{
			$options .= ' MULTIPLE SIZE="'.intval($multiple).'"';
			if (substr($name,-2) != '[]')
			{
				$name .= '[]';
			}
		}
		$out = "<select name=\"$name\" $options>\n";

		if (is_array($key))
		{
			$key = implode(',',$key);
		}
		foreach($arr as $k => $text)
		{
			$out .= '<option value="'.htmlspecialchars($k).'"';

			if("$k" == "$key" || strstr(",$key,",",$k,"))
			{
				$out .= " SELECTED";
			}
			$out .= ">" . ($no_lang || $text == '' ? $text : lang($text)) . "</option>\n";
		}
		$out .= "</select>\n";

		return $out;
	}

	function div($content,$options='')
	{
		return "<DIV $options>\n$content</DIV>\n";
	}

	function input_hidden($vars,$value='',$ignore_empty=True)
	{
		$html = '';
		if (!is_array($vars))
		{
			$vars = array( $vars => $value );
		}
		foreach($vars as $name => $value)
		{
			if (is_array($value))
			{
				$value = serialize($value);
			}
			if (!$ignore_empty || $value && !($name == 'filter' && $value == 'none'))	// dont need to send all the empty vars
			{
				$html .= "<INPUT TYPE=\"HIDDEN\" NAME=\"$name\" VALUE=\"".htmlspecialchars($value)."\">\n";
			}
		}
		return $html;
	}

	function textarea($name,$value='',$options='' )
	{
		return "<TEXTAREA name=\"$name\" $options>".htmlspecialchars($value)."</TEXTAREA>\n";
	}

	function input($name,$value='',$type='',$options='' )
	{
		if ($type)
		{
			$type = 'TYPE="'.$type.'"';
		}
		return "<INPUT $type NAME=\"$name\" VALUE=\"".htmlspecialchars($value)."\" $options>\n";
	}

	function submit_button($name,$lang,$onClick='',$no_lang=0,$options='',$image='',$app='')
	{
		if ($image != '')
		{
			if (strpos($image,'.')) 
			{
				$image = substr($image,0,strpos($image,'.'));
			}
			if (!($path = $GLOBALS['phpgw']->common->image($app,$image)) &&
				!($path = $GLOBALS['phpgw']->common->image('phpgwapi',$image)))
			{
				$path = $image;		// name may already contain absolut path 
			}
			$image = ' SRC="'.$path.'"';
		}
		if (!$no_lang)
		{
			$lang = lang($lang);
		}
		if (($accesskey = strstr($lang,'&')) && $accesskey[1] != ' ' &&
			(($pos = strpos($accesskey,';')) === False || $pos > 5))
		{
			$lang_u = str_replace('&'.$accesskey[1],'<u>'.$accesskey[1].'</u>',$lang);
			$lang = str_replace('&','',$lang);
			$options = 'ACCESSKEY="'.$accesskey[1].'" '.$options;
		}
		else
		{
			$accesskey = '';
			$lang_u = $lang;
		}
		if ($onClick) $options .= " onClick=\"$onClick\"";

		// <button> is not working in all cases if ($this->user_agent == 'mozilla' && $this->ua_version < 5 || $image)
		{
			return $this->input($name,$lang,$image != '' ? 'IMAGE' : 'SUBMIT',$options.$image);
		}
		return '<button TYPE="submit" NAME="'.$name.'" VALUE="'.$lang.'" '.$options.'>'.
			($image != '' ? "<img$image $this->prefered_img_title=\"$lang\"> " : '').
			($image == '' || $accesskey ? $lang_u : '').'</button>';
	}

	/**
	 * creates an absolut link + the query / get-variables
	*
	 * @param $url phpgw-relative link, may include query / get-vars
	 * $vars query or array ('name' => 'value', ...) with query
	 * link('/index.php?menuaction=infolog.uiinfolog.get_list',array('info_id' => 123))
	 *  = 'http://domain/phpgw-path/index.php?menuaction=infolog.uiinfolog.get_list&info_id=123'
	 * @return absolut link already run through $GLOBALS['phpgw']->link
	 */
	function link($url, $vars='')
	{
		//echo "<p>html::link(url='$url',vars='"; print_r($vars); echo "')</p>\n" . _debug_array(debug_backtrace());
		if (!is_array($vars))
		{
			parse_str($vars, $vars);
		}
		
		if(strpos('?',$url) > 0)
		{
			list($url,$v) = explode('?',$url);	// url may contain additional vars
		}
		
		if (isset($v))
		{
			parse_str($v,$v);
			$vars += $v;
		}
		return $GLOBALS['phpgw']->link($url,$vars);
	}

	function checkbox($name,$value='')
	{
		return "<input type=\"checkbox\" name=\"$name\" value=\"True\"" .($value ? ' checked' : '') . ">\n";
	}

	function form($content,$hidden_vars,$url,$url_vars='',$name='',$options='',$method='POST')
	{
		$html = "<form method=\"$method\" ".($name != '' ? "name=\"$name\" " : '')."action=\"".$this->link($url,$url_vars)."\" $options>\n";
		$html .= $this->input_hidden($hidden_vars);

		if ($content) 
		{
			$html .= $content;
			$html .= "</form>\n";
		}
		return $html;
	}

	function form_1button($name,$lang,$hidden_vars,$url,$url_vars='',$form_name='',$method='POST')
	{
		return $this->form($this->submit_button($name,$lang),
			$hidden_vars,$url,$url_vars,$form_name,'',$method);
	}

	/**
	 * creates table from array with rows
	 * abstract the html stuff
	 * @param $rows array with rows, each row is an array of the cols
	 * @param $options options for the table-tag
	 * $rows = array ( '1'  => array( 1 => 'cell1', '.1' => 'colspan=3',
	 *                                2 => 'cell2', 3 => 'cell3', '.3' => 'width="10%"' ),
	 *                 '.1' => 'BGCOLOR="#0000FF"' );
	 * table($rows,'WIDTH="100%"') = '<table WIDTH="100%"><tr><td colspan=3>cell1</td><td>cell2</td><td width="10%">cell3</td></tr></table>'
	 * @return string with html-code of the table
	 */
	function table($rows,$options = '',$no_table_tr=False)
	{
		$html = $no_table_tr ? '' : "<TABLE $options>\n";

		foreach($rows as $key => $row)
		{
			if (!is_array($row))
			{
				continue;					// parameter
			}
			if(isset($rows['.'.$key]))
			{
				$html .= $no_table_tr && $key == 1 ? '' : "\t<TR ".$rows['.'.$key].">\n";
			}

			foreach($row as $key => $cell)
			{
				if ($key[0] == '.')
				{
					continue;				// parameter
				}
				$table_pos = strpos($cell,'<TABLE');
				$td_pos = strpos($cell,'<TD');
				if ($td_pos !== False && ($table_pos === False || $td_pos < $table_pos))
				{
					$html .= $cell;
				}
				else
				{
					$html .= "\t\t<TD ".$row['.'.$key].">$cell</TD>\n";
				}
			}
			$html .= "\t</TR>\n";
		}
		$html .= "</TABLE>\n";

		if ($no_table_tr)
		{
			$html = substr($html,0,-16);
		}
		return $html;
	}
	
	function sbox_submit( $sbox,$no_script=0 )
	{
		$html = str_replace('<select','<select onChange="this.form.submit()" ',
								  $sbox);
		if ($no_script)
		{
			$html .= '<noscript>'.$this->submit_button('send','>').'</noscript>';
		}
		return $html;
	}

	function image( $app,$name,$title='',$options='' )
	{
		if (strstr($name,'.') === False)
		{
			$name .= '.gif';
		}
		if (!($path = $GLOBALS['phpgw']->common->image($app,$name)))
		{
			$path = $name;		// name may already contain absolut path
		}
		if (!@is_readable($_SERVER['DOCUMENT_ROOT'] . $path))
		{
			return $title;
		}
		if ($title)
		{
			$options .= " $this->prefered_img_title=\"".htmlspecialchars($title).'"';
		}
		return "<IMG SRC=\"$path\" $options>";
	}

	function a_href( $content,$url,$vars='',$options='')
	{
		if (!strstr($url,'/') && count(explode('.',$url)) == 3)
		{
			$url = "/index.php?menuaction=$url";
		}
		if (is_array($url))
		{
			$vars = $url;
			$url = '/index.php';
		}
		return '<a href="'.$this->link($url,$vars).'" '.$options.'>'.$content.'</a>';
	}

	function bold($content)
	{
		return '<b>'.$content.'</b>';
	}

	function italic($content)
	{
		return '<i>'.$content.'</i>';
	}

	function hr($width,$options='')
	{
		if ($width)
			$options .= " WIDTH=$width";
		return "<hr $options>\n";
	}

	/**
	 * formats option-string for most of the above functions
	*
	 * @param $options String (or Array) with option-values eg. '100%,,1'
	 * @param $names String (or Array) with the option-names eg. 'WIDTH,HEIGHT,BORDER'
	 * formatOptions('100%,,1','WIDTH,HEIGHT,BORDER') = ' WIDTH="100%" BORDER="1"'
	 * @return option string
	 */
	function formatOptions($options,$names)
	{
		if (!is_array($options)) $options = explode(',',$options);
		if (!is_array($names))   $names   = explode(',',$names);
		$html = '';
		while (list($n,$val) = each($options))
			if ($val != '' && $names[$n] != '')
				$html .= ' '.$names[$n].'="'.$val.'"';

		return $html;
	}

	/**
	 * returns simple stylesheet (incl. <STYLE> tags) for nextmatch row-colors
	*
	 * @return the classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	function themeStyles()
	{
		return $this->style($this->theme2css());
	}

	/**
	 * returns simple stylesheet for nextmatch row-colors
	*
	 * @return the classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	function theme2css()
	{
		//XXX Temporary fix, Caeies
		//if(isset($GLOBALS['phpgw_info']['theme']))
		//{
		//	return 
		//		".th { background: ".$GLOBALS['phpgw_info']['theme']['th_bg']."; font-weight: bold; }\n".
		//		".row_on,.th_bright { background: ".$GLOBALS['phpgw_info']['theme']['row_on']."; }\n".
		//		".row_off { background: ".$GLOBALS['phpgw_info']['theme']['row_off']."; }\n";
		//}
		//else
		//{
			return '';
		//}
	}

	function style($styles)
	{
		return $styles ? "<STYLE type=\"text/css\">\n<!--\n$styles\n-->\n</STYLE>" : '';
	}

	function label($content,$id='',$accesskey='',$options='')
	{
		if ($id != '')
		{
			$id = " FOR=\"$id\"";
		}
		if ($accesskey != '')
		{
			$accesskey = " ACCESSKEY=\"$accesskey\"";
		}
		return "<LABEL$id$accesskey $options>$content</LABEL>";
	}
}
