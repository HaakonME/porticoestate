<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socompleted_reservation_export extends booking_socommon
	{
		protected 
			$file_storage,
			$completed_reservation_so,
			$completed_reservation_bo;
			
		const TEMP_FILE_NAME = '##TEMP##';
		
		function __construct()
		{
			$this->file_storage = CreateObject('booking.filestorage', $this);
			$this->completed_reservation_so = CreateObject('booking.socompleted_reservation');
			$this->completed_reservation_bo = CreateObject('booking.bocompleted_reservation');
			
			parent::__construct('bb_completed_reservation_export', 
				array(
					'id' 						=> array('type' => 'int'),
					'season_id' 			=> array('type' => 'int'),
					'building_id'    		=> array('type' => 'int'),
					'from_'					=> array('type' => 'timestamp', 'required' => true),
					'to_'						=> array('type' => 'timestamp', 'required' => true),
					'filename'    			=> array('type' => 'string', 'required' => true),
					key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
					// 'account_codes_id'    => array('type' => 'int', 'precision' => '4'), 
					'season_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'building_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
				)
			);
		}
		

		protected function preValidate(&$entity)
		{
			if (!isset($entity['id']) && !isset($entity['filename']) && !$entity['filename']) {
				$entity['filename'] = self::TEMP_FILE_NAME;
			} 
		}
		
		protected function doValidate($entity, booking_errorstack $errors)
		{
			$exportable_reservations =& $this->get_completed_reservations_for($entity);
			if (!$exportable_reservations) {
				$errors['nothing_to_export'] = 'Nothing to export';
			}
		}
		
		function read_single($id)
		{
			if (($entity = parent::read_single($id))) {
				$entity['file'] = $this->file_storage->get($entity['filename']);
			}
			
			return $entity;
		}
		
		function add($entry) {
			$export_reservations =& $this->get_completed_reservations_for($entry);
			
			if (!$export_reservations) {
				throw new LogicException('Nothing to export');
			}
			
			$this->db->transaction_begin();
			
			$entry['filename'] = self::TEMP_FILE_NAME;
			
			$receipt = parent::add($entry);
			
			$entry['id'] = $receipt['id'];
			$entry['filename'] = 'export_'.$entry['id'].'.txt';
			
			$export_file = new booking_storage_object($entry['filename']);
			
			$export_file->set_data(
				$this->export($export_reservations)
			);
			
			$this->update_completed_reservations_exported_state($entry, $export_reservations);
			
			$this->file_storage->attach($export_file)->persist();
			
			parent::update($entry);
			
			if ($this->db->transaction_commit()) { 
				return $receipt;
			}
			
			try {
				if ($export_file->exists()) {
					$export_file->delete();
				}
			} catch (booking_unattached_storage_object $e) { }
			
			throw new UnexpectedValueException('Transaction failed.');
		}
		
		public function &get_completed_reservations_for($entity) {
			$to_date = (isset($entity['to_']) && $entity['to_'] ? $entity['to_'] : date('Y-m-d'));
			
			$filters = array(
				'where' => "%%table%%".sprintf(".to_ <= '%s 23:59:59'", $to_date),
				'exported' => '0',
			);
			
			if ($entity['season_id']) {
				$filters['season_id'] = $entity['season_id'];
			}
			
			//TODO: watch out! No building_id field on completed reservations!
			if ($entity['building_id']) {
				$filters['building_id'] = $entity['building_id'];
			}
			
			//TODO: order by to_ to get the oldest one in the bunch (to use as export from_)
			$reservations = $this->completed_reservation_so->read(array('filters' => $filters, 'results' => 'all'));
			
			if (count($reservations['results']) > 0) {
				return $reservations['results'];
			}
			
			return null;
		}
		
		protected function update_completed_reservations_exported_state($entity, &$reservations) {
			return $this->completed_reservation_so->update_exported_state_of($reservations, $entity['id']);
		}
		
		public function export(array &$reservations) {
			if (is_array($reservations)) {
				return $this->format_agresso($reservations);
			}
			return '';
		}
		
		public function format_agresso(array &$reservations) {
			//$orders = array();
			$output = array();
			//$batch_id_pattern = '{$date}-%s';
			//%s - should be replaced with K (Kultur) or I (Idrett) 
			
			
			
			/* TODO: The specification states that values of type date
			 * should be left padded with spaces. The example file,
			 * however, is right padded with spaces.
			 *
			 * Using left padding with spaces (i.e specced version).
			 *
			 * Quote from spec. about values of type date:
			 * Dato. Begynner med mellomrom. Format: ÅÅMMDD
			 */
			$date = str_pad(date('Ymd'), 17, ' ', STR_PAD_LEFT);
			//$date = str_pad(date('ymd'), 17, ' ');
			
			$batch_id_pattern = 'BO%s'.date('ymd');
			
			$client_id = str_pad(substr(strtoupper('BY'), 0, 2), 2, ' ');
			$currency = str_pad(substr(strtoupper('NOK'), 0, 3), 3, ' ');
			$order_type = str_pad(substr(strtoupper('FS'), 0, 2), 2, ' ');
			$pay_method = str_pad(substr(strtoupper('IP'), 0, 2), 2, ' ');
			
			/* TODO: The specification states i8 format (integer left padded with zeroes)
			 * whereas the example file uses c8 format (8 characters right padded with spaces).
			 *
			 * Using i8 for now (i.e specced version)
			 *
			 * Quoted from spec (note the use of leading zeroes):
			 * ÅÅÅÅMM (ok leveres, eksempel 00200806). Skal leveres, perioden for ordren - blir liggende på selve ordren i Agresso, har ikke betydning for reskontro/hoevdbok - som vil få aktuell måned ved fakturering (SO13)
			 */
			$period = str_pad(substr('00'.date('Ym'), 0, 8), 8, '0', STR_PAD_LEFT);
			//$period = str_pad(substr(date('Ym'), 0, 8), 8, ' ');
			
			$responsible = str_pad(substr(strtoupper('BOOKING'), 0, 8), 8, ' ');
			$responsible2 = str_pad(substr(strtoupper($responsible), 0, 8), 8, ' ');
			$status = str_pad(substr(strtoupper('N'), 0, 1), 1, ' ');
			$trans_type = str_pad(substr(strtoupper('42'), 0, 2), 2, ' ');
			$voucher_type = str_pad(substr(strtoupper('XX'), 0, 2), 2, ' ');
			
			foreach($reservations as &$reservation) {
				foreach($reservation as &$field) {
					$field = utf8_decode($field);
				}
				
				$reservation['payee_identifier'] = $this->completed_reservation_bo->get_active_customer_identifier($reservation);
				$string_payee_identifier = (is_null(current($reservation['payee_identifier'])) ? 'N/A' : current($reservation['payee_identifier']));
				
				//header level
				$header = $this->get_agresso_row_template();
				$header['accept_flag'] = '1';
				
				/* TODO: Should contain K (Kultur) or I (Idrett), using X for now to denote that it should be completed
				 * once we have ascertained where and how we should manage this information
				 *
				 * TODO: Introduce a unique id if several transfers in one day?
				 */
				$header['batch_id'] = str_pad(substr(sprintf($batch_id_pattern, 'X'), 0, 12), 12, ' ');
				
				$header['client'] = $client_id;
				$header['confirm_date'] = $date;
				$header['currency'] = $currency;
				$header['deliv_date'] = $header['confirm_date'];
				
				//TODO: Skal leverer oppdragsgiver, blir et nr. pr. fagavdeling. XXXX, et pr. fagavdeling
				$header['dim_value_1'] = str_pad(strtoupper(substr('todo', 0, 12)), 12, ' ');
				
				//Nøkkelfelt, kundens personnr/orgnr.
				$header['ext_ord_ref'] = str_pad(substr($string_payee_identifier, 0, 15), 15, ' ');
				 
				$header['line_no'] = '0000'; //Nothing here according to example file but spec. says so
				
				//TODO: Topptekst til faktura, knyttet mot fagavdeling
				$header['long_info1'] = str_pad(substr('TODO: Topptekst til faktura, knyttet mot fagavdeling', 0, 120), 120, ' ');
				
				
				//Ordrenr. UNIKT, løpenr. genereres i booking ut fra gitt serie, eks. 38000000
				$header['order_id'] = str_pad($reservation['id'], 9, 0, STR_PAD_LEFT); //TODO: generate from a given series
				
				$header['order_type'] = $order_type;
				$header['pay_method'] = $pay_method;
				$header['period'] = $period;
				$header['responsible'] = $responsible;
				$header['responsible2'] = $responsible2;
				//$header['sequence_no'] = str_repeat('0', 8); //Shouldn't be here although some examples provide it here
				$header['status'] = $status;
				$header['trans_type'] = $trans_type;
				$header['voucher_type'] = $voucher_type;
				
				//item level
				$item = $this->get_agresso_row_template();
				$line_no = 1;
				
				$item['amount'] = str_pad($reservation['cost']*100, 17, 0, STR_PAD_LEFT); //Feltet viser netto totalbeløp i firmavaluta for hver ordrelinje. Brukes hvis amount_set er 1. Hvis ikke, brukes prisregisteret (*100 angis). Dersom beløpet i den aktuelle valutaen er angitt i filen, vil beløpet beregnes på grunnlag av beløpet i den aktuelle valutaen ved hjelp av firmaets valutakurs-oversikt.
				$item['amount_set'] = '1';
				
				/* Data hentes fra booking, tidspunkt legges i eget felt som kommer på 
				 * linjen under: 78_short_info. <navn på bygg>,  <navn på ressurs>
				 * TODO: Fix so that description does not include dates
				 */
				$item['art_descr'] = str_pad(substr($reservation['article_description'], 0, 35), 35, ' '); //35 chars long
				
				//TODO: Artikkel opprettes i Agresso (4 siffer), en for kultur og en for idrett, inneholder konteringsinfo.
				$item['article'] = str_pad(substr(strtoupper('todo_article_article_article'), 0, 15), 15, ' '); 
				
				$item['batch_id'] = $header['batch_id'];
				$item['client'] = $header['client'];
				
				//TODO: Ansvarssted for inntektsføring for varelinjen avleveres i feltet (ANSVAR - f.eks 724300). ansvarsted (6 siffer) knyttet mot bygg /sesong
				$item['dim_1'] = str_pad(strtoupper(substr('tododim1', 0, 8)), 8, ' '); 
				
				//TODO: Tjeneste, eks. 38010 drift av idrettsbygg.  Kan ligge på artikkel i Agresso. Blank eller tjenestenr. (eks.38010) vi ikke legger det i artikkel
				$item['dim_2'] = str_pad(strtoupper(substr('tododim2', 0, 8)), 8, ' ');
				
				//TODO: Objektnr. vil være knyttet til hvert hus (FDVU)
				$item['dim_3'] = str_pad(strtoupper(substr('tododim3', 0, 8)), 8, ' ');
				
				//TODO: Kan være aktuelt å levere prosjektnr knyttet mot en booking, valgfritt 
				$item['dim_5'] = str_pad(strtoupper(substr('todotoddim_5', 0, 12)), 12, ' ');
				
				$item['line_no'] = str_pad($line_no, 4, 0, STR_PAD_LEFT);
				
				$item['order_id'] = $header['order_id'];
				$item['period'] = $header['period'];
				$item['sequence_no'] = str_repeat('0', 8);

				$item['status'] = $header['status'];
				$item['trans_type'] = $header['trans_type'];
				
				$item['value_1'] = str_pad(1*100, 17, 0, STR_PAD_LEFT); //Units. Multiplied by 100.
				$item['voucher_type'] = $header['voucher_type'];
				
				//text level
				$text = $this->get_agresso_row_template();
				$text['batch_id'] = $header['batch_id'];
				$text['client'] = $header['client'];
				$text['line_no'] = $item['line_no']; 
				$text['short_info'] = str_pad(substr($reservation['description'], 0, 60), 60, ' ');
				$text['trans_type'] = $header['trans_type'];
				$text['voucher_type'] = $header['voucher_type'];
				
				$text['sequence_no'] = str_pad(intval($item['sequence_no'])+1, 8, '0', STR_PAD_LEFT);
				
				//Add to orders
				//$orders[] = array('header' => $header, 'items' => array('item' => $item, 'text' => $text));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $header));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $item));
				$output[] = implode('', str_replace(array("\n", "\r"), '', $text));
			}
			
			return implode("\n", $output);
		}
		
		protected function get_agresso_row_template() {
			static $row_template = false;
			if ($row_template) { return $row_template; }
			
			$row_template = array('accept_flag' => str_repeat(' ', 1), 'account' => str_repeat(' ', 8), 'accountable' => str_repeat(' ', 20), 'address' => str_repeat(' ', 160), 'allocation_key' => str_repeat(' ', 2), 'amount' => str_repeat(' ', 17), 'amount_set' => str_repeat(' ', 1), 'apar_id' => str_repeat(' ', 8), 'apar_name' => str_repeat(' ', 30), 'art_descr' => str_repeat(' ', 35), 'article' => str_repeat(' ', 15), 'att_1_id' => str_repeat(' ', 2), 'att_2_id' => str_repeat(' ', 2), 'att_3_id' => str_repeat(' ', 2), 'att_4_id' => str_repeat(' ', 2), 'att_5_id' => str_repeat(' ', 2), 'att_6_id' => str_repeat(' ', 2), 'att_7_id' => str_repeat(' ', 2), 'bank_account' => str_repeat(' ', 35), 'batch_id' => str_repeat(' ', 12), 'client' => str_repeat(' ', 2), 'client_ref' => str_repeat(' ', 2), 'confirm_date' => str_repeat(' ', 17), 'control' => str_repeat(' ', 1), 'cur_amount' => str_repeat(' ', 17), 'currency' => str_repeat(' ', 3), 'del_met_descr' => str_repeat(' ', 60), 'del_term_descr' => str_repeat(' ', 60), 'deliv_addr' => str_repeat(' ', 255), 'deliv_attention' => str_repeat(' ', 50), 'deliv_countr' => str_repeat(' ', 3), 'deliv_date' => str_repeat(' ', 17), 'deliv_method' => str_repeat(' ', 8), 'deliv_terms' => str_repeat(' ', 8), 'dim_1' => str_repeat(' ', 8), 'dim_2' => str_repeat(' ', 8), 'dim_3' => str_repeat(' ', 8), 'dim_4' => str_repeat(' ', 8), 'dim_5' => str_repeat(' ', 12), 'dim_6' => str_repeat(' ', 4), 'dim_7' => str_repeat(' ', 4), 'dim_value_1' => str_repeat(' ', 12), 'dim_value_2' => str_repeat(' ', 12), 'dim_value_3' => str_repeat(' ', 12), 'dim_value_4' => str_repeat(' ', 12), 'dim_value_5' => str_repeat(' ', 12), 'dim_value_6' => str_repeat(' ', 12), 'dim_value_7' => str_repeat(' ', 12), 'disc_percent' => str_repeat(' ', 17), 'exch_rate' => str_repeat(' ', 17), 'ext_ord_ref' => str_repeat(' ', 15), 'intrule_id' => str_repeat(' ', 6), 'line_no' => str_repeat(' ', 4), 'location' => str_repeat(' ', 4), 'long_info1' => str_repeat(' ', 120), 'long_info2' => str_repeat(' ', 120), 'lot' => str_repeat(' ', 10), 'main_apar_id' => str_repeat(' ', 8), 'mark_attention' => str_repeat(' ', 50), 'mark_ctry_cd' => str_repeat(' ', 3), 'markings' => str_repeat(' ', 120), 'obs_date' => str_repeat(' ', 17), 'order_date' => str_repeat(' ', 17), 'order_id' => str_repeat(' ', 9), 'order_type' => str_repeat(' ', 2), 'pay_method' => str_repeat(' ', 2), 'period' => str_repeat(' ', 8), 'place' => str_repeat(' ', 30), 'province' => str_repeat(' ', 40), 'rel_value' => str_repeat(' ', 12), 'responsible' => str_repeat(' ', 8), 'responsible2' => str_repeat(' ', 8), 'sequence_no' => str_repeat(' ', 8), 'sequence_ref' => str_repeat(' ', 8), 'serial_no' => str_repeat(' ', 20), 'short_info' => str_repeat(' ', 60), 'status' => str_repeat(' ', 1), 'tax_code' => str_repeat(' ', 2), 'tax_system' => str_repeat(' ', 2), 'template_id' => str_repeat(' ', 8), 'terms_id' => str_repeat(' ', 2), 'tekx1' => str_repeat(' ', 12), 'tekst2' => str_repeat(' ', 12), 'tekst3' => str_repeat(' ', 12), 'text4' => str_repeat(' ', 12), 'trans_type' => str_repeat(' ', 2), 'unit_code' => str_repeat(' ', 3), 'unit_descr' => str_repeat(' ', 50), 'value_1' => str_repeat(' ', 17), 'voucher_ref' => str_repeat(' ', 9), 'voucher_type' => str_repeat(' ', 2), 'warehouse' => str_repeat(' ', 4), 'zip_code' => str_repeat(' ', 15));
			return $row_template;
		}
	}