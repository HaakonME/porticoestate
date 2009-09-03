<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'toggle_show_inactive'	=>	true,
		);

		protected $module = 'booking';
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation');
			//self::set_active_menu('booking::completed_reservation');
			$this->url_prefix = 'booking.uicompleted_reservation';
		}
		
		public function link_to($action, $params = array())
		{
			return $this->link($this->link_to_params($action, $params));
		}
		
		public function link_to_params($action, $params = array())
		{
			if (isset($params['ui'])) {
				$ui = $params['ui'];
				unset($params['ui']);
			} else {
				$ui = 'completed_reservation';
				//$this->apply_inline_params($params);
			}
			
			$action = sprintf($this->module.'.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'completed_reservation.js');
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'autocomplete', 
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building').':',
								'onItemSelect' => 'updateBuildingFilter',
								'onClearSelection' => 'clearBuildingFilter'
							),
							array('type' => 'autocomplete', 
								'name' => 'season',
								'ui' => 'season',
								'text' => lang('Season').':',
								'requestGenerator' => 'requestWithBuildingFilter',
							),
							array('type' => 'date-picker', 
								'name' => 'to',
								'text' => lang('To').':',
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search'),
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => $this->link_to('toggle_show_inactive'),
							),
						)
					),
				),
				'datatable' => array(
					'source' => $this->link_to('index', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'reservation_type',
							'label' => lang('Res. Type'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building'),
						),
						array(
							'key' => 'from_',
							'label' => lang('From'),
						),
						array(
							'key' => 'to_',
							'label' => lang('To'),
						),
						array(
							'key' => 'payee_type',
							'label' => lang('Cust. Type'),
						),
						array(
							'key' => 'payee_identifier',
							'label' => lang('Cust. #'),
							'sortable' => false,
						),
						array(
							'key' => 'cost',
							'label' => lang('Cost'),
						),
						array(
							'key' => 'exported',
							'label' => lang('Exported'),
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
			$reservations = $this->bo->read();
			array_walk($reservations["results"], array($this, "_add_links"), $this->module.".completed_reservation.show");
			foreach($reservations["results"] as &$reservation) {
				$reservation['exported'] = $reservation['exported'] === 1 ? 'Yes' : 'No';
				$reservation['reservation_type'] = array(
					'href' => $this->link_to('show', array('ui' => $reservation['reservation_type'], 'id' => $reservation['reservation_id'])),
					'label' => lang($reservation['reservation_type']),
				);
				$reservation['from_'] = substr($reservation['from_'], 0, -3);
				$reservation['to_'] = substr($reservation['to_'], 0, -3);
				$reservation['payee_type'] = lang($reservation['payee_type']);
				$reservation['payee_identifier'] = $this->bo->get_active_customer_identifier($reservation);
				$string_payee_identifier = (is_null(current($reservation['payee_identifier'])) ? 'N/A' : current($reservation['payee_identifier']));
				$reservation['payee_identifier'] = $string_payee_identifier;
			}
			
			$results = $this->yui_results($reservations);
			
			return $results;
		}
		
	}