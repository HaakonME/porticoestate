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
	* @subpackage location
 	* @version $Id: class.fileuploader.inc.php 5083 2010-03-19 14:29:26Z sigurd $
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

	class property_fileuploader
	{

		var $public_functions = array
		(
			'add'  	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app']			= false;
			$GLOBALS['phpgw_info']['flags']['noframework']		= true;
			$GLOBALS['phpgw_info']['flags']['no_reset_fonts']	= true;
		}

		function add()
		{
			$upload_target 	= phpgw::get_var('upload_target');
			$id			 	= phpgw::get_var('id');

			$oArgs = "{menuaction:'$upload_target',"
				."id:'$id',"
				."last_loginid:'". phpgw::get_var('last_loginid')."',"
				."last_domain:'" . phpgw::get_var('last_domain')."',"
				."sessionphpgwsessid:'" . phpgw::get_var('sessionphpgwsessid')."',"
				."domain:'" . phpgw::get_var('domain')."'";

			foreach ($_GET as $varname => $value)
			{
				if(strpos($varname, '_')===0)
				{
					$oArgs .= ',' . substr($varname,1,strlen($varname)-1) . ":'{$value}'";
				}
			}
			$oArgs .= '}';

			$js_code = self::get_js($oArgs);

			$title = lang('fileuploader');
			$lang_cancel = lang('cancel');
			$html = <<<HTML
			<!DOCTYPE html>
			<html>
				<head>
					<title>{$title}</title>
					<link href="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/default.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/core/base.js"></script>
					<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/swfupload.js"></script>
					<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/swfupload.queue.js"></script>
					<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/fileprogress.js"></script>
					<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/handlers.js"></script>
					$js_code
				</head>
				<body>

				<div id="content">
					<h2>{$title}</h2>
					<form id="form1" action="index.php" method="post" enctype="multipart/form-data">

							<div class="fieldset flash" id="fsUploadProgress">
							<span class="legend">Upload Queue</span>
							</div>
						<div id="divStatus">0 Files Uploaded</div>
							<div>
								<span id="spanButtonPlaceHolder"></span>
								<input id="btnCancel" type="button" value="{$lang_cancel}" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
							</div>
					</form>
				</div>
			</body>
		</html>
HTML;
			echo $html;
		}


		static function get_js($oArgs = '')
		{
			$button_text = lang('Select');
			$str_base_url = 'http';
			$str_base_url .= phpgw::get_var('HTTPS', 'bool', 'SERVER') ? 's' : '' ;
			$str_base_url .= '://';
			$str_base_url .= phpgw::get_var('HTTP_HOST', 'string', 'SERVER');

			$str_base_url .= $GLOBALS['phpgw']->link('/', array(), true);
			$image_url = $GLOBALS['phpgw']->common->image('property', 'TestImageNoText_65x29');
			$js_code = <<<JS
<script type="text/javascript">
		var swfu;
		var strBaseURL = '$str_base_url';

		var sUrl = phpGWLink('index.php', $oArgs);

		window.onload = function() {
			var settings = {
				flash_url : "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/swfupload.swf",
				flash9_url : "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/swfupload/swfupload_fp9.swf",
				upload_url: sUrl,
//				post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
				file_size_limit : "100 MB",
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : 100,
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
				button_image_url: "{$image_url}",
				button_width: "65",
				button_height: "29",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: '<span class="theFont">{$button_text}</span>',
				button_text_style: ".theFont { font-size: 16; }",
				button_text_left_padding: 12,
				button_text_top_padding: 3,

				// The event handler functions are defined in handlers.js
				swfupload_preload_handler : preLoad,
				swfupload_load_failed_handler : loadFailed,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete	// Queue plugin event
			};

			swfu = new SWFUpload(settings);
		 };
	</script>
JS;
			return $js_code;
		}


/*
This is an upload script for SWFUpload that attempts to properly handle uploaded files
in a secure way.

Notes:

	SWFUpload doesn't send a MIME-TYPE. In my opinion this is ok since MIME-TYPE is no better than
	 file extension and is probably worse because it can vary from OS to OS and browser to browser (for the same file).
	 The best thing to do is content sniff the file but this can be resource intensive, is difficult, and can still be fooled or inaccurate.
	 Accepting uploads can never be 100% secure.

	You can't guarantee that SWFUpload is really the source of the upload.  A malicious user
	 will probably be uploading from a tool that sends invalid or false metadata about the file.
	 The script should properly handle this.

	The script should not over-write existing files.

	The script should strip away invalid characters from the file name or reject the file.

	The script should not allow files to be saved that could then be executed on the webserver (such as .php files).
	 To keep things simple we will use an extension whitelist for allowed file extensions.  Which files should be allowed
	 depends on your server configuration. The extension white-list is _not_ tied your SWFUpload file_types setting

	For better security uploaded files should be stored outside the webserver's document root.  Downloaded files
	 should be accessed via a download script that proxies from the file system to the webserver.  This prevents
	 users from executing malicious uploaded files.  It also gives the developer control over the outgoing mime-type,
	 access restrictions, etc.  This, however, is outside the scope of this script.

	SWFUpload sends each file as a separate POST rather than several files in a single post. This is a better
	 method in my opinion since it better handles file size limits, e.g., if post_max_size is 100 MB and I post two 60 MB files then
	 the post would fail (2x60MB = 120MB). In SWFupload each 60 MB is posted as separate post and we stay within the limits. This
	 also simplifies the upload script since we only have to handle a single file.

	The script should properly handle situations where the post was too large or the posted file is larger than
	 our defined max.  These values are not tied to your SWFUpload file_size_limit setting.

 */

		function upload($bofiles, $save_path = '')
		{
			$use_vfs = true;
			// Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
			{
				header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
				echo "POST exceeded maximum allowed size.";
				$GLOBALS['phpgw']->common->phpgw_exit();
			}	

			// Settings

			if(!$save_path)
			{
				$save_path = "{$GLOBALS['phpgw_info']['server']['temp_dir']}";
				$use_vfs = false;
			}
			$upload_name = "Filedata";
			$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
			$extension_whitelist = array("jpg", "gif", "png");	// Allowed file extensions
			$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)

			// Other variables	
			$MAX_FILENAME_LENGTH = 260;
			$file_name = "";
			$file_extension = "";
			$uploadErrors = array
				(
					0=>"There is no error, the file uploaded successfully",
					1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
					2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
					3=>"The uploaded file was only partially uploaded",
					4=>"No file was uploaded",
					6=>"Missing a temporary folder"
				);


			// Validate the upload
			if (!isset($_FILES[$upload_name]))
			{
				$this->HandleError("No upload found in \$_FILES for " . $upload_name);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0)
			{
				$this->HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"]))
			{
				$this->HandleError("Upload failed is_uploaded_file test.");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else if (!isset($_FILES[$upload_name]['name']))
			{
				$this->HandleError("File has no name.");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			// Validate the file size (Warning: the largest files supported by this code is 2GB)
			$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
			if (!$file_size || $file_size > $max_file_size_in_bytes)
			{
				$this->HandleError("File exceeds the maximum allowed size");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if ($file_size <= 0)
			{
				$this->HandleError("File size outside allowed lower bound");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			// Validate file name (for our purposes we'll just remove invalid characters)
			$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
			{
				$this->HandleError("Invalid file name");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}


			$to_file	= "{$save_path}/{$file_name}";

			// Validate that we won't over-write an existing file
			if ($bofiles->vfs->file_exists(array(
				'string' => $to_file,
				'relatives' => Array(RELATIVE_NONE)
			)))
			{
				$receipt['error'][]=array('msg'=>lang('This file already exists !'));
				$this->HandleError("File with this name already exists");
				exit(0);
			}

			$bofiles->create_document_dir($save_path);

/*
		// Validate that we won't over-write an existing file
			if (file_exists("{$save_path}/{$file_name}"))
			{
				$this->HandleError("File with this name already exists");
				exit(0);
			}
 */
			// Validate file extension
			$path_info = pathinfo($_FILES[$upload_name]['name']);
			$file_extension = $path_info["extension"];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension)
			{
				if (strcasecmp($file_extension, $extension) == 0)
				{
					$is_valid_extension = true;
					break;
				}
			}
			if (!$is_valid_extension)
			{
				$this->HandleError("Invalid file extension");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			// Validate file contents (extension and mime-type can't be trusted)
			/*
				Validating the file contents is OS and web server configuration dependant.  Also, it may not be reliable.
				See the comments on this page: http://us2.php.net/fileinfo

				Also see http://72.14.253.104/search?q=cache:3YGZfcnKDrYJ:www.scanit.be/uploads/php-file-upload.pdf+php+file+command&hl=en&ct=clnk&cd=8&gl=us&client=firefox-a
				 which describes how a PHP script can be embedded within a GIF image file.

				Therefore, no sample code will be provided here.  Research the issue, decide how much security is
				needed, and implement a solution that meets the need.
			 */


			// Process the file
			/*
				At this point we are ready to process the valid file. This sample code shows how to save the file. Other tasks
				 could be done such as creating an entry in a database or generating a thumbnail.

				Depending on your server OS and needs you may need to set the Security Permissions on the file after it has
				been saved.
			 */

			$bofiles->vfs->override_acl = 1;
			if(!$bofiles->vfs->cp (array (
				'from'	=> $_FILES[$upload_name]["tmp_name"],
				'to'	=> "{$save_path}/{$file_name}",
				'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
			{
				$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
				$this->HandleError("File could not be saved.");
				exit(0);
			}

			$bofiles->vfs->override_acl = 0;

/*
			if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], "{$save_path}/{$file_name}"))
			{
				$this->HandleError("File could not be saved.");
				exit(0);
			}
 */
			//			exit(0);
		}

		/* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
		will have to check for any error messages and react as needed. */
		function HandleError($message)
		{
			echo $message;
		}
	}
