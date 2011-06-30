<?php

	class property_uiimport
	{
		var $public_functions = array
		(
			'index'		=> true
		);

		const DELIMITER = ";";
		const ENCLOSING = "'";
		
		// List of messages, warnings and errors to be displayed to the user after the import
		protected $messages = array();
		protected $warnings = array();
		protected $errors = array();
		
		// File system path to import folder on server
		protected $file;
		protected $district;
		protected $csvdata;
		protected $account;
		protected $conv_type;
		protected $import_conversion;
		
		// Label on the import button. Changes as we step through the import process.
		protected $import_button_label;
		
		protected $defalt_values;
		
		public function __construct()
		{
			if ( !$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				&& !$GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'property'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				execMethod('property.bocommon.no_access');
			}

			set_time_limit(10000);
//			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
		}
		

		/**
		 * Public method. 
		 * 
		 * @return unknown_type
		 */
		public function index()
		{
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";

			// If the parameter 'importsubmit' exist (submit button in import form), set path
			if (phpgw::get_var("importsubmit")) 
			{

				if($GLOBALS['phpgw']->session->is_repost())
				{
					echo('Hmm... looks like a repost!');
					$action =  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiimport.index'));
					echo "<br><a href= '$action'>Start over</a>" ;
					
					$GLOBALS['phpgw']->common->phpgw_exit();
				}


				$this->conv_type 	= phpgw::get_var('conv_type');

				$start_time = time(); // Start time of import
				$start = date("G:i:s",$start_time);
				echo "<h3>Import started at: {$start}</h3>";
				echo "<ul>";

				if($this->conv_type)
				{
					if ( preg_match('/\.\./', $this->conv_type) )
					{
						break;
					}

					$file = PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}/{$this->conv_type}";
	
					if ( is_file($file) )
					{
						require_once $file;
						$this->import_conversion = new import_conversion;
						$this->import_conversion->debug	= phpgw::get_var('debug', 'bool');
					}
				}


				// Get the path for user input or use a default path

				$files = array();
				if(isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'])
				{
					$files[] = array
					(
						'name'	=> $_FILES['file']['tmp_name'],
						'type'	=> $_FILES['file']['type']
					);
					
				}
				else
				{
					$path = phpgw::get_var('path', 'string');
					$files = $this->get_files($path);
				}

				foreach ($files as $file)
				{
					$valid_type = false;
					switch ($file['type'])
					{
						case 'application/vnd.ms-excel':
							$this->csvdata = $this->getexceldata($this->file);
							$valid_type = true;
							break;
						case 'text/csv':
							$this->csvdata = $this->getcsvdata($this->file);
							$valid_type = true;
							break;
					}
					
					if($valid_type)
					{
						$result = $this->import();
						$this->csvdata = array();
						echo '<li class="info">Import: finished step ' .$result. '</li>';
					}
				}


				echo "</ul>";
				$end_time = time();
				$difference = ($end_time - $start_time) / 60;
				$end = date("G:i:s",$end_time);
				echo "<h3>Import ended at: {$end}. Import lasted {$difference} minutes.";
				
				$this->messages = array_merge($this->messages,$this->import_conversion->messages);
				$this->warnings = array_merge($this->warnings,$this->import_conversion->warnings);
				$this->errors = array_merge($this->errors,$this->import_conversion->errors);

				if ($this->errors)
				{ 
					echo "<ul>";
					foreach ($this->errors as $error)
					{
						echo '<li class="error">Error: ' . $error . '</li>';
					}
		
					echo "</ul>";
				}
		
				if ($this->warnings)
				{ 
					echo "<ul>";
					foreach ($this->warnings as $warning)
					{
						echo '<li class="warning">Warning: ' . $warning . '</li>';
					}
					echo "</ul>";
				}
		
				if ($this->messages)
				{
					echo "<ul>";
		
					foreach ($this->messages as $message)
					{
						echo '<li class="info">' . $message . '</li>';
					}
					echo "</ul>";
				}
			}
			else
			{

				$conv_list = $this->get_import_conv($this->conv_type);
				
				$conv_option = '<option value="">' . lang('none selected') . '</option>' . "\n";
				foreach ( $conv_list as $conv)
				{
					$selected = '';
					if ( $conv['selected'])
					{
						$selected = 'selected =  "selected"';
					}

					$conv_option .=  <<<HTML
					<option value='{$conv['id']}'{$selected}>{$conv['name']}</option>
HTML;
				}			
				$action =  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiimport.index'));
				$html = <<<HTML
				<h1><img src="rental/templates/base/images/32x32/actions/document-save.png" /> Importer ( MsExcel / CSV )</h1>
				<div id="messageHolder"></div>
				<form action="{$action}" method="post" enctype="multipart/form-data">
					<fieldset>
						<label for="file">Choose file:</label>
						<input type="file" name="file" id="file" title = 'Single file'/>
						<label for="path">Local path:</label>
						<input type="text" name="path" id="path" title = 'Alle filer i katalogen'/>
						<label for="conv_type">Choose conversion:</label>
						<select name="conv_type" id="conv_type">
						{$conv_option}
						</select>
						<label for="debug">Debug:</label>
						<input type="checkbox" name="debug" id="debug" value ='1' />
						<input type="submit" name="importsubmit" value="{$this->import_button_label}"  />
		 			</fieldset>
				</form>
HTML;
				echo $html;
			}
		}
		
		/**
		 * Import Facilit data to Portico Estate's rental module
		 * The function assumes CSV files have been uploaded to a location on the server reachable by the
		 * web server user.  The CSV files must correspond to the table names from Facilit, as exported
		 * from Access. Field should be enclosed in single quotes and separated by comma.  The CSV files
		 * must contain the column headers on the first line.
		 * 
		 * @return unknown_type
		 */
		public function import()
		{
			$steps = 1;
			
			/* Import logic:
			 * 
			 * 1. Do step logic if the session variable is not set
			 * 2. Set step result on session
			 * 3. Set label for import button
			 * 4. Log messages for this step
			 *  
			 */
			
			$this->messages = array();
			$this->warnings = array();
			$this->errors = array();

			$this->import_data();
			$this->log_messages(1);
			return '1';
		}
		
		protected function import_data()
		{
			$start_time = time();
			
			$datalines = $this->csvdata;
			
			$this->messages[] = "Read 'import_all.csv' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'importfile.csv' contained " . count($datalines) . " lines";
			

			$ok = true;
			$_ok = false;
			$this->db->transaction_begin();

			//Do your magic...
			foreach ($datalines as $data)
			{
				if(!$_ok = $this->import_conversion->add($data))
				{
					$ok = false;
				}
			}
			
			if($ok)
			{
				$this->messages[] = "Imported data. (" . (time() - $start_time) . " seconds)";
				$this->db->transaction_commit();
				return true;
			}
			else
			{
				$this->errors[] = "Import of data failed. (" . (time() - $start_time) . " seconds)";
				$this->db->transaction_abort();
				return false;
			}
		}


		protected function getcsvdata($path, $skipfirstline = true)
		{
			// Open the csv file
			$handle = fopen($path, "r");
			
			if ($skipfirstline)
			{
				// Read the first line to get the headers out of the way
				$this->getcsv($handle);
			}
			
			$result = array();
			
			while(($data = $this->getcsv($handle)) !== false)
			{
				$result[] = $data;
			}
			
			fclose($handle);
			
			return $result;
		}

		protected function getexceldata($path, $skipfirstline = true)
		{
			$data = CreateObject('phpgwapi.excelreader');
			$data->setOutputEncoding('CP1251');
			$data->read($path);
			$result = array();

			$start = $skipfirstline ? 2 : 1; // Read the first line to get the headers out of the way

			$rows = $data->sheets[0]['numRows']+1;

			for ($i=$start; $i<$rows; $i++ ) //First data entry on row 2
			{
				foreach($data->sheets[0]['cells'][$i] as &$value)
				{
					$value = utf8_encode(trim($value));
				}
				$result[] = array_values($data->sheets[0]['cells'][$i]);
			}

			return $result;
		}

		
		/**
		 * Read the next line from the given file handle and parse it to CSV according to the rules set up
		 * in the class constants DELIMITER and ENCLOSING.  Returns FALSE like getcsv on EOF.
		 * 
		 * @param file-handle $handle
		 * @return array of values from the parsed csv line
		 */
		protected function getcsv($handle)
		{
			return fgetcsv($handle, 1000, self::DELIMITER, self::ENCLOSING);
		}
		

		private function log_messages($step)
        {
        	sort($this->errors);
        	sort($this->warnings);
        	sort($this->messages);
        	
            $msgs = array_merge(
            	array('----------------Errors--------------------'),
            	$this->errors,
            	array('---------------Warnings-------------------'),
            	$this->warnings,
            	array('---------------Messages-------------------'),
            	$this->messages
            );

            $path = $GLOBALS['phpgw_info']['server']['temp_dir'];
            if(is_dir($path.'/logs') || mkdir($path.'/logs'))
            {
                file_put_contents("$path/logs/$step.log", implode(PHP_EOL, $msgs));
            }
        }

		protected function get_import_conv($selected='')
		{
			$dir_handle = @opendir(PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}");
			$i=0; $myfilearray = array();
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}/{$file}") )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('/_/',' ',$myfilearray[$i]);

				$conv_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $myfilearray[$i]==$selected ? 1 : 0
				);
			}

			return $conv_list;
		}

		protected function get_files($dirname)
		{
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$mime_magic = createObject('phpgwapi.mime_magic');
			
			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable())
//						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'xls' ) != 0 )
//						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'csv' ) != 0 ))
 					{
						continue;
					}

					$file_name = $file->__toString();
					$file_list[] = array
					(
						'name'	=> (string) "{$dirname}/{$file_name}",
						'type'	=> $mime_magic->filename2mime($file_name)
					);
				}
			}

			return $file_list;
		}


	}
