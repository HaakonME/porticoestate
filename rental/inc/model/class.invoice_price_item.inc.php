<?php
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');

	/**
	 * Represents a price item in an invoice. The data is typically built from
	 * an instance of rental_contract_price_item.
	 *
	 */
	class rental_invoice_price_item extends rental_price_item
	{
		protected $decimals;
		protected $invoice_id;
		protected $is_area;
		protected $price_per_year;
		protected $area;
		protected $count;
		protected $total_price;
		protected $timestamp_start; // Start date for the given invoice
		protected $timestamp_end; // End date for the given invoice
		
		public static $so;
		
		public function __construct(int $decimals, int $id, int $invoice_id, string $title, string $agresso_id, boolean $is_area, float $price_per_year, float $area, int $count, int $timestamp_start, int $timestamp_end)
		{
			$this->decimals = (int)$decimals;
			$this->id = (int)$id;
			$this->invoice_id = (int)$invoice_id;
			$this->title = $title;
			$this->agresso_id = $agresso_id;
			$this->is_area = (boolean)$is_area;
			$this->price_per_year = (float)$price_per_year;
			$this->area = (float)$area;
			$this->count = (int)$count;
			$this->timestamp_start = (int)$timestamp_start;
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_price = null; // Needs to be re-calculated
		}
		
		public function set_invoice_id(int $invoice_id)
		{
			$this->invoice_id = (int)$invoice_id;
		}
		
				public function get_invoice_id(){ return $this->invoice_id; }
			
		public function set_is_area(boolean $is_area)
		{
			$this->is_area = (boolean)$is_area;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function is_area(){ return $this->is_area; }
		
		public function set_count(int $count)
		{
			$this->count = (int)$count;
			$this->total_price = null; // Needs to be re-calculated
		}

		public function set_price($price_per_year)
		{
			$this->price_per_year = (float)$price_per_year;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_price(){ return $this->price_per_year; }
			
		public function set_area($area)
		{
			$this->area = (float)$area;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_area(){ return $this->area; }
		
		public function get_count(){ return $this->count; }
		
		public function set_total_price(float $total_price)
		{
			$this->total_price = (float)$total_price;
		}
	
		/**
		 * This method calculated the total price of the invoice price item if it hasn't been done before
		 * 
		 * @return float	the total price of the price item
		 */
		public function get_total_price(){ 
			if($this->total_price == null) // Needs to be calculated
			{
				// The calculation of the price for complete months (meaning the item applies for the whole month) ..
				$num_of_complete_months = 0;
				
				// ..is different than the calculation for incomplete months
				$incomplete_months = array(); 
				
				// Get the year, month and day from the startdate timestamp 
				$date_start = array();
				$date_start['year'] = (int)date('Y', $this->get_timestamp_start());
				$date_start['month'] = (int)date('n', $this->get_timestamp_start());
				$date_start['day'] = (int)date('j', $this->get_timestamp_start());
				
				// Get the year, month and day from the enddate timestamp 
				$date_end = array();
				$date_end['year'] = (int)date('Y', $this->get_timestamp_end());
				$date_end['month'] = (int)date('n', $this->get_timestamp_end());
				$date_end['day'] = (int)date('j', $this->get_timestamp_end());
				
				// Runs through all the years this price item goes for
				for($current_year = $date_end['year']; $current_year >= $date_start['year']; $current_year--) 
				{
					// Within each year: which months do the price item run for
					
					// First we set the defaults (whole year)
					$current_start_month = 1; // January
					$current_end_month = 12; // December
					
					// If we are at the start year, use the start month of this year as start month
					if($current_year == $date_start['year']) 
					{
						$current_start_month = $date_start['month'];
					}
					
					// If we are at the start year, use the end month of this year as end month
					if($current_year == $date_end['year'])
					{
						$current_end_month = $date_end['month'];
					}
					
					// Runs through all of the months of the current year (we go backwards since we go backwards with the years)
					for($current_month = $current_end_month; $current_month >= $current_start_month; $current_month--) 
					{
						// Retrive the number of days in the current month
						$num_of_days_in_current_month = date('t', strtotime($current_year . '-' . $current_month . '-01'));
						
						// Set the defaults (whole month)
						$first_day = 1;
						$last_day = $num_of_days_in_current_month;
						
						// If we are at the start month in the start year, use day in this month as first day
						if($current_year == $date_start['year'] && $current_month == $date_start['month']) 
						{
							$first_day = $date_start['day'];
						}
						
						// If we are at the end month in the end year, use the day in this month as end day
						if($current_year == $date_end['year'] && $current_month == $date_end['month']) 
						{
							$last_day = $date_end['day']; // The end date's day is the item's end day
						}
						
						// Increase counter: complete months or incomplete months (number of days in this year and number of days )
						if($first_day === 1 && $last_day == $num_of_days_in_current_month){ // This is a whole month
							$num_of_complete_months++;
						}
						else // Incomplete month
						{
							// YYY: There must be a better day to do this!?
							$num_of_days_in_current_year = (date('L', strtotime($current_year . '01-01')) == 0) ? 365 : 366; 
							$num_of_days = $last_day - $first_day + 1;
							$incomplete_months[] = array($num_of_days_in_current_year, $num_of_days);
						}
					}
				}
				// ---- Calculate complemete months
				
				// Retrieve the amount: rented area of contract or the number of items (depending on type of price element)
				$amount = $this->is_area() ? $this->get_area() : $this->get_count();
				
				// We have to first _round_ the element price per month (NB! using precision of 4 decimals in mid-calculation)
				//$rounded_element_price_per_month = $this->get_price() / 12.0; // round($this->get_price() / 12.0, 2);
				
				//Alternativ: Round by two
				//var_dump("Price. " . $this->get_price());
				//var_dump("Per month " . $this->get_price()/12.0);
				
				
				$rounded_element_price_for_complete_months = round(($this->get_price() * $num_of_complete_months)/12.0,2);
				//var_dump("Rounded for complete months period " . $rounded_element_price_for_complete_months);
				
				// @TODO: FACILIT BILLING LOGIC (TO BE REMOVED IN 2011)
				//$rounded_element_price_per_month = $this->cut_off_after_two_decimals($rounded_element_price_per_month);
				
				// The price per month is the rounded element price multipied by the amount
				$this->total_price = $rounded_element_price_for_complete_months * $amount;
				//var_dump("Total price for period " . $this->total_price);
				
				// The total price for the complete months are just the monthly price multiplied by the number of months
				//$this->total_price = $price_per_month * $num_of_complete_months;
				//var_dump("Total for this price element " . $this->total_price); 
				
				// ---- Calculate incomplete months 
				
				// We have to first _round_ the element price per year (NB! using precision of 4 decimals in mid-calculation)
				//$rounded_price_per_year = $this->get_price() * $amount;
				
				//Alternativ: Round by two
				$rounded_price_per_year = round($this->get_price() * $amount, 2);
				
				// @TODO: FACILIT BILLING LOGIC (TO BE REMOVED IN 2011)				
				//$rounded_price_per_year = $this->cut_off_after_two_decimals($rounded_price_per_year);
				
				// Run through all the incomplete months ...
				foreach($incomplete_months as $day_factors) 
				{
					// ... and add the sum of each incomplete month to the total price of the price item
					// Calculation: Price per day (price per year divided with number of days in year) multiplied with number of days in incomple month
					$this->total_price += ($rounded_price_per_year / $day_factors[0]) * $day_factors[1];
				}
				// We round the total price for each price item with the sepcified number of decimals precision
				$this->total_price = round($this->total_price, $this->decimals);
			}
			return $this->total_price;
		}
		
		private function round_half_down($number)
		{
			//var_dump($number);
			$parts = explode(".",$number);
			if(count($parts) == 2){
				$integer = $parts[0];
				$decimals = $parts[1];
				if(strlen($decimals) > 2)
				{
					$first = substr($decimals,0,1);
					$second = substr($decimals,1,1);
					$third = substr($decimals,2,1);
					$fourth = substr($decimals,2,1);
				 	if($third < 5)
				 	{
				 		return $integer . "." . $first . $second;
				 	}
				 	else if($third == 5)
				 	{
				 		if($fourth && $fourth <= 5)
				 		{
				 			return $integer . "." . $first . $second;	
				 		}
				 	}
				 	return $integer . "." . $first . ($second+1);
				}
			}
			return $number;
		}
		
		
		public function set_timestamp_start(int $timestamp_start)
		{
			$this->timestamp_start = (int)$timestamp_start;
			$this->total_price = null; // Needs to be re-calculated
		}
		
		public function get_timestamp_start(){ return $this->timestamp_start; }
		
		public function set_timestamp_end(int $timestamp_end)
		{
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_timestamp_end(){ return $this->timestamp_end; }
		
		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			return array
				(
					'title'				=> $this->get_title(),
					'agresso_id'		=> $this->get_agresso_id(),
					'is_area'			=> $this->get_type_text(),
					'price'				=> $this->get_price(),
					'area'				=> $this->get_area(),
					'count'				=> $this->get_count(),
					'total_price'		=> $this->get_total_price(),
					'timestamp_start'	=> date($date_format, $this->get_timestamp_start()),
					'timestamp_end'		=> date($date_format, $this->get_timestamp_end()),
				);
		}
		
	}
		
?>