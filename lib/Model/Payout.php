<?php

namespace xMLM;

class Model_Payout extends \SQL_Model {
	public $table = "xmlm_payouts";

	function init(){
		parent::init();
		$this->hasOne('xMLM/Distributor','distributor_id');

		// $this->addField('session_intros_amount');

		$this->addField('session_left_pv')->type('int')->defaultValue(0);
		$this->addField('session_right_pv')->type('int')->defaultValue(0);
		$this->addField('session_carried_left_pv')->type('int')->defaultValue(0);
		$this->addField('session_carried_right_pv')->type('int')->defaultValue(0);
		
		$this->addField('session_self_bv')->type('int')->defaultValue(0);
		$this->addField('session_left_bv')->type('int')->defaultValue(0);
		$this->addField('session_right_bv')->type('int')->defaultValue(0);

		// $this->addField('session_direct_count');

		$this->addField('pairs')->type('int')->defaultValue(0);
		
		$this->addField('session_business_volume')->type('int')->defaultValue(0);
		$this->addField('generation_level')->type('int')->defaultValue(0);
		$this->addField('generation_gross_amount')->type('int')->defaultValue(0);
		
		$this->addField('pair_income')->type('int')->defaultValue(0);
		$this->addField('introduction_income')->type('int')->defaultValue(0);
		$this->addField('generation_difference_income')->type('int')->defaultValue(0);
		$this->addField('bonus')->type('int')->defaultValue(0);

		$this->addExpression('total_pay')->set('introduction_income+pair_income+generation_difference_income+bonus');

		$this->addField('tds')->type('money')->defaultValue(0);
		$this->addField('admin_charge')->type('money')->defaultValue(0);
		// $this->addField('repurchase_deduction')->type('money');
		$this->addField('other_deduction_name')->type('money')->defaultValue(0);
		$this->addField('other_deduction')->type('money')->defaultValue(0);
		
		$this->addExpression('total_deduction')->set('tds+admin_charge+other_deduction');

		$this->addField('net_amount')->type('money')->defaultValue(0);
		$this->addField('carried_amount')->type('money')->defaultValue(0);

		$this->addField('on_date')->type('date');

		$this->setOrder('on_date');
	}

	function distributor(){
		return $this->ref('distributor_id');
	}

	function generatePayout($on_date,$pay_generation){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();
		$pair_pv = $config['tail_pv'];
		$admin_charge = $config['admin_charge'];
		$min_payout = $config['minimum_payout_amount'];

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
				SELECT 0, id, session_left_pv,session_right_pv,0,0,0,0,0,0,carried_amount,'$on_date',session_self_bv, session_left_bv,session_right_bv, 0, 0,0,session_intros_amount,0,0,0 FROM xmlm_distributors
			)
		";
		$this->query($q);


		// Set distributor carried_amount to 0
		$q="
			UPDATE
				xmlm_distributors d
			SET
				carried_amount=0;
		";

		$this->query($q);

		// calculate Pairs
		$q="
			UPDATE xmlm_payouts
			SET
				pairs = IF(session_left_pv > session_right_pv, session_right_pv ,session_left_pv ),
				pairs = IF(session_left_pv = session_right_pv AND session_left_pv <> 0 AND session_left_pv <= (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), pairs - $pair_pv ,pairs),
				pairs = IF(pairs >= (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), (select capping from xmlm_distributors WHERE id=xmlm_payouts.distributor_id), pairs)
			WHERE
				on_date = '$on_date'
		";
		$this->query($q);

		// Update Total Pairs to Store in distributor table
		$q="
			UPDATE
				xmlm_distributors d
			SET
				d.total_pairs = d.total_pairs + (select pairs from xmlm_payouts WHERE xmlm_payouts.on_date='$on_date' AND xmlm_payouts.distributor_id=d.id) 
		";
		$this->query($q);

		if($pay_generation){
			// Generation Income
			$q="
				UPDATE
					xmlm_payouts p
				SET
					session_business_volume = session_self_bv + session_left_bv + session_right_bv,
					generation_level=0
			";
			$this->query($q);
			// find all levels as per slab table
			$slabs = $this->add('xMLM/Model_BVSlab');
			foreach ($slabs as $slb) {
				$q="
					UPDATE
						xmlm_payouts p
					SET
						generation_level = ".$slb['percentage']."
					WHERE
						session_business_volume >= ". $slb['name'] ."
				";
				$this->query($q);
			}
			// get percentage and payments
			$q="
				UPDATE
					xmlm_payouts p
				SET
					generation_gross_amount = session_business_volume * generation_level
			";
			$this->query($q);
			// get differences
			$q="
				UPDATE
					xmlm_payouts p 
				JOIN
					xmlm_distributors d ON p.distributor_id = d.id
				SET
					generation_difference_income = generation_gross_amount - (
							(SELECT generation_gross_amount FROM xmlm_payouts ldp JOIN xmlm_distributors ldp_d ON ldp.distributor_id = ldp_d.id WHERE ldp_d.id = d.left_id) /* Left Distributor generation gross amount*/
							+
							(SELECT generation_gross_amount FROM xmlm_payouts ldp JOIN xmlm_distributors ldp_d ON ldp.distributor_id = ldp_d.id WHERE ldp_d.id = d.right_id) /* Right Distributor generation gross amount*/
						)
			";

			$this->query($q);
		}

		$q="
			UPDATE 
				xmlm_payouts payouts
			SET
				pair_income = pairs,
				TDS = (payouts.carried_amount + pair_income + introduction_income + generation_difference_income + bonus) * IF(length((select pan_no from xmlm_distributors where id=payouts.distributor_id))=10,10,20) / 100,
				admin_charge = (payouts.carried_amount + pair_income + introduction_income + generation_difference_income + bonus) * $admin_charge / 100,
				net_amount = (payouts.carried_amount + pair_income + introduction_income + generation_difference_income + bonus) - (TDS + admin_charge)
			WHERE
				on_date = '$on_date'
		";
		$this->query($q);

		// set carried amounts for minimum_payouts and red entries 
				// Put all back to carried_amountif you are still red
		// in payouts as well as store it in distributors
		$q="
			UPDATE 
				xmlm_payouts p
			JOIN
				xmlm_distributors d on p.distributor_id = d.id
			SET
				p.carried_amount = (p.carried_amount + pair_income + introduction_income + generation_difference_income + bonus),
				p.TDS=0,
				p.admin_charge=0,
				p.net_amount=0,
				p.other_deduction=0,
				d.carried_amount = (p.carried_amount + pair_income + introduction_income + generation_difference_income + bonus)

			WHERE
				p.on_date='$on_date'
				AND (
					d.greened_on is null
					OR
					p.net_amount < $min_payout
				)
		";
		$this->query($q); //yes


		// Set Session PV Carry forwards
		$q="
			UPDATE 
				xmlm_distributors d
			SET
				d.session_intros_amount=0,
				d.temp=0,
				d.temp = IF(d.session_left_pv = d.session_right_pv AND d.session_left_pv > 0, d.session_left_pv - $pair_pv, IF(d.session_left_pv > d.session_right_pv,d.session_right_pv,d.session_left_pv)),
				d.session_left_pv = d.session_left_pv - d.temp,
				d.session_right_pv = d.session_right_pv - d.temp
		";
		$this->query($q);

		// Set carried pv back in this Closing [for records]
		$q="
			UPDATE 
				xmlm_payouts p
			JOIN
				xmlm_distributors d on p.distributor_id = d.id
			SET
				p.session_carried_left_pv = d.session_left_pv,
				p.session_carried_right_pv = d.session_right_pv
			WHERE
				p.on_date='$on_date'
		";
		$this->query($q);
		
		
		if($pay_generation){
			// set session fields zero
			// $q="
			// 	UPDATE 
			// 		xmlm_distributors
			// 	SET
			// 		session_left_bv=0,
			// 		session_right_bv=0,
			// 		session_self_bv=0
			// ";
			// $this->query($q);
		}

		// Remove all non Effected Distributors 
		$q="
			DELETE FROM xmlm_payouts
			WHERE
				on_date='$on_date'
				AND
				net_amount = 0
				AND
				carried_amount = 0
		";
		$this->query($q);


	}

	function query($q){
		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
	}
}