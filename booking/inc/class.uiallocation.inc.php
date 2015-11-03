<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.boorganization');
    
        phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');
	
//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uiallocation extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
            'query'         =>  true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'delete'			=>	true,
			'info'			=>	true,
			'toggle_show_inactive'	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boallocation');
			$this->organization_bo    = CreateObject('booking.boorganization');
			$this->building_bo    = CreateObject('booking.bobuilding');
			$this->season_bo    = CreateObject('booking.boseason');
			$this->resource_bo = CreateObject('booking.boresource');
			self::set_active_menu('booking::applications::allocations');
			$this->fields = array('resources', 'cost', 'application_id',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'organization_id', 'organization_name', 
			                      'organization_shortname', 'from_', 'to_', 'active');
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('booking', 'booking', 'allocation_list.js');
//			self::add_javascript('booking', 'booking', 'datatable.js');
//			phpgwapi_yui::load_widget('datatable');
//			phpgwapi_yui::load_widget('paginator');
                        phpgwapi_jquery::load_widget('menu');
                        phpgwapi_jquery::load_widget('autocomplete');
                        $build_id = phpgw::get_var('buildings', 'int', 'REQUEST', null);
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
                                                                'depends' => 'building',
								'requestGenerator' => 'requestWithBuildingFilter',
							),
							array('type' => 'filter', 
								'name' => 'organizations',
                                                                'text' => lang('Organization').':',
                                                                'list' => $this->bo->so->get_organizations(),
							),
#							array('type' => 'filter', 
#								'name' => 'buildings',
#                                'text' => lang('Building').':',
#                                'list' => $this->bo->so->get_buildings(),
#								'onItemSelect' => 'updateBuildingFilter',
#								'onClearSelection' => 'clearBuildingFilter'
#							),
#							array('type' => 'filter', 
#								'name' => 'seasons',
#                                'text' => lang('Season').':',
#                                'list' => $this->bo->so->get_seasons($build_id),
#								'requestGenerator' => 'requestWithBuildingFilter',
#							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiallocation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'organization_name',
							'label' => lang('Organization'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'organization_shortname',
							'label' => lang('Organization shortname')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'season_name',
							'label' => lang('Season')
						),
						array(
							'key' => 'from_',
							'label' => lang('From')
						),
						array(
							'key' => 'to_',
							'label' => lang('To')
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
					'value' => lang('New allocation'),
					'href' => self::link(array('menuaction' => 'booking.uiallocation.add'))
				));
			}
			$data['filters'] = $this->export_filters;
                        self::render_template_xsl('datatable_jquery',$data);
//			self::render_template('datatable', $data);
		}

        public function query()
		{
			if(isset($_SESSION['showall']))
			{
                            unset($filters['building_name']);
                            unset($filters['organization_id']);
                            unset($filters['season_id']);
			} else {
                $testdata =  phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
                if ($testdata != 0) {
                    $filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('filter_building_id', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['building_name']);                
                }
                $testdata2 =  phpgw::get_var('organizations', 'int', 'REQUEST', null);
                if ($testdata2 != 0) {
                    $filters['organization_id'] = $this->bo->so->get_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['organization_id']);
                }
                $testdata3 =  phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
                if ($testdata3 != 0) {
                    $filters['season_id'] = $this->bo->so->get_season(phpgw::get_var('filter_season_id', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['season_id']);                
                }
            }
           
            $search = phpgw::get_var('search');
            $order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
                                'query' => $search['value'],
                                'order' => $columns[$order[0]['column']]['data'],
				'sort'	=> $columns[$order[0]['column']]['data'],
                                'dir'	=> $order[0]['dir'],
				'filters' => $filters,
			);
            
			$allocations = $this->bo->so->read($params);
			array_walk($allocations["results"], array($this, "_add_links"), "booking.uiallocation.show");

			foreach($allocations['results'] as &$allocation)
			{
				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);
			}
            
			return $this->jquery_results($allocations);
		}
        
		public function index_json()
		{
			if(isset($_SESSION['showall']))
			{
        		unset($filters['building_name']);
                unset($filters['organization_id']);
                unset($filters['season_id']);
			} else {
                $testdata =  phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
                if ($testdata != 0) {
                    $filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('filter_building_id', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['building_name']);                
                }
                $testdata2 =  phpgw::get_var('organizations', 'int', 'REQUEST', null);
                if ($testdata2 != 0) {
                    $filters['organization_id'] = $this->bo->so->get_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['organization_id']);
                }
                $testdata3 =  phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
                if ($testdata3 != 0) {
                    $filters['season_id'] = $this->bo->so->get_season(phpgw::get_var('filter_season_id', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['season_id']);                
                }
            }
            
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);

			$allocations = $this->bo->so->read($params);
			array_walk($allocations["results"], array($this, "_add_links"), "booking.uiallocation.show");

			foreach($allocations['results'] as &$allocation)
			{
				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);
			}

			return $this->yui_results($allocations);
		}

		public function add()
		{
			$errors = array();
			$step = phpgw::get_var('step', 'str', 'POST');
			if (! isset($step)) $step = 1;
			$invalid_dates = array();
			$valid_dates = array();

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$season = $this->season_bo->read_single($_POST['season_id']);
				array_set_default($_POST, 'resources', array());
				$allocation = extract_values($_POST, $this->fields);
				$allocation['active'] = '1';
				$allocation['completed'] = '0';

				if (phpgw::get_var('weekday', 'str', 'GET') != '')
				{
					$from_date = phpgw::get_var('from_', 'str', 'GET');
					$to_date = phpgw::get_var('to_', 'str', 'GET');
					$weekday = phpgw::get_var('weekday', 'str', 'GET');
					$datef = explode(' ',$from_date[0]);
					$timef = $_POST['from_'];
					$datet = explode(' ',$to_date[0]);
					$timet = $_POST['to_'];

					if (strlen($_POST['from_']) < 14)  {
						$allocation['from_'] = $datef[0]." ".$timef;
						$allocation['to_'] = $datet[0]." ".$timet;
						$from_date = $allocation['from_'];
						$to_date = $allocation['to_'];
					} else {
						$allocation['from_'] = $_POST['from_'];
						$allocation['to_'] = $_POST['to_'];
						$from_date = $allocation['from_'];
						$to_date = $allocation['to_'];
					}
				} else {
					$from_date = $_POST['from_'];
					$to_date = $_POST['to_'];
					$weekday = $_POST['weekday'];

					$allocation['from_'] = strftime("%Y-%m-%d %H:%M", strtotime($_POST['weekday']." ".$_POST['from_']));
					$allocation['to_'] = strftime("%Y-%m-%d %H:%M", strtotime($_POST['weekday']." ".$_POST['to_']));
				}


				if (($_POST['weekday'] != 'sunday' &&  date('w')  > date('w',strtotime($_POST['weekday']))) || (date('w') == 'sunday' &&  date('w') < date('w',strtotime($_POST['weekday'])))) {
					if(phpgw::get_var('weekday', 'str', 'GET') == ''){
						$allocation['from_'] = strftime("%Y-%m-%d %H:%M", strtotime($_POST['weekday']." ".$_POST['from_'])-60*60*24*7);
						$allocation['to_'] = strftime("%Y-%m-%d %H:%M", strtotime($_POST['weekday']." ".$_POST['to_'])-60*60*24*7);
					}
				} 
                                $_POST['from_'] = $allocation['from_'];
				$_POST['to_'] = $allocation['to_'];

				$errors = $this->bo->validate($allocation);

				if (!$errors)
				{
					$step++;
				}
				if (!$errors && $_POST['outseason'] != 'on' )
				{
					try {
						$receipt = $this->bo->add($allocation);
                                                $this->bo->so->update_id_string();
						$this->redirect(array('menuaction' => 'booking.uiallocation.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
				else if ($_POST['outseason'] == 'on' && !$errors && $step > 1)
				{

					$repeat_until = strtotime($season['to_'])+60*60*24; 
					$_POST['repeat_until'] = $season['to_'];

					$max_dato = strtotime($_POST['to_']); // highest date from input
					$interval = $_POST['field_interval']*60*60*24*7; // weeks in seconds
					$i = 0;
					// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
					// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.
					while (($max_dato+($interval*$i)) <= $repeat_until)
					{
						$fromdate = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval*$i));
						$todate = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval*$i));
						$allocation['from_'] = $fromdate;
						$allocation['to_'] = $todate;
						$err = $this->bo->validate($allocation);
						if ($err) 
						{
							$invalid_dates[$i]['from_'] = $fromdate;
							$invalid_dates[$i]['to_'] = $todate;
						} 
						else 
						{
							$valid_dates[$i]['from_'] = $fromdate;
							$valid_dates[$i]['to_'] = $todate;
							if ($step == 3)
							{
								try {
									$receipt = $this->bo->add($allocation);
								} catch (booking_unauthorized_exception $e) {
									$errors['global'] = lang('Could not add object due to insufficient permissions');
								}
							}
						}
						$i++;
					}
					if ($step == 3) 
					{
                                                $this->bo->so->update_id_string();
						$this->redirect(array('menuaction' => 'booking.uiallocation.show', 'id'=>$receipt['id']));
					}
				}
			}
			if(phpgw::get_var('building_name', 'GET') == '')
			{			
				array_set_default($allocation, 'resources', array());
				$weekday =  'monday';
			}
			else 
			{
				$dateTimeFrom = phpgw::get_var('from_', 'GET');
				$dateTimeTo = phpgw::get_var('to_', 'GET');                                
				$dateTimeFromE = explode(" ", $dateTimeFrom[0]);
				$dateTimeToE = explode(" ", $dateTimeTo[0]);
				if (phpgw::get_var('from_', 'GET') < 14) {
					$timeFrom[] = phpgw::get_var('from_', 'GET');
					$timeTo[] = phpgw::get_var('to_', 'GET');
				}else {
					$timeFrom[] = $dateTimeFromE[1];
					$timeTo[] = $dateTimeToE[1];
				}

				array_set_default($allocation, 'resources', array(get_var('resource', int, 'GET')));
				array_set_default($allocation, 'building_id', phpgw::get_var('building_id', 'GET'));
				array_set_default($allocation, 'building_name', phpgw::get_var('building_name', 'GET'));
				array_set_default($allocation, 'from_', $timeFrom);
				array_set_default($allocation, 'to_', $timeTo);
				$weekday =  phpgw::get_var('weekday', 'GET');
			}

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'allocation.js');
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			array_set_default($allocation, 'cost', '0');

			$GLOBALS['phpgw']->jqcal->add_listener('field_from', 'time');
			$GLOBALS['phpgw']->jqcal->add_listener('field_to', 'time');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Allocation New'), 'link' => '#allocation_new');
			$active_tab = 'generic';

			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$allocation['validator'] = phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'));
            
			if ($step < 2) 
			{
				if($_SERVER['REQUEST_METHOD'] == 'POST' && $errors) {				
					$allocation['from_'] = strftime("%H:%M", strtotime($_POST['weekday']." ".$_POST['from_']));
					$allocation['to_'] = strftime("%H:%M", strtotime($_POST['weekday']." ".$_POST['to_']));
				}
				self::render_template_xsl('allocation_new', array('allocation' => $allocation,
					'step' => $step, 
					'interval' => $_POST['field_interval'],
					'repeat_until' => $_POST['repeat_until'],
					'outseason' => $_POST['outseason'],
					'weekday' => $weekday,
				));
			} 
			else if ($step == 2) 
			{
				self::render_template_xsl('allocation_new_preview', array('allocation' => $allocation,
					'step' => $step,
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'interval' => $_POST['field_interval'],
					'repeat_until' => $_POST['repeat_until'],
					'weekday' => $weekday,
					'from_date' => $from_date,
					'to_date' => $to_date,
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates
				));
			}
		}

		private function send_mailnotification_to_organization($organization, $subject, $body)
		{
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0) 
			{
				return false;
			}

			foreach($organization['contacts'] as $contact) 
			{
				if (strlen($contact['email']) > 0) 
				{
					try
					{
						$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, '', 'plain');
					}
					catch (phpmailerException $e)
					{
					}
				}
			}
		}

		public function edit()
		{
                    
			$id = intval(phpgw::get_var('id', 'GET'));
			$allocation = $this->bo->read_single($id);
			$allocation['building'] = $this->building_bo->so->read_single($allocation['building_id']);
			$allocation['building_name'] = $allocation['building']['name'];
			$errors = array();
			$tabs = array();
			$tabs['generic']	= array('label' => lang('Allocations Edit'), 'link' => '#allocations_edit');
			$active_tab = 'generic';
            
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
				$_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
				array_set_default($_POST, 'resources', array());
				$allocation = array_merge($allocation, extract_values($_POST, $this->fields));
				$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'POST')));
                                
                                
                                
				$errors = $this->bo->validate($allocation);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($allocation);
						$this->bo->so->update_id_string();
						$this->send_mailnotification_to_organization($organization, lang('Allocation changed'), phpgw::get_var('mail', 'POST'));
						$this->redirect(array('menuaction' => 'booking.uiallocation.show', 'id'=>$allocation['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$allocation['from_'] = pretty_timestamp($allocation['from_']);
			$allocation['to_'] = pretty_timestamp($allocation['to_']);

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'allocation.js');
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.show', 'id' => $allocation['id']));
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id' => $allocation['application_id']));
			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$allocation['validator'] = phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'));
			$GLOBALS['phpgw']->jqcal->add_listener('field_from', 'datetime');
			$GLOBALS['phpgw']->jqcal->add_listener('field_to', 'datetime');

			self::render_template_xsl('allocation_edit', array('allocation' => $allocation));
		}

		public function delete()
		{
			$id = intval(phpgw::get_var('allocation_id', 'GET'));
			$outseason = phpgw::get_var('outseason', 'GET');
			$recurring = phpgw::get_var('recurring', 'GET');
			$repeat_until = phpgw::get_var('repeat_until', 'GET');
			$field_interval = intval(phpgw::get_var('field_interval', 'GET'));
			$allocation = $this->bo->read_single($id);
                        $season = $this->season_bo->read_single($allocation['season_id']);
			$step = phpgw::get_var('step', 'str', 'POST');
                        if (! isset($step)) $step = 1;
                        $errors = array();
			$invalid_dates = array();
			$valid_dates = array();

			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
                            
                            $_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
                            $_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
                            $_POST['repeat_until'] = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']));
                            
				$from_date = $_POST['from_'];
				$to_date = $_POST['to_'];

  				if ($_POST['recurring'] != 'on' && $_POST['outseason'] != 'on' )
                                {

                                    $err  = $this->bo->so->check_for_booking($id);
                                    if ($err)
                                    {
                                            $errors['booking'] = lang('Could not delete allocation due to a booking still use it');
                                    }
                                    else
                                    {
                                        $err = $this->bo->so->delete_allocation($id);
                                        $this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id'=>$allocation['building_id']));
                                    }
                                } 
                                else
                                { 
                                        $step++;
                                        if ($_POST['recurring'] == 'on') {
                                                $repeat_until = strtotime($_POST['repeat_until'])+60*60*24; 
                                        } 
                                        else
                                        {
                                                $repeat_until = strtotime($season['to_'])+60*60*24; 
                                                $_POST['repeat_until'] = $season['to_'];
                                        } 

                                        $max_dato = strtotime($_POST['to_']); // highest date from input
                                        $interval = $_POST['field_interval']*60*60*24*7; // weeks in seconds
                                        $i = 0;
                                        // calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
                                        // the form from step 1 should validate and if we encounter any errors they are caused by double bookings.

                                        while (($max_dato+($interval*$i)) <= $repeat_until)
                                        {
                                                $fromdate = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval*$i));
                                                $todate = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval*$i));
                                                $allocation['from_'] = $fromdate;
                                                $allocation['to_'] = $todate;                                                
                                                $fromdate = pretty_timestamp($fromdate);
                                                $todate = pretty_timestamp($todate);

                                                $id = $this->bo->so->get_allocation_id($allocation);
                                                if ($id)
                                                {
                                                   $err  = $this->bo->so->check_for_booking($id);
                                                }
                                                else 
                                                {
                                                   $err = true;
                                                }

                                                if ($err) 
                                                {
                                                        $invalid_dates[$i]['from_'] = $fromdate;
                                                        $invalid_dates[$i]['to_'] = $todate;
                                                } 
                                                else 
                                                {
                                                        $valid_dates[$i]['from_'] = $fromdate;
                                                        $valid_dates[$i]['to_'] = $todate;
                                                        if ($step == 3)
                                                        {
                                                            $stat = $this->bo->so->delete_allocation($id);
                                                        }                            
                                                }
                                                $i++;
                                        }
                                        if ($step == 3) 
                                        {
                                                $this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id'=>$allocation['building_id']));
                                        }
                                }
			}
                        
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'allocation.js');
                        
                        $allocation['from_'] = pretty_timestamp($allocation['from_']);
                        $allocation['to_'] = pretty_timestamp($allocation['to_']);
                        
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.show', 'id' => $allocation['id']));
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id' => $allocation['application_id']));
                        
                        $tabs = array();
                        $tabs['generic'] = array('label' => lang('Allocation Delete'), 'link' => '#allocation_delete');
                        $active_tab = 'generic';
                        $allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
                        
                        $GLOBALS['phpgw']->jqcal->add_listener('field_repeat_until', 'date');
                        
			if ($step < 2) 
                        {
                                self::render_template('allocation_delete', array('allocation' => $allocation,
                                                'recurring' => $recurring,
                                                'outseason' => $outseason,
                                                'interval' => $field_interval,
                                                'repeat_until' => $repeat_until,
                                ));
                        }
			elseif ($step == 2) 
                        {
				self::render_template('allocation_delete_preview', array('allocation' => $allocation,
					'step' => $step,
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'interval' => $_POST['field_interval'],
					'repeat_until' => pretty_timestamp($_POST['repeat_until']),
					'from_date' => pretty_timestamp($from_date),
					'to_date' => pretty_timestamp($to_date),
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates
				));
                        }                
		}
		
		public function show()
		{
			$allocation = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$allocation['allocations_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			$allocation['delete_link'] = self::link(array('menuaction' => 'booking.uiallocation.delete', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_'], 'resource'=>$allocation['resource']));
			$allocation['edit_link'] = self::link(array('menuaction' => 'booking.uiallocation.edit', 'id' => $allocation['id']));
            
            $tabs = array();
			$tabs['generic']	= array('label' => lang('Allocations'), 'link' => '#allocations');
			$active_tab = 'generic';
            
			$resource_ids = '';
			foreach($allocation['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$allocation['resource_ids'] = $resource_ids;
            $allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
//            self::render_template_xsl('datatable_jquery',$data);
			self::render_template_xsl('allocation', array('allocation' => $allocation));
		}
		public function info()
		{
			$allocation = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
			$resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$allocation['resources']), 'sort'=>'name'));
			$allocation['resources'] = $resources['results'];
			$res_names = array();
			foreach($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$allocation['resource'] = phpgw::get_var('resource', 'GET');
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $allocation['resources'][0]['building_id']));
			$allocation['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $allocation['organization_id']));
			$allocation['delete_link'] = self::link(array('menuaction' => 'booking.uiallocation.delete', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_'], 'resource'=>$allocation['resource']));
			$allocation['add_link'] = self::link(array('menuaction' => 'booking.uibooking.add', 'allocation_id'=>$allocation['id'], 'from_'=>$allocation['from_'], 'to_'=>$allocation['to_'], 'resource'=>$allocation['resource']));
			$allocation['when'] = pretty_timestamp($allocation['from_']).' - '.pretty_timestamp($allocation['to_']);
			self::render_template('allocation_info', array('allocation'=>$allocation));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}

	}
