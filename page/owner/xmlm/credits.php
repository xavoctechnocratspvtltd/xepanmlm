<?php

class page_xMLM_page_owner_xmlm_credits extends page_xMLM_page_owner_xmlm_main{
	
	function init(){
		parent::init();

		$cols = $this->add('Columns');
		$trans_col = $cols->addColumn(8);
		$req_col = $cols->addColumn(4);

		$trans_col->add('View')->set('Credits Movement')->addClass('text-center atk-swatch-ink atk-size-exa atk-box');
		$current_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();
		$trans_credit = $current_distributor->creditMovements();
		$trans_credit->addCondition('action',array('Purchase','Consumed','Collapsed'));
		$grid = $trans_col->add('Grid');
		$grid->setModel($trans_credit);


		$req_col->add('View')->set('Request Credits')->addClass('text-center atk-swatch-yellow atk-size-exa atk-box');
		$form = $req_col->add('Form_Stacked');
		$form->addField('DropDown','request_for')->setModel('xMLM/Kit');
		$form->addfield('Number','qty');
		$form->addfield('Text','payment_details');
		$form->addSubmit('Request');

	}
}