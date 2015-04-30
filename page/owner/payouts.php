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
		$form = $this->add('Form');
		$form->addField('DatePicker','on_date');
		$form->addField('Checkbox','close_generation');
		$form->addSubmit('Close');

		if($form->isSubmitted()){
			$payout_m = $this->add('xMLM/Model_Payout');
			$payout_m->generatePayout($form['on_date'],$form['close_generation']);
			$form->js()->univ()->successMessage("Done")->execute();			
		}
	}

	function page_old_pays(){

		echo "old";
	}
}