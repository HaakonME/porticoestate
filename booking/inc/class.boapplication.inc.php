<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boapplication extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soapplication');
		}

		function send_notification($application, $created=false)
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
				return;
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_mail_subject'];
#			$subject = 'Melding fra Bergen kommune - AktivBy';

			$link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id='.$application['id'].'&secret='.$application['secret'];

			if ($created) {
				$body = "<pre>".$config->config_data['application_mail_created']."</pre>";
#				$body = '<p>Din søknad om leie/lån er mottatt.</p>';
#				$body .= '<p>Praktisk informasjon finner du i dokumenter knyttet til bygget, ref. juridiske betingelser pk.8 i søknad.</p>';
#				$body .= '<p>Klikk på linken under for å se på, redigere eller ha dialog med saksbehandler om din søknad.</p>';
				$body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';

			} elseif ($application['status'] == 'PENDING') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= "<pre>".$config->config_data['application_mail_pending']."</pre>";
#				$body = '<p>Din søknad i AktivBy? om leie/lån er '.lang($application['status']).'.<br />Saksbehandler trenger ytterligere informasjon, ber om at du klikker på linken under og gir nødvendig tilbakemeldinger slik at saken kan ferdigbehandles.</p>';
				$body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			} elseif ($application['status'] == 'ACCEPTED') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= '<pre>'.$config->config_data['application_mail_pending'].' <a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
#				$body = '<p>Din søknad i AktivBy om leie/lån er '.lang($application['status']).'.<br /> For å skrive ut en bekreftelse eller ha dialog med saksbehandler bruk <a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			} elseif ($application['status'] == 'REJECTED') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= '<pre>'.$config->config_data['application_mail_rejected'].'<a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
#				$body = '<p>Din søknad i AktivBy om leie/lån er '.lang($application['status']).'.<br />For ytterligere informasjon se <a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			}
			$body .= "<p>".$config->config_data['application_mail_signature']."</p>";
#			$body .= '<p>Med vennlig hilsen AktivBy - Bergen Kommune</p>';

			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, '', 'html');
			}
			catch (phpmailerException $e)
			{
				// TODO: Inform user if something goes wrong
			}
		}
		
		/**
		* Returns an array of application ids from applications assocciated with buildings
		* which the given user has access to
		*
		* @param int $user_id
		*/
		public function accessable_applications($user_id)
		{
			$applications = array();
			$this->db = & $GLOBALS['phpgw']->db;

			$sql = "select distinct ap.id
					from bb_application ap
					inner join bb_application_resource ar on ar.application_id = ap.id
					inner join bb_resource re on re.id = ar.resource_id
					inner join bb_building bu on bu.id = re.building_id
					inner join bb_permission pe on pe.object_id = bu.id and pe.object_type = 'building'
					where pe.subject_id = ".$user_id;
			$this->db->query($sql);
			$result = $this->db->resultSet;

			foreach($result as $r)
			{
				$applications[] = $r['id'];
			}

			return $applications;
		}

		public function read_dashboard_data($for_case_officer_id = null) {
			$params = $this->build_default_read_params();
			
			if (!isset($params['filters'])) $params['filters'] = array();
			$where_clauses = !isset($params['filters']['where']) ? array() : (array)$params['filters']['where'];
			
			if (!is_null($for_case_officer_id)) {
				$where_clauses[] = "(%%table%%.display_in_dashboard = 1 AND %%table%%.case_officer_id = ".intval($for_case_officer_id).')';
			}
			
			if ($building_id = phpgw::get_var('filter_building_id', 'int', 'GET', false)) {
				$where_clauses[] = "(%%table%%.id IN (SELECT DISTINCT a.id FROM bb_application a, bb_application_resource ar, bb_resource r WHERE ar.application_id = a.id AND ar.resource_id = r.id AND r.building_id = ".intval($building_id)."))";
			}
#			if ($type = phpgw::get_var('type') != 'not') {
#                    $params['filters']['type'] = phpgw::get_var('type');       
#            }

			if ($status = phpgw::get_var('status') != 'not') {
                    $params['filters']['status'] = phpgw::get_var('status');       
            }

			$params['filters']['where'] = $where_clauses;

			return $this->so->read($params);
		}

	}

	class booking_boapplication_association extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = new booking_soapplication_association();
		}
	}
