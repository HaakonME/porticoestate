<?php
	/**
	 * phpGroupWare custom fields
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @author Dave Hall dave.hall at skwashd.com
	 * @copyright Copyright (C) 2003-2006 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v3 or later
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id: class.custom_fields.inc.php 1114 2008-06-02 18:15:22Z sigurd $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * Import the parent class
	 */
	phpgw::import_class('phpgwapi.custom_fields');

	/**
	 * Custom Fields
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class property_custom_fields extends phpgwapi_custom_fields
	{
		/**
		 * Constructor
		 *
		 * @param string $appname the name of the module using the custom fields
		 *
		 * @return void
		 */
		public function __construct($appname = null)
		{
			parent::__construct($appname);
		}

		/**
		 * Prepare custom attributes for ui
		 * 
		 * @param array $values    values and definitions of custom attributes
		 * @param ????  $appname   ????
		 * @param ????  $location  ????
		 * @param ????  $view_only ????
		 *
		 * @return array values and definitions of custom attributes prepared for ui
		 *
		 * @internal this is a UI related method - WTF was it doing in an API logic class?
		 * this is property specific code and so has been moved there!
		 * this code needs some serious attention
		 */
		public function prepare($values, $appname, $location, $view_only='')
		{
			$contacts		= CreateObject('phpgwapi.contacts');
			$vendor 		= CreateObject('property.soactor');
			$vendor->role	= 'vendor';

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$input_type_array = array
			(
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox'
			);

			$m=0;
			for ($i=0;$i<count($values['attributes']);$i++)
			{
				$values['attributes'][$i]['datatype_text'] 	= $this->_translate_datatype($values['attributes'][$i]['datatype']);
				$values['attributes'][$i]['help_url']		= $values['attributes'][$i]['helpmsg'] ? $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'manual.uimanual.attrib_help', 'appname'=> $appname, 'location'=> $location, 'id' => $values['attributes'][$i]['id'])): '';
				if($values['attributes'][$i]['datatype']=='D')
				{
					if(!$view_only)
					{
						if ( !isset($GLOBALS['phpgw']->jscal) || !is_object($GLOBALS['phpgw']->jscal) )
						{
							$GLOBALS['phpgw']->jscal = createObject('phpgwapi.jscalendar');
						}

						$GLOBALS['phpgw']->jscal->add_listener('values_attribute_' . $i);
						$values['attributes'][$i]['img_cal']= $GLOBALS['phpgw']->common->image('phpgwapi','cal');
						$values['attributes'][$i]['lang_datetitle']= lang('Select date');
					}

					if(isset($values['attributes'][$i]['value']) && $values['attributes'][$i]['value'])
					{
						$timestamp_date= mktime(0,0,0,date('m',strtotime($values['attributes'][$i]['value'])),date('d',strtotime($values['attributes'][$i]['value'])),date('y',strtotime($values['attributes'][$i]['value'])));
						$values['attributes'][$i]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
					}
				}
				else if($values['attributes'][$i]['datatype']=='AB')
				{
					if($values['attributes'][$i]['value'])
					{
						$contact_data	= $contacts->read_single_entry($values['attributes'][$i]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$values['attributes'][$i]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='VENDOR')
				{
					if($values['attributes'][$i]['value'])
					{
						$vendor_data	= $vendor->read_single($values['attributes'][$i]['value'],array('attributes' => array(0 => array('column_name' => 'org_name'))));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['column_name'] == 'org_name')
							{
								$values['attributes'][$i]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='user')
				{
					if($values['attributes'][$i]['value'])
					{
						$values['attributes'][$i]['user_name']= $GLOBALS['phpgw']->accounts->id2name($values['attributes'][$i]['value']);
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->_appname.'.uilookup.phpgw_user', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='R' || $values['attributes'][$i]['datatype']=='CH' || $values['attributes'][$i]['datatype']=='LB')
				{
					$values['attributes'][$i]['choice']	= $this->read_attrib_choice($appname, $location,$values['attributes'][$i]['id']);
					$input_type=$input_type_array[$values['attributes'][$i]['datatype']];

					if($values['attributes'][$i]['datatype']=='CH')
					{
						$values['attributes'][$i]['value']=unserialize($values['attributes'][$i]['value']);

						if (isset($values['attributes'][$i]['choice']) AND is_array($values['attributes'][$i]['choice']))
						{
							foreach($values['attributes'][$i]['choice'] as &$choice)
							{
								$choice['input_type'] = $input_type;
								if(isset($values['attributes'][$i]['value']) && is_array($values['attributes'][$i]['value']))
								{
									foreach ($values['attributes'][$i]['value'] as &$selected)
									{
										if($selected == $choice['id'])
										{
											$choice['checked'] = 'checked';
										}
									}
								}
							}
						}
					}
					else
					{
						for ($j=0;$j<count($values['attributes'][$i]['choice']);$j++)
						{
							$values['attributes'][$i]['choice'][$j]['input_type']=$input_type;
							if($values['attributes'][$i]['choice'][$j]['id']==$values['attributes'][$i]['value'])
							{
								$values['attributes'][$i]['choice'][$j]['checked']='checked';
							}
						}
					}
				}
				else if (isset($entity['attributes'][$i]) && $entity['attributes'][$i]['datatype']!='I' && $entity['attributes'][$i]['value'])
				{
					$entity['attributes'][$i]['value'] = stripslashes($entity['attributes'][$i]['value']);
				}

				$values['attributes'][$i]['datatype_text'] = $this->_translate_datatype($values['attributes'][$i]['datatype']);
				$values['attributes'][$i]['counter']	= $i;
//				$values['attributes'][$i]['type_id']	= $data['type_id'];
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{ 
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$values['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
					$values['lookup_functions'] .= '{'."\r\n";
					$values['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
					$values['lookup_functions'] .= '}'."\r\n";
				}
			}

			if(isset($lookup_functions) && $lookup_functions)
			{
				$GLOBALS['phpgw']->session->appsession('insert_record_values' . $location,$appname,$insert_record_values);
			}

			return $values;
		}
	}
