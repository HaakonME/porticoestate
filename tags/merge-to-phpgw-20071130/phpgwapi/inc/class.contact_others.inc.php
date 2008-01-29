<?php
	/**
	* Query statements for "others" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_others.inc.php 15562 2004-12-30 06:47:35Z skwashd $
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
	* Query statements for "others" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_others extends sql_entity
	{
		var $map = array('key_other_id'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'other_id',
								 'type'		=> 'integer'),
				 'other_contact_id'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'contact_id',
								 'type'		=> 'integer'),
				 'other_name'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'),
				 'other_value'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'),
				 'other_owner'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'contact_owner',
								 'type'		=> 'integer'),
				 'other_count_row'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''),
				 'label'               => array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''),
				 'email_type'          => array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''),
				 'email_home_type'     => array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''),
				 'adr_one_type'        => array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''),
				 'adr_two_type'        => array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> ''));
		

		function contact_others ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact_others', 'contact_others');
			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('other_contact_id', 'phpgwapi.contact_central','contact_id');
		}

		function criteria_other_contact_id($element)
		{
			$field = $this->put_alias($element['real_field']);
			if(is_array($element['value']))
			{
				$this->_add_criteria(sql_criteria::in($field, $element['value']));
			}
			else
			{
				$this->_add_criteria(sql_criteria::equal($field, $element['value']));
			}
		}
	}
?>
