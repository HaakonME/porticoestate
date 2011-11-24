<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socheck_list');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	
	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;
				
		public $public_functions = array
		(
			'index'	=>	true,
			'view_check_lists_for_control'		=>	true,
			'save_check_list'					=>	true,
			'view_check_list'					=>	true,
			'edit_check_list'					=>	true,
			'save_check_items'					=>	true,
			'get_check_list_info'				=>	true,
			'control_calendar_status_overview'	=>	true
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');
			
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::check_list";
		}
		
		public function index()
		{
/*			$check_list_array = $this->so->get_check_list();
			
			$data = array
			(
				'check_list_array'	=> $check_list_array
			);
			
			self::render_template_xsl('control_check_lists', $data);
			*/
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter', 
								'name' => 'status',
                                'text' => lang('Status'),
                                'list' => array(
                                    array(
                                        'id' => 'none',
                                        'name' => lang('Not selected')
                                    ), 
                                    array(
                                        'id' => 'NEW',
                                        'name' => lang('NEW')
                                    ), 
                                    array(
                                        'id' => 'PENDING',
                                        'name' =>  lang('PENDING')
                                    ), 
                                    array(
                                        'id' => 'REJECTED',
                                        'name' => lang('REJECTED')
                                    ), 
                                    array(
                                        'id' => 'ACCEPTED',
                                        'name' => lang('ACCEPTED')
                                    )
                                )
                            ),
							array('type' => 'text', 
                                'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}
		
		public function view_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_items($check_list_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$data = array
			(
				'check_list' => $check_list,
				'date_format' => $date_format
			);
			
			self::render_template_xsl('view_check_list', $data);
		}
		
		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_items($check_list_id);
			
			return json_encode( $check_list );
		}
		
		public function edit_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_items($check_list_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$data = array
			(
				'check_list' 	=> $check_list,
				'date_format' 	=> $date_format
			);
			
			self::render_template_xsl('edit_check_list', $data);
		}
		
		public function control_calendar_status_overview()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		
			$check_list_array = $this->so->get_check_lists_for_control( $control_id );	
			
			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);
			
			self::render_template_xsl('control_calendar_status_overview', $data);
		}
		
		
		public function view_check_lists_for_control()
		{
			$control_id = phpgw::get_var('id');
			$control = $this->so_control->get_single($control_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		
			$check_list_array = $this->so->get_check_lists_for_control( $control_id );	
			
			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);
			
			self::render_template_xsl('view_check_lists', $data);
		}
		
		public function view_control_items_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
						
			$control_groups_array = $this->so_control_group_list->get_control_groups_by_control_id( $control_id );

			$saved_groups_with_items_array = array();
			
			foreach ($control_groups_array as $control_group)
			{	
				$control_group_id = $control_group->get_id();
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}	
		
			$data = array
			(
				'control_as_array'				=> $control->toArray(),
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
								
			self::render_template_xsl('view_check_list', $data);
		}
		
		public function save_check_items(){
			$check_item_ids = phpgw::get_var('check_item_ids');
			$check_list_id = phpgw::get_var('check_list_id');
			
			foreach($check_item_ids as $check_item_id){
				$status = phpgw::get_var('status_' . $check_item_id);
				$comment = phpgw::get_var('comment_' . $check_item_id);
				
				$check_item = $this->so_check_item->get_single($check_item_id);
				
				$check_item->set_status( $status );
				$check_item->set_comment( $comment );
				
				$this->so_check_item->store( $check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list', 'check_list_id'=>$check_list_id));	
		}
		
		public function save_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = "FALSE";
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = 0;
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));	
		}
		
		public function make_check_list_for_control(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = true;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = true;
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));	
		}
		
		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);
			
			$search_for = phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
/*			$control_id = phpgw::get_var('control_id');
			if(isset($control_id))
			{
				$control = $this->so->get_single($control_id);
			}
*/
			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);
								
			$results = array();
			
			foreach($result_objects as $check_list_obj)
			{
				$results['results'][] = $check_list_obj->serialize();	
			}
			
			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicheck_list.view_check_lists_for_control");

			return $this->yui_results($results);
		}
	}
