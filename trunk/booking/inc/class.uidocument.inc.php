<?php
	phpgw::import_class('booking.uicommon');

	abstract class booking_uidocument extends booking_uicommon
	{
		protected
			$documentOwnerType = null,
			$module;
		
		public 
			$public_functions = array(
				'index'			=> true,
				'show'			=> true,
				'add'			=> true,
				'edit'			=> true,
				'download'		=> true,
				'delete'		=> true,
			);
		
		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->set_business_object();
			
			//'name' is not in fields as it will always be generated from the uploaded filename
			$this->fields = array('category', 'description', 'owner_id', 'owner_name');
			
			$this->module = 'booking';
		}
		
		protected function set_business_object(booking_bodocument $bo = null)
		{
			$this->bo = is_null($bo) ? $this->create_business_object() : $bo;
		}
		
		protected function create_business_object()
		{
			return CreateObject(sprintf('booking.bodocument_%s', $this->get_document_owner_type()));
		}
		
		protected function get_document_owner_type()
		{
			if (!$this->documentOwnerType) { $this->set_document_owner_type(); }
			return $this->documentOwnerType;
		}
		
		protected function set_document_owner_type($type = null)
		{
			if (is_null($type)) {
				$class = get_class($this);
				$r = new ReflectionObject($this);
				while(__CLASS__ != ($current_class = $r->getParentClass()->getName())) {
					$class =  $current_class;
					$r = $r->getParentClass();
				}
				$type = substr($class, 19);
			}
			
			$this->documentOwnerType = $type;
		}
		
		public function get_parent_url_link_params()
		{
			$inlineParams = $this->get_inline_params();
			return array('menuaction' => sprintf($this->module.'.ui%s.show', $this->get_document_owner_type()), 'id' => $inlineParams['filter_owner_id']);
		}
		
		public function redirect_to_parent_if_inline()
		{
			if ($this->is_inline())
			{
				$this->redirect($this->get_parent_url_link_params());
			}
			
			return false;
		}
		
		public function get_owner_typed_link_params($action, $params = array())
		{
			$action = sprintf($this->module.'.uidocument_%s.%s', $this->get_document_owner_type(), $action);
			return array_merge(array('menuaction' => $action), $this->apply_inline_params($params));
		}
		
		public function get_owner_typed_link($action, $params = array())
		{
			return $this->link($this->get_owner_typed_link_params($action, $params));
		}
		
		public function apply_inline_params(&$params)
		{
			if($this->is_inline()) {
				$params['filter_owner_id'] = intval(phpgw::get_var('filter_owner_id'));
			}
			return $params;
		}
		
		protected function get_parent_if_inline()
		{
			return $this->is_inline() ? $this->bo->read_parent($this->get_parent_id()) : null;
		}
		
		public function get_parent_id()
		{
			$inlineParams = $this->get_inline_params();
			return $inlineParams['filter_owner_id'];
		}
		
		public function get_inline_params()
		{
			return array('filter_owner_id' => intval(phpgw::get_var('filter_owner_id', 'any', false)));
		}
		
		public function is_inline()
		{
			return false != phpgw::get_var('filter_owner_id', 'any', false);
		}
		
		public static function generate_inline_link($documentOwnerType, $documentOwnerId, $action)
		{
			return self::link(array('menuaction' => sprintf('booking.uidocument_%s.%s', $documentOwnerType, $action), 'filter_owner_id' => $documentOwnerId));
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}

			$this->redirect_to_parent_if_inline();
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			// if($_SESSION['showall'])
			// {
			// 	$active_botton = lang('Show only active');
			// }else{
			// 	$active_botton = lang('Show all');
			// }
			
						
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							// array(
							// 	'type' => 'link',
							// 	'value' => $active_botton,
							// 	'href' => self::link(array('menuaction' => $this->get_owner_typed_link('active')))
							// ),
						)
					),
				),
				'datatable' => array(
					'source' => $this->get_owner_typed_link('index', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Document Name'),
							'formatter' => 'YAHOO.booking.formatLink',
						),
						array(
							'key' => 'owner_name',
							'label' => lang($this->get_document_owner_type()),
						),
						array(
							'key' => 'description',
							'label' => lang('Description'),
						),
						array(
							'key' => 'category',
							'label' => lang('Category'),
						),
						array(
							'key' => 'actions',
							'label' => lang('Actions'),
							'formatter' => 'YAHOO.booking.'.sprintf('formatGenericLink(\'%s\', \'%s\')', lang('edit'), lang('delete')),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New document'),
					'href' => $this->get_owner_typed_link('add')
			 	));
			}	
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$documents = $this->bo->read();
			foreach($documents['results'] as &$document)
			{
				$document['link'] = $this->get_owner_typed_link('download', array('id' => $document['id']));
				$document['category'] = lang(self::humanize($document['category']));
				#$document['active'] = $document['active'] ? lang('Active') : lang('Inactive');
				
				$document_actions = array();
				if ($this->bo->allow_write($document))  $document_actions[] = $this->get_owner_typed_link('edit', array('id' => $document['id']));
				if ($this->bo->allow_delete($document)) $document_actions[] = $this->get_owner_typed_link('delete', array('id' => $document['id']));
				
				$document['actions'] = $document_actions;
			}
			if (phpgw::get_var('no_images'))
			{
				$documents['results'] = array_filter($documents['results'], array($this, 'is_image'));

				// the array_filter function preserves the array keys. The javascript that later iterates over the resultset don't like gaps in the array keys
				// reindexing the results array solves the problem
				$doc_backup = $documents;
				unset($documents['results']);
				foreach($doc_backup['results'] as $doc)
				{
					$documents['results'][] = $doc;
				}
				$documents['total_records'] = count($documents['results']);
			}
			return $this->yui_results($documents);
		}

 		private function is_image($document)
		{
			return $document['is_image'] == false;
		}

		public function index_images()
		{
			$images = $this->bo->read_images();
			
			foreach($images['results'] as &$image) {
				$image['src'] = $this->get_owner_typed_link('download', array('id' => $image['id']));
			}
			
			return $this->yui_results($images);
		}
		
		protected function get_document_categories()
		{
			$types = array();
			foreach($this->bo->get_categories() as $type) { $types[$type] = self::humanize($type); }
			return $types;
		}
		
		protected function add_default_display_data(&$document_data)
		{
			$document_data['owner_pathway'] 	= $this->get_owner_pathway($document_data);
			$document_data['owner_type']  		= $this->get_document_owner_type();
			$document_data['owner_type_label'] 	= ucfirst($document_data['owner_type']);
			$document_data['inline'] 			= $this->is_inline();
			$document_data['document_types'] 	= $this->get_document_categories();
			$document_data['documents_link'] 	= $this->get_owner_typed_link('index');
			$document_data['cancel_link'] 		= $this->get_owner_typed_link('index');
		}
		
		public function show()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$document = $this->bo->read_single($id);
			$this->add_default_display_data($document);
			self::render_template('document', array('document' => $document));
		}
		
		public function add()
		{	
			$errors = array();
			$document = array();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = extract_values($_POST, $this->fields);	
				$document['files'] = $this->get_files();
				$errors = $this->bo->validate($document);
				if(!$errors)
				{
					echo "<pre>";print_r($document);print_r($errors);exit;
					try {

						$receipt = $this->bo->add($document);
						$this->redirect_to_parent_if_inline();
						$this->redirect($this->get_owner_typed_link_params('index'));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}

				}
			}
			
			self::add_javascript('booking', 'booking', 'document.js');
			
			$this->add_default_display_data($document);
			
			if (is_array($parentData = $this->get_parent_if_inline()))
			{
				$document['owner_id'] = $parentData['id'];
				$document['owner_name'] = $parentData['name'];
			}
			
			$this->flash_form_errors($errors);

			self::render_template('document_form', array('document' => $document));
		}
		
		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$document = $this->bo->read_single($id);
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = array_merge($document, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($document);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($document);	
						$this->redirect_to_parent_if_inline();
						$this->redirect($this->get_owner_typed_link_params('index'));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			
			self::add_javascript('booking', 'booking', 'document.js');
			
			$this->add_default_display_data($document);
			
			$this->flash_form_errors($errors);
			
			self::render_template('document_form', array('document' => $document));
		}
		
		public function download()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			
			$document = $this->bo->read_single($id);
			
			self::send_file($document['filename'], array('filename' => $document['name']));
		}
		
		public function delete()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$this->bo->delete($id);
			
			$this->redirect_to_parent_if_inline();
			$this->redirect($this->get_owner_typed_link_params('index'));
		}
		
		
		/**
		 * Implement to return the full hierarchical pathway to this documents owner(s).
		 *
		 * @param int $document_id
		 *
		 * @return array of url(s) to owner(s) in order of hierarchy.
		 */
		protected function get_owner_pathway(array $forDocumentData) { return array(); }
	}
