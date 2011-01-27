<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */


	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'property'
	);

	include_once('../header.inc.php');


	if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
	{
		$organize = new organize_pdf_bkb();
		$organize->pre_run();
	}
	else
	{
		echo 'go away';
	}

	class organize_pdf_bkb
	{
		/* In Admin->Property->Async servises:
		*  Name: property.custom_functions.index
		*  Data: function=organize_pdf,dir=C:/path/to/pdfs
		*/

		protected $dir = '/opt/BK_Branntegninger_pdf';
//		protected $suffix = 'dwg';
		protected $suffix = 'pdf';
		protected $bypass = true; // bypass location check (only for debugging)
		protected $cat_id = 17;
		protected $function_name = 'organize_pdf_BBE';

		function __construct()
		{
			$this->bocommon		= CreateObject('property.bocommon');
			$this->vfs 			= CreateObject('phpgwapi.vfs');
			$this->rootdir 		= $this->vfs->basedir;
			$this->fakebase 	= $this->vfs->fakebase = '/property';
			$this->db           = & $GLOBALS['phpgw']->db;
		}

		function pre_run()
		{
			$confirm	= get_var('confirm',array('POST'));
			$execute	= get_var('execute',array('GET'));
			if(get_var('dir',array('GET')))
			{
				$this->dir = urldecode (get_var('dir',array('GET')));
			}
			if(get_var('suffix',array('GET')))
			{
				$this->suffix = get_var('suffix',array('GET'));
			}

			if(!$execute)
			{
				$dry_run=True;
			}

			if ($confirm)
			{
				$this->execute($dry_run,$cron);
			}
			else
			{
				$this->confirm($execute=False);
			}
		}

		function confirm($execute='',$done='')
		{

			$link_data = array
			(
				'execute'	=> $execute,
				'dir'		=> $this->dir,
				'suffix'	=> $this->suffix,
			);

			$lang_confirm_msg = '';
			if(!$done)
			{
				if(!$execute)
				{
					$lang_confirm_msg 	= 'Gå videre for å se hva som blir lagt til';
				}
				else
				{
					$lang_confirm_msg 	= 'Vil du virkelig utføre denne operasjonen';
				}
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('organize_pdf_BKB.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('organize_pdf_BKB.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Organisere tegninger i register og på disk',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= 'Organisere tegninger';
			$function_msg	= 'Organisere tegninger i register og på disk';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($dry_run='',$cron='')
		{
			set_time_limit(1000);

			if(!is_dir("{$this->dir}/archive"))
			{
				if(!mkdir("{$this->dir}/archive"))
				{
					$this->receipt['error'][]=array('msg'=>lang('failed to create directory') . " :{$this->dir}/archive");
					$this->confirm('',true);
					return;
				}
			}

			$file_list = $this->get_files();

			if($dry_run)
			{
				$this->confirm($execute=True);
				_debug_array($file_list);
			}
			else
			{
				if ($file_list && isset($file_list['valid']))
				{
					foreach($file_list['valid'] as $file_entry)
					{
						$this->create_dir($file_entry['location_code']);
						$this->copy_file($file_entry);
					}
				}
				if(!$cron)
				{
					$this->confirm($execute=false,$done=true);
				}

				$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

				$insert_values= array
				(
					$cron,
					date($this->bocommon->datetimeformat),
					$this->function_name,
					implode(',',(array_keys($msgbox_data)))
				);

				$insert_values	= $this->db->validate_insert($insert_values);

				$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
						. "VALUES ($insert_values)";
				$this->db->query($sql,__LINE__,__FILE__);
			}
		}

		function get_files()
		{
			$myfilearray = array();

			if(!is_dir($this->dir))
			{
				return $myfilearray;
			}
			$dir = new DirectoryIterator($this->dir); 

			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), $this->suffix ) != 0 )
 					{
						continue;
					}
					$file_name = $file->getFilename();

					if($this->check_old_building($file_name))
					{
						$myfilearray['valid'][] = array
						(
							'last_modified'=> $file->getMTime(),
							'file_name'=> $file_name,
							'file_path'=> (string) "{$this->dir}/{$file_name}",
							'tips_bygg'=> substr($file_name, 0, 6),
							'loc1'=>  $this->get_loc(substr($file_name, 0, 6)),
							'loc2'=>  $this->loc2,
							'location_code' => $this->location_code,
							'cat_id'	=> $this->cat_id,
							'new_file_name'	=>str_replace(' Model (1)', '', $this->loc1 . $this->loc2 . (substr($file_name, 6, (strlen($file_name)-6)))),
						);
					}
					else
					{
						$myfilearray['rejected'][] = array
						(
							'file_name' => $file_name,
							'strlen'	=> strlen($file_name),
							'building'	=> $this->check_old_building($file_name)
						);
					}
				}
			}

			return $myfilearray;
		}

		function get_loc($file)
		{
			$tips_bygg = substr($file, 0, 6);
			
			$sql = "SELECT loc1,loc2  FROM fm_location2 WHERE tips_bygg= '$tips_bygg'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$this->loc1 = $this->db->f('loc1');
			$this->loc2 = $this->db->f('loc2');
			$this->location_code = $this->db->f('loc1') . '-' .$this->db->f('loc2');
			return $this->db->f('loc1');
		}


		function check_old_building($file)
		{
			$tips_bygg = substr($file, 0, 6);
			
			$sql = "SELECT count(*) as cnt FROM fm_location2 WHERE tips_bygg= '$tips_bygg'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('cnt'))
			{
				return True;
			}
		}
		function check_building($file)
		{
			$loc1 = substr($file, 0, 4);
			$loc2 = substr($file, 4, 2);
			$sql = "SELECT count(*) as cnt FROM fm_location2 WHERE loc1= '$loc1' AND loc2= '$loc2'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return !!$this->db->f('cnt');
		}

		function create_dir($location_code='')
		{
			$this->vfs->override_acl = 1;

			$dir = "{$this->fakebase}/document/{$location_code}";

			if(!$this->vfs->file_exists(array(
					'string' => $dir,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				if(!$this->vfs->mkdir (array(
				     'string' => $dir,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$this->receipt['error'][]=array('msg'=>lang('failed to create directory') . " :{$dir}");
				}
				else
				{
					$this->receipt['message'][]=array('msg'=>lang('directory created') . " :{$dir}");
				}
			}


			$dir = "{$this->fakebase}/document/{$location_code}/{$this->cat_id}";
			if(!$this->vfs->file_exists(array(
					'string' => $dir,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				if(!$this->vfs->mkdir (array(
				     'string' => $dir,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$this->receipt['error'][]=array('msg'=>lang('failed to create directory') . " :{$dir}");
				}
				else
				{
					$this->receipt['message'][]=array('msg'=>lang('directory created') . " :{$dir}");
				}
			}


			$this->vfs->override_acl = 0;
		}

		function copy_file($values)
		{
			$to_new_file = "{$this->fakebase}/document/{$values['location_code']}/{$this->cat_id}/{$values['new_file_name']}";

			$from_file = $values['file_path'];

			$this->db->transaction_begin();

			$this->vfs->override_acl = 1;


			if($this->vfs->file_exists(array(
					'string' => $to_new_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->receipt['error'][]=array('msg'=>lang('File %1 already exists!',$values['new_file_name']));
			}
			else
			{
				if(!$this->vfs->cp (array (
					'from'	=> $from_file,
					'to'	=> $to_new_file,
					'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
				{
					$this->receipt['error'][]=array('msg'=>lang('Failed to copy file !') . $values['new_file_name']);
				}
				else
				{
					$address = $this->get_address($values['loc1'],$values['loc2'],$values['loc3']);

					$values['title'] = 'Branntegning - pdf';
					
					$insert_values= array(
						$values['new_file_name'],
						$values['title'],
						'public',
						$values['cat_id'],
						time(),
						$values['last_modified'],
						1,
						6,
						2,
						$values['location_code'],
						$address,
						$values['branch_id'],
						$values['vendor_id'],
						6,
						$values['loc1'],
						$values['loc2']
						);

					$insert_values	= $this->db->validate_insert($insert_values);

					$sql = "INSERT INTO fm_document (document_name,title,access,category,entry_date,document_date,version,coordinator,status,"
						. "location_code,address,branch_id,vendor_id,user_id,loc1,loc2) "
						. "VALUES ($insert_values)";

					$this->db->query($sql,__LINE__,__FILE__);
					
					$ok = rename($from_file, "{$this->dir}/archive/{$values['file_name']}");

					$this->receipt['message'][]=array('msg'=>lang('File %1 copied!',$values['new_file_name']));
					$this->receipt['message'][]=array('msg'=>lang('File %1 moved!',$from_file));
				}
			}

			$this->db->transaction_commit();
			$this->vfs->override_acl = 0;
		}

		function get_address($loc1='',$loc2='')
		{
			if ($loc3)
			{
				$sql = "SELECT loc3_name as address FROM fm_location3 WHERE loc1='$loc1' AND loc2='$loc2' AND loc3='$loc3'";
			}
			else
			{
				$sql = "SELECT loc2_name as address FROM fm_location2 WHERE loc1='$loc1' AND loc2='$loc2'";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('address');
		}
	}
