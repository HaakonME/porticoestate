<?php
	class booking_menu
	{
		function get_menu()
		{
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'booking';
			$menus = array();

			$menus['navbar'] = array
			(
				'booking' => array
				(
					'text'	=> lang('Booking'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidashboard.index') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'dashboard' => array
				(
					'text'	=> lang('Dashboard'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidashboard.index') ),
				                    'image'	=> array('property', 'location'),
				),
				// 'applications' => array
				// (
				// 	'text'	=> lang('Applications'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.applications') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'applications' => array
				(
					'text'	=> lang('Applications'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiapplication.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array(
						'allocations' => array
						(
							'text'	=> lang('Allocations'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiallocation.index') ),
						                    'image'	=> array('property', 'location'),
						),
						'bookings' => array
						(
							'text'	=> lang('Bookings'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.index') ),
						                    'image'	=> array('property', 'location'),
						),
						'events' => array
						(
							'text'	=> lang('Events'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uievent.index') ),
						                    'image'	=> array('property', 'location'),
						),
					)
				),
				'buildings' => array
				(
					'text'	=> lang('Buildings'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibuilding.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array
					(
						'documents' => array
						(
							'text'	=> lang('Documents'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_building.index') ),
		                    'image'	=> array('property', 'documentation'),
						),
						'permissions' => array
						(
							'text'	=> lang('Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_building.index') ),
						),
						'resources' => array
						(
							'text'	=> lang('Resources'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiresource.index') ),
		                    'image'	=> array('property', 'location'),
							'children' => array
							(
								// 'equipment' => array
								// (
								// 	'text'	=> lang('Equipment'),
								// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiequipment.index') ),
								// 	'image'	=> array('property', 'location'),
								// ),
								'documents' => array
								(
									'text'	=> lang('Documents'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_resource.index') ),
				                    'image'	=> array('property', 'documentation'),
								),
								'permissions' => array
								(
									'text'	=> lang('Permissions'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_resource.index') ),
								),
							)
						),
						'seasons' => array
						(
							'text'	=> lang('Seasons'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiseason.index') ),
		                    'image'	=> array('property', 'location'),
							'children' => array
							(
								'permissions' => array
								(
									'text'	=> lang('Permissions'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_season.index') ),
								),
							),
						),
					)
				),
				// 'events' => array
				// (
				// 	'text'	=> lang('Events'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uievent.index') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'organizations' => array
				(
					'text'	=> lang('Organizations'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiorganization.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array
					(
						'groups' => array
						(
							'text'	=> lang('Groups'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uigroup.index') ),
		                    'image'	=> array('property', 'location'),
						)
					)
				),
				// 'costs' => array
				// (
				// 	'text'	=> lang('Costs'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicost.index') ),
				//                     'image'	=> array('property', 'location'),
				// )
				'settings' => array
				(
					'text'	=> lang('Settings'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking')),
					'children' => array
					(
						'permissions'	=> array
						(
							'text'	=> lang('Root Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking') )
						),
						'activity'	=> array
						(
							'text'	=> lang('Activity'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index', 'appname' => 'booking') )
						),
						'audience'	=> array
						(
							'text'	=> lang('Audience'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index', 'appname' => 'booking') )
						),
						'agegroup'	=> array
						(
							'text'	=> lang('Age group'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index', 'appname' => 'booking') )
						)
					)
				),
			);
			$menus['admin'] = array
			(
				'permissions'	=> array
				(
					'text'	=> lang('Root Permissions'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking') )
				),
				'activity'	=> array
				(
					'text'	=> lang('Activity'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index', 'appname' => 'booking') )
				),
				'audience'	=> array
				(
					'text'	=> lang('Audience'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index', 'appname' => 'booking') )
				),
				'agegroup'	=> array
				(
					'text'	=> lang('Agegroup'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index', 'appname' => 'booking') )
				)
			);
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			return $menus;
		}
	}
