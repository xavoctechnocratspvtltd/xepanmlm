<?php

namespace xMLM;

class Model_Payout extends \SQL_Model {
	public $table = "xmlm_payouts";

	function init(){
		parent::init();
		$this->hasOne('xMLM/Distributor','distributor_id');

		$this->addField('session_left_pv');
		$this->addField('session_right_pv');

		$this->addField('pairs');
		
		$this->addField('pair_income');
		$this->addField('bonus');
		$this->addField('tds');
		$this->addField('admin_charge');
		$this->addField('repurchase_deduction');

		$this->addField('net_amount');
		$this->addField('carried_amount');

		$this->addField('on_date');

	}

	function generatePayout($on_date){

	}
}