<?php

class page_xMLM_page_owner_xmlm_mypayout extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();
		$container=$this->add('View')->addClass('atk-size-micro');
		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		


		$cr_view = $container->add('View')->setHTML("My Payouts")->addClass('text-center atk-swatch-green atk-size-exa atk-box');
		
		$payout_model = $this->add('xMLM/Model_Payout');
		if(!$payout_model->count()->getone()){
			$grid = $this->add('Grid');
			$payout_model->addCondition('id',-1);
			$grid->setModel($payout_model);
		}else{
			$grid=$container->add('xMLM/Grid_Payout',array('generation_income'=>$config['include_generation']))->addStyle(array('overflow-x'=>'scroll'));
			$grid->setModel('xMLM/Payout')
					->addCondition('distributor_id',$distributor->id);
			$grid->addPaginator(1);
			$grid->addTotals(array());
			$grid->addGrandTotals(array('total_pay','tds','admin_charge','net_amount'));
		}


		
	}
}