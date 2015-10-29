<?php

class page_xMLM_page_owner_xmlm_credits_report extends page_xMLM_page_owner_main{
	
	function init(){
		parent::init();

		$distributer=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('autocomplete/Basic','distributor')->setModel($distributer);
		$form->addField('DropDown','status')->setValueList(array('approved'=>'Approved','Purchase'=>'Purchase',
																 'Consumed'=>'Consumed','Rejected'=>'Rejected','Request'=>'Request'))
											->setEmptyText('Please Select Status');
		$form->addSubmit('Get Report');

		$credit_mov=$this->add('xMLM/Model_CreditMovement');
		$credit_mov->addExpression('credit')->set($credit_mov->dsql()->fx('IF',array($credit_mov->dsql()->expr('status="Purchase"'),$credit_mov->dsql()->expr('credits'),'0')));
		$credit_mov->addExpression('debit')->set($credit_mov->dsql()->fx('IF',array($credit_mov->dsql()->expr('status="Consumed"'),$credit_mov->dsql()->expr('credits'),'0')));
		
		$credit_mov->getElement('created_at')->caption('Request Date');	
		$credit_mov->getElement('credits_given_on')->caption('Request Approval Date ');	
		$credit_mov->getElement('credits')->caption('Request Amount ');	

		$grid=$this->add('xMLM/Grid_CreditMovement');

			$q = $credit_mov->dsql();



		$distributor_name="";
		
		$this->api->stickyGET('distributor_id');
		$this->api->stickyGET('from_date');
		$this->api->stickyGET('to_date');
		$this->api->stickyGET('status');

		if($_GET['distributor_id']){
			$credit_mov->addCondition('distributor_id',$_GET['distributor_id']);
			$distributor_name=$this->add('xMLM/Model_Distributor')->load($_GET['distributor_id'])->get('name');
		}

		if($_GET['from_date']){
			$credit_mov->addCondition(
					$q->orExpr()
						->where(
								$q->andExpr()
									// ->where('status','Purchase')
									->where('credits_given_on','>=',$_GET['from_date'])
							)
						->where(
								$q->andExpr()
									// ->where('status','Consumed')
									->where('created_at','>=',$_GET['from_date'])
							)
				);
		}

		if($_GET['to_date']){
			$credit_mov->addCondition(
					$q->orExpr()
						->where(
								$q->andExpr()
									// ->where('status','Purchase')
									->where('credits_given_on','<',$this->api->nextDate($_GET['to_date']))
							)
						->where(
								$q->andExpr()
									// ->where('status','Consumed')
									->where('created_at','<',$this->api->nextDate($_GET['to_date']))
							)
				);
		}
		


		if($_GET['status']){
			$credit_mov->addCondition('status',$_GET['status']);
		}

			

		$grid->setModel($credit_mov);
		$grid->add('xMLM/Controller_Export',
				array('output_filename'=>'credit Report'.$form['from_date']."_".$form['to_date']."_".$distributor_name.'.csv','model'=>$credit_mov,
					'fields'=>array('distributor','status',
									'created_at','credits_given_on',
									'credit','debit','credits'
									),
					'totals'=>array('credit','debit')
					)
				);	

		$grid->removeColumn('item_name');
		$grid->removeColumn('created_by');
		$grid->removeColumn('related_document');
		$grid->removeColumn('distributor_id');
		$grid->addPaginator(50);
		// $grid->addQuickSearch(array(,'status','credits','credits_given_on'));
		$grid->addGrandTotals(array('credit','debit'));


		if($form->isSubmitted()){
			$grid->js()->reload(
						array(
							'distributor_id'=>$form['distributor'],
							'from_date'=>($form['from_date'])?:0,
							'to_date'=>($form['to_date'])?:0,
							'status'=>($form['status'])?:0,
							)
						)->execute();
		}
	}
}