<?php

class page_xMLM_page_owner_print extends page_xMLM_page_owner_main {

	function page_index(){
	
	if(!$_GET['on_date']){
		$this->add('View_Error')->set('No Result Found');
	}

	$payout_model = $this->add('xMLM/Model_Payout');
	$dist_j = $payout_model->join('xmlm_distributors','distributor_id');
	$dist_j->addField('greened_on')->caption('Qualified Date');

	if($g_on_date=$this->api->stickyGET('on_date')){
		$payout_model->addCondition('on_date',$_GET['on_date']);
	}

	if($g_dist_id = $this->api->stickyGET('distributor_id')){
		$payout_model->addCondition('distributor_id',$_GET['distributor_id']);
	}

	if(!$payout_model->count()->getOne()){
		$this->add('View')->set('No Record Found');
	}

	foreach ($payout_model as $key => $value) {
		$print_view = $this->add('xMLM/View_Print')->setModel($payout_model);
	}

	}
}