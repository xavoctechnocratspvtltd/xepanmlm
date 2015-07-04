<?php

namespace xMLM;

class View_ClosingHint extends \View {

	function init(){
		parent::init();

		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();
		$pair_pv = $config['tail_pv'];
		
		$hint_trimming_percentage = $_GET['hint_trimming_percentage']?:0;
		$hint_trimming_base = $_GET['hint_trimming_base']?:0;

		$last_payout=$this->add('xMLM/Model_Payout');
		$last_payout->_dsql()->group('on_date')->order('on_date');
		$last_payout->tryLoadAny();

		$fund_times = 4;

		$last_closing_date=$last_payout->get('on_date')?:"1970-01-01";

		// Total Joining Fund 
		$total_binay_fund = $this->add('xMLM/Model_Kit');
		$total_binay_fund->join('xmlm_distributors.kit_item_id')
				->addField('greened_on');
		$total_binay_fund = $total_binay_fund
						->addCondition('greened_on','<=',$last_payout['on_date'])
						->sum('pv_value')->getOne();
		$total_binay_fund *= $fund_times;


		$funds_received =$total_binay_fund;

		// calculate Pairs
		$q="
			UPDATE xmlm_distributors
			SET
				temp = IF(session_left_pv > session_right_pv, session_right_pv ,session_left_pv ),
				temp = IF(session_left_pv = session_right_pv AND session_left_pv <> 0 AND session_left_pv <= capping , temp - $pair_pv ,temp),
				temp = IF(temp >= capping, capping, temp)
		";
		$this->query($q);

		$trimming_query=" (temp - (temp * $hint_trimming_percentage / 100.00)) ";
		$trimming_base_cond =" temp >= $hint_trimming_base";

		$q="
			UPDATE
				xmlm_distributors
			SET
				temp = $trimming_query
			WHERE
				$trimming_base_cond
		";

		$this->query($q);

		$approx_pair_income = $this->add('xMLM/Model_Distributor')->sum('temp')->getOne();

		$this->add('View')->setHTML('<b>Funds Received for Binary </b> '. $funds_received);
		$this->add('View')->setHTML('<b>Trimming Base</b> '. $_GET['hint_trimming_base']);

		$this->add('View')->setHTML('<b>Trimming Percentage</b> '. $_GET['hint_trimming_percentage'].' %');
		$this->add('View')->setHTML('<b>pair Income </b> '. $approx_pair_income);

	}

	function query($q){
		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
	}

}