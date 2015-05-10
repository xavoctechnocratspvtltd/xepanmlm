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
		$tabs->addTabURL('./old_pays','Payouts');
	}

	function page_gen_pay(){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$cols = $this->add('Columns');
		$lc= $cols->addColumn(6);
		$rc= $cols->addColumn(6);

		$form = $lc->add('Form');
		$form->addField('DatePicker','on_date');
		if($config['include_generation'])
			$form->addField('Checkbox','close_generation','Close Generation Income Also');
		$field=$form->addField('line','captcha');
		$field->belowField()->add('H5')->set('Please enter the code shown above');
		$field->add('x_captcha/Controller_Captcha');
		$form->addSubmit('Close');

		if($form->isSubmitted()){
			if (!$form->getElement('captcha')->captcha->isSame($form->get('captcha'))){
				$form->displayError('captcha','Wrong captcha');
			}

			$payout_m = $this->add('xMLM/Model_Payout');
			try{
				$this->api->db->beginTransaction();
					$close_generation = false;
					if($config['include_generation'])
						$close_generation = $form['close_generation'];
					$payout_m->generatePayout($form['on_date'],$close_generation);
				$this->api->db->commit();
			}catch(\Exception $e){
				$this->api->db->rollback();
				throw $e;
			}
			$form->js()->univ()->successMessage("Done")->execute();			
		}
	}

	function page_old_pays(){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$payouts_list=$this->add('xMLM/Model_Payout');
		$payouts_list->addExpression('name')->set('on_date');
		$payouts_list->id_field = 'name';
		$payouts_list->_dsql()->group('name');

		$cols = $this->add('Columns');
		$lc= $cols->addColumn(6);
		$rc= $cols->addColumn(6);

		$form = $lc->add('Form');
		$closings_field = $form->addField('DropDown','closings')->setEmptyText('Please select any closing')->setModel($payouts_list);
		$form->addField('autocomplete/Basic','distributor')->setModel('xMLM/Distributor');
		$form->addSubmit('Get Details');

		$payout_model = $this->add('xMLM/Model_Payout');
		$payout_model->join('xmlm_distributors','distributor_id')->addField('greened_on');
		
		if($g_on_date=$this->api->stickyGET('on_date')){
			$payout_model->addCondition('on_date',$_GET['on_date']);
		}

		if($g_dist_id = $this->api->stickyGET('distributor_id')){
			$payout_model->addCondition('distributor_id',$_GET['distributor_id']);
		}

		if(!$g_on_date and !$g_dist_id)
			$payout_model->addCondition('id',-1);


		$payout_grid = $this->add('xMLM/Grid_Payout',array('hide_distributor'=>false,'generation_income'=>$config['include_generation']));
		$payout_grid->setModel($payout_model);

		$payout_grid->addTotals(array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount'));
		// $payout_grid->addGrandTotals();

		// $payout_grid->addPaginator(100);
		$payout_grid->addSno();

		$payout_grid->add('misc/Export');

		if($form->isSubmitted()){
			$payout_grid->js()->reload(array(
					'on_date'=>$form['closings'],
					'distributor_id'=>$form['distributor']
				))->execute();
		}

	}
}