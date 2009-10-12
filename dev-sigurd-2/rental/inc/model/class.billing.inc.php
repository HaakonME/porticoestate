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
	protected $timestamp_commit;
	protected $deleted;
	protected $created_by;
	protected $has_generated_export;
	
	public static $so;
	
	public function __construct(int $id, int $location_id, int $billing_term, int $year, int $month, int $created_by)
	{
		$this->id = (int)$id;
		$this->location_id = (int)$location_id;
		$this->billing_term = (int)$billing_term;
		$this->year = (int)$year;
		$this->month = (int)$month;
		$this->success = false;
		$this->created_by = (int)$created_by;
		$this->has_generated_export = false;
		$this->deleted = false;
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
	
	public function set_timestamp_commit($timestamp_commit)
	{
		$this->timestamp_commit = $timestamp_commit;
	}

	public function get_timestamp_commit(){ return $this->timestamp_commit; }
	
	/**
	 * Convenience method for checking if a billing job has been commited or
	 * not. Checks if the timestamp for commit has been set.
	 * 
	 * @return boolean true if job has been commited, false if not.
	 */
	public function is_commited()
	{
		return $this->timestamp_commit != null && $this->timestamp_commit != '';
	}

	public function is_success(){ return $this->success; }

	public function set_created_by(int $created_by)
	{
		$this->created_by = (int)$created_by;
	}

	public function set_deleted(boolean $deleted)
	{
		$this->deleted = (boolean)$deleted;
	}

	public function is_deleted(){ return $this->deleted; }

	public function get_created_by(){ return $this->created_by; }
	
	public function has_generated_export(){ return $this->has_generated_export; }
	
	public function set_generated_export(boolean $has_generated_export)
	{
		$this->has_generated_export = (boolean)$has_generated_export;
	}
	
	public function set_export_format($export_format)
	{
		$this->export_format = $export_format;
	}

	public function get_export_format(){ return $this->export_format; }
	
	public function serialize()
	{
		$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		$description = '';
		$location_id = $this->get_location_id();
		$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
		foreach($fields as $id => $label)
		{
			if($id == $location_id)
			{
				$description = lang($label) . ' ';
			}
		}
		$description .= lang('month ' . $this->get_month()) . ' ';
		$description .= $this->get_year();
		$account = $GLOBALS['phpgw']->accounts->get($this->get_created_by());
		$timestamp_commit = '';
		if($this->get_timestamp_commit() != null && $this->get_timestamp_commit())
		{
			$timestamp_commit = date($date_format . ' H:i:s', $this->get_timestamp_commit());
		}
		return array(
			'id'				=> $this->get_id(),
			'description'		=> $description,
			'total_sum'			=> $this->get_total_sum(),
			'timestamp_stop'	=> date($date_format . ' H:i:s', $this->get_timestamp_stop()),
			'timestamp_commit'	=> $timestamp_commit,
			'created_by'		=> "{$account->__get('firstname')} {$account->__get('lastname')}"
		);
	}
	
}
?>