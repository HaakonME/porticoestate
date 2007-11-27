<?php
/**
* eTemplate - basic application development environment
* @copyright Copyright (C) 2002-2006 Free Software Foundation, Inc. http://www.fsf.org/
* @author Ralf Becker <ralf.becker@outdoortraining.de>
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package etemplate
* @version $Id: class.soetemplate.inc.php 17957 2007-02-18 21:01:34Z Caeies $
*/

	/**
	 * Storage Objects: Everything to store and retrive the eTemplates.
	 *
	 * eTemplates are stored in the db in table 'phpgw_etemplate' and gets distributed
	 * through the file 'etemplates.inc.php' in the setup dir of each app. That file gets
	 * automatically imported in the db, whenever you show a eTemplate of the app. For
	 * performace reasons the timestamp of the file is stored in the db, so 'new'
	 * eTemplates need to have a newer file. The distribution-file is generated with the
	 * function dump, usually by pressing a button in the editor.
	 * writeLangFile writes an lang-file with all Labels, incorporating an existing one.
	 * Beside a name eTemplates use the following keys to find the most suitable template
	 * for an user (in order of precedence):
	 *  1) User-/Group-Id (not yet implemented)
	 *  2) preferd languages of the user (templates for all langs have $lang='')
	 *  3) selected template: verdilak, ... (the default is called '' in the db, not default)
	 *  4) a version-number of the form, eg: '0.9.13.001' (filled up with 0 same size)
	 */
	class soetemplate
	{
		var $public_functions = array(
			'init'	=> True,
			'empty_cell' => True,
			'new_cell' => True,
			'read'	=> True,
			'search'	=> True,
			'save'	=> True,
			'delete'	=> True,
			'dump2setup'	=> True,
			'import_dump'	=> True,
			'writeLangFile' => True
		);
		var $debug;		// =1 show some debug-messages, = 'app.name' show messages only for eTemplate 'app.name'
		var $name;		// name of the template, e.g. 'infolog.edit'
		var $template;	// '' = default (not 'default')
		var $lang;		// '' if general template else language short, e.g. 'de'
		var $group;		// 0 = not specific else groupId or if < 0  userId
		var $version;	// like 0.9.13.001
		var $size;		// witdh,height,border of table
		var $style;		// embeded CSS style-sheet
		var $db,$db_name = 'phpgw_etemplate'; // DB name
		var $db_key_cols = array(
			'et_name' => 'name',
			'et_template' => 'template',
			'et_lang' => 'lang',
			'et_group' => 'group',
			'et_version' => 'version'
		);
		var $db_data_cols = array(
			'et_data' => 'data',
			'et_size' => 'size',
			'et_style' => 'style',
			'et_modified' => 'modified'
		);
		var $db_cols;

		/**
		* constructor of the class
		*
		* @param as read
		*/
		function soetemplate($name='',$template='',$lang='',$group=0,$version='',$rows=2,$cols=2)
		{
			$this->db = $GLOBALS['phpgw']->db;
			$this->db_cols = $this->db_key_cols + $this->db_data_cols;

			if (empty($name))
			{
				$this->init($name,$template,$lang,$group,$version,$rows,$cols);
			}
			else
			{
				$this->read($name,$template,$lang,$group,$version,$rows,$cols);
			}
		}

		/*!
		@function num2chrs
		@abstract generates column-names from index: 'A', 'B', ..., 'AA', 'AB', ..., 'ZZ' (not more!)
		@syntax num2chrs($num)
		@param $num index to generate name from 1 => 'A'
		@result the name
		*/
		function num2chrs($num)
		{
			$chrs = '';
			$min = ord('A');
			$max = ord('Z') - $min + 1;
			if ($num >= $max)
			{
				$chrs = chr(($num / $max) + $min - 1);
			}
			$chrs .= chr(($num % $max) + $min);

			return $chrs;
		}

		/*!
		@function empty_cell
		@abstracts constructor for a new / empty cell (nothing fancy so far)
		@syntax empty_cell()
		@result the cell
		*/
		function empty_cell($type='label',$name='')
		{
			return array(
				'type' => $type,
				'name' => $name,
			);
		}

		/**
		* constructs a new cell in a give row or the last row, not existing rows will be created
		*
		* @param int $row row-number starting with 1 (!)
		* @param string $type type of the cell
		* @param string $label label for the cell
		* @param string $name name of the cell (index in the content-array)
		* @param array $attributes other attributes for the cell
		* @return a reference to the new cell, use $new_cell = &$tpl->new_cell(); (!)
		*/
		function &new_cell($row=False,$type='label',$label='',$name='',$attributes=False)
		{
			$row = $row >= 0 ? intval($row) : 0;
			if ($row && !isset($this->data[$row]) || !isset($this->data[1]))	// new row ?
			{
				if (!$row) $row = 1;

				$this->data[$row] = array();
			}
			if (!$row)	// use last row
			{
				$row = count($this->data);
				while (!isset($this->data[$row]))
				{
					--$row;
				}
			}
			$row = &$this->data[$row];
			$col = $this->num2chrs(count($row));
			$cell = &$row[$col];
			$cell = $this->empty_cell($type,$name);
			if ($label !== '')
			{
				$attributes['label'] = $label;
			}
			if (is_array($attributes))
			{
				foreach($attributes as $name => $value)
				{
					$cell[$name] = $value;
				}
			}
			return $cell;
		}

		/**
		* initialises rows & cols from the size of the data-array
		*
		*/
		function set_rows_cols()
		{
			$this->rows = count($this->data) - 1;
			$this->cols = count($this->data[1]); // 1 = first row, not 0
		}

		/**
		* initialises all internal data-structures of the eTemplate and sets the keys
		*
		* @param $name name of the eTemplate or array with the keys or all data
		* @param $template,$lang,$group,$version see class
		* @param $rows,$cols initial size of the template
		*/
		function init($name='',$template='',$lang='',$group=0,$version='',$rows=1,$cols=1)
		{
			reset($this->db_cols);
			while (list($db_col,$col) = each($this->db_cols))
			{
				if(is_array($name))
				{
					if(isset($name[$col]))
					{
						$this->$col = $name[$col];
					}
				}
				else
				{
					if(isset($$col))
					{
						$this->$col = $$col;
					}
				}
			}
			if ($this->template == 'default')
			{
				$this->template = '';
			}
			if ($this->lang == 'default')
			{
				$this->lang = '';
			}
			$this->tpls_in_file = is_array($name) && isset($name['tpls_in_file']) ? $name['tpls_in_file'] : 0;

			if (is_array($name) && isset($name['data']))
			{
				$this->set_rows_cols();
				return;	// data already set
			}
			$this->size = $this->style = '';
			$this->data = array(0 => array());
			$this->rows = $rows < 0 ? 1 : $rows;
			$this->cols = $cols < 0 ? 1 : $cols;
			for ($row = 1; $row <= $rows; ++$row)
			{
				for ($col = 0; $col < $cols; ++$col)
				{
					$this->data[$row][$this->num2chrs($col)] = $this->empty_cell();
				}
			}
		}

		/**
		* Reads an eTemplate from the database
		*
		* @param as discripted with the class, with the following exeptions
		* @param $template as '' loads the prefered template 'default' loads the default one '' in the db
		* @param $lang as '' loads the pref. lang 'default' loads the default one '' in the db
		* @param $group is NOT used / implemented yet
		* @return True if a fitting template is found, else False
		*/
		function read($name,$template='default',$lang='default',$group=0,$version='')
		{
			$this->init($name,$template,$lang,$group,$version);
			if ($this->debug == 1 || $this->debug == $this->name)
			{
				echo "<p>soetemplate::read('$this->name','$this->template','$this->lang',$this->group,'$this->version')</p>\n";
			}
			if (isset($GLOBALS['phpgw_info']['server']['eTemplate-source'])	&& ($GLOBALS['phpgw_info']['server']['eTemplate-source'] == 'files' || 
				$GLOBALS['phpgw_info']['server']['eTemplate-source'] == 'xslt')	&& $this->readfile())
			{
				return True;
			}
			if ($this->name)
			{
				$this->test_import($this->name);	// import updates in setup-dir
			}
			$pref_lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			$pref_templ = $GLOBALS['phpgw_info']['server']['template_set'];

			$sql = "SELECT * FROM $this->db_name WHERE et_name='".$this->db->db_addslashes($this->name)."' AND ";
			if (is_array($name))
			{
				$template = $name['template'];
			}
			if ($template == 'default')
			{
				$sql .= "(et_template='".$this->db->db_addslashes($pref_templ)."' OR et_template='')";
			}
			else
			{
				$sql .= "et_template='".$this->db->db_addslashes($this->template)."'";
			}
			$sql .= ' AND ';
			if (is_array($name))
			{
				$lang = $name['lang'];
			}
			if ($lang == 'default' || $name['lang'] == 'default')
			{
				$sql .= "(et_lang='".$this->db->db_addslashes($pref_lang)."' OR et_lang='')";
			}
			else
			{
				$sql .= "et_lang='".$this->db->db_addslashes($this->lang)."'";
			}
			if ($this->version != '')
			{
				$sql .= "AND et_version='".$this->db->db_addslashes($this->version)."'";
			}
			$sql .= " ORDER BY et_lang DESC,et_template DESC,et_version DESC";

			if ($this->debug == $this->name)
			{
				echo "<p>soetemplate::read: sql='$sql'</p>\n";
			}
			$this->db->query($sql,__LINE__,__FILE__);
			if (!$this->db->next_record())
			{
				$version = $this->version;
				return $this->readfile() && (empty($version) || $version == $this->version);
			}
			$this->db2obj();

			return True;
		}

		/**
		* Reads an eTemplate from the filesystem, the keys are already set by init in read
		*
		* @return True if a template is found, else False
		*/
		function readfile()
		{
			list($app,$name) = split("\.",$this->name,2);
			$template = $this->template == '' ? 'base' : $this->template;

			if ($this->lang)
			{
				$lang = '.' . $this->lang;
			}
			$first_try = $ext = isset($GLOBALS['phpgw_info']['server']['eTemplate-source']) && ($GLOBALS['phpgw_info']['server']['eTemplate-source']) == 'xslt' ? '.xsl' : '.xet';

			while ((!$lang || !@file_exists($file = PHPGW_SERVER_ROOT . "/$app/templates/$template/$name$lang$ext") &&
			                  !@file_exists($file = PHPGW_SERVER_ROOT . "/$app/templates/base/$name$lang$ext") &&
			                  !@file_exists($file = PHPGW_SERVER_ROOT . "/$app/templates/default/$name$lang$ext")) &&
			       !@file_exists($file = PHPGW_SERVER_ROOT . "/$app/templates/$template/$name$ext") &&
			       !@file_exists($file = PHPGW_SERVER_ROOT . "/$app/templates/default/$name$ext"))
			{
				if ($ext == $first_try)
				{
					$ext = $ext == '.xet' ? '.xsl' : '.xet';

					if ($this->debug == 1 || $this->name != '' && $this->debug == $this->name)
					{
						echo "<p>tried '$file' now trying it with extension '$ext' !!!</p>\n";
					}
				}
				else
				{
					break;
				}
			}
			if ($this->name == '' || $app == '' || $name == '' || !@file_exists($file) || !($f = @fopen($file,'r')))
			{
				if ($this->debug == 1 || $this->name != '' && $this->debug == $this->name)
				{
					echo "<p>Can't open template '$this->name' / '$file' !!!</p>\n";
				}
				return False;
			}
			$xml = fread ($f, filesize ($file));
			fclose($f);

			if ($ext == '.xsl')
			{
				$cell = $this->empty_cell();
				$cell['type'] = 'xslt';
				$cell['size'] = $this->name;
				//$cell['xslt'] = &$xml;	xslttemplate class cant use it direct at the moment
				$cell['name'] = '';
				$this->data = array(0 => array(),1 => array('A' => &$cell));
				$this->rows = $this->cols = 1;
			}
			else
			{
				if (!is_object($this->xul_io))
				{
					$this->xul_io = CreateObject('etemplate.xul_io');
				}
				$loaded = $this->xul_io->import(&$this,$xml);

				if (!is_array($loaded))
				{
					return False;
				}
				$this->name = $app . '.' . $name;	// if template was copied or app was renamed

				$this->tpls_in_file = count($loaded);
			}
			return True;
		}

		/**
		 * Lists the eTemplates matching the given criteria
		 *
		 * @param as discripted with the class, with the following exeptions
		 * @param $template as '' loads the prefered template 'default' loads the default one '' in the db
		 * @param $lang as '' loads the pref. lang 'default' loads the default one '' in the db
		 * @param $group is NOT used / implemented yet
		 * @return array of arrays with the template-params
		 */
		function search($name,$template='default',$lang='default',$group=0,$version='')
		{
			if ($this->name)
			{
				$this->test_import($this->name);	// import updates in setup-dir
			}
			$pref_lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			$pref_templ = $GLOBALS['phpgw_info']['server']['template_set'];

			if (is_array($name))
			{
				$template = $name['template'];
				$lang = $name['lang'];
				$group = $name['group'];
				$version = $name['version'];
				$name = $name['name'];
			}
			$sql = "SELECT et_name,et_template,et_lang,et_group,et_version FROM $this->db_name WHERE et_name LIKE '".$this->db->db_addslashes($name)."%'";

			if ($template != '' && $template != 'default')
			{
				$sql .= " AND et_template LIKE '".$this->db->db_addslashes($template)."%'";
			}
			if ($lang != '' && $lang != 'default')
			{
				$sql .= " AND et_lang LIKE '".$this->db->db_addslashes($lang)."%'";
			}
			if ($this->version != '')
			{
				$sql .= " AND et_version LIKE '".$this->db->db_addslashes($version)."%'";
			}
			$sql .= " ORDER BY et_name DESC,et_lang DESC,et_template DESC,et_version DESC";

			$tpl = new soetemplate;
			$tpl->db->query($sql,__LINE__,__FILE__);
			$result = array();
			while ($tpl->db->next_record())
			{
				if ($tpl->db->f('et_lang') != '##')	// exclude or import-time-stamps
				{
					$tpl->db2obj();
					
					$result[] = $tpl->as_array();
				}
			}
			if ($this->debug)
			{
				echo "<p>soetemplate::search('$name') sql='$sql'</p>\n<pre>\n";
				print_r($result);
				echo "</pre>\n";
			}
			return $result;
		}

		/**
		* copies all cols into the obj and unserializes the data-array
		*
		*/
		function db2obj()
		{
			for (reset($this->db_cols); list($db_col,$name) = each($this->db_cols); )
			{
				$this->$name = $this->db->f($db_col);
			}
			$this->data = unserialize(stripslashes($this->data));
			if (!is_array($this->data)) $this->data = array();

			if ($this->name[0] != '.')
			{
				reset($this->data); each($this->data);
				while (list($row,$cols) = each($this->data))
				{
					while (list($col,$cell) = each($cols))
					{
						if (is_array($cell['type']))
						{
							$this->data[$row][$col]['type'] = $cell['type'][0];
							//echo "corrected in $this->name cell $col$row attribute type<br>\n";
						}
						if (isset($cell['align']) && is_array($cell['align']))
						{
							$this->data[$row][$col]['align'] = $cell['align'][0];
							//echo "corrected in $this->name cell $col$row attribute align<br>\n";
						}
					}
				}
			}
			$this->set_rows_cols();
		}

		/**
		 * to save space in the db all empty values in the array got unset
		 *
		 * The never-'' type field ensures a cell does not disapear completely.
		 * Calls it self recursivly for arrays / the rows
		 * @param $arr the array to compress
		 * @return the compressed array
		 */
		function compress_array($arr)
		{
			if (!is_array($arr))
			{
				return $arr;
			}
			while (list($key,$val) = each($arr))
			{
				if (is_array($val))
				{
					$arr[$key] = $this->compress_array($val);
				}
				elseif ($val == '')
				{
					unset($arr[$key]);
				}
			}
			return $arr;
		}

		/**
		* returns obj-data as array
		*
		* @param $data_too 0 = no data array, 1 = data array too, 2 = serialize data array
		* @return the array
		*/
		function as_array($data_too=0)
		{
			$arr = array();
			reset($this->db_cols);
			while (list($db_col,$col) = each($this->db_cols))
			{
				if (isset($this->$col) && ($col != 'data' || $data_too) )
				{
					$arr[$col] = isset($this->$col) ? $this->$col : '';
				}
			}
			if ($data_too == 2)
			{
				$arr['data'] = serialize($arr['data']);
			}
			if ($this->tpls_in_file) {
				$arr['tpls_in_file'] = $this->tpls_in_file;
			}
			return $arr;
		}

		/**
		* saves eTemplate-object to db, can be used as saveAs by giving keys as params
		*
		* keys see class
		* @return the number of affected rows, 1 should be ok, 0 somethings wrong
		*/
		function save($name='',$template='.',$lang='.',$group='',$version='.')
		{
			if (is_array($name))
			{
				$template = $name['template'];
				$lang     = $name['lang'];
				$group    = $name['group'];
				$version  = $name['version'];
				$name     = $name['name'];
			}
			if ($name != '')
			{
				$this->name = $name;
			}
			if ($lang != '.')
			{
				$this->lang = $lang;
			}
			if ($template != '.')
			{
				$this->template = $template;
			}
			if ($group != '')
			{
				$this->group = $group;
			}
			if ($version != '.')
			{
				$this->version = $version;
			}
			if ($this->name == '')	// name need to be set !!!
			{
				return False;
			}
			if ($this->debug > 0 || $this->debug == $this->name)
			{
				echo "<p>soetemplate::save('$this->name','$this->template','$this->lang',$this->group,'$this->version')</p>\n";
			}
			$this->delete();	// so we have always a new insert

			if ($this->name[0] != '.')		// correct up old messed up templates
			{
				reset($this->data); each($this->data);
				while (list($row,$cols) = each($this->data))
				{
					while (list($col,$cell) = each($cols))
					{
						if (is_array($cell['type'])) {
							$this->data[$row][$col]['type'] = $cell['type'][0];
							//echo "corrected in $this->name cell $col$row attribute type<br>\n";
						}
						if (is_array($cell['align'])) {
							$this->data[$row][$col]['align'] = $cell['align'][0];
							//echo "corrected in $this->name cell $col$row attribute align<br>\n";
						}
					}
				}
			}
			if (!$this->modified)
			{
				$this->modified = time();
			}
			$data = $this->as_array(1);
			$data['data'] = serialize($this->compress_array($data['data']));

			$sql = "INSERT INTO $this->db_name (";
			foreach ($this->db_cols as $db_col => $col)
			{
				$sql .= $db_col . ',';
				$vals .= $db_col == 'et_group' ? intval($data[$col]).',' : "'" . addslashes($data[$col]) . "',";
			}
			$sql[strlen($sql)-1] = ')';
			$sql .= " VALUES ($vals";
			$sql[strlen($sql)-1] = ')';

			$this->db->query($sql,__LINE__,__FILE__);

			return $this->db->affected_rows();
		}

		/**
		* Deletes the eTemplate from the db, object itself is unchanged
		*
		* @return the number of affected rows, 1 should be ok, 0 somethings wrong
		*/
		function delete()
		{
			foreach ($this->db_key_cols as $db_col => $col)
			{
				$vals .= ($vals ? ' AND ' : '') . $db_col . '=' . ($db_col == 'et_group' ? intval($this->$col) : "'".$this->$col."'");
			}
			$this->db->query("DELETE FROM $this->db_name WHERE $vals",__LINE__,__FILE__);

			return $this->db->affected_rows();
		}

		/**
		 * dumps all eTemplates to <app>/setup/etemplates.inc.php for distribution
		*
		 * @param $app app- or template-name
		 * @return the number of templates dumped as message
		 */
		function dump2setup($app)
		{
			list($app) = explode('.',$app);

			$this->db->query("SELECT * FROM $this->db_name WHERE et_name LIKE '$app%'");

			$dir = PHPGW_SERVER_ROOT . "/$app/setup";
			if (!is_writeable($dir))
			{
				return lang("Error: webserver is not allowed to write into '%1' !!!",$dir);
			}
			$file = "$dir/etemplates.inc.php";
			if (file_exists($file))
			{
				$old_file = "$dir/etemplates.old.inc.php";
				if (file_exists($old_file))
				{
					unlink($old_file);
				}
				rename($file,$old_file);
			}

			if (!($f = fopen($file,'w')))
			{
				return 0;
			}
			fwrite($f,"<?php\n// eTemplates for Application '$app', generated by etemplate.dump() ".date('Y-m-d H:i')."\n\n".
				'/* $'.'Id$ */'."\n\n");

			for ($n = 0; $this->db->next_record(); ++$n)
			{
				$str = '$templ_data[] = array(';
				for (reset($this->db_cols); list($db_col,$name) = each($this->db_cols); )
				{
					$str .= "'$name' => '".addslashes($this->db->f($db_col))."',";
				}
				$str .= ");\n\n";
				fwrite($f,$str);
			}
			fclose($f);

			return lang("%1 eTemplates for Application '%2' dumped to '%3'",$n,$app,$file);
		}

		function getToTranslateCell($cell,&$to_trans)
		{
			$strings = explode('|',$cell['help']);

			if ($cell['type'] != 'image')
			{
				$strings += explode('|',$cell['label']);
			}
			list($extra_row) = explode(',',$cell['size']);
			if (substr($cell['type'],0,6) == 'select' && !empty($extra_row) && !intval($extra_row))
			{
				$strings[] = $extra_row;
			}
			if (!empty($cell['blur']))
			{
				$strings[] = $cell['blur'];
			}
			foreach($strings as $str)
			{
				if (strlen($str) > 1 && $str[0] != '@')
				{
					$to_trans[trim(strtolower($str))] = $str;
				}
			}
		}

		/**
		* extracts all texts: labels and helptexts from an eTemplate-object
		*
		* some extensions use a '|' to squezze multiple texts in a label or help field
		* @return array with messages as key AND value
		*/
		function getToTranslate()
		{
			$to_trans = array();

			reset($this->data); each($this->data); // skip width
			while (list($row,$cols) = each($this->data))
			{
				foreach($cols as $col => $cell)
				{
					$this->getToTranslateCell($cell,$to_trans);

					if ($cell['type'] == 'vbox' || $cell['type'] == 'hbox')
					{
						for ($n = 1; $n <= $cell['size']; ++$n)
						{
							$this->getToTranslateCell($cell[$n],$to_trans);
						}
					}
				}
			}
			return $to_trans;
		}

		/**
		* Read all eTemplates of an app an extracts the texts to an array
		*
		* @param $app name of the app
		* @return the array with texts
		*/
		function getToTranslateApp($app)
		{
			$to_trans = array();

			$tpls = $this->search($app);

			$tpl = new soetemplate;	// to not alter our own data
			
			while (list(,$keys) = each($tpls))
			{
				if (($keys['name'] != $last['name'] ||		// write only newest version
					 $keys['template'] != $last['template']) &&
					 !strstr($keys['name'],'test'))
				{
					$tpl->read($keys);
					$to_trans += $tpl->getToTranslate();
					$last = $keys;
				}
			}
			return $to_trans;
		}

		/**
		* Write new lang-file using the existing one and all text from the eTemplates
		*
		* @param $app app- or template-name
		* @param $lang language the messages in the template are, defaults to 'en'
		* @param $additional extra texts to translate, if you pass here an array with all messages and
		* @param select-options they get writen too (form is <unique key> => <message>)
		* @return message with number of messages written (total and new)
		*/
		function writeLangFile($app,$lang='en',$additional='')
		{
			if (!$additional)
			{
				$addtional = array();
			}
			list($app) = explode('.',$app);

			if (!file_exists(PHPGW_SERVER_ROOT.'/developer_tools/inc/class.solangfile.inc.php'))
			{
				$solangfile = CreateObject('etemplate.solangfile');
			}
			else
			{
				$solangfile = CreateObject('developer_tools.solangfile');
			}
			$langarr = $solangfile->load_app($app,$lang);
			if (!is_array($langarr))
			{
				$langarr = array();
			}
			$commonarr = $solangfile->load_app('phpgwapi',$lang) + $solangfile->load_app('etemplate',$lang);

			$to_trans = $this->getToTranslateApp($app);
			if (is_array($additional))
			{
				//echo "writeLangFile: additional ="; _debug_array($additional);
				foreach($additional as $msg)
				{
					$to_trans[trim(strtolower($msg))] = $msg;
				}
			}
			unset($to_trans['']);

			for ($new = $n = 0; list($message_id,$content) = each($to_trans); ++$n)
			{
				if (!isset($langarr[$message_id]) && !isset($commonarr[$message_id]))
				{
					if (@isset($langarr[$content]))	// caused by not lowercased-message_id's
					{
						unset($langarr[$content]);
					}
					$langarr[$message_id] = array(
						'message_id' => $message_id,
						'app_name'   => $app,
						'content'    => $content
					);
					++$new;
				}
			}
			ksort($langarr);

			$dir = PHPGW_SERVER_ROOT . "/$app/setup";
			if (!is_writeable($dir))
			{
				return lang("Error: webserver is not allowed to write into '%1' !!!",$dir);
			}
			$file = "$dir/phpgw_$lang.lang";
			if (file_exists($file))
			{
				$old_file = "$dir/phpgw_$lang.old.lang";
				if (file_exists($old_file))
				{
					unlink($old_file);
				}
				rename($file,$old_file);
			}
			$solangfile->write_file($app,$langarr,$lang);
			$solangfile->loaddb($app,$lang);

			return lang("%1 (%2 new) Messages writen for Application '%3' and Languages '%4'",$n,$new,$app,$lang);
		}

		/**
		* Imports the dump-file /$app/setup/etempplates.inc.php unconditional (!)
		*
		* @param $app app name
		* @return message with number of templates imported
		*/
		function import_dump($app)
		{
			include($path = PHPGW_SERVER_ROOT."/$app/setup/etemplates.inc.php");
			$templ = new etemplate($app);

			for ($n = 0; isset($templ_data[$n]); ++$n)
			{
				for (reset($this->db_cols); list($db_col,$name) = each($this->db_cols); )
				{
					$templ->$name = $templ_data[$n][$name];
				}
				$templ->data = unserialize(stripslashes($templ->data));
				if (!$templ->modified)
				{
					$templ->modified = filemtime($path);
				}
				$templ->save();
			}
			return lang("%1 new eTemplates imported for Application '%2'",$n,$app);
		}

		/**
		* test if new template-import necessary for app and does the import
		*
		* Get called on every read of a eTemplate, caches the result in phpgw_info.
		* The timestamp of the last import for app gets written into the db.
		* @param $app app- or template-name
		*/
		function test_import($app)	// should be done from the setup-App
		{
			list($app) = explode('.',$app);

			if ((!isset($app) || !$app) || (isset($GLOBALS['phpgw_info']['etemplate']['import_tested'][$app]) && $GLOBALS['phpgw_info']['etemplate']['import_tested'][$app]))
			{
				return '';	// ensure test is done only once per call and app
			}
			$GLOBALS['phpgw_info']['etemplate']['import_tested'][$app] = True;	// need to be done before new ...

			$path = PHPGW_SERVER_ROOT."/$app/setup/etemplates.inc.php";
			$ret = false;
			if ($time = @filemtime($path))
			{
				$templ = new soetemplate(".$app",'','##');
				if ($templ->lang != '##' || $templ->modified < $time) // need to import
				{
					$ret = $this->import_dump($app);
					$templ->modified = $time;
					$templ->save(".$app",'','##');
				}
			}
			return $ret;
		}
	};
