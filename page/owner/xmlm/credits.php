<?php

class page_xMLM_page_owner_xmlm_credits extends page_xMLM_page_owner_xmlm_main{
	
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('container');
		$current_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

		$cols = $container->add('Columns');
		$trans_col = $cols->addColumn(8);
		$req_col = $cols->addColumn(4);

		$trans_col->add('View')->setHTML('Credits Movement <div class="atk-size-micro">Current Credits Available : '.$current_distributor['credit_purchase_points'].'</div>')->addClass('text-center atk-swatch-ink atk-size-exa atk-box');
		$trans_credit = $current_distributor->creditMovements();
		$trans_credit->addCondition('status',array('Purchase','Consumed','Collapsed'));
		$grid = $trans_col->add('Grid');
		$grid->setModel($trans_credit,array('status','created_at','credits','narration'));


		$req_col->add('View')->set('Request Credits')->addClass('text-center atk-swatch-yellow atk-size-exa atk-box');
		$form = $req_col->add('Form_Stacked');
		
		$req_col->add('H3')->set('Pending Requests');
		$grid = $req_col->add('Grid');

		$credit_request_model = $this->add('xMLM/Model_Credit_Request');
		$credit_request_model->addCondition('distributor_id',$current_distributor->id);

		$form->setModel($credit_request_model);
		// $form->addField('DropDown','request_for')->setEmptyText("Please select kit")->validateNotNull()->setModel('xMLM/Kit');
		// $form->addfield('Number','qty')->validateNotNull();
		// $form->addfield('Text','payment_details');
		$form->addSubmit('Request');

		if($form->isSubmitted()){
			$form->save();
			$form->js(null,array($form->js()->univ()->successMessage("Request Sent"),$grid->js()->reload()))->univ()->reload()->execute();
		}

		$grid->setModel('xMLM/CreditMovement',array('status','created_at','credits','narration'))->addCondition('status',array('Request','Canceled'));

	}
}