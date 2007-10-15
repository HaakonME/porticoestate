<?php
	/**
	* Query statements for "note" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_note.inc.php,v 1.2 2004/12/30 06:47:30 skwashd Exp $
	*/

	/**
	* Use SQL criteria
	*/
	include_once(PHPGW_API_INC . '/class.sql_criteria.inc.php');
	/**
	* Use SQL entity
	*/
	include_once(PHPGW_API_INC . '/class.sql_entity.inc.php');

	/**
	* Query statements for "note" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_note extends sql_entity
	{
		var $map = array('key_note_id'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'contact_note_id',
								 'type'		=> 'integer'),
				 'note_contact_id'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'contact_id',
								 'type'		=> 'integer'),
				 'note_type'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'note_type_id',
								 'type'		=> 'integer'),
				 'note_text'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'),
				 'note_creaton'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'created_on',
								 'type'		=> 'integer'),
				 'note_creatby'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'created_by',
								 'type'		=> 'integer'),
				 'note_modon'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'modified_on',
								 'type'		=> 'integer'),
				 'note_modby'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'modified_by',
								 'type'		=> 'integer'),
				 'note'			=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'));
		

		function contact_note ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact_note', 'contact_note');
			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('note_contact_id', 'phpgwapi.contact_central','contact_id');
			$this->set_elinks('note_type', 'phpgwapi.contact_note_type','note_type_id');
		}
	}
?>
