<?php
	/**
	* Virtual File System with SQL backend
	* @author Jason Wies <zone@phpgroupware.org>
	* @author Giancarlo Susin
	* @copyright Copyright (C) 2001 Jason Wies
	* @copyright Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id: class.vfs_sql.inc.php 18302 2007-10-07 09:47:59Z sigurdne $
	*/

	/**
	* VFS SQL select
	* @see extra_sql()
	*/
	define ('VFS_SQL_SELECT', 1);
	/**
	* VFS SQL delete
	* @see extra_sql()
	*/
	define ('VFS_SQL_DELETE', 2);
	/**
	* VFS SQL update
	* @see extra_sql()
	*/
	define ('VFS_SQL_UPDATE', 4);


	/**
	* Virtual File System with SQL backend
	* 
	* @package phpgwapi
	* @subpackage vfs
	* @ignore
	*/
	class vfs extends vfs_shared
	{
		var $file_actions;

		/**
		 * constructor, sets up variables
		*
		 */
		function vfs ()
		{
			parent::vfs_shared();
			/*
			   File/dir attributes, each corresponding to a database field.  Useful for use in loops
			   If an attribute was added to the table, add it here and possibly add it to
			   set_attributes ()

			   set_attributes now uses this array().   07-Dec-01 skeeter
			*/

			$this->attributes[] = 'deleteable';
			$this->attributes[] = 'content';

			/*
			   Decide whether to use any actual filesystem calls (fopen(), fread(),
			   unlink(), rmdir(), touch(), etc.).  If not, then we're working completely
			   in the database.
			*/
			$conf = CreateObject('phpgwapi.config', 'phpgwapi');
			$conf->read_repository();
			if ($conf->config_data['file_store_contents'] == 'filesystem' || !$conf->config_data['file_store_contents'])
			{
				$this->file_actions = 1;
			}
			else
			{
				$this->file_actions = 0;
			}
	
			// test if the files-dir is inside the document-root, and refuse working if so
			//
			if ($this->file_actions && $this->in_docroot($this->basedir))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				if ($GLOBALS['phpgw_info']['flags']['noheader']) 
				{
					echo parse_navbar();
				}
				echo '<p align="center"><font color="red"><b>'.lang('Path to user and group files HAS TO BE OUTSIDE of the webservers document-root!!!')."</b></font></p>\n";
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			/* We store the linked directories in an array now, so we don't have to make the SQL call again */
			if ($GLOBALS['phpgw_info']['server']['db_type']=='mssql'
				|| $GLOBALS['phpgw_info']['server']['db_type']=='sybase')
			{
				$query = $GLOBALS['phpgw']->db->query ("SELECT directory, name, link_directory, link_name FROM phpgw_vfs WHERE CONVERT(varchar,link_directory) != '' AND CONVERT(varchar,link_name) != ''" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__,__FILE__);
			}
			else
			{
				$query = $GLOBALS['phpgw']->db->query ("SELECT directory, name, link_directory, link_name FROM phpgw_vfs WHERE (link_directory IS NOT NULL or link_directory != '') AND (link_name IS NOT NULL or link_name != '')" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__,__FILE__);
			}

			$this->linked_dirs = array ();
			while ($GLOBALS['phpgw']->db->next_record ())
			{
				$this->linked_dirs[] = $this->Record();
			}
		}

		/**
		 * test if $path lies within the webservers document-root
		*
		 */
		function in_docroot($path)
		{
			$docroots = array(PHPGW_SERVER_ROOT, $_SERVER['DOCUMENT_ROOT']);

			foreach ($docroots as $docroot)
			{
				$len = strlen($docroot);

				if ($docroot == substr($path,0,$len))
				{
					$rest = substr($path,$len);

					if (!strlen($rest) || $rest[0] == DIRECTORY_SEPARATOR)
					{
						return True;
					}
				}
			}
			return False;
		}

		/**
		 * Return extra SQL code that should be appended to certain queries
		*
		 * @param query_type The type of query to get extra SQL code for, in the form of a VFS_SQL define
		 * @return Extra SQL code
		 */
		function extra_sql ($data)
		{
			if (!is_array ($data))
			{
				$data = array ('query_type' => VFS_SQL_SELECT);
			}

			if ($data['query_type'] == VFS_SQL_SELECT || $data['query_type'] == VFS_SQL_DELETE || $data['query_type'] = VFS_SQL_UPDATE)
			{
				$sql = ' AND ((';

				reset ($this->meta_types);
				while (list ($num, $type) = each ($this->meta_types))
				{
					if ($num)
						$sql .= ' AND ';

					$sql .= "mime_type != '$type'";
				}
				$sql .= ') OR mime_type IS NULL)';
			}

			return ($sql);
		}

		/**
		 * Add a journal entry after (or before) completing an operation,
		*
		 * 	  and increment the version number.  This function should be used internally only
		 * Note that state_one and state_two are ignored for some VFS_OPERATION's, for others
		 * 		 * they are required.  They are ignored for any "custom" operation
		 * 		 * The two operations that require state_two:
		 * 		 * operation		 * 	state_two
		 * 		 * VFS_OPERATION_COPIED	fake_full_path of copied to
		 * 		 * VFS_OPERATION_MOVED		 * fake_full_path of moved to

		 * 		 * If deleting, you must call add_journal () before you delete the entry from the database
		 * @param string File or directory to add entry for
		 * @param relatives Relativity array
		 * @param operation The operation that was performed.  Either a VFS_OPERATION define or
		 * 		 *   a non-integer descriptive text string
		 * @param state_one The first "state" of the file or directory.  Can be a file name, size,
		 * 		 *   location, whatever is appropriate for the specific operation
		 * @param state_two The second "state" of the file or directory
		 * @param incversion Boolean True/False.  Increment the version for the file?  Note that this is
		 * 		 *    handled automatically for the VFS_OPERATION defines.
		 * 		 *    i.e. VFS_OPERATION_EDITED would increment the version, VFS_OPERATION_COPIED
		 * 		 *    would not
		 * @return Boolean True/False
		 */
		function add_journal ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'state_one'	=> False,
					'state_two'	=> False,
					'incversion'	=> True
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$p = $this->path_parts (array ('string' => $data['string'], 'relatives' => array ($data['relatives'][0])));

			/* We check that they have some sort of access to the file other than read */
			if (!$this->acl_check (array ('string' => $p->fake_full_path, 'relatives' => array ($p->mask), 'operation' => PHPGW_ACL_ADD)) &&
				!$this->acl_check (array ('string' => $p->fake_full_path, 'relatives' => array ($p->mask), 'operation' => PHPGW_ACL_EDIT)) &&
				!$this->acl_check (array ('string' => $p->fake_full_path, 'relatives' => array ($p->mask), 'operation' => PHPGW_ACL_DELETE)))
			{
				return False;
			}

			if (!$this->file_exists (array ('string' => $p->fake_full_path, 'relatives' => array ($p->mask))))
			{
				return False;
			}

			$ls_array = $this->ls (array (
					'string' => $p->fake_full_path,
					'relatives' => array ($p->mask),
					'checksubdirs' => False,
					'mime_type'	=> False,
					'nofiles'	=> True
				)
			);
			$file_array = $ls_array[0];

			$sql = 'INSERT INTO phpgw_vfs (';
			$sql2 = ' VALUES (';
			$morethanone = False;
			$modified = False;

			for ($i = 0; list ($attribute, $value) = each ($file_array); $i++)
			{
				if ($attribute == 'file_id' || $attribute == 'content')
				{
					continue;
				}

				if ($attribute == 'owner_id')
				{
					$value = $account_id;
				}

				if ($attribute == 'created')
				{
					$value = $this->now;
				}

				if ($attribute == 'modified' && !$modified)
				{
					unset ($value);
				}

				if ($attribute == 'mime_type')
				{
					$value = 'journal';
				}

				if ($attribute == 'comment')
				{
					switch ($data['operation'])
					{
						case VFS_OPERATION_CREATED:
							$value = 'Created';
							$data['incversion'] = True;
							break;
						case VFS_OPERATION_EDITED:
							$value = 'Edited';
							$data['incversion'] = True;
							break;
						case VFS_OPERATION_EDITED_COMMENT:
							$value = 'Edited comment';
							$data['incversion'] = False;
							break;
						case VFS_OPERATION_COPIED:
							if (!$data['state_one'])
							{
								$data['state_one'] = $p->fake_full_path;
							}
							if (!$data['state_two'])
							{
								return False;
							}
							$value = 'Copied '.$data['state_one'].' to '.$data['state_two'];
							$data['incversion'] = False;
							break;
						case VFS_OPERATION_MOVED:
							if (!$data['state_one'])
							{
								$data['state_one'] = $p->fake_full_path;
							}
							if (!$data['state_two'])
							{
								return False;
							}
							$value = 'Moved '.$data['state_one'].' to '.$data['state_two'];
							$data['incversion'] = False;
							break;
						case VFS_OPERATION_DELETED:
							$value = 'Deleted';
							$data['incversion'] = False;
							break;
						default:
							$value = $data['operation'];
							break;
					}
				}

				/*
				   Let's increment the version for the file itself.  We keep the current
				   version when making the journal entry, because that was the version that
				   was operated on.  The maximum numbers for each part in the version string:
				   none.99.9.9
				*/
				if ($attribute == 'version' && $data['incversion'])
				{
					$version_parts = split ("\.", $value);
					$newnumofparts = $numofparts = count ($version_parts);

					if ($version_parts[3] >= 9)
					{
						$version_parts[3] = 0;
						$version_parts[2]++;
						$version_parts_3_update = 1;
					}
					elseif (isset ($version_parts[3]))
					{
						$version_parts[3]++;
					}

					if ($version_parts[2] >= 9 && $version_parts[3] == 0 && $version_parts_3_update)
					{
						$version_parts[2] = 0;
						$version_parts[1]++;
					}

					if ($version_parts[1] > 99)
					{
						$version_parts[1] = 0;
						$version_parts[0]++;
					}
					$newversion = '';
					for ($j = 0; $j < $newnumofparts; $j++)
					{
						if (!isset ($version_parts[$j]))
						{
							break;
						}

						if ($j)
						{
							$newversion .= '.';
						}

						$newversion .= $version_parts[$j];
					}

					$this->set_attributes (array(
							'string'	=> $p->fake_full_path,
							'relatives'	=> array ($p->mask),
							'attributes'	=> array(
										'version' => $newversion
									)
						)
					);
				}
				if (isset ($value) && !empty($value))
				{
					if ($morethanone)
					{
						$sql .= ', ';
						$sql2 .= ', ';
					}
					else
					{
						$morethanone = True;
					}
					$sql .= "$attribute";
					$sql2 .= "'" . $this->clean_string (array ('string' => $value)) . "'";
				}
			}
			unset($morethanone);
			$sql .= ')';
			$sql2 .= ')';

			$sql .= $sql2;

			/*
			   These are some special situations where we need to flush the journal entries
			   or move the 'journal' entries to 'journal-deleted'.  Kind of hackish, but they
			   provide a consistent feel to the system
			*/
			$flush_path = '';
			if ($data['operation'] == VFS_OPERATION_CREATED)
			{
				$flush_path = $p->fake_full_path;
				$deleteall = True;
			}

			if ($data['operation'] == VFS_OPERATION_COPIED || $data['operation'] == VFS_OPERATION_MOVED)
			{
				$flush_path = $data['state_two'];
				$deleteall = False;
			}

			if ($flush_path)
			{
				$flush_path_parts = $this->path_parts (array(
						'string'	=> $flush_path,
						'relatives'	=> array (RELATIVE_NONE)
					)
				);

				$this->flush_journal (array(
						'string'	=> $flush_path_parts->fake_full_path,
						'relatives'	=> array ($flush_path_parts->mask),
						'deleteall'	=> $deleteall
					)
				);
			}

			if ($data['operation'] == VFS_OPERATION_COPIED)
			{
				/*
				   We copy it going the other way as well, so both files show the operation.
				   The code is a bad hack to prevent recursion.  Ideally it would use VFS_OPERATION_COPIED
				*/
				$this->add_journal (array(
						'string'	=> $data['state_two'],
						'relatives'	=> array (RELATIVE_NONE),
						'operation'	=> 'Copied '.$data['state_one'].' to '.$data['state_two'],
						'state_one'	=> NULL,
						'state_two'	=> NULL,
						'incversion'	=> False
					)
				);
			}

			if ($data['operation'] == VFS_OPERATION_MOVED)
			{
				$state_one_path_parts = $this->path_parts (array(
						'string'	=> $data['state_one'],
						'relatives'	=> array (RELATIVE_NONE)
					)
				);

				$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET mime_type='journal-deleted' WHERE directory='".$state_one_path_parts->fake_leading_dirs_clean."' AND name='".$state_one_path_parts->fake_name_clean."' AND mime_type='journal'");

				/*
				   We create the file in addition to logging the MOVED operation.  This is an
				   advantage because we can now search for 'Create' to see when a file was created
				*/
				$this->add_journal (array(
						'string'	=> $data['state_two'],
						'relatives'	=> array (RELATIVE_NONE),
						'operation'	=> VFS_OPERATION_CREATED
					)
				);
			}

			/* This is the SQL query we made for THIS request, remember that one? */
			$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);

			/*
			   If we were to add an option of whether to keep journal entries for deleted files
			   or not, it would go in the if here
			*/
			if ($data['operation'] == VFS_OPERATION_DELETED)
			{
				$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET mime_type='journal-deleted' WHERE directory='$p->fake_leading_dirs_clean' AND name='$p->fake_name_clean' AND mime_type='journal'");
			}

			return True;
		}

		/**
		 * Flush journal entries for $string.  Used before adding $string
		*
		 * flush_journal () is an internal function and should be called from add_journal () only
		 * @param string File/directory to flush journal entries of
		 * @param relatives Realtivity array
		 * @param deleteall Delete all types of journal entries, including the active Create entry.
		 * 		 *   Normally you only want to delete the Create entry when replacing the file
		 * 		 *   Note that this option does not effect $deleteonly
		 * @param deletedonly Only flush 'journal-deleted' entries (created when $string was deleted)
		 * @return Boolean True/False
		 */
		function flush_journal ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'deleteall'	=> False,
					'deletedonly'	=> False
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$sql = "DELETE FROM phpgw_vfs WHERE directory='".$p->fake_leading_dirs_clean."' AND name='".$p->fake_name_clean."'";

			if (!$data['deleteall'])
			{
				$sql .= " AND (mime_type != 'journal' AND comment != 'Created')";
			}

			$sql .= "  AND (mime_type='journal-deleted'";

			if (!$data['deletedonly'])
			{
				$sql .= " OR mime_type='journal'";
			}

			$sql .= ")";

			$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);

			if ($query)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		/*
		 * See vfs_shared
		 */
		function get_journal ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'type'	=> False
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string' => $p->fake_full_path,
					'relatives' => array ($p->mask)
				)))
			{
				return False;
			}

			$sql = "SELECT * FROM phpgw_vfs WHERE directory='".$p->fake_leading_dirs_clean."' AND name='".$p->fake_name_clean."'";

			if ($data['type'] == 1)
			{
				$sql .= " AND mime_type='journal'";
			}
			elseif ($data['type'] == 2)
			{
				$sql .= " AND mime_type='journal-deleted'";
			}
			else
			{
				$sql .= " AND (mime_type='journal' OR mime_type='journal-deleted')";
			}

			$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);

			while ($GLOBALS['phpgw']->db->next_record ())
			{
				$rarray[] = $this->Record();
			}

			return $rarray;
		}

		/*
		 * See vfs_shared
		 */
		function acl_check ($data)
		{
			//echo 'checking vfs_sql::acl_check(' . print_r($data, true) . '</pre>';
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'operation'	=> PHPGW_ACL_READ,
					'must_exist'	=> False
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			/* Accommodate special situations */
			if ($this->override_acl || $data['relatives'][0] == RELATIVE_USER_APP)
			{
				return True;
			}

			if (!isset($data['owner_id']) || !$data['owner_id'])
			{
				$p = $this->path_parts (array(
						'string'	=> $data['string'],
						'relatives'	=> array ($data['relatives'][0])
					)
				);

				/* Temporary, until we get symlink type files set up */
				if ($p->outside)
				{
					return True;
				}

				/* Read access is always allowed here, but nothing else is */
				if ($data['string'] == '/' || $data['string'] == $this->fakebase)
				{
					if ($data['operation'] == PHPGW_ACL_READ)
					{
						return True;
					}
					else
					{
						return False;
					}
				}

				/* If the file doesn't exist, we get ownership from the parent directory */
				if (!$this->file_exists (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					))
				)
				{
					if ($data['must_exist'])
					{
						return False;
					}

					$data['string'] = $p->fake_leading_dirs;
					$p2 = $this->path_parts (array(
							'string'	=> $data['string'],
							'relatives'	=> array ($p->mask)
						)
					);

					if (!$this->file_exists (array(
							'string'	=> $data['string'],
							'relatives'	=> array ($p->mask)
						))
					)
					{
						return False;
					}
				}
				else
				{
					$p2 = $p;
				}

				/*
				   We don't use ls () to get owner_id as we normally would,
				   because ls () calls acl_check (), which would create an infinite loop
				*/
				$query = $GLOBALS['phpgw']->db->query ("SELECT owner_id FROM phpgw_vfs WHERE directory='".$p2->fake_leading_dirs_clean."' AND name='".$p2->fake_name_clean."'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
				$GLOBALS['phpgw']->db->next_record ();

				$record		= $this->Record();
				$owner_id	= $record['owner_id'];
			}
			else
			{
				$owner_id = $data['owner_id'];
			}

			/* This is correct.  The ACL currently doesn't handle undefined values correctly */
			if (!$owner_id)
			{
				$owner_id = 0;
			}

			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			/* They always have access to their own files */
			if ($owner_id == $user_id)
			{
				return True;
			}

			/* Check if they're in the group */
			$memberships = $GLOBALS['phpgw']->accounts->membership ($user_id);

			if (is_array ($memberships))
			{
				reset ($memberships);
				while (list ($num, $group_array) = each ($memberships))
				{
					if ($owner_id == $group_array['account_id'])
					{
						$group_ok = 1;
						break;
					}
				}
			}

			$acl = CreateObject ('phpgwapi.acl', $owner_id);
			$acl->account_id = $owner_id;
			$acl->read_repository ();

			$rights = $acl->get_rights ($user_id);

			/* Add privileges from the groups this user belongs to */
			if (is_array ($memberships))
			{
				reset ($memberships);
				while (list ($num, $group_array) = each ($memberships))
				{
					$rights |= $acl->get_rights ($group_array['account_id']);
				}
			}

			if ($rights & $data['operation'])
			{
				return True;
			}
			elseif (!$rights && $group_ok)
			{
				$conf = CreateObject('phpgwapi.config', 'phpgwapi');
				$conf->read_repository();
				if ($conf->config_data['acl_default'] == 'grant')
				{
					return True;
				}
				else
				{
					return False;
				}
			}
			else
			{
				return False;
			}
		}

		/*
		 * See vfs_shared
		 */
		function read ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_READ
				))
			)
			{
				return False;
			}

			$conf = CreateObject('phpgwapi.config', 'phpgwapi');
			$conf->read_repository();
			if ($this->file_actions || $p->outside)
			{
				if (filesize ($p->real_full_path) > 0 && $fp = fopen ($p->real_full_path, 'rb'))
				{
					$contents = fread ($fp, filesize ($p->real_full_path));
					fclose ($fp);
				}
				else
				{
					$contents = False;
				}
			}
			else
			{
				$ls_array = $this->ls (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)
				);

				$contents = $ls_array[0]['content'];
			}

			return $contents;
		}

		/*
		 * See vfs_shared
		 */
		function write ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'content'	=> ''
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if ($this->file_exists (array (
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask)
				))
			)
			{
				$acl_operation = PHPGW_ACL_EDIT;
				$journal_operation = VFS_OPERATION_EDITED;
			}
			else
			{
				$acl_operation = PHPGW_ACL_ADD;
			}

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> $acl_operation
				))
			)
			{
				return False;
			}

			umask(000);

			/*
			   If 'string' doesn't exist, touch () creates both the file and the database entry
			   If 'string' does exist, touch () sets the modification time and modified by
			*/
			$this->touch (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask)
				)
			);

			$conf = CreateObject('phpgwapi.config', 'phpgwapi');
			$conf->read_repository();
			if ($this->file_actions)
			{
				if ($fp = fopen ($p->real_full_path, 'wb'))
				{
					fwrite ($fp, $data['content'], strlen ($data['content']));
					fclose ($fp);
					$write_ok = 1;
				}
			}

			if ($write_ok || !$this->file_actions)
			{
				if ($this->file_actions)
				{
					$set_attributes_array = array(
						'size' => filesize ($p->real_full_path)
					);
				}
				else
				{
					$set_attributes_array = array (
						'size'	=> strlen ($data['content']),
						'content'	=> $data['content']
					);
				}


				$this->set_attributes (array
					(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'attributes'	=> $set_attributes_array
					)
				);

				if ($journal_operation)
				{
					$this->add_journal (array(
							'string'	=> $p->fake_full_path,
							'relatives'	=> array ($p->mask),
							'operation'	=> $journal_operation
						)
					);
				}

				return True;
			}
			else
			{
				return False;
			}
		}

		/*
		 * See vfs_shared
		 */
		function touch ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			umask (000);

			if ($this->file_actions)
			{
				/*
				   PHP's touch function will automatically decide whether to
				   create the file or set the modification time
				*/
				$rr = @touch ($p->real_full_path);

				if ($p->outside)
				{
					return $rr;
				}
			}

			/* We, however, have to decide this ourselves */
			if ($this->file_exists (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask)
				))
			)
			{
				if (!$this->acl_check (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> PHPGW_ACL_EDIT
					)))
				{
					return False;
				}

				$vr = $this->set_attributes (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'attributes'	=> array(
									'modifiedby_id' => $account_id,
									'modified' => $this->now
								)
						)
					);
			}
			else
			{
				if (!$this->acl_check (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> PHPGW_ACL_ADD
					))
				)
				{
					return False;
				}

				$query = $GLOBALS['phpgw']->db->query ('INSERT INTO phpgw_vfs (owner_id, directory, name) VALUES (' . $this->working_id . ",'" . $p->fake_leading_dirs_clean . "','"
														. $p->fake_name_clean . "')", __LINE__, __FILE__);

				$this->set_attributes(array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'attributes'	=> array (
								'createdby_id' => $account_id,
								'created' => $this->now,
								'size' => 0,
								'deleteable' => 'Y',
								'app' => $currentapp
							)
					)
				);
				$this->correct_attributes (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)
				);
	
				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> VFS_OPERATION_CREATED
					)
				);
			}

			if ($rr || $vr || $query)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		/*
		 * See vfs_shared
		 */
		function cp ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$f = $this->path_parts (array(
					'string'	=> $data['from'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$t = $this->path_parts (array(
					'string'	=> $data['to'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_READ
				))
			)
			{
				return False;
			}

			if ($this->file_exists (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask)
				))
			)
			{
				if (!$this->acl_check (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'operation'	=> PHPGW_ACL_EDIT
					))
				)
				{
					return False;
				}
			}
			else
			{
				if (!$this->acl_check (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'operation'	=> PHPGW_ACL_ADD
					))
				)
				{
					return False;
				}

			}

			umask(000);

			if ($this->file_type (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				)) != 'Directory'
			)
			{
				if ($this->file_actions)
				{
					if (!copy ($f->real_full_path, $t->real_full_path))
					{
						return False;
					}

					$size = filesize ($t->real_full_path);
				}
				else
				{
					$content = $this->read (array(
							'string'	=> $f->fake_full_path,
							'relatives'	=> array ($f->mask)
						)
					);

					$size = strlen ($content);
				}

				if ($t->outside)
				{
					return True;
				}

				$ls_array = $this->ls (array(
						'string'	=> $f->real_full_path, // Sigurd: seems to work better with real - old: 'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask),
						'checksubdirs'	=> False,
						'mime_type'	=> False,
						'nofiles'	=> True
					)
				);
				$record = $ls_array[0];

				if ($this->file_exists (array(
						'string'	=> $data['to'],
						'relatives'	=> array ($data['relatives'][1])
					))
				)
				{
					$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET owner_id='$this->working_id', directory='$t->fake_leading_dirs_clean', name='$t->fake_name_clean' WHERE owner_id='$this->working_id' AND directory='$t->fake_leading_dirs_clean' AND name='$t->fake_name_clean'" . $this->extra_sql (VFS_SQL_UPDATE), __LINE__, __FILE__);

					$set_attributes_array = array (
						'createdby_id' => $account_id,
						'created' => $this->now,
						'size' => $size,
						'mime_type' => $record['mime_type'],
						'deleteable' => $record['deleteable'],
						'comment' => $record['comment'],
						'app' => $record['app']
					);

					if (!$this->file_actions)
					{
						$set_attributes_array['content'] = $content;
					}

					$this->set_attributes(array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'attributes'	=> $set_attributes_array
						)
					);

					$this->add_journal (array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask),
							'operation'	=> VFS_OPERATION_EDITED
						)
					);
				}
				else
				{
					$this->touch (array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask)
						)
					);

					$set_attributes_array = array (
						'createdby_id' => $account_id,
						'created' => $this->now,
						'size' => $size,
						'mime_type' => $record['mime_type'],
						'deleteable' => $record['deleteable'],
						'comment' => $record['comment'],
						'app' => $record['app']
					);

					if (!$this->file_actions)
					{
						$set_attributes_array['content'] = $content;
					}

					$this->set_attributes(array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask),
							'attributes'	=> $set_attributes_array
						)
					);
				}
				$this->correct_attributes (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask)
					)
				);
			}
			else	/* It's a directory */
			{
				/* First, make the initial directory */
				if ($this->mkdir (array(
						'string'	=> $data['to'],
						'relatives'	=> array ($data['relatives'][1])
					)) === False
				)
				{
					return False;
				}

				/* Next, we create all the directories below the initial directory */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask),
						'checksubdirs'	=> True,
						'mime_type'	=> 'Directory'
					)
				);

				while (list ($num, $entry) = each ($ls))
				{
					$newdir = ereg_replace ("^$f->fake_full_path", "$t->fake_full_path", $entry['directory']);
					if ($this->mkdir (array(
							'string'	=> $newdir.'/'.$entry['name'],
							'relatives'	=> array ($t->mask)
						)) === False
					)
					{
						return False;
					}
				}

				/* Lastly, we copy the files over */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask)
					)
				);

				while (list ($num, $entry) = each ($ls))
				{
					if ($entry['mime_type'] == 'Directory')
					{
						continue;
					}

					$newdir = ereg_replace ("^$f->fake_full_path", "$t->fake_full_path", $entry['directory']);
					$this->cp (array(
							'from'	=> "$entry[directory]/$entry[name]",
							'to'	=> "$newdir/$entry[name]",
							'relatives'	=> array ($f->mask, $t->mask)
						)
					);
				}
			}

			if (!$f->outside)
			{
				$this->add_journal (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask),
						'operation'	=> VFS_OPERATION_COPIED,
						'state_one'	=> NULL,
						'state_two'	=> $t->fake_full_path
					)
				);
			}

			return True;
		}

		/*
		 * See vfs_shared
		 */
		function mv ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$f = $this->path_parts (array(
					'string'	=> $data['from'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$t = $this->path_parts (array(
					'string'	=> $data['to'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);
			if (!$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_READ
				))
				|| !$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_DELETE
				))
			)
			{
				return False;
			}

			if (!$this->acl_check (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask),
					'operation'	=> PHPGW_ACL_ADD
				))
			)
			{
				return False;
			}

			if ($this->file_exists (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask)
				))
			)
			{
				if (!$this->acl_check (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'operation'	=> PHPGW_ACL_EDIT
					))
				)
				{
					return False;
				}
			}

			umask (000);

			/* We can't move directories into themselves */
			if (($this->file_type (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				) == 'Directory'))
				&& ereg ("^$f->fake_full_path", $t->fake_full_path)
			)
			{
				if (($t->fake_full_path == $f->fake_full_path) || substr ($t->fake_full_path, strlen ($f->fake_full_path), 1) == '/')
				{
					return False;
				}
			}
			if ($this->file_exists (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				))
			)
			{
				/* We get the listing now, because it will change after we update the database */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask)
					)
				);

				if ($this->file_exists (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask)
					))
				)
				{
					$this->rm (array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask)
						)
					);
				}

				/*
				   We add the journal entry now, before we delete.  This way the mime_type
				   field will be updated to 'journal-deleted' when the file is actually deleted
				*/
				if (!$f->outside)
				{
					$this->add_journal (array(
							'string'	=> $f->fake_full_path,
							'relatives'	=> array ($f->mask),
							'operation'	=> VFS_OPERATION_MOVED,
							'state_one'	=> $f->fake_full_path,
							'state_two'	=> $t->fake_full_path
						)
					);
				}

				/*
				   If the from file is outside, it won't have a database entry,
				   so we have to touch it and find the size
				*/
				if ($f->outside)
				{
					$size = filesize ($f->real_full_path);
					if ( $size === False )
					{
						_debug_array($f);
						$size = 1;
					}
					$this->touch (array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask)
						)
					);
					$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET size=$size WHERE directory='$t->fake_leading_dirs_clean' AND name='$t->fake_name_clean'" . $this->extra_sql (array ('query_type' => VFS_SQL_UPDATE)), __LINE__, __FILE__);
				}
				elseif (!$t->outside)
				{
					$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET name='$t->fake_name_clean', directory='$t->fake_leading_dirs_clean' WHERE directory='$f->fake_leading_dirs_clean' AND name='$f->fake_name_clean'" . $this->extra_sql (array ('query_type' => VFS_SQL_UPDATE)), __LINE__, __FILE__);
				}

				$this->set_attributes(array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'attributes'	=> array (
									'modifiedby_id' => $account_id,
									'modified' => $this->now
								)
					)
				);

				$this->correct_attributes (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask)
					)
				);

				if ($this->file_actions)
				{
					$rr = rename ($f->real_full_path, $t->real_full_path);
				}

				/*
				   This removes the original entry from the database
				   The actual file is already deleted because of the rename () above
				*/
				if ($t->outside)
				{
					$this->rm (array(
							'string'	=> $f->fake_full_path,
							'relatives'	=> $f->mask
						)
					);
				}
			}
			else
			{
				return False;
			}

			if ($this->file_type (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask)
				)) == 'Directory'
			)
			{
				/* We got $ls from above, before we renamed the directory */
				while (list ($num, $entry) = each ($ls))
				{
					$newdir = ereg_replace ("^$f->fake_full_path", $t->fake_full_path, $entry['directory']);
					$newdir_clean = $this->clean_string (array ('string' => $newdir));

					$query = $GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET directory='$newdir_clean' WHERE file_id='$entry[file_id]'" . $this->extra_sql (array ('query_type' => VFS_SQL_UPDATE)), __LINE__, __FILE__);
					$this->correct_attributes (array(
							'string'	=> "$newdir/$entry[name]",
							'relatives'	=> array ($t->mask)
						)
					);
				}
			}

			$this->add_journal (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask),
					'operation'	=> VFS_OPERATION_MOVED,
					'state_one'	=> $f->fake_full_path,
					'state_two'	=> $t->fake_full_path
				)
			);

			return True;
		}

		/*
		 * See vfs_shared
		 */
		function rm ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_DELETE
				))
			)
			{
				return False;
			}

			if (!$this->file_exists (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				))
			)
			{
				if ($this->file_actions)
				{
					$rr = unlink ($p->real_full_path);
				}
				else
				{
					$rr = True;
				}

				if ($rr)
				{
					return True;
				}
				else
				{
					return False;
				}
			}

			if ($this->file_type (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)) != 'Directory'
			)
			{
				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> VFS_OPERATION_DELETED
					)
				);

				$query = $GLOBALS['phpgw']->db->query ("DELETE FROM phpgw_vfs WHERE directory='".$p->fake_leading_dirs_clean."' AND name='".$p->fake_name_clean."'".$this->extra_sql (array ('query_type' => VFS_SQL_DELETE)), __LINE__, __FILE__);

				if ($this->file_actions)
				{
					$rr = unlink ($p->real_full_path);
				}
				else
				{
					$rr = True;
				}

				if ($query || $rr)
				{
					return True;
				}
				else
				{
					return False;
				}
			}
			else
			{
				$ls = $this->ls (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)
				);

				/* First, we cycle through the entries and delete the files */
				while (list ($num, $entry) = each ($ls))
				{
					if ($entry['mime_type'] == 'Directory')
					{
						continue;
					}

					$this->rm (array(
							'string'	=> "$entry[directory]/$entry[name]",
							'relatives'	=> array ($p->mask)
						)
					);
				}

				/* Now we cycle through again and delete the directories */
				reset ($ls);
				while (list ($num, $entry) = each ($ls))
				{
					if ($entry['mime_type'] != 'Directory')
					{
						continue;
					}

					/* Only the best in confusing recursion */
					$this->rm (array(
							'string'	=> "$entry[directory]/$entry[name]",
							'relatives'	=> array ($p->mask)
						)
					);
				}

				/* If the directory is linked, we delete the placeholder directory */
				$ls_array = $this->ls (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'checksubdirs'	=> False,
						'mime_type'	=> False,
						'nofiles'	=> True
					)
				);
				$link_info = $ls_array[0];

				if ($link_info['link_directory'] && $link_info['link_name'])
				{
					$path = $this->path_parts (array(
							'string'	=> $link_info['directory'] . '/' . $link_info['name'],
							'relatives'	=> array ($p->mask),
							'nolinks'	=> True
						)
					);

					if ($this->file_actions)
					{
						rmdir ($path->real_full_path);
					}
				}

				/* Last, we delete the directory itself */
				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operaton'	=> VFS_OPERATION_DELETED
					)
				);

				$query = $GLOBALS['phpgw']->db->query ("DELETE FROM phpgw_vfs WHERE directory='$p->fake_leading_dirs_clean' AND name='$p->fake_name_clean'" . $this->extra_sql (array ('query_type' => VFS_SQL_DELETE)), __LINE__, __FILE__);

				if ($this->file_actions)
				{
					rmdir ($p->real_full_path);
				}

				return True;
			}
		}

		/*
		 * See vfs_shared
		 */
		function mkdir ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_ADD)
				)
			)
			{
				return False;
			}

			/* We don't allow /'s in dir names, of course */
			if (ereg ("/", $p->fake_name))
			{
				return False;
			}

			umask (000);

			if ($this->file_actions)
			{
				//if block from patch #1989 - auto create home - from lpiepho
				if (!file_exists($this->basedir.'/home'))
 				{
  					@mkdir ($this->basedir.'/home', 0770);
 				}

				if (file_exists($p->real_full_path))
				{
					if (!is_dir($p->real_full_path))
					{
						return False;
					}
				}
				elseif (!@mkdir ($p->real_full_path, 0770))
				{
					return False;
				}
			}

			if (!$this->file_exists (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask)
				))
			)
			{
				$query = $GLOBALS['phpgw']->db->query ('INSERT INTO phpgw_vfs (owner_id, name, directory) VALUES (' . $this->working_id . ", '" 
														. $p->fake_name_clean . "', '" . $p->fake_leading_dirs_clean . "')", __LINE__, __FILE__);
				$this->set_attributes(array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'attributes'	=> array (
								'createdby_id' => $account_id,
								'size' => 4096,
								'mime_type' => 'Directory',
								'created' => $this->now,
								'deleteable' => 'Y',
								'app' => $currentapp
							)
					)
				);

				$this->correct_attributes (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)
				);

				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> VFS_OPERATION_CREATED
					)
				);
			}
			else
			{
				return False;
			}

			return True;
		}

		/*
		 * See vfs_shared
		 */
		function make_link ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$vp = $this->path_parts (array(
					'string'	=> $data['vdir'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$rp = $this->path_parts (array(
					'string'	=> $data['rdir'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask),
					'operation'	=> PHPGW_ACL_ADD
				))
			)
			{
				return False;
			}

			if ($this->file_exists (array(
					'string'	=> $rp->real_full_path,
					'relatives'	=> array ($rp->mask)
				))
			)
			{
				if (!is_dir ($rp->real_full_path))
				{
					return False;
				}
			}
			elseif (!mkdir ($rp->real_full_path, 0770))
			{
				return False;
			}

			if (!$this->mkdir (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask)
				))
			)
			{
				return False;
			}

			$size = $this->get_size (array(
					'string'	=> $rp->real_full_path,
					'relatives'	=> array ($rp->mask)
				)
			);

			$this->set_attributes(array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask),
					'attributes'	=> array (
								'link_directory' => $rp->real_leading_dirs,
								'link_name' => $rp->real_name,
								'size' => $size
							)
				)
			);

			$this->correct_attributes (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask)
				)
			);

			return True;
		}

		/*
		 * See vfs_shared
		 */
		function set_attributes ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'attributes'	=> array ()
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			/*
			   This is kind of trivial, given that set_attributes () can change owner_id,
			   size, etc.
			*/
			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_EDIT
				))
			)
			{
				return False;
			}

			if (!$this->file_exists (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				))
			)
			{
				return False;
			}

			/*
			   All this voodoo just decides which attributes to update
			   depending on if the attribute was supplied in the 'attributes' array
			*/

			$ls_array = $this->ls (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'checksubdirs'	=> False,
					'nofiles'	=> True
				)
			);
			$record = $ls_array[0];

			$sql = 'UPDATE phpgw_vfs SET ';

			$change_attributes = 0;
			$edited_comment = False;

			reset ($this->attributes);
			while (list ($num, $attribute) = each ($this->attributes))
			{
				if (isset ($data['attributes'][$attribute]))
				{
					$$attribute = $data['attributes'][$attribute];

					/*
					   Indicate that the EDITED_COMMENT operation needs to be journaled,
					   but only if the comment changed
					*/
					if ($attribute == 'comment' && $data['attributes'][$attribute] != $record[$attribute])
					{
						$edited_comment = True;
					}

					$$attribute = $this->clean_string (array ('string' => $$attribute));

					if ($change_attributes > 0)
					{
						$sql .= ', ';
					}

					$sql .= "$attribute='" . $$attribute . "'";

					$change_attributes++;
				}
			}

			if ( $change_attributes )
			{
				$sql .= ' WHERE file_id=' . intval($record['file_id']);
				$sql .= $this->extra_sql (array ('query_type' => VFS_SQL_UPDATE));

				//echo 'sql: ' . $sql;

				$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);
				if ($query) 
				{
					if ($edited_comment)
					{
						$this->add_journal (array(
								'string'	=> $p->fake_full_path,
								'relatives'	=> array ($p->mask),
								'operation'	=> VFS_OPERATION_EDITED_COMMENT
							)
						);
					}

					return True;
				}
				else
				{
					return False;
				}
			}
			else
			{
				//Nothing was done, because nothing required !
				//This is a kind of bug isn't it ?
				//So I let people choose to debug here :/
				//FIXME : decide what we are doing here !
				return True;
			}
		}

		/*
		 * See vfs_shared
		 */
		function file_type ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_READ,
					'must_exist'	=> True
				))
			)
			{
				return False;
			}

			if ($p->outside)
			{
				if (is_dir ($p->real_full_path))
				{
					return ('Directory');
				}

				/*
				   We don't return an empty string here, because it may still match with a database query
				   because of linked directories
				*/
			}

			/*
			   We don't use ls () because it calls file_type () to determine if it has been
			   passed a directory
			*/
			$db2 = clone($GLOBALS['phpgw']->db);
			$db2->query ("SELECT mime_type FROM phpgw_vfs WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
			$db2->next_record();
			$mime_type = $db2->f('mime_type');
			if(!$mime_type)
			{
				$mime_type = $this->get_ext_mime_type (array ('string' => $data['string']));
				{
					$db2->query ("UPDATE phpgw_vfs SET mime_type='{$mime_type}' WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
				}
			}

			return $mime_type;
		}

		/*
		 * See vfs_shared
		 */
		function file_exists ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if ($p->outside)
			{
				$rr = file_exists ($p->real_full_path);

				return $rr;
			}

			$db2 = clone($GLOBALS['phpgw']->db);
			$db2->query ("SELECT name FROM phpgw_vfs WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);

			if ($db2->next_record ())
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		/*
		 * See vfs_shared
		 */
		function get_size ($data)
		{
			$size = parent::get_size($data);
			/*XXX Caeies : Not sure about this, but ... */
			/* If the virtual size is always 4096, we don't need this ... */
/*			if ($data['checksubdirs'])
			{
				$query = $GLOBALS['phpgw']->db->query ("SELECT size FROM phpgw_vfs WHERE directory='".$p->fake_leading_dirs_clean."' AND name='".$p->fake_name_clean."'" . $this->extra_sql (array ('query_text' => VFS_SQL_SELECT)));
				$GLOBALS['phpgw']->db->next_record ();
				$size += $GLOBALS['phpgw']->db->Record[0];
			}
*/
			return $size;
		}

		/* temporary wrapper function for not working Record function in adodb layer (ceb)*/
		function Record()
		{
			$values = array();
			foreach($this->attributes as $attribute)
			{
				$values[$attribute] = $GLOBALS['phpgw']->db->f($attribute);
			}
			return $values;
		}

		/*
		 * See vfs_shared
		 */
		function ls ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
			(
				'relatives'	=> array (RELATIVE_CURRENT),
				'checksubdirs'	=> True,
				'mime_type'	=> False,
				'nofiles'	=> False,
				'orderby'	=> 'directory'
			);

			$data = array_merge ($this->default_values ($data, $default_values), $data);
			//_debug_array($data);

			$p = $this->path_parts(array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			//_debug_array($p);

			$ftype = $this->file_type( array('string' => $p->fake_full_path, 'relatives' => array ($p->mask) ) );
			/* If they pass us a file or 'nofiles' is set, return the info for $dir only */
			if ( ($ftype != 'Directory' || $data['nofiles'] ) && !$p->outside)
			{
				/* SELECT all, the, attributes */
				$sql = 'SELECT ' . implode(', ', $this->attributes)
						. " FROM phpgw_vfs WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}' " 
						. $this->extra_sql (array ('query_type' => VFS_SQL_SELECT));

				$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);
				if($GLOBALS['phpgw']->db->num_rows() == 0)
				{
					return array();
				}
				$GLOBALS['phpgw']->db->next_record();
				$record = $this->Record();
				//echo 'record: ' . _debug_array($record);

				/* We return an array of one array to maintain the standard */
				$rarray = array ();
				reset ($this->attributes);
				while (list ($num, $attribute) = each ($this->attributes))
				{
					if ($attribute == 'mime_type' && !$record[$attribute])
					{
						$db2 = clone($GLOBALS['phpgw']->db);
						$record[$attribute] = $this->get_ext_mime_type (array(
								'string' => $p->fake_name_clean
							)
						);

						if($record[$attribute])
						{
							$db2->query ("UPDATE phpgw_vfs SET mime_type='{$record[$attribute]}' WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
						}
					}

					$rarray[0][$attribute] = $record[$attribute];
				}

				return $rarray;
			}

			//WIP - this should recurse using the same options the virtual part of ls () does
			/* If $dir is outside the virutal root, we have to check the file system manually */
			if ($p->outside)
			{
				if ($this->file_type (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)) == 'Directory'
					&& !$data['nofiles']
				)
				{
					$dir_handle = opendir ($p->real_full_path);
					while ($filename = readdir ($dir_handle))
					{
						if ($filename == '.' || $filename == '..')
						{
							continue;
						}
						$rarray[] = $this->get_real_info (array(
								'string'	=> "{$p->real_full_path}/{$filename}",
								'relatives'	=> array ($p->mask)
							)
						);
					}
				}
				else
				{
					$rarray[] = $this->get_real_info (array(
							'string'	=> $p->real_full_path,
							'relatives'	=> array ($p->mask)
						)
					);
				}

				return $rarray;
			}

			/* $dir's not a file, is inside the virtual root, and they want to check subdirs */
			/* SELECT all, the, attributes FROM phpgw_vfs WHERE file=$dir */
			$sql = 'SELECT ' . implode(',', $this->attributes);

			$dir_clean = $this->clean_string (array ('string' => $p->fake_full_path));
			$sql .= " FROM phpgw_vfs WHERE directory LIKE '$dir_clean%'";
			$sql .= $this->extra_sql (array ('query_type' => VFS_SQL_SELECT));

			if ($data['mime_type'])
			{
				$sql .= " AND mime_type='".$data['mime_type']."'";
			}

			$sql .= ' ORDER BY '.$data['orderby'];

			$query = $GLOBALS['phpgw']->db->query ($sql, __LINE__, __FILE__);

			$rarray = array ();
			while ( $GLOBALS['phpgw']->db->next_record() )
			{
				$record = $this->Record();

				//_debug_array($record);
				/* Further checking on the directory.  This makes sure /home/user/test won't match /home/user/test22 */
				if (!@ereg ("^{$p->fake_full_path}(/|$)", $record['directory']))
				{
					continue;
				}

				/* If they want only this directory, then $dir should end without a trailing / */
				if (!$data['checksubdirs'] && ereg ("^{$p->fake_full_path}/", $record['directory']))
				{
					continue;
				}

				$db2 = clone($GLOBALS['phpgw']->db);
				if ( isset($this->attributes['mime_type']) && !isset($record['mime_type']) )
				{
					$record['mime_type'] == $this->get_ext_mime_type(array('string' => $p->fake_name_clean));

					if ( $record['mime_type'] )
					{
						$db2->query ("UPDATE phpgw_vfs SET mime_type='{$record[$attribute]}' WHERE directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'" . $this->extra_sql(array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
						
					}
				}
				$rarray[] = $record;
			}
			return $rarray;
		}

		/*
		 * See vfs_shared
		 */
		function update_real ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (file_exists ($p->real_full_path))
			{
				if (is_dir ($p->real_full_path))
				{
					$dir_handle = opendir ($p->real_full_path);
					while ($filename = readdir ($dir_handle))
					{
						if ($filename == '.' || $filename == '..')
						{
							continue;
						}

						$rarray[] = $this->get_real_info (array(
								'string'	=> $p->fake_full_path . '/' . $filename,
								'relatives'	=> array (RELATIVE_NONE)
							)
						);
					}
				}
				else
				{
					$rarray[] = $this->get_real_info (array(
							'string'	=> $p->fake_full_path,
							'relatives'	=> array (RELATIVE_NONE)
						)
					);
				}

				if (!is_array ($rarray))
				{
					$rarray = array ();
				}

				while (list ($num, $file_array) = each ($rarray))
				{
					$p2 = $this->path_parts (array(
							'string'	=> $file_array['directory'] . '/' . $file_array['name'],
							'relatives'	=> array (RELATIVE_NONE)
						)
					);

					/* Note the mime_type.  This can be "Directory", which is how we create directories */
					$set_attributes_array = Array(
						'size' => $file_array['size'],
						'mime_type' => $file_array['mime_type']
					);

					if (!$this->file_exists (array(
							'string'	=> $p2->fake_full_path,
							'relatives'	=> array (RELATIVE_NONE)
						))
					)
					{
						$this->touch (array(
								'string'	=> $p2->fake_full_path,
								'relatives'	=> array (RELATIVE_NONE)
							)
						);

						$this->set_attributes (array(
								'string'	=> $p2->fake_full_path,
								'relatives'	=> array (RELATIVE_NONE),
								'attributes'	=> $set_attributes_array
							)
						);
					}
					else
					{
						$this->set_attributes (array(
								'string'	=> $p2->fake_full_path,
								'relatives'	=> array (RELATIVE_NONE),
								'attributes'	=> $set_attributes_array
							)
						);
					}
				}
			}
		}

		/* Helper functions */

		/* This fetchs all available file system information for string (not using the database) */
		function get_real_info ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (is_dir ($p->real_full_path))
			{
				$mime_type = 'Directory';
			}
			else
			{
				$mime_type = $this->get_ext_mime_type (array(
						'string'	=> $p->fake_name
					)
				);

				if($mime_type)
				{
					$GLOBALS['phpgw']->db->query ("UPDATE phpgw_vfs SET mime_type='".$mime_type."' WHERE directory='".$p->fake_leading_dirs_clean."' AND name='".$p->fake_name_clean."'" . $this->extra_sql (array ('query_type' => VFS_SQL_SELECT)), __LINE__, __FILE__);
				}
			}

			$size = filesize ($p->real_full_path);
			$rarray = array(
				'directory' => $p->fake_leading_dirs,
				'name' => $p->fake_name,
				'size' => $size,
				'mime_type' => $mime_type
			);

			return ($rarray);
		}
	}
?>
