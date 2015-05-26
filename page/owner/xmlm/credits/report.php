<?php

class page_xMLM_page_owner_xmlm_credits_report extends page_xMLM_page_owner_main{
	
	function init(){
		parent::init();

		$distributer=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('autocomplete/Basic','distributor')->setModel($distributer);
		$form->addSubmit('Get Report');

		$credit_mov=$this->add('xMLM/Model_CreditMovement');
		$credit_mov->addExpression('credit')->set($credit_mov->dsql()->fx('IF',array($credit_mov->dsql()->expr('status="Purchase"'),$credit_mov->dsql()->expr('credits'),'0')));
		$credit_mov->addExpression('debit')->set($credit_mov->dsql()->fx('IF',array($credit_mov->dsql()->expr('status="Consumed"'),$credit_mov->dsql()->expr('credits'),'0')));
		

		$grid=$this->add('xMLM/Grid_CreditMovement');

			$this->api->stickyGET('distributor_id');
			$this->api->stickyGET('from_date');
			$this->api->stickyGET('to_date');

			$q = $credit_mov->dsql();

		
			if($_GET['distributor_id']){
				$credit_mov->addCondition('distributor_id',$_GET['distributor_id']);
			}

			if($_GET['from_date']){
				$credit_mov->addCondition(
						$q->orExpr()
							->where(
									$q->andExpr()
										->where('status','Purchase')
										->where('credits_given_on','>=',$_GET['from_date'])
								)
							->where(
									$q->andExpr()
										->where('status','Consumed')
										->where('created_at','>=',$_GET['from_date'])
								)
					);
			}

			if($_GET['to_date']){
				$credit_mov->addCondition(
						$q->orExpr()
							->where(
									$q->andExpr()
										->where('status','Purchase')
										->where('credits_given_on','<',$this->api->nextDate($_GET['to_date']))
								)
							->where(
									$q->andExpr()
										->where('status','Consumed')
										->where('created_at','<',$this->api->nextDate($_GET['to_date']))
								)
					);
			}


		$grid->setModel($credit_mov, array('status','created_at','credits_given_on','credit','debit','narration'));

		$grid->removeColumn('item_name');
		$grid->removeColumn('created_by');
		$grid->removeColumn('related_document');

		$grid->addQuickSearch(array('status','credits','credits_given_on'));

		if($form->isSubmitted()){
			$grid->js()->reload(
						array(
							'distributor_id'=>$form['distributor'],
							'from_date'=>($form['from_date'])?:0,
							'to_date'=>($form['to_date'])?:0,
							)
						)->execute();
		}
	}
}