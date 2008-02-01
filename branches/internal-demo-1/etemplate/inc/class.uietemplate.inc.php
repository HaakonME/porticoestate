<?php
/**
* eTemplate - basic application development environment
* @copyright Copyright (C) 2002-2006 Free Software Foundation, Inc. http://www.fsf.org/
* @author Ralf Becker <ralf.becker@outdoortraining.de>
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package etemplate
* @version $Id: class.uietemplate.inc.php 17953 2007-02-14 09:56:08Z sigurdne $
*/
	include_once(PHPGW_INCLUDE_ROOT . '/etemplate/inc/class.boetemplate.inc.php');

	/**
	 * creates dialogs / HTML-forms from eTemplate descriptions
	 *
	 * etemplate or uietemplate extends boetemplate, all vars and public functions are inherited
	 * $tmpl = createObject('etemplate.etemplate','app.template.name');
	 * $tmpl->exec('app.class.callback',$content_to_show);
	 * This creates a form from the eTemplate 'app.template.name' and takes care that
	 * the method / public function 'callback' in (bo)class 'class' of 'app' gets called
	 * if the user submitts the form. Vor the complete param's see the description of exec.
	 * @param $debug enables debug messages: 0=no, 1=calls to show and process_show, 2=content of process_show
	 * @param                                3=calls to show_cell OR template- or cell-type name
	 * @param $html instances of html class used to generate the html
	 */
	class etemplate extends boetemplate
	{
		var $debug; // 1=calls to show and process_show, 2=content after process_show,
					// 3=calls to show_cell and process_show_cell, or template-name or cell-type
		var $html;	// instance of html-class
		var $class_conf = array('nmh' => 'th','nmr0' => 'row_on','nmr1' => 'row_off');

		/**
		* constructor of etemplate class, reads an eTemplate if $name is given
		*
		* @param $name name of etemplate or array with name and other keys
		* @param $load_via name/array with keys of other etemplate to load in order to get $name
		*/
		function etemplate($name='',$load_via='')
		{
			$this->public_functions += array(
				'exec'			=> True,
				'process_exec'	=> True,
				'show'			=> True,
				'process_show'	=> True,
			);
			$this->html = CreateObject('etemplate.html');	// should  be in the api (older version in infolog)

			$this->boetemplate($name,$load_via);

			list($a,$b,$c,$d) = explode('.',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
			//echo "Version: $a.$b.$c.$d\n";
			$this->stable = true;//$a <= 0 && $b <= 9 && $c <= 16 && !is_object($GLOBALS['phpgw']->xslttpl);
		}

		/*!
		@function location
		@abstract Abstracts a html-location-header call
		@discussion In other UI's than html this needs to call the methode, defined by menuaction or
		@discussion open a browser-window for any other links.
		*/
		function location($vars='')
		{
			$GLOBALS['phpgw']->redirect_link(is_array($vars) ? '/index.php' : $vars,
				is_array($vars) ? $vars : '');
		}

		/*!
		@function exec
		@abstract Generats a Dialog from an eTemplate - abstract the UI-layer
		@discussion This is the only function an application should use, all other are INTERNAL and
		@discussion do NOT abstract the UI-layer, because they return HTML.
		@discussion Generates a webpage with a form from the template and puts process_exec in the
		@discussion form as submit-url to call process_show for the template before it
		@discussion ExecuteMethod's the given $method of the caller.
		@param $method Methode (e.g. 'etemplate.editor.edit') to be called if form is submitted
		@param $content Array with content to fill the input-fields of template, eg. the text-field
		@param          with name 'name' gets its content from $content['name']
		@param $sel_options Array or arrays with the options for each select-field, keys are the
		@param              field-names, eg. array('name' => array(1 => 'one',2 => 'two')) set the
		@param              options for field 'name'. ($content['options-name'] is possible too !!!)
		@param $readonlys Array with field-names as keys for fields with should be readonly
		@param            (eg. to implement ACL grants on field-level or to remove buttons not applicable)
		@param $preserv Array with vars which should be transported to the $method-call (eg. an id) array('id' => $id)
			sets $_POST['id'] for the $method-call
		@param $return_html if true, dont show the page, just return the html
		@result nothing
		*/
		function exec($method,$content,$sel_options='',$readonlys='',$preserv='',$changes='',$return_html=False)
		{
			//echo "<br>globals[java_script] = '".$GLOBALS['phpgw_info']['etemplate']['java_script']."', this->java_script() = '".$this->java_script()."'\n";
			if (!$sel_options)
			{
				$sel_options = array();
			}
			if (!$readonlys)
			{
				$readonlys = array();
			}
			if (!$preserv)
			{
				$preserv = array();
			}
			if (!$changes)
			{
				$changes = array();
			}
			if (isset($content['app_header']))
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = $content['app_header'];
			}
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'etemplate')
			{
				$GLOBALS['phpgw']->translation->add_app('etemplate');	// some extensions have own texts
			}
			$id = $this->appsession_id();
			$GLOBALS['phpgw_info']['etemplate']['loop'] = False;
			$GLOBALS['phpgw_info']['etemplate']['form_options'] = '';	// might be set in show
			$GLOBALS['phpgw_info']['etemplate']['to_process'] = array();
			$html = ($this->stable ? $this->html->themeStyles()."\n\n" : ''). // so they get included once
				$this->html->form($this->include_java_script(1).
					$this->show($this->complete_array_merge($content,$changes),$sel_options,$readonlys,'exec'),array(
						'etemplate_exec_id' => $id
					),'/etemplate/process_exec.php', array('menuaction' => $method),'eTemplate', $GLOBALS['phpgw_info']['etemplate']['form_options']);
			//_debug_array($GLOBALS['phpgw_info']['etemplate']['to_process']);
			if ($this->stable)
			{
				$hooked = $GLOBALS['phpgw']->template->get_var('phpgw_body');
				if (!@$GLOBALS['phpgw_info']['etemplate']['hooked'] && !$return_html)
				{
					$GLOBALS['phpgw_info']['flags']['java_script'] = isset($GLOBALS['phpgw_info']['flags']['java_script']) ? $GLOBALS['phpgw_info']['flags']['java_script'] : '' . $this->include_java_script(2);
					$GLOBALS['phpgw']->common->phpgw_header();
				}
				else
				{
					$html = $this->include_java_script(2).$html;	// better than nothing
				}
			}
			else
			{
				$hooked = $GLOBALS['phpgw']->xslttpl->get_var('phpgw');
				$hooked = $hooked['body_data'];
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('java_script' => $GLOBALS['phpgw_info']['flags']['java_script'].$this->include_java_script(2)));
			}

			$tmpa = explode(',',$this->size);
			$width = isset($tmpa[0]) ? $tmpa[0] : '';
			$height = isset($tmpa[1]) ? $tmpa[1] : '';
			$overflow = isset($tmpa[6]) ? $tmpa[6] : '';
			unset($tmpa);
			if ($overflow)
			{
				$html = $this->html->div($html,'STYLE="'.($width?"width: $width; ":'').($height?"height: $height; ":'')."overflow: $overflow;\"");
			}
			$id = $this->save_appsession($this->as_array(1) + array(
				'readonlys' => $readonlys,
				'content' => $content,
				'changes' => $changes,
				'sel_options' => $sel_options,
				'preserv' => $preserv,
				'extension_data' => isset($GLOBALS['phpgw_info']['etemplate']['extension_data']) ? $GLOBALS['phpgw_info']['etemplate']['extension_data'] : '',
				'to_process' => isset($GLOBALS['phpgw_info']['etemplate']['to_process']) ? $GLOBALS['phpgw_info']['etemplate']['to_process'] : '',
				'java_script' => isset($GLOBALS['phpgw_info']['etemplate']['java_script']) ? $GLOBALS['phpgw_info']['etemplate']['java_script'] : '',
				'dom_enabled' => isset($GLOBALS['phpgw_info']['etemplate']['dom_enabled']) ? $GLOBALS['phpgw_info']['etemplate']['dom_enabled'] : '',
				'method' => $method,
				'hooked' => $hooked != '' ? $hooked : isset($GLOBALS['phpgw_info']['etemplate']['hook_content']) ? $GLOBALS['phpgw_info']['etemplate']['hook_content'] : ''
			),$id);

			if ($return_html)
			{
				return $html;
			}
			if ($this->stable)
			{
				if (!@$GLOBALS['phpgw_info']['etemplate']['hooked'])
				{
					echo parse_navbar();
				}
				echo (isset($GLOBALS['phpgw_info']['etemplate']['hook_content']) ? $GLOBALS['phpgw_info']['etemplate']['hook_content'] : '')
					. $html;

				if (!@$GLOBALS['phpgw_info']['etemplate']['hooked'] &&
					(!isset($_GET['menuaction']) || strstr($_SERVER['PHP_SELF'],'process_exec.php')))
				{
					$GLOBALS['phpgw_info']['flags']['xslt_app'] = false; // this get wrongfullfy picked up somewhere when writing langfiles
					$GLOBALS['phpgw']->common->phpgw_footer();
				}
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('body_data' => $html));
				$GLOBALS['phpgw']->xslttpl->add_file('app_data');
				$GLOBALS['phpgw']->xslttpl->pparse();				
			}
		}

		/*!
		@function process_exec
		@abstract Makes the necessary adjustments to _POST before it calls the app's method
		@discussion This function is only to submit forms to, create with exec.
		@discussion All eTemplates / forms executed with exec are submited to this function
		@discussion (via the global index.php and menuaction). It then calls process_show
		@discussion for the eTemplate (to adjust the content of the _POST) and
		@discussion ExecMethod's the given callback from the app with the content of the form as first argument.
		*/
		function process_exec()
		{
			//echo "process_exec: _POST ="; _debug_array($_POST);
			$session_data = $this->get_appsession(isset($_POST['etemplate_exec_id']) ? $_POST['etemplate_exec_id'] : '');
			//echo "<p>process_exec: session_data ="; _debug_array($session_data);

			if ((!isset($_POST['etemplate_exec_id']) || !$_POST['etemplate_exec_id']) || !is_array($session_data) || count($session_data) < 10)
			{
				// this prevents an empty screen, if the sessiondata gets lost somehow
				$this->location(array('menuaction' => $_GET['menuaction']));
			}
			$content = isset($_POST['exec']) ? $_POST['exec'] : '';
			if (!is_array($content))
			{
				$content = array();
			}
			$this->init($session_data);
			$GLOBALS['phpgw_info']['etemplate']['extension_data'] = $session_data['extension_data'];
			$GLOBALS['phpgw_info']['etemplate']['java_script'] = $session_data['java_script'] || $_POST['java_script'];
			$GLOBALS['phpgw_info']['etemplate']['dom_enabled'] = $session_data['dom_enabled'] || $_POST['dom_enabled'];
			//echo "globals[java_script] = '".$GLOBALS['phpgw_info']['etemplate']['java_script']."', session_data[java_script] = '".$session_data['java_script']."', _POST[java_script] = '".$_POST['java_script']."'\n";
			//echo "process_exec($this->name) content ="; _debug_array($content);
			$this->process_show($content,$session_data['to_process'],'exec');

			//echo "process_exec($this->name) process_show(content) ="; _debug_array($content);
			//echo "process_exec($this->name) session_data[changes] ="; _debug_array($session_data['changes']);
			$content = $this->complete_array_merge($session_data['changes'],$content);
			//echo "process_exec($this->name) merge(changes,content) ="; _debug_array($content);

			if (isset($GLOBALS['phpgw_info']['etemplate']['loop']) && $GLOBALS['phpgw_info']['etemplate']['loop'])
			{
				if ($session_data['hooked'] != '')	// set previous phpgw_body if we are called as hook
				{
					if ($this->stable)
					{
						//echo "<p>process_exec: hook_content set</p>\n";
						$GLOBALS['phpgw_info']['etemplate']['hook_content'] = $session_data['hooked'];
					}
					else
					{
						$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('body_data' => $session_data['hooked']));
					}
				}
				//echo "<p>process_exec($this->name): <font color=red>loop is set</font>, content=</p>\n"; _debug_array($content);
				$this->exec($_GET['menuaction'],$session_data['content'],$session_data['sel_options'],
					$session_data['readonlys'],$session_data['preserv'],$content);
			}
			else
			{
				ExecMethod($_GET['menuaction'],$this->complete_array_merge($session_data['preserv'],$content));
			}
		}

		function check_disabled($disabled,$content)
		{
			//return False;
			if ($not = $disabled[0] == '!')
			{
				$disabled = substr($disabled,1);
			}
			@list($val,$check_val) = $vals = explode('=',$disabled);

			if ($val[0] == '@')
			{
				$val = $this->get_array($content,substr($val,1));
			}
			if ($check_val[0] == '@')
			{
				$check_val = $this->get_array($content,substr($check_val,1));
			}
			$result = count($vals) == 1 ? $val != '' : $val == $check_val;
			if ($not) $result = !$result;
			//echo "<p>check_disabled: '".($not?'!':'')."$disabled' = '$val' ".(count($vals) == 1 ? '' : ($not?'!':'=')."= '$check_val'")." = ".($result?'True':'False')."</p>\n";
			return $result;
		}

		/**
		 * creates HTML from an eTemplate
		*
		 * This is done by calling show_cell for each cell in the form. show_cell itself
		 * calls show recursivly for each included eTemplate.
		 * You can use it in the UI-layer of an app, just make shure to call process_show !!!
		 * This is intended as internal function and should NOT be called by new app's direct,
		 * as it deals with HTML and is so UI-dependent, use exec instead.
		 * @param $content array with content for the cells, keys are the names given in the cells/form elements
		 * @param $sel_options array with options for the selectboxes, keys are the name of the selectbox
		 * @param $readonlys array with names of cells/form-elements to be not allowed to change
		 * @param            This is to facilitate complex ACL's which denies access on field-level !!!
		 * @param $cname basename of names for form-elements, means index in $_POST
		 * @param        eg. $cname='cont', element-name = 'name' returned content in $_POST['cont']['name']
		 * @param $show_xxx row,col name/index for name expansion
		 * @return the generated HTML
		 */
		function show($content,$sel_options='',$readonlys='',$cname='',$show_c=0,$show_row=0,
			$no_table_tr=False,$tr_class='')
		{
			if (!$sel_options)
			{
				$sel_options = array();
			}
			if (!$readonlys)
			{
				$readonlys = array();
			}
			if (is_int($this->debug) && $this->debug >= 1 || $this->debug == $this->name && $this->name)
			{
				echo "<p>etemplate.show($this->name): $cname =\n"; _debug_array($content);
			}
			if (!is_array($content))
			{
				$content = array();	// happens if incl. template has no content
			}
			$content += array(	// for var-expansion in names in show_cell
				'.c' => $show_c,
				'.col' => $this->num2chrs($show_c-1),
				'.row' => $show_row
			);
			reset($this->data);
			if (isset($this->data[0]))
			{
				list(,$opts) = each($this->data);
			}
			else
			{
				$opts = array();
			}
			for ($r = 0; $row = 1+$r /*list($row,$cols) = each($this->data)*/; ++$r)
			{
				if (!(list($r_key) = each($this->data)))	// no further row
				{
					if (!($this->autorepeat_idx($cols['A'],0,$r,$idx,$idx_cname) && $idx_cname) &&
						!($this->autorepeat_idx((isset($cols['B']) ? $cols['B'] : ''),1,$r,$idx,$idx_cname) && $idx_cname) ||
						!$this->isset_array($content,$idx))
					{
						break;                     	// no auto-row-repeat
					}
				}
				else
				{
					$cols = &$this->data[$r_key];
					if(isset($opts["h$row"]))
					{
						@list($height,$disabled) = explode(',',$opts["h$row"]);
					}
					else
					{
						$height ='';
						$disabled= '';
					}
					if(isset($opts["c$row"]))
					{
						$class = $no_table_tr ? $tr_class : $opts["c$row"];
					}
					else
					{
						$class = '';
					}
				}
				if ($disabled != '' && $this->check_disabled($disabled,$content))
				{
					continue;	// row is disabled
				}
				if(!isset($rows[".$row"]))
				{
					$rows[".$row"] = '';
				}
				$rows[".$row"] .= $this->html->formatOptions($height,'HEIGHT');
				list($cl) = explode(',',$class);
				if ($cl == 'nmr' || $cl == 'row')
				{
					$cl = 'row_'.(@$nmr_alternate++ & 1 ? 'off' : 'on'); // alternate color
				}
				$cl = isset($this->class_conf[$cl]) ? $this->class_conf[$cl] : $cl;
				$rows[".$row"] .= $this->html->formatOptions($cl,'CLASS');
				$rows[".$row"] .= $this->html->formatOptions($class,',VALIGN');

				reset ($cols);
				$row_data = array();
				for ($c = 0; True /*list($col,$cell) = each($cols)*/; ++$c)
				{
					$col = $this->num2chrs($c);
					if (!(list($c_key) = each($cols)))		// no further cols
					{
						if (!$this->autorepeat_idx($cell,$c,$r,$idx,$idx_cname,True) ||
							!$this->isset_array($content,$idx))
						{
							break;	// no auto-col-repeat
						}
					}
					else if(isset($c_key) && isset($cols[$c_key]))
					{
						$cell = &$cols[$c_key];
						if(isset($opts[$col]))
						{
							$col_disabled = '';
							$tmp = explode(',',$opts[$col]);
							$col_width = $tmp[0];
							if ( count($tmp) == 2 )
							{
								$col_disabled = $tmp[1];
							}
							unset($tmp);
						}
						else
						{
							$col_width=0;
							$col_disabled=false;
						}
						
						if (!isset($cell['height']))	// if not set, cell-height = height of row
						{
							$cell['height'] = $height;
						}
						if (!isset($cell['width']))	// if not set, cell-width = width of column or table
						{
							if(isset($cell['span']))
							{
								list($col_span) = explode(',',$cell['span']);
							}
							else
							{
								$col_span = '';
							}
							if ($col_span == 'all' && !$c)
							{
								list($cell['width']) = explode(',',$this->size);
							}
							else
							{
								$cell['width'] = $col_width;
							}
						}
					}
					if ( isset($cell['type']) && isset($cell['onchange'])
						&& $cell['type'] == 'template' && $cell['onchange'])
					{
						$cell['tr_class'] = $cl;
					}
					if ($col_disabled != '' && $this->check_disabled($col_disabled,$content))
					{
						continue;	// col is disabled
					}
					$row_data[$col] = $this->show_cell($cell,$content,$sel_options,$readonlys,$cname,
						$c,$r,$span);
					if ($row_data[$col] == '' && $this->rows == 1)
					{
						unset($row_data[$col]);	// omit empty/disabled cells if only one row
						continue;
					}
					if(!isset($row_data[".$col"]))
					{
						$row_data[".$col"] = '';
					}
					if (isset($cell['onclick']) && $cell['onclick'])	// can only be set via source at the moment
					{
						$row_data[".$col"] .= ' onClick="'.$cell['onclick'].'"';

						if ($cell['id'])
						{
							$row_data[".$col"] .= ' ID="'.$cell['id'].'"';
						}
					}
					$colspan = $span == 'all' ? $this->cols-$c : 0+$span;
					if ($colspan > 1)
					{
						$row_data[".$col"] .= " COLSPAN=\"$colspan\"";
						for ($i = 1; $i < $colspan; ++$i,++$c)
						{
							each($cols);	// skip next cell(s)
						}
					}
					else
					{
						if(isset($opts[$col]))
						{
							$disable = '';
							$tmp = explode(',', $opts[$col]);
							$width = $tmp[0];
							if ( count($tmp) == 2 )
							{
								$disable = $tmp[1];
							}
							unset($tmp);

							if ($width)		// width only once for a non colspan cell
							{
								$row_data[".$col"] .= " width=\"$width\"";
								$opts[$col] = "0,$disable";
							}
						}
					}
					if(isset($cell['align']))
					{
						$row_data[".$col"] .= $this->html->formatOptions($cell['align'],'ALIGN');
					}
					if(isset($cell['span']))
					{
						$cl = explode(',',$cell['span']);
						$cl = isset($cl[1]) ? $cl[1] : '';
						$cl = $this->expand_name(isset($this->class_conf[$cl]) ? $this->class_conf[$cl] : $cl,
							$c,$r,$show_c,$show_row,$content);
						$row_data[".$col"] .= $this->html->formatOptions($cl,'CLASS');
					}
				}
				$rows[$row] = $row_data;
			}
			if ( !isset($GLOBALS['phpgw_info']['etemplate']['styles_included'][$this->name])
				|| !$GLOBALS['phpgw_info']['etemplate']['styles_included'][$this->name] )
			{
				$style = $this->html->style($this->style);
				$GLOBALS['phpgw_info']['etemplate']['styles_included'][$this->name] = True;
			}
			$html = $this->html->table($rows,$this->html->formatOptions($this->size,'WIDTH,HEIGHT,BORDER,CLASS,CELLSPACING,CELLPADDING'),$no_table_tr);

			$tmpa = explode(',',$this->size);
			$width = isset($tmpa[0]) ? $tmpa[0] : '';
			$height = isset($tmpa[1]) ? $tmpa[1] : '';
			$overflow = isset($tmpa[6]) ? $tmpa[6] : '';
			unset($tmpa);

			if (!empty($overflow)) {
				$div_style=' STYLE="'.($width?"width: $width; ":'').($height ? "height: $height; ":'')."overflow: $overflow\"";
				$html = $this->html->div($html,$div_style);
			}
			$style = isset($style) ? $style : '';
			return "\n\n<!-- BEGIN $this->name -->\n$style\n".$html."<!-- END $this->name -->\n\n";
		}

		/**
		* generates HTML for 1 input-field / cell
		*
		* calls show to generate included eTemplates. Again only an INTERMAL function.
		* @param $cell array with data of the cell: name, type, ...
		* @param for rest see show
		* @return the generated HTML
		*/
		function show_cell($cell,$content,$sel_options,$readonlys,$cname,$show_c,$show_row,&$span)
		{
			$options = '';
			if (is_int($this->debug) && $this->debug >= 3 || $this->debug == $cell['type'])
			{
				echo "<p>etemplate.show_cell($this->name,name='${cell['name']}',type='${cell['type']}',cname='$cname')</p>\n";
			}

			if(isset($cell['span']))
			{
				list($span) = explode(',',$cell['span']);	// evtl. overriten later for type template
			}
			else
			{
				$span = '';
			}

			if (isset($cell['name'][0]) && $cell['name'][0] == '@' && $cell['type'] != 'template')
			{
				$cell['name'] = $this->get_array($content,substr($cell['name'],1));
			}
			$name = $this->expand_name(isset($cell['name']) ? $cell['name'] : '',$show_c,$show_row,$content['.c'],$content['.row'],$content);
			
			$name_parts = isset($name) && !is_object($name) ? explode('[',str_replace(']','',$name)) : array();
			
			if (!empty($cname))
			{
				array_unshift($name_parts,$cname);
			}
			$form_name = array_shift($name_parts);
			if (count($name_parts))
			{
				$form_name .= '['.implode('][',$name_parts).']';
			}
			
			$value = !is_object($name) ? $this->get_array($content,$name) : '';

			if ((isset($readonly) && $readonly = $cell['readonly']) || (@$readonlys[$name] && !is_array($readonlys[$name])) || (isset($readonlys['__ALL__']) && $readonlys['__ALL__']))
			{
				$options .= ' READONLY';
			}
	
			if ((isset($cell['disabled']) && $cell['disabled']) || (isset($readonly) && $readonly) && $cell['type'] == 'button' && !strstr($cell['size'],','))
			{
				if ($this->rows == 1) {
					return '';	// if only one row omit cell
				}
				$cell = $this->empty_cell(); // show nothing
				$value = '';
			}
			$extra_label = True;

			@list($type,$sub_type) = explode('-',$cell['type']); // fix this

			if (((!isset($this->types[$cell['type']]) || !$this->types[$cell['type']]) || !empty($sub_type)) && $this->haveExtension($type,'pre_process'))
			{
				$ext_type = $type;
				$extra_label = $this->extensionPreProcess($ext_type,$form_name,$value,$cell,$readonlys[$name]);

				$readonly = (isset($readonly) && $readonly) || (isset($cell['readonly']) && $cell['readonly']); // might be set be extension
				$this->set_array($content,$name,$value);
			}

			$cell_options =  '';
			if ( isset($cell['size']) )
			{
				$cell_options = $cell['size'];
			}
			if ( strlen($cell_options) && $cell_options[0] == '@')
			{
				$cell_options = $this->get_array($content,substr($cell_options,1));
			}

			if ( !isset($cell['label']) )
			{
				$cell['label'] = '';
			}
			$label = $this->expand_name($cell['label'],$show_c,$show_row,$content['.c'],$content['.row'],$content);

			$help = '';
			if ( isset($cell['help']) )
			{
				$help = $cell['help'];
			}
			if ( isset($help[0]) && $help[0] == '@')
			{
				$help = $this->get_array($content,substr($help,1));
			}

			$blur = '';
			if ( isset($call['blur']) )
			{
				if ( $cell['blur'][0] == '@' )
				{
					$blur = $this->get_array($content,substr($cell['blur'],1));
				}
				else if ( strlen($cell['blur']) <= 1 )
				{
					$blur = $cell['blur'];
				}
				$blur = lang($blur);
			}

			$onBlur = '';
			$onFocus = '';
			if ($this->java_script())
			{
				if ($blur)
				{
					if (empty($value))
					{
						$value = $blur;
					}
					$onFocus .= "if(this.value=='".addslashes(htmlspecialchars($blur))."') this.value='';";
					$onBlur  .= "if(this.value=='') this.value='".addslashes(htmlspecialchars($blur))."';";
				}
				if ($help)
				{
					if ( isset($cell['no_lang']) && $cell['no_lang'] < 2)
					{
						$help = lang($help);
					}
					$onFocus .= "self.status='".addslashes(htmlspecialchars($help))."'; return true;";
					$onBlur  .= "self.status=''; return true;";
					if ($cell['type'] == 'button' || $cell['type'] == 'file')	// for button additionally when mouse over button
					{
						$options .= " onMouseOver=\"self.status='".addslashes(htmlspecialchars($help))."'; return true;\"";
						$options .= " onMouseOut=\"self.status=''; return true;\"";
					}
				}
				if ($onBlur)
				{
					$options .= " onFocus=\"$onFocus\" onBlur=\"$onBlur\"";
				}
				if ( isset($cell['onchange']) && $cell['onchange'] && $cell['type'] != 'button') // values != '1' can only set by a program (not in the editor so fa
				{
					$options .= ' onChange="'.($cell['onchange']=='1'?'this.form.submit();':$cell['onchange']).'"';
				}
			}
			if ($form_name != '')
			{
				$options = "ID=\"$form_name\" $options";
			}

			$html = '';

			@list($type,$sub_type) = explode('-',$cell['type']);
			switch ($type)
			{
				case 'label':		//  size: [[b]old][[i]talic][,link]
/*					if (is_array($value))
						break;
					list($style,$extra_link) = explode(',',$cell_options);
					$value = strlen($value) > 1 && !$cell['no_lang'] ? lang($value) : $value;
					$value = nl2br(htmlspecialchars($value));
					if ($value != '' && strstr($style,'b')) $value = $this->html->bold($value);
					if ($value != '' && strstr($style,'i')) $value = $this->html->italic($value);
					$html .= $value;
					break;
					
*/

					if (is_array($value))
					{
						break;
					}
					$extra_link = '';
					$tmp = explode(',', $cell_options);
					$style = $tmp[0];
					if ( count($tmp) == 2 )
					{
						$extra_link = $tmp[1];
					}
					unset($tmp);
					$value = strlen($value) > 1 && ( !isset($cell['no_lang']) || !$cell['no_lang']) ? lang($value) : $value;
					$value = nl2br(htmlspecialchars($value));
					if ($value != '' && strstr($style,'b')) $value = $this->html->bold($value);
					if ($value != '' && strstr($style,'i')) $value = $this->html->italic($value);
					$html .= $value;
					break;
				case 'html':
					$extra_link = $cell_options;
					$html .= $value;
					break;
				case 'int':		// size: [min][,[max][,len]]
				case 'float':
					$tmp = explode(',', $cell_options);
					$min = $tmp[0];
					$max = isset($tmp[1]) ? $tmp[1] : '';
					$cell_options = isset($tmp[2]) ? $tmp[2] : '';
					unset($tmp);

					if ($cell_options == '')
					{
						$cell_options = $cell['type'] == 'int' ? 5 : 8;
					}

				case 'text':		// size: [length][,maxLength]
					if (isset($readonly) && $readonly)
					{
						$html .= $this->html->bold(htmlspecialchars($value));
					}
					else
					{
						$html .= $this->html->input($form_name,$value,'',
							$options.$this->html->formatOptions($cell_options,'SIZE,MAXLENGTH'));
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					}
					break;
				case 'textarea':	// Multiline Text Input, size: [rows][,cols]
					$html .= $this->html->textarea($form_name,$value,
						$options.$this->html->formatOptions($cell_options,'ROWS,COLS'));
					if (!isset($readonly) || !$readonly)
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					break;
				case 'checkbox':
					if (!empty($cell_options))
					{
						@list($true_val,$false_val,$ro_true,$ro_false) = explode(',',$cell_options);
						$value = $value == $true_val;
					}
					else
					{
						$ro_true = 'x';
						$ro_false = '';
					}
					if (isset($value) && $value)
					{
						$options .= ' CHECKED';
					}
					if ( isset($readonly) && $readonly)
					{
						$html .= $value ? $this->html->bold($ro_true) : $ro_false;
					}
					else
					{
						$html .= $this->html->input($form_name,'1','CHECKBOX',$options);
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = array(
							'type' => $cell['type'],
							'values' => $cell_options
						);
					}
					break;
				case 'radio':		// size: value if checked
					$set_val = $this->expand_name($cell_options,$show_c,$show_row,$content['.c'],$content['.row'],$content);

					if ($value == $set_val)
					{
						$options .= ' CHECKED';
					}
					if (isset($readonly) && $readonly)
					{
						$html .= $value == $set_val ? $this->html->bold('x') : '';
					}
					else
					{
						$html .= $this->html->input($form_name,$set_val,'RADIO',$options);
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					}
					break;
				case 'button':
					list($app) = explode('.',$this->name);
					if ($this->java_script() && isset($cell['onchange']) && $cell['onchange'] != '' && (!isset($cell['needed']) || !$cell['needed'])) // use a link instead of a button
					{
						if ($cell['onchange'] == 1)
						{
							$html .= $this->html->input_hidden($form_name,'',False) . "\n";
							$html .= '<a href="" onClick="set_element(document.eTemplate,\''.$form_name.'\',\'pressed\'); document.eTemplate.submit(); return false;" '.$options.'>' .
								(strlen($label) <= 1 || (isset($cell['no_lang']) && $cell['no_lang']) ? $label : lang($label)) . '</a>';
						}
						else	// use custom javascript
						{
							$html .= '<a href="" onClick="'.$cell['onchange'].'; return false;" '.$options.'>' .
								(strlen($label) <= 1 || $cell['no_lang'] ? $label : lang($label)) . '</a>';
						}
					}
					else
					{
						$ro_img = '';
						$tmp = explode(',', $cell_options);
						$img = $tmp[0];
						if ( count($tmp) == 2 )
						{
							$ro_img = $tmp[1];
						}
						unset($tmp);

						if (!empty($img))
						{
							$options .= ' title="'.(strlen($label)<=1||(isset($cell['no_lang']) && $cell['no_lang'])?$label:lang($label)).'"';
						}
						$html .= ( !isset($readonly) || !$readonly )  
									? $this->html->submit_button($form_name,$label, isset($cell['onchange']) ? $cell['onchange'] : '', strlen($label) <= 1 || isset($cell['no_lang']) ,$options, $img, $app) 
									: $this->html->image($app,$ro_img);
					}
					$extra_label = False;
					if (!isset($readonly) || !$readonly )
					{
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					}
					break;
				case 'hrule':
					$html .= $this->html->hr($cell_options);
					break;
				case 'template':	// size: index in content-array (if not full content is past further on)
					if (is_object($cell['name']))
					{
						$cell['obj'] = &$cell['name'];
						unset($cell['name']);
						$cell['name'] = 'was Object';
						echo "<p>Object in Name in tpl '$this->name': "; _debug_array($this->data);
					}
					$obj_read = 'already loaded';
					if (!isset($cell['obj']) || !is_object($cell['obj']))
					{
						if ($cell['name'][0] == '@')
						{
							$cell['obj'] = $this->get_array($content,substr($cell['name'],1));
							$obj_read = is_object($cell['obj']) ? 'obj from content' : 'obj read, obj-name from content';
							if (!is_object($cell['obj']))
							{
								$cell['obj'] = new etemplate($cell['obj'],$this->as_array());
							}
						}
						else
						{  $obj_read = 'obj read';
							$cell['obj'] = new etemplate(/*** TESTWEISE ***$cell['name']*/$name,$this->as_array());
						}
					}
					if (is_int($this->debug) && $this->debug >= 3 || $this->debug == $cell['type'])
					{
						echo "<p>show_cell::template(tpl=$this->name,name=$cell[name]): $obj_read</p>\n";
					}
					if ($this->autorepeat_idx($cell,$show_c,$show_row,$idx,$idx_cname) || $cell_options != '')
					{
						if ($span == '' && isset($content[$idx]['span']))
						{	// this allows a colspan in autorepeated cells like the editor
							list($span) = explode(',',$content[$idx]['span']);
							if ($span == 'all')
							{
								$span = 1 + $content['cols'] - $show_c;
							}
						}
						$readonlys = $this->get_array($readonlys,$idx); //$readonlys[$idx];
						$content = $this->get_array($content,$idx); // $content[$idx];
						if ($idx_cname != '')
						{
							$cname .= $cname == '' ? $idx_cname : '['.str_replace('[','][',str_replace(']','',$idx_cname)).']';
						}
						//echo "<p>show_cell-autorepeat($name,$show_c,$show_row,cname='$cname',idx='$idx',idx_cname='$idx_cname',span='$span'): content ="; _debug_array($content);
					}
					if (isset($readonly) && $readonly)
					{
						$readonlys['__ALL__'] = True;
					}
					$html = $cell['obj']->show($content,$sel_options,$readonlys,$cname,$show_c,$show_row,isset($cell['onchange']) ? $cell['onchange'] : '',isset($cell['tr_class']) ? $cell['tr_class'] : '');
					break;
				case 'select':	// size:[linesOnMultiselect]
					$sels = array();
					list($multiple) = explode(',',$cell_options);
					if (!empty($multiple) && 0+$multiple <= 0)
					{
						$sels[''] = $multiple < 0 ? 'all' : $multiple;
						if ($cell['no_lang'])
						{
							$sels[''] = lang($sels['']);
						}
						$multiple = 0;
					}
					if (!empty($cell['sel_options']))
					{
						if (!is_array($cell['sel_options']))
						{
							$opts = explode(',',$cell['sel_options']);
							while (list(,$opt) = each($opts))
							{
								list($k,$v) = explode('=',$opt);
								$sels[$k] = $v;
							}
						}
						else
						{
							$sels += $cell['sel_options'];
						}
					}
					if (isset($sel_options[$name]) && is_array($sel_options[$name]))
					{
						$sels += $sel_options[$name];
					}
					elseif (count($name_parts))
					{
						$org_name = $name_parts[count($name_parts)-1];
						if (isset($sel_options[$org_name]) && is_array($sel_options[$org_name]))
						{
							$sels += $sel_options[$org_name];
						}
					}
					if (isset($content["options-$name"]))
					{
						$sels += $content["options-$name"];
					}
					if (isset($readonly) && $readonly)
					{
						$html .= $cell['no_lang'] ? (isset($sels[$value])?$sels[$value]:'') : lang($sels[$value]);
					}
					else
					{
						$html .= $this->html->select($form_name.($multiple > 1 ? '[]' : ''),$value,$sels,
							isset($cell['no_lang']) ? $cell['no_lang'] : '',$options,$multiple);
						$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					}
					break;
				case 'image':
					$image = $value != '' ? $value : $name;
					$image = $this->html->image(substr($this->name,0,strpos($this->name,'.')),
						$image,strlen($label) > 1 && (!isset($cell['no_lang']) || !$cell['no_lang']) ? lang($label) : $label,'BORDER="0"');
					$html .= $image;
					$extra_link = $cell_options;
					$extra_label = False;
					break;
				case 'file':
					$html .= $this->html->input_hidden($path = str_replace($name,$name.'_path',$form_name),'.');
					$html .= $this->html->input($form_name,'','file',$options);
					$GLOBALS['phpgw_info']['etemplate']['form_options'] =
						"enctype=\"multipart/form-data\" onSubmit=\"set_element2(this,'$path','$form_name')\"";
					$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = $cell['type'];
					break;
				case 'vbox':
				case 'hbox':
					$rows = array();
					$box_row = 1;
					$box_col = 'A';
					$box_anz = 0;
					for ($n = 1; $n <= $cell_options; ++$n)
					{
						$h = $this->show_cell($cell[$n],$content,$sel_options,$readonlys,$cname,$show_c,$show_row,$nul);
						if ($h != '' && $h != '&nbsp;')
						{
							if ($cell['type'] == 'vbox')
							{
								$box_row = $n;
							}
							else
							{
								$box_col = $this->num2chrs($n);
							}
							$rows[$box_row][$box_col] = $html = $h;
							$box_anz++;
							if ( isset($cell[$n]['align']) && $cell[$n]['align'] )
							{
								$rows[$box_row]['.'.$box_col] = $this->html->formatOptions($cell[$n]['align'],'ALIGN');
							}
							$cl = '';
							if ( isset($cell[$n]['span']) )
							{
								$tmp = explode(',',$cell[$n]['span']);
								if ( count($tmp) == 2 )
								{
									$cl = $tmp[1];
								}
							}
							$cl = $this->expand_name(isset($this->class_conf[$cl]) ? $this->class_conf[$cl] : $cl,
								$show_c,$show_row,$content['.c'],$content['.row'],$content);
							if ( !isset($rows[$box_row][".$box_col"]) )
							{
								$rows[$box_row][".$box_col"] = '';
							}
							$rows[$box_row][".$box_col"] .= $this->html->formatOptions($cl, 'CLASS');
						}
					}
					if ($box_anz > 1)	// a single cell is NOT placed into a table
					{
						$html = "\n\n<!-- BEGIN {$cell['type']} -->\n\n".
							$this->html->table($rows,$this->html->formatOptions($cell_options,',CELLPADDING,CELLSPACING').
							(isset($cell['align']) && $cell['align'] && $type == 'vbox' ? ' width="100%"' : '')).	// alignment only works if table has full width - 100% tables break IE
							"\n\n<!-- END {$cell['type']} -->\n\n";
					}
					break;

				case 'deck':
					for ($n = 1; $n <= $cell_options && (empty($value) || $value != $cell[$n]['name']); ++$n) ;
					if ($n > $cell_options)
					{
						$value = $cell[1]['name'];
					}
					if ($s_width = $cell['width'])
					{
						$s_width = "width: $s_width".(substr($s_width,-1) != '%' ? 'px' : '').';';
					}
					if ($s_height = $cell['height'])
					{
						$s_height = "height: $s_height".(substr($s_height,-1) != '%' ? 'px' : '').';';
					}
					for ($n = 1; $n <= $cell_options; ++$n)
					{
						$h = $this->show_cell($cell[$n],$content,$sel_options,$readonlys,$cname,$show_c,$show_row,$nul);
						$vis = !empty($value) && $value == $cell_options[$n]['name'] || $n == 1 && $first ? 'visible' : 'hidden';
						list (,$cl) = explode(',',$cell[$n]['span']);
						$html .= $this->html->div($h,$this->html->formatOptions(array(
							$cl.($cl ? ' ':'').'tab_body',
							"$s_width $s_height position: absolute; left: 0px; top: 0px; visibility: $vis; z-index: 50;",
							$cell[$n]['name']
						),'CLASS,STYLE,ID'));
					}
					$html .= $this->html->input_hidden($form_name,$value);	// to store active plane
					
					list (,$cl) = explode(',',$cell['span']);
					$html = $this->html->input_hidden($form_name,$value)."\n".	// to store active plane
						$this->html->div($html,$this->html->formatOptions(array(
							$cl,
							"$s_width $s_height position: relative; z-index: 100;"
						),'CLASS,STYLE'));
					break;
				default:
					if ($ext_type && $this->haveExtension($ext_type,'render'))
					{
						$html .= $this->extensionRender($ext_type,$form_name,$value,$cell,$readonly);
					}
					else
					{
						$html .= "<i>unknown type '$cell[type]'</i>";
					}
					break;
			}
	//		if ($ext_type && !$readonly && $this->haveExtension($ext_type,'post_process'))	// extension-processing need to be after all other and only with diff. name
			if ( isset($ext_type) && $ext_type && !$readonly && $this->haveExtension($ext_type,'post_process'))	// extension-processing need to be after all other and only with diff. name
			{	// unset it first, if it is already set, to be after the other widgets of the ext.
				unset($GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name]);
				$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = 'ext-'.$ext_type;
			}
			// save blur-value to strip it in process_exec
			if (!empty($blur) && isset($GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name]))
			{
				$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] = is_array($GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name]) ? $GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name] : array('type' => $GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name]);
				$GLOBALS['phpgw_info']['etemplate']['to_process'][$form_name]['blur'] = $blur;
			}
			if ($extra_label && ($label != '' || $html == ''))
			{
//				if (strlen($label) > 1 && !($cell['no_lang'] && $cell['label'] != $label || $cell['no_lang'] == 2))
				if (strlen($label) > 1 && !(isset($cell['no_lang']) && $cell['no_lang'] && $cell['label'] != $label || (isset($cell['no_lang']) && $cell['no_lang'] == 2)) )
				{
					$label = lang($label);
				}
				if (($accesskey = strstr($label,'&')) && $accesskey[1] != ' ' && $form_name != '' &&
				    (($pos = strpos($accesskey,';')) === False || $pos > 5))
				{
					$label = str_replace('&'.$accesskey[1],'<u>'.$accesskey[1].'</u>',$label);
					$label = $this->html->label($label,$form_name,$accesskey[1]);
				}
				if ($type == 'radio' || $type == 'checkbox' || strstr($label,'%s'))	// default for radio is label after the button
				{
					$html = strstr($label,'%s') ? str_replace('%s',$html,$label) : $html.' '.$label;
				}
				elseif (($html = $label . ' ' . $html) == ' ')
				{
					$html = '&nbsp;';
				}
			}
			if (isset($extra_link) && $extra_link)
			{
				$extra_link = $this->expand_name($extra_link,$show_c,$show_row,$content['.c'],$content['.row'],$content);
				if ($extra_link[0] == '@')
				{
					$extra_link = $this->get_array($content,substr($extra_link,1));
				}
				if ($extra_link)
				{
					$options = " onMouseOver=\"self.status='".addslashes(lang($help))."'; return true;\"";
					$options .= " onMouseOut=\"self.status=''; return true;\"";
					return $this->html->a_href($html,$extra_link,'',$help != '' ? $options : '');
				}
			}
			return $html;
		}


		/*!
		@function process_show
		@abstract makes necessary adjustments on _POST after a eTemplate / form gots submitted
		@discussion This is only an internal function, dont call it direct use only exec
		@discussion Process_show uses a list of input-fields/widgets generated by show.
		@syntax process_show(&$content,$to_process,$cname='')
		@param $content _POST[$cname]
		@param $to_process list of widgets/form-fields to process
		@param $cname basename of our returnt content (same as in call to show)
		@result the adjusted content (by using the var-param &$content)
		*/

		function process_show(&$content,$to_process,$cname='')
		{
			if (!isset($content) || !is_array($content) || !is_array($to_process))
			{
				return;
			}
			if (is_int($this->debug) && $this->debug >= 1 || $this->debug == $this->name && $this->name)
			{
				echo "<p>process_show($this->name) start: content ="; _debug_array($content);
			}
			$content_in = $cname ? array($cname => $content) : $content;
			$content = array();
			reset($to_process);
			while (list($form_name,$type) = each($to_process))
			{
				if (is_array($type))
				{
					$attr = $type;
					$type = $attr['type'];
				}
				else
				{
					$attr = array();
				}
				$value = $this->get_array($content_in,$form_name);

				if (isset($attr['blur']) && $attr['blur'] == stripslashes($value))
				{
					$value = '';	// blur-values is equal to emtpy
				}
				//echo "<p>process_show($this->name) $type: $form_name = '$value'</p>\n";
				$sub = '';
				$tmp = explode('-', $type);
				$type = $tmp[0];
				if ( count($tmp) == 2 )
				{
					$sub = $tmp[1];
				}
				unset($tmp);
				switch ($type)
				{
					case 'ext':
						$this->extensionPostProcess($sub,$form_name,$this->get_array($content,$form_name),$value);
						break;
					case 'text':
					case 'textarea':
						if (isset($value))
						{
							$value = stripslashes($value);
						}
						$this->set_array($content,$form_name,$value);
						break;
					case 'button':
						if ($value)
						{
							$this->set_array($content,$form_name,$value);
						}
						break;
					case 'select':
						$this->set_array($content,$form_name,is_array($value) ? implode(',',$value) : $value);
						break;
					case 'checkbox':
						if (!isset($value))	// checkbox was not checked
						{
							$value = 0;			// need to be reported too
						}
						if (!empty($attr['values']))
						{
							list($true_val,$false_val) = explode(',',$attr['values']);
							$value = $value ? $true_val : $false_val;
						}
						$this->set_array($content,$form_name,$value);
						break;
					case 'file':
						$parts = explode('[',str_replace(']','',$form_name));
						$name = array_shift($parts);
						$index  = count($parts) ? '['.implode('][',$parts).']' : '';
						$value = array();
						$parts = array('tmp_name','type','size','name');
						while (list(,$part) = each($parts))
						{
							$value[$part] = $this->get_array($GLOBALS['HTTP_POST_FILES'][$name],$part.$index);
						}
						$value['path'] = $this->get_array($content_in,substr($form_name,0,-1).'_path]');
						$value['ip'] = get_var('REMOTE_ADDR',Array('SERVER'));
						if (function_exists('is_uploaded_file') && !is_uploaded_file($value['tmp_name']))
						{
							$value = array();	// to be on the save side
						}
						//_debug_array($value);
						// fall-throught
					default:
						$this->set_array($content,$form_name,$value);
						break;
				}
			}
			if ($cname)
			{
				$content = $content[$cname];
			}
			if (is_int($this->debug) && $this->debug >= 2 || $this->debug == $this->name && $this->name)
			{
				echo "<p>process_show($this->name) end: content ="; _debug_array($content);
			}
		}

		/*!
		@function java_script
		@syntax java_script( $consider_not_tested_as_enabled = True )
		@author ralfbecker
		@abstract is javascript enabled?
		@discussion this should be tested by the api at login
		@result true if javascript is enabled or not yet tested and $consider_not_tested_as_enabled 
		*/
		function java_script($consider_not_tested_as_enabled = True)
		{
			$ret = ( isset($GLOBALS['phpgw_info']['etemplate']['java_script']) && $GLOBALS['phpgw_info']['etemplate']['java_script']) ||
				$consider_not_tested_as_enabled && !isset($GLOBALS['phpgw_info']['etemplate']['java_script']);
			//echo "<p>java_script($consider_not_tested_as_enabled)='$ret', java_script='".$GLOBALS['phpgw_info']['etemplate']['java_script']."', isset(java_script)=".isset($GLOBALS['phpgw_info']['etemplate']['java_script'])."</p>\n";
			
			return $ret;
			return !!$GLOBALS['phpgw_info']['etemplate']['java_script'] ||
				$consider_not_tested_as_enabled &&
				(!isset($GLOBALS['phpgw_info']['etemplate']['java_script']) ||
				$GLOBALS['phpgw_info']['etemplate']['java_script'].'' == '');
		}


		/*!
		@function include_java_script
		@syntax include_java_script(  )
		@author ralfbecker
		@abstract returns the javascript to be included by exec
		@param $what &1 = returns the test, note: has to be included in the body, not the header\
			&2 = returns the common functions, best to be included in the header
		*/
		function include_java_script($what = 3)
		{
			$js = '';
			// this is to test if javascript is enabled
			if ($what & 1 && !isset($GLOBALS['phpgw_info']['etemplate']['java_script']))
			{
				$js = '<script language="javascript">
document.write(\''.str_replace("\n",'',$this->html->input_hidden('java_script','1')).'\');
if (document.getElementById) {
	document.write(\''.str_replace("\n",'',$this->html->input_hidden('dom_enabled','1')).'\');
}
</script>
';
			}

			// here are going all the necesarry functions if javascript is enabled
			if ($what & 2 && $this->java_script(True))
			{
				$js .= '<script type="text/javascript" src="'.
					$GLOBALS['phpgw_info']['server']['webserver_url'].'/etemplate/js/etemplate.js"></script>';
			}
			return $js;
		}
	};
