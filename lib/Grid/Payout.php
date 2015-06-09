<?php

namespace xMLM;

class Grid_Payout extends \Grid {
	public $hide_distributor=true;
	public $generation_income=false;
	public $introducer_vp;
	public $all_kits=true;

	function init(){
		parent::init();
		$this->all_kits= $this->add('xMLM/Model_Kit')->getRows();
	}


	function setModel($model,$fields=null){

		$m = parent::setModel($model,$fields);

		if($this->hide_distributor and $this->hasColumn('distributor')) $this->removeColumn('distributor');
		if($this->hasColumn('pairs')) $this->removeColumn('pairs');
		if($this->hasColumn('session_left_pv')) $this->removeColumn('session_left_pv');
		if($this->hasColumn('session_right_pv')) $this->removeColumn('session_right_pv');
		if($this->hasColumn('session_left_bv')) $this->removeColumn('session_left_bv');
		if($this->hasColumn('session_right_bv')) $this->removeColumn('session_right_bv');
		if($this->hasColumn('session_business_volume')) $this->removeColumn('session_business_volume');
		if($this->hasColumn('session_carried_left_pv')) $this->removeColumn('session_carried_left_pv');
		if($this->hasColumn('session_carried_right_pv')) $this->removeColumn('session_carried_right_pv');
		
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
			$this->addFormatter('introduction_income','introduction_income');
		
		if($this->hasColumn('greened_on'))
			$this->removeColumn('greened_on');

		// $this->addColumn('total_pay','total_pay');
		$this->addColumn('total_deduction','total_deduction');
		$this->addOrder()
			->move('total_pay','after','introduction_income')
			->move('total_deduction','after','admin_charge')
			->now();


		$this->introducer_vp = $this->add('VirtualPage');
		$this->introducer_vp->set(function($p){
			$pout=$p->add('xMLM/Model_Payout'); 
			$pout->load($this->api->stickyGET('payout_id'));

			$last_payout_date=$p->add('xMLM/Model_Payout');
			$last_payout_date->addCondition('on_date','<',$pout['on_date']);
			$last_payout_date->setOrder('on_date','desc');

			$last_closing_date=$last_payout_date->tryLoadAny()->get('on_date')?:"1970-01-01";

			$intro_grid = $p->add('xMLM/Grid_Distributor');
			$intro_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item','color_value'))
					->addCondition('introducer_id',$pout['distributor_id'])
					->addCondition('greened_on','<>',null)
					->addCondition('greened_on','>=',$last_closing_date)
					->addCondition('greened_on','<=',$pout['on_date']);

		});	

		return $m;
	}

	// function format_total_pay($field){
	// 	$this->current_row[$field] = $this->current_row['introduction_income'] + $this->current_row['pair_income'];
	// }

	function format_total_deduction($field){
		$this->current_row[$field] = $this->model['admin_charge'] + $this->model['tds'];
	}

	function format_introduction_income($f){
		$this->current_row_html[$f] = '<a href="#na" onclick="javascript:'.$this->js()->univ()->frameURL('Introductions ', $this->api->url($this->introducer_vp->getURL(),array('payout_id'=>$this->model['id']))).'">'.$this->current_row[$f]."</a>";

		$counts=array();
			
		foreach ($this->all_kits as $kit) {
			$last_payout_date=$this->add('xMLM/Model_Payout','p'.$this->model->id);
			$last_payout_date->addCondition('on_date','<',$this->model['on_date']);
			$last_payout_date->setOrder('on_date','desc');

			$last_closing_date=$last_payout_date->tryLoadAny()->get('on_date')?:"1970-01-01";

			$kit_counts = $this->add('xMLM/Model_Distributor');
			$counts[$kit['name']] = $kit_counts->addCondition('introducer_id',$this->model['distributor_id'])
					->addCondition('greened_on','<>',null)
					->addCondition('greened_on','>=',$last_closing_date)
					->addCondition('greened_on','<=',$this->model['on_date'])
					->addCondition('kit_item_id',$kit['id'])
					->count()->getOne()
					;

		}


		$str = '<div class="atk-size-micro atk-text-dimmed">';
		foreach ($counts as $kit => $cnt) {
			$str .= $kit.':'.$cnt.',';
		}
		$str .='</div>';
		$this->current_row_html[$f] = $str;

	}

	function format_totals_introduction_income($f){
		$this->current_row_html[$f]= $this->current_row[$f];
	}

	function format_distributor($field){
		if(!$this->model['greened_on']) {
			$this->setTDparam($field,'style/color','red');
		}else{
			$this->setTDparam($field,'style/color','');
		}

	}

	function format_totals_distributor($field){
		$this->current_row_html[$field]="<b>Total</b>";
	}


	function updateGrandTotals()
    {
        // get model
        $m = clone $this->getIterator();
        // create DSQL query for sum and count request
        $fields = array_keys($this->totals);

        // select as sub-query
        $sub_q = $m->_dsql()->del('limit')->del('order')->del('group');

        // $q = $this->api->db->dsql();//->debug();
        // $q->table($sub_q->render(), 'grandTotals'); // alias is mandatory if you pass table as DSQL
        foreach ($fields as $field) {
            $sub_q->field($sub_q->sum('xmlm_payouts.'.$field), $field);
        }
        $sub_q->field($sub_q->count(), 'total_cnt');

        // execute DSQL
        $data = $sub_q->getHash();

        // parse results
        $this->total_rows = $data['total_cnt'];
        unset($data['total_cnt']);
        $this->totals = $data;
    }

}