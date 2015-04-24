<?php

namespace xMLM;

class Model_Payout extends \SQL_Model {
	public $table = "xmlm_payouts";

	function init(){
		parent::init();
		$this->hasOne('xMLM/Distributor','distributor_id');

		$this->addField('session_left_pv');
		$this->addField('session_right_pv');
		
		$this->addField('session_left_bv');
		$this->addField('session_right_bv');

		$this->addField('session_direct_count');

		$this->addField('pairs');
		
		$this->addField('session_business_volume');
		$this->addField('generation_level');
		$this->addField('generation_gross_amount');
		
		$this->addField('pair_income');
		$this->addField('introduction_income');
		$this->addField('generation_difference_income');
		$this->addField('bonus');

		$this->addField('tds');
		$this->addField('admin_charge');
		$this->addField('repurchase_deduction');
		$this->addField('other_deduction_name');
		$this->addField('other_deduction');

		$this->addField('net_amount');
		$this->addField('carried_amount');

		$this->addField('on_date');

	}

	function generatePayout($on_date){

	}
}