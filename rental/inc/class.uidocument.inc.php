<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.socontract');
	include_class('rental', 'document', 'inc/model/');

	class rental_uidocument extends rental_uicommon
	{	
		public $public_functions = array
		(
				'query'		=> true,
				'add'		=> true,
				'view'		=> true,
				'delete'	=> true
			);
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function query()
		{
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;
			
			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			if(isset($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
			}
			
			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'documents_for_contract':
					$filters = array('contract_id' => $contract_id, 'document_type' => phpgw::get_var('document_type'));
					break;
				case 'documents_for_party':
					$filters = array('party_id' => phpgw::get_var('party_id'), 'document_type' => phpgw::get_var('document_type'));
					break;
			}

			$result_objects = rental_sodocument::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_sodocument::get_instance()->get_count($search_for, $search_type, $filters);
			
			//Serialize the documents found
			$rows = array();
			foreach ($result_objects as $result) {
				if(isset($result))
				{
					if($result->has_permission(PHPGW_ACL_READ)) // check for read permission
					{
						$rows[] = $result->serialize();
					}
				}
			}
			
		
			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($type, isset($contract) ? $contract->serialize() : null, $this->type_of_user));
				
			
			//Build a YUI result from the data
			$result_data = array('results' => $rows, 'total_records' => $result_count);	
			return $this->yui_results($result_data, 'total_records', 'results');
		}
		
		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			//view/download
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.view', 'id' => $value['id'])));
			$value['labels'][] = lang('view_document');
			
			$type = $params[0];
			$serialized_contract = $params[1];
			$user_is = $params[2];
			
			// Get permissions on contract
			if(isset($serialized_contract))
			{
				$permissions = $serialized_contract['permissions'];
			}
			
			switch($type)
			{
				case 'documents_for_contract':
					if($permissions[PHPGW_ACL_EDIT]) {
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete', 'id' => $value['id'])));
						$value['labels'][] = lang('delete_document');
					}
					break;
				case 'documents_for_party':
					
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete', 'id' => $value['id'])));
						$value['labels'][] = lang('delete_document');	
					break;
			}
		}
		
		/**
		 * Public function to add a document.
		 * 
		 * @param HTTP::contract_id	the contract id
		 * @param HTTP::party_id	the party id
		 * @return unknown_type
		 */
		public function add()
		{	
			// Get target ids
			$contract_id = intval(phpgw::get_var('contract_id'));
			$party_id = intval(phpgw::get_var('party_id'));
			
			// Check permissions if contract id is set
			if(isset($contract_id) && $contract_id > 0)
			{
				//Load contract
				$contract = rental_socontract::get_instance()->get_single($contract_id);
				if(!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					$this->render('permission_denied.php');
					return;
				}
			}
			
			// Check permissions if party id is set
			if(isset($party_id) && $party_id > 0)
			{
				//Load party
				$party = rental_socontract::get_instance()->get_single($party_id);
				if(!($this->isAdministrator() || $this->isExecutiveOfficer()))
				{
					$this->render('permission_denied.php');
					return;
				}
			}
			
			// If no contract or party is loaded
			if(!(isset($party) || isset($contract)))
			{
				$this->render('permission_denied.php');
				return;
			}
			
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				//Create a document object
				$document = new rental_document();
				$document->set_title(phpgw::get_var('document_title'));
				$document->set_name($_FILES["file_path"]["name"]);
				$document->set_type_id(phpgw::get_var('document_type'));
				$document->set_contract_id($contract_id);
				$document->set_party_id($party_id);
				
				//Retrieve the document properties
				$document_properties = $this->get_type_and_id($document);
				
				// Move file from temporary storage to vfs
				$result = rental_sodocument::get_instance()->write_document_to_vfs
				(
					$document_properties['document_type'], 
					$_FILES["file_path"]["tmp_name"],
					$document_properties['id'],
					$_FILES["file_path"]["name"]
				);
				
				if($result)
				{
					if(rental_sodocument::get_instance()->store($document))
					{
						if(isset($party))
						{
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit', 'id' => $party->get_id(), 'tab' => 'documents'));		
						}
						else if(isset($contract))
						{
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'tab' => 'documents'));
						}
					}
					else
					{
						// Handle failure on storing document
						$this->redirect($document, $document_propeties,'','');
					}
				}
				else
				{
					//Handle vfs failure to store document
					$this->redirect($document, $document_propeties,'','');
				}
			}
		}
		
		/**
		 * Public function for viewing/downloading a document.
		 * 
		 * @param HTTP::id	the document id
		 * @return document on success, error message on failure
		 */
		public function view()
		{
			$document_id = intval(phpgw::get_var('id'));
			$document = rental_sodocument::get_instance()->get_single($document_id);
			if($document->has_permission(PHPGW_ACL_READ))
			{
				$document_properties = $this->get_type_and_id($document);
				
				header("Content-Disposition: attachment; filename={$document->get_name()}");
				header("Content-Type: $file_type");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				
				echo rental_sodocument::get_instance()->read_document_from_vfs
				(
					$document_properties['document_type'],	
					$document_properties['id'],
					$document->get_name()
				);
			}
			else
			{
				$this->redirect($document, $document_properties, lang('no_access'), '');
			}
			exit;
		}
		
		/**
		 * Public function for deleting a document. Deletes the document from
		 * the database and the virtual file system (vfs).
		 * 
		 * @param HTTP::id	the document id
		 * @return true if successful, false if error, permission denied message on
		 * 			not enough privileges
		 */
		public function delete()
		{	
			$document_id = intval(phpgw::get_var('id'));
			$document = rental_sodocument::get_instance()->get_single($document_id);
			$document_properties = $this->get_type_and_id($document);

			if(!$this->check_permissions($document,$document_properties))
			{
				$this->render('permission_denied.php');
				return;
			}
			
			$result = rental_sodocument::get_instance()->delete_document_from_vfs
			(
				$document_properties['document_type'],	
				$document_properties['id'],
				$document->get_name()
			);
			
			if($result)
			{
				return rental_sodocument::get_instance()->delete_document($document_id);
			} 
			// TODO: communicate error/message to user
			return false;
		}
		
		/**
		 * Utitity function for redirecting to correct edit mode (contract/party)
		 * 
		 * @param $document	the target document
		 * @param $document_properties	the document properies (name/value array)
		 * @param $error	an error message
		 * @param $message	a user message
		 */
		public function redirect($document, $document_properties, $error, $message)
		{
			if($document_properties['document_type'] == rental_sodocument::$CONTRACT_DOCUMENTS)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit', 'id' => $document_properties['id'], 'error' => $error, 'message' => $message));		
			}
			else if($document_properties['document_type'] == rental_sodocument::$PARTY_DOCUMENTS)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit', 'id' => $document_properties['id'], 'error' => $error, 'message' => $message));
			}
		}
		
		/**
		 * Utiity method for checking the users permission on this document. If the
		 * document is bound to a contract, then the user must have edit privileges
		 * on the given contract. If no contract, the user must be an executive 
		 * officer or an administrator.
		 * 
		 * @param $document	the document in question
		 * @param $document_properties	the document type and object id
		 * @return true if correct privileges, false otherwise
		 */
		private function check_permissions($document, $document_properties)
		{
			if($document_properties == rental_sodocument::$CONTRACT_DOCUMENTS)
			{
				$contract = rental_socontract::get_instance()->get_single($document_properties['id']);
				if(!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					return false;
				}
			} 
			else
			{
				if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
				{
					return false;
				}
			}
			return true;
		}
		
		/**
		 * Utility method for finding out whether a document is bound to a
		 * contract or a party.
		 * 
		 * @param $document	the given document
		 * @return name/value array ('document_type','id')
		 */
		private function get_type_and_id($document)
		{
			$document_type;
			$id;
			$contract_id = $document->get_contract_id();
			$party_id = $document->get_party_id();
			if(isset($contract_id) && $contract_id > 0)
			{
				$document_type = rental_sodocument::$CONTRACT_DOCUMENTS;
				$id = $contract_id;
			} 
			else if(isset($party_id) && $party_id > 0)
			{
				$document_type = rental_sodocument::$PARTY_DOCUMENTS;
				$id = $party_id;
			}
			return array
			(
				'document_type' => $document_type,
				'id' => $id
			);
		}
	}