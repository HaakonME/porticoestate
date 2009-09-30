<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.socontract');

include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');

class rental_uiprice_item extends rental_uicommon
{
	public $public_functions = array
	(
			'add' => true,
			'index' => true,
			'query' => true,
			'view'		=> true,
			'edit'		=> true,
			'set_value' => true
	);

	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('admin::rental::contract_type_list');
	}

	public function index()
	{
		if(!$this->isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}
		$this->render('admin_price_item_list.php');
	}

	/*
	 * View the price item with the id given in the http variable 'id'
	 */
	public function view()
	{
		if(!self::isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}
		$id = (int)phpgw::get_var('id');
		$price_item = rental_price_item::get($id);
		return $this->viewedit(false, $price_item);
	}

	/*
	 * Edit the price item with the id given in the http variable 'id'
	 */
	public function edit()
	{
		if(!self::isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}

		$id = (int)phpgw::get_var('id');
		$price_item = rental_price_item::get($id);

		// Save the price item if it was posted
		if(isset($_POST['save']))
		{
			$price_item->set_title(phpgw::get_var('title'));
			$price_item->set_agresso_id(phpgw::get_var('agresso_id'));
			$price_item->set_is_area(phpgw::get_var('is_area') == 'true' ? true : false);
			$price_item->set_price(phpgw::get_var('price'));
			if ($price_item->store()) {
				return $this->viewedit(true, $price_item, lang('messages_saved_form'));
			} else {
				return $this->viewedit(true, $price_item, '', lang('messages_form_error'));
			}

		}

		return $this->viewedit(true, $price_item);
	}

	/*
	 * Add a new price item to the database.  Requires only a title.
	 */
	public function add()
	{
		if(!self::isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}
			
		$title = phpgw::get_var('price_item_title');
		if ($title) {
			$price_item = new rental_price_item();
			$price_item->set_title($title);
			if ($price_item->store()) {
				// The object was stored, forward to edit it further
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiprice_item.edit', 'id' => $price_item->get_id()));
			}
		}

		return $this->index();
	}

	public function set_value()
	{
		if(!self::isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}

		$field = phpgw::get_var('field');
		$value = phpgw::get_var('value');
		$id = phpgw::get_var('id');

		$price_item = rental_contract_price_item::get($id);
		$price_item->set_field($field, $value);

		$price_item->store();
		print_r($price_item);

		//			print_r($price_item);
	}

	/**
	 * View or edit rental price_item
	 *
	 * @param $editable true renders fields editable, false renders fields disabled
	 * @param $price_item the price item to display
	 */
	protected function viewedit($editable, $price_item, $message = '', $error = '')
	{
		$data = array
		(
				'price_item' 	=> $price_item,
				'editable' => $editable,
				'message' => $message,
				'error' => $error,
				'cancel_link' => self::link(array('menuaction' => 'rental.uiprice_item.index'))
		);
		$this->render('admin_price_item.php', $data);
	}
	/**
	 * (non-PHPdoc)
	 * @see rental/inc/rental_uicommon#query()
	 */
	public function query()
	{
		//Create an empty result set
		$records = array();
		
		//Retrieve a contract identifier and load corresponding contract
		$contract_id = phpgw::get_var('contract_id');
		if(isset($contract_id))
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
		}
		
		//Retrieve the type of query and perform type specific logic
		$type = phpgw::get_var('type');
		switch($type)
		{
			case 'included_price_items':
				if(isset($contract))
				{
					$filters = array('contract_id' => $contract->get_id());
					$result_objects = rental_socontract_price_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socontract_price_item::get_instance()->get_count($search_for, $search_type, $filters);
				}
				break;
			case 'not_included_price_items': // We want to show price items in the source list even after they've been added to a contract
			default:
				$result_objects = rental_price_item::get_all(
				phpgw::get_var('startIndex'),
				phpgw::get_var('results'),
				phpgw::get_var('sort'),
				phpgw::get_var('dir'),
				phpgw::get_var('query'),
				phpgw::get_var('search_option')
				);
				$object_count = count($result_objects); // TODO: Fix
				break;
		}

		// Create an empty row set
		$rows = array();
		foreach ($result_objects as $record) {
			if(isset($record))
			{
				if($record->has_permission(PHPGW_ACL_READ))
				{
					// ... add a serialized record if read permission
					$rows[] = $record->serialize();
		
				}
			}
		}
		$data = array('results' => $rows, 'total_records' => $object_count);

		$editable = phpgw::get_var('editable') == 'true' ? true : false;

		//Add action column to each row in result table
		array_walk(
			$data['results'], 
			array($this, 'add_actions'), 
			array(
				phpgw::get_var('id'),
				$type,
				$editable,
				$this->type_of_user,
				isset($contract) ? $contract->serialize() : null
			)
		);
		return $this->yui_results($data, 'total_records', 'results');
	}

	/**
	 * Add action links and labels for the context menu of the list items
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [price_item.id, type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{

		$value['actions'] = array();
		$value['labels'] = array();

		// Get parameters
		$contract_id = $params[0];
		$type = $params[1];
		$editable = $params[2];
		$user_is = $params[3];
		$serialized_contract= $params[4];
		
		// Get permissions on contract
		if(isset($serialized_contract))
		{
			$permissions = $serialized_contract['permissions'];
		}

		// Depending on the type of query: set an ajax flag and define the action and label for each row
		switch($type)
		{
			case 'included_price_items':
				if($permissions[PHPGW_ACL_EDIT] && ($editable == true))
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_price_item', 'price_item_id' => $value['id'], 'contract_id' => $serialized_contract['id'])));
					$value['labels'][] = lang('remove');

					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.reset_price_item', 'price_item_id' => $value['id'], 'contract_id' => $serialized_contract['id'])));
					$value['labels'][] = lang('reset');
				}
				break;
			case 'not_included_price_items':
				if($permissions[PHPGW_ACL_EDIT] && ($editable == true))
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_price_item', 'price_item_id' => $value['id'], 'contract_id' => $serialized_contract['id'])));
					$value['labels'][] = lang('add');
				}
				break;
			default:
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');

				if($user_is[ADMINISTRATOR])
				{
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');
				}
		}
	}
}