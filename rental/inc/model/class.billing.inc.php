<?php

	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'invoice', 'inc/model/');
	include_class('rental', 'model', 'inc/model/');

	/**
	 * Class that represents the actual billing job.
	 *
	 */
	class rental_billing extends rental_model
	{
		protected $id;
		protected $location_id; // Contract type
		protected $billing_term;
		protected $year;
		protected $month;
		protected $success;
		protected $total_sum;
		protected $timestamp_start;
		protected $timestamp_stop;
		protected $invoices;
		
		public static $so;
		
		public function __construct(int $id, int $location_id, int $billing_term, int $year, int $month)
		{
			$this->id = (int)$id;
			$this->location_id = (int)$location_id;
			$this->billing_term = (int)$billing_term;
			$this->year = (int)$year;
			$this->month = (int)$month;
			$this->success = false;
			$this->invoices = null;
		}
		
		public function get_id(){ return $this->id; }
		
		public function set_id(int $id)
		{
			$this->id = (int)$id;
		}
		
		public function get_billing_term(){ return $this->billing_term; }
		
		public function set_total_sum(float $total_sum)
		{
			$this->total_sum = (float)$total_sum;
		}
		public function get_location_id(){ return $this->location_id; }
		
		public function get_year(){ return $this->year; }
		
	
		public function get_month(){ return $this->month; }
	
		public function get_total_sum(){ return $this->total_sum; }
		
		public function set_timestamp_start(int $timestamp_start)
		{
			$this->timestamp_start = (int)$timestamp_start;
		}
	
		public function get_timestamp_start(){ return $this->timestamp_start; }
				
		public function set_timestamp_stop(int $timestamp_stop)
		{
			$this->timestamp_stop = (int)$timestamp_stop;
		}
	
		public function get_timestamp_stop(){ return $this->timestamp_stop; }
		
		public function set_success($success)
		{
			$this->success = (boolean)$success;
		}
	
		public function get_success(){ return $this->success; }
		
		/**
		 * Adds an invoice to the billing job.
		 * NOTE: The 
		 * @param $invoice
		 * @return unknown_type
		 */
		public function add_invoice(rental_invoice &$invoice)
		{
			if($this->invoices == null)
			{
				$this->invoices = array();
			}
			$this->invoices[] = $invoice;
		}
		
		/**
		 * Returns the invoices belonging the contract.
		 * @return unknown_type
		 */
		public function get_invoices()
		{
			if($this->invoices == null)
			{
				$this->invoices = rental_invoice::get_so()->get_invoices_for_billing($this->get_id());
			}
			return $this->invoices;
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.sobilling');
			}
			return self::$so;
		}
		
		public static function create_billing(int $decimals, int $contract_type, int $billing_term, int $year, int $month, array $contracts_to_bill, array $contract_billing_start_date)
		{
			// We start a transaction before running the billing
			rental_billing::get_so()->db->transaction_begin();
			$billing = new rental_billing(-1, $contract_type, $billing_term, $year, $month); // The billing job itself
			$billing->set_timestamp_start(time()); // Start of run
			$billing->store(); // Store job as it is
			$billing_end_timestamp = strtotime('-1 day', strtotime(($month == 12 ? ($year + 1) : $year) . '-' . ($month == 12 ? '01' : ($month + 1)) . '-01')); // Last day of billing period is the last day of the month we're billing
			$counter = 0;
			$total_sum = 0;
			foreach($contracts_to_bill as $contract_id) // Runs through all the contracts that should be billed in this run
			{
				$invoice = rental_invoice::create_invoice($decimals, $billing->get_id(), $contract_id, $contract_billing_start_date[$counter++], $billing_end_timestamp); // Creates an invoice of the contract
				if($invoice != null)
				{
					$total_sum += $invoice->get_total_sum();
					$billing->add_invoice($invoice);
				}
			}
			$billing->set_total_sum(round($total_sum, $decimals));
			$billing->set_timestamp_stop(time()); //  End of run
			$billing->set_success(true); // Billing job is a success
			$billing->store(); // Store job now that we're done
			// End of transaction!
			if (rental_billing::get_so()->db->transaction_commit()) { 
				return $billing;
			}
			throw new UnexpectedValueException('Transaction failed.');
		}
			
		/**
		 * Get a key/value array of titles of billing term types keyed by their id
		 * 
		 * @return array
		 */
		public static function get_billing_terms()
		{
			$so = self::get_so();
			return $so->get_billing_terms();
		}
		
		public function serialize()
		{
			return array();
		}
		
	}
		
?>