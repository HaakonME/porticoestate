<?php
	/**
	* Query statements for "addr_type" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_addr_type.inc.php 17062 2006-09-03 06:15:27Z skwashd $
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
	* Query statements for "addr_type" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
		class contact_addr_type extends sql_entity
		{
				var $map = array('addr_type_id'         => array('select'       => '',
																 'criteria'     => '',
																 'insert'       => '',
																 'update'       => '',
																 'delete'       => '',
																 'sort'         => '',
																 'field'        => '',
								 'type'		=> 'integer'),
								 'addr_description'     => array('select'       => '',
																 'criteria'     => '',
																 'insert'       => '',
																 'update'       => '',
																 'delete'       => '',
																 'sort'         => '',
																 'field'        => 'description',
								 'type'		=> 'string'));
				
				function contact_addr_type ($ali = '', $field = '', $criteria = '')
				{
						$this->_constructor('phpgw_contact_addr_type', 'contact_addr_type');
						if($field)
						{
								$this->add_select($field);
						}
						if($criteria)
						{
								$this->add_criteria($criteria);
						}
						$this->set_ilinks('addr_type_id', 'phpgwapi.contact_addr','addr_type');
				}

		function criteria_addr_type_id($element)
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
