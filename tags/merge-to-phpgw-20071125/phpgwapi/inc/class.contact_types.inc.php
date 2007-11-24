<?php
	/**
	* Query statements for "contact_type" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_types.inc.php,v 1.2 2004/12/30 06:47:30 skwashd Exp $
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
	* Query statements for "contact_type" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_types extends sql_entity
	{
		var $map = array('contact_type_id'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'integer'),
				 'contact_type_descr'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'),
				 'contact_type_table'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'string'));
		
		function contact_types ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact_types', 'contact_types');
			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_elinks('contact_type_id', 'phpgwapi.contact_central','contact_type');
		}

		function criteria_contact_type_id($element)
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
