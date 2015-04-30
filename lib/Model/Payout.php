<?php

namespace xMLM;

class Model_Payout extends \SQL_Model {
	public $table = "xmlm_payouts";

	function init(){
		parent::init();
		$this->hasOne('xMLM/Distributor','distributor_id');

		$this->addField('session_intros_amount');

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

	function generatePayout($on_date,$pay_generation){
		// check if closing before max on_date
		$on_date_check_model =$this->newInstance();
		$max_date = $on_date_check_model->dsql()->del('field')->field($on_date_check_model->dsql()->expr('max(on_date)'))->getOne();
		if($max_date != null and strtotime($on_date) <= strtotime($max_date) ){
			throw $this->exception('Closing before this date is already done ...','ValidityCheck')->setField('on_date');
		}

		// copy all distributors in here
		$q="
			INSERT INTO xmlm_payouts
			(
				SELECT 0, id, session_intros_amount,session_left_pv,session_right_pv, session_left_bv,session_right_bv,session_direct_count,0, session_business_volume,0,0,0,0,0,0,  0,0,0,0,0,0,0,'$on_date' FROM distributors
			)
		";
		$this->query($q);

		// calculate Pairs
		$q="
			UPDATE xmlm_payouts
			SET
				pairs = IF(session_left_pv > session_right_pv, session_right_pv ,session_left_pv ),
				pairs = IF(session_left_pv = session_right_pv AND session_left_pv <> 0 AND session_left_pv <= (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), pairs - $pair_pv ,pairs),
				pairs = IF(pairs > (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), pairs)
			WHERE
				on_date = '$on_date'
		";
		$this->query($q);

		// Update Total Pairs to Store in distributor table
		$q="
			UPDATE
				xmlm_distributors d
			SET
				d.TotalPairs = d.TotalPairs + (select pairs from xmlm_payouts WHERE xmlm_payouts.on_date='$on_date' AND xmlm_payouts.distributor_id=d.id) 
		";
		$this->query($q);

		if($pay_generation){
			// Generation Income
			// find all levels as per slab table
			// get percentage and payments
			// get differences
		}

		$q="
			UPDATE 
				payouts
			SET
				pair_income = Pairs,
				TDS = (payouts.carried_amount + pair_income + performance_bonus) * IF(length((select pan_no from distributors where id=payouts.distributor_id))=10,10,20) / 100,
				admin_charge = (payouts.carried_amount + pair_income + performance_bonus) * 5 / 100,
				repurchase_deduction = (payouts.carried_amount + pair_income + performance_bonus) * 5 / 100,
				net_amount = (payouts.carried_amount + pair_income + performance_bonus) - (TDS + admin_charge + repurchase_deduction)
			WHERE
				on_date = '$on_date'
		";
		$this->query($q);



	}

	function query($q){
		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
	}
}