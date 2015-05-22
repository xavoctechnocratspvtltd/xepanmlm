<?php

namespace xMLM;

class Grid_Payout extends \Grid {
	public $hide_distributor=true;
	public $generation_income=false;

	function setModel($model,$fields=null){
		$m = parent::setModel($model,$fields);

		if($this->hide_distributor and $this->hasColumn('distributor')) $this->removeColumn('distributor');
		if($this->hasColumn('pairs')) $this->removeColumn('pairs');
		if($this->hasColumn('session_left_pv')) $this->removeColumn('session_left_pv');
		if($this->hasColumn('session_right_pv')) $this->removeColumn('session_right_pv');
		if($this->hasColumn('session_left_bv')) $this->removeColumn('session_left_bv');
		if($this->hasColumn('session_right_bv')) $this->removeColumn('session_right_bv');
		if($this->hasColumn('session_business_volume')) $this->removeColumn('session_business_volume');
		if(!$this->generation_income OR $this->hasColumn('session_self_bv')) $this->removeColumn('session_self_bv');
		if(!$this->generation_income OR $this->hasColumn('generation_level')) $this->removeColumn('generation_level');
		if(!$this->generation_income OR $this->hasColumn('generation_gross_amount')) $this->removeColumn('generation_gross_amount');
		if(!$this->generation_income OR $this->hasColumn('generation_difference_income')) $this->removeColumn('generation_difference_income');

		if($this->hasColumn('other_deduction_name')) $this->removeColumn('other_deduction_name');
		if($this->hasColumn('other_deduction')) $this->removeColumn('other_deduction');
		if($this->hasColumn('bonus')) $this->removeColumn('bonus');

		$order = $this->addOrder();
		if($this->hasColumn('on_date')) $order->move('on_date','first');

		$order->now();

		if(!$this->hide_distributor and $this->hasColumn('distributor')){
			$this->addFormatter('distributor','distributor');
		}
		
		if($this->hasColumn('greened_on'))
			$this->removeColumn('greened_on');

		$this->addColumn('total_pay','total_pay');
		$this->addColumn('total_diduction','total_diduction');
		$this->addOrder()
			->move('total_pay','after','introduction_income')
			->move('total_diduction','after','admin_charge')
			->now();

		return $m;
	}

	function format_total_pay($field){
		$this->current_row[$field] = $this->current_row['introduction_income'] + $this->current_row['pair_income'];
	}

	function format_total_diduction($field){
		$this->current_row[$field] = $this->model['admin_charge'] + $this->model['tds'];
	}

	function format_distributor($field){
		if(!$this->model['greened_on']) {
			$this->setTDparam($field,'style/color','red');
		}else{
			$this->setTDparam($field,'style/color','');
		}
	}

}