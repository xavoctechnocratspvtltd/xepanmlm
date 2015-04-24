<?php


class page_xMLM_page_owner_payouts extends page_xMLM_page_owner_main {
	
	function init(){
		parent::init();

		$this->app->title='Payouts Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Payouts Management');

	}

	function page_index(){
		$tabs= $this->add('Tabs');
		$tabs->addTabURL('./gen_pay','Generate Payout');
		$tabs->addTabURL('./old_pays','Old Payouts');
	}

	function page_gen_pay(){
		$this->add('View')->set('');
	}

	function page_old_pays(){

		echo "old";
	}
}