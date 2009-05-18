<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	class booking_uibuilding extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'active'		=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'schedule'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bobuilding');
			self::set_active_menu('booking::buildings');
			$this->fields = array('name', 'homepage', 'description', 'email', 'phone', 'address');
		}
		
		public function active()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}else{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
		}
		
		
		public function index()
		{
			
			
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New building'),
								'href' => self::link(array('menuaction' => 'booking.uibuilding.add'))
							),
							array(
								'type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'booking.uibuilding.active'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uibuilding.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Building'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'address',
							'label' => lang('Street'),
						),
						array(
							'key' => 'zip_code',
							'label' => lang('Zip code'),
						),
						array(
							'key' => 'city',
							'label' => lang('City'),
						),
						array(
							'key' => 'area',
							'label' => lang('Area'),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
						
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			
			$buildings = $this->bo->read();
			foreach($buildings['results'] as &$building)
			{
				$building['link'] = $this->link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($buildings);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = extract_values($_POST, $this->fields);
				$building['active'] = true;
				$errors = $this->bo->validate($building);
				if(!$errors)
				{
					$receipt = $this->bo->add($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
				}
			}
			$this->flash_form_errors($errors);
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			self::render_template('building_new', array('building' => $building));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$building = $this->bo->read_single($id);
			$building['id'] = $id;
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
			$building['top-nav-bar-buildings'] = lang('Buildings');
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = array_merge($building, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($building);
				if(!$errors)
				{
					$receipt = $this->bo->update($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
				}
			}
			$this->flash_form_errors($errors);
			self::render_template('building_edit', array('building' => $building));
		}
		
		public function show()
		{
			$this->check_active('booking.uibuilding.show');
			$building = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['edit_link'] = self::link(array('menuaction' => 'booking.uibuilding.edit', 'id' => $building['id']));
			$building['schedule_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id']));
			$building['add_document_link'] = booking_uidocument::generate_inline_link('building', $building['id'], 'add');
			$building['add_permission_link'] = booking_uipermission::generate_inline_link('building', $building['id'], 'add');
			self::render_template('building', array('building' => $building));
		}

		public function schedule()
		{
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), "booking.uibuilding");
			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building));
		}
	}
