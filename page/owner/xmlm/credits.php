<?php

class page_xMLM_page_owner_xmlm_credits extends page_xMLM_page_owner_xmlm_main{
	
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('');
		$current_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

		$export_model = $current_distributor->creditMovements()->setOrder('created_at');
		$export_model->getElement('created_at')->caption('Request Date');
		$export_model->getElement('narration')->caption('Remark');
		$export_model->addExpression('credit')->set($export_model->dsql()->fx('IF',array($export_model->dsql()->expr('status="Purchase"'),$export_model->dsql()->expr('credits'),'0')));
		$export_model->addExpression('debit')->set($export_model->dsql()->fx('IF',array($export_model->dsql()->expr('status="Consumed"'),$export_model->dsql()->expr('credits'),'0')));
		
		
		$cols = $container->add('Columns');
		$trans_col = $cols->addColumn(8);
		$req_col = $cols->addColumn(4);

		$trans_col->add('View')->setHTML('Credits Movement <div class="atk-size-micro">Current Credits Available : '.$current_distributor['credit_purchase_points'].'</div>')->addClass('text-center atk-swatch-ink atk-size-exa atk-box');
		$exp = $trans_col->add('xMLM/Controller_Export',array('output_filename'=>'distributor_credit_repot.csv','model'=>$export_model,'fields'=>array('status','credit','debit','created_at','narration'),'totals'=>array('credit','debit')));
		$exp->btn->addClass('atk-box atk-swatch-yellow atk-move-center');

		$trans_cr_dr_col = $trans_col->add('Columns');
		$trans_cr_col = $trans_cr_dr_col->addColumn(6);
		$trans_dr_col = $trans_cr_dr_col->addColumn(6);

		$trans_credit = $current_distributor->creditMovements()->setOrder('created_at','desc');
		$trans_credit->addCondition('status',array('Purchase'));
		$trans_cr_col->add('H3')->setHTML('Credits<br/><small>'.$trans_credit->sum('credits')->getOne().' /-</small>')->addClass('text-center');
		

		$grid = $trans_cr_col->add('Grid');
		$grid->setModel($trans_credit,array('created_at','credits'));
		$grid->addPaginator(20);
		// $grid->addTotals(array('credits'));

		$trans_debit = $current_distributor->creditMovements()->setOrder('created_at','desc');
		$trans_debit->addCondition('status',array('Consumed','Collapsed'));
		
		$trans_dr_col->add('H3')->setHTML('Debits<br/><small>'.$trans_debit->sum('credits')->getOne().' /-</small>')->addClass('text-center');
		$grid = $trans_dr_col->add('Grid');
		$grid->setModel($trans_debit,array('created_at','credits','narration'));
		// $grid->add('xMLM/Controller_Export');
		// $grid->addSno(50);
		$grid->addPaginator(20);
		// $grid->addTotals(array('credits'));
		// $grid->addGrandTotals(array('credits'));

		$req_col->add('View')->set('Request Credits')->addClass('text-center atk-swatch-yellow atk-size-exa atk-box');
		$form = $req_col->add('Form_Stacked');
		
		$req_col->add('H3')->set('Pending Requests');
		$grid = $req_col->add('xMLM/Grid_CreditMovement');

		$credit_request_model = $this->add('xMLM/Model_Credit_Request');
		$credit_request_model->addCondition('distributor_id',$current_distributor->id);
		$form->addField('Readonly','request_date')->set($this->api->today);
		$form->setModel($credit_request_model,array('credits','narration','attachment_id'));
		// $form->addField('DropDown','request_for')->setEmptyText("Please select kit")->validateNotNull()->setModel('xMLM/Kit');
		// $form->addfield('Number','qty')->validateNotNull();
		// $form->addfield('Text','payment_details');
		$form->addSubmit('Request');

		if($form->isSubmitted()){
			if($form['credits'] > 500000)
				$form->displayError('credits','Credit Limit : 5,00,000/-');
			if($form['credits'] < 6000)
				$form->displayError('credits','Must be greater then : 6,000/-');
			
			$form->save();

			try{
				$form->model->email_authorities();
			}catch(\Exception $e){
				$form->js(null,array($form->js()->univ()->errorMessage("Request Sent, Admin not emailed"),$grid->js()->reload()))->univ()->reload()->execute();
			}
			
			$form->js(null,array($form->js()->univ()->successMessage("Request Sent"),$grid->js()->reload()))->univ()->reload()->execute();
		}

		$credit_request_list_model = $this->add('xMLM/Model_CreditMovement');
		$credit_request_list_model->addCondition('distributor_id',$current_distributor->id);
		$grid->setModel($credit_request_list_model,array('attachment','created_at','credits','narration','status'))->addCondition('status',array('Request','Canceled'));

		$grid->addMethod('format_req',function($g,$f){
			if($g->model['status'] == 'Canceled'){
				$g->setTDParam($f,'style/color','red');
				$g->setTDParam($f,'style/text-decoration','line-through');
				$g->setTDParam($f,'title','Canceled Request: '. $g->add('xCRM/Model_Activity')->loadWhoseRelatedDocIs($g->model)->addCondition('action','Canceled')->tryLoadAny()->get('message'));
			}
			else{
				$g->setTDParam($f,'style/color','');
				$g->setTDParam($f,'text-decoration','');
				$g->setTDParam($f,'title','');
				$g->setTDParam($f,'title','');
			}
		});

		$grid->addFormatter('credits','req');
		$grid->removeColumn('status');
		$grid->js(true)->xtooltip();

	}
}