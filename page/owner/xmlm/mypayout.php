<?php

class page_xMLM_page_owner_xmlm_mypayout extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('atk-size-micro');
		$distributor=$this->add('xMLM/Model_Distributor');
		
		foreach ($this->add('xMLM/Model_Kit') as $kit) {
        
        }

		$distributor->loadLoggedIn();

		$cr_view = $container->add('View')->setHTML("My Payouts")->addClass('text-center atk-swatch-green atk-size-exa atk-box');

		$grid=$container->add('xMLM/Grid_Payout');
		$grid->setModel('xMLM/Payout')
				->addCondition('distributor_id',$distributor->id);

		
	}
}