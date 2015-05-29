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
		$tabs->addTabURL('./report_pays','Cumulative Payouts');
		$tabs->addTabURL('./inout','Income Expense Report');
	}

	function page_gen_pay(){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$cols = $this->add('Columns');
		$lc= $cols->addColumn(6);
		$rc= $cols->addColumn(6);

		$form = $lc->add('Form');
		$form->addField('Readonly','on_date')->set($this->api->now);
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
		$dist_j = $payout_model->join('xmlm_distributors','distributor_id');
		$dist_j->addField('greened_on');
		
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

		$payout_grid->addGrandTotals(array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount'));
		// $payout_grid->addGrandTotals();

		$payout_grid->addPaginator(100);
		$payout_grid->addSno();

		$payout_grid->add('xMLM/Controller_Export',
				array(
					'fields'=>array('distributor','session_left_pv','session_right_pv',
									'session_carried_left_pv','session_carried_right_pv','pairs',
									'session_business_volume','generation_level','generation_gross_amount',
									'pair_income','introduction_income','generation_difference_income','bonus',
									'total_pay',
									'tds','admin_charge','other_deduction','total_deduction',
									'net_amount','carried_amount','greened_on','on_date'
									),
					'totals'=>array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount')
					)
				);

		if($form->isSubmitted()){
			$payout_grid->js()->reload(array(
					'on_date'=>$form['closings'],
					'distributor_id'=>$form['distributor']
				))->execute();
		}

	}

	function page_report_pays(){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$cols = $this->add('Columns');
		$lc= $cols->addColumn(6);
		$rc= $cols->addColumn(6);

		$form = $lc->add('Form');
		$from_date_field = $form->addField('DatePicker','from_date');
		$to_date_field = $form->addField('DatePicker','to_date');
		
		$form->addSubmit('Get Details');

		$payout_model = $this->add('xMLM/Model_Payout');
		$dist_j = $payout_model->join('xmlm_distributors','distributor_id');
		$dist_j->addField('greened_on');
		
		if($from_date=$this->api->stickyGET('from_date')){
			$payout_model->addCondition('on_date','>=',$_GET['from_date']);
		}

		if($to_date=$this->api->stickyGET('to_date')){
			$payout_model->addCondition('on_date','<',$this->api->nextDate($_GET['to_date']));
		}

		if($g_dist_id = $this->api->stickyGET('distributor_id')){
			$payout_model->addCondition('distributor_id',$_GET['distributor_id']);
		}

		$q=$payout_model->dsql();

		$payout_model->addExpression('total_pair_income')->set($q->expr('sum(IF(net_amount>0,pair_income,0))'));
		$payout_model->addExpression('total_introduction_income')->set($q->expr('sum(IF(net_amount>0,introduction_income,0))'));
		$payout_model->addExpression('total_total_pay')->set($q->expr('sum(IF(net_amount>0,(introduction_income+pair_income+generation_difference_income+bonus),0))'));
		$payout_model->addExpression('total_tds')->set($q->expr('sum(IF(net_amount>0,tds,0))'));
		$payout_model->addExpression('total_admin_charge')->set($q->expr('sum(IF(net_amount>0,admin_charge,0))'));
		$payout_model->addExpression('total_total_deduction')->set($q->expr('sum(IF(net_amount>0,(tds+admin_charge+other_deduction),0))'));
		$payout_model->addExpression('total_net_amount')->set($q->expr('sum(net_amount)'));
		// $payout_model->addExpression('carried_amount')->set($q->expr('sum(xmlm_payouts.carried_amount)'));

		$payout_model->_dsql()->group('distributor_id');

		$payout_grid = $this->add('xMLM/Grid_Payout',array('hide_distributor'=>false,'generation_income'=>$config['include_generation']));
		$payout_grid->setModel($payout_model);

		$payout_grid->removeColumn('pair_income');
		$payout_grid->removeColumn('introduction_income');
		$payout_grid->removeColumn('total_pay');
		$payout_grid->removeColumn('tds');
		$payout_grid->removeColumn('admin_charge');
		$payout_grid->removeColumn('total_deduction');
		$payout_grid->removeColumn('net_amount');
		$payout_grid->removeColumn('carried_amount');
		$payout_grid->removeColumn('on_date');

		$payout_grid->addGrandTotals(array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount'));
		// $payout_grid->addGrandTotals();

		$payout_grid->addPaginator(100);
		$payout_grid->addSno();

		$payout_grid->add('xMLM/Controller_Export',
				array(
					'fields'=>array('distributor','session_left_pv','session_right_pv',
									'session_carried_left_pv','session_carried_right_pv','pairs',
									'session_business_volume','generation_level','generation_gross_amount',
									'pair_income','introduction_income','generation_difference_income','bonus',
									'total_pay',
									'tds','admin_charge','other_deduction','total_deduction',
									'net_amount','carried_amount','greened_on','on_date'
									),
					'totals'=>array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount')
					)
				);

		if($form->isSubmitted()){
			$payout_grid->js()->reload(array(
					'on_date'=>$form['closings'],
					'distributor_id'=>$form['distributor']
				))->execute();
		}

	}

	function page_inout(){
		$distributer=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('autocomplete/Basic','distributor')->setModel($distributer);
		$form->addSubmit('Get Report');

		$distributor_model = $this->add('xMLM/Model_Distributor');
		$fields=array('date','username');
		$amount_field = array();

        $from_date = $this->api->stickyGET('from_date')?:'1970-01-01';
        $to_date = $this->api->stickyGET('to_date')?:$this->api->today;

		foreach ($this->add('xMLM/Model_Kit') as $kit) {
            $kit_id= $kit->id;

            $distributor_model->addExpression($this->api->normalizeName($kit['name']).'_sold')->set(function($m,$q)use($kit_id, $from_date, $to_date){
            	$mycrds = $m->add('xMLM/Model_CreditMovement');
            	$mycrds->addCondition('distributor_id',$q->getField('id'));


                $solds = $m->add('xMLM/Model_Distributor',array('table_alias'=>'k'.$kit_id.'count'))
                		->addCondition('id','in',$mycrds->fieldQuery('joined_distributor_id'))
                		->addCondition('greened_on','>=',$from_date)
                		->addCondition('greened_on','<',$m->api->nextDate($to_date))
                		->addCondition('kit_item_id',$kit_id);

                return $solds->count();
            })->sortable(true);

            $amount_field[] = $fields[] = $this->api->normalizeName($kit['name']).'_sold';
        }

	    $distributor_model->addExpression('total_income')->set(function($m,$q)use($from_date, $to_date){
		    	$mycrds = $m->add('xMLM/Model_CreditMovement',array('table_alias'=>'ti'));
            	$mycrds->addCondition('distributor_id',$q->getField('id'));

	            $intros=$m->add('xMLM/Model_Distributor',array('table_alias'=>'total_income'))
	            		->addCondition('id','in',$mycrds->fieldQuery('joined_distributor_id'))
	            		->addCondition('greened_on','>=',$from_date)
	            		->addCondition('greened_on','<',$m->api->nextDate($to_date));
	            $kit_join = $intros->join('xshop_items','kit_item_id');
	            $kit_join->addField('sale_price');
	            
	            return $intros->sum('sale_price');

	    	})->sortable(true);
	    
	    $amount_field[] = $fields[]= 'total_income';

	    $distributor_model->addExpression('total_expense')->set(function($m,$q)use($from_date, $to_date){
	            $exp=$m->add('xMLM/Model_Payout',array('table_alias'=>'total_exp'))
	            		->addCondition('distributor_id',$q->getField('id'))
	            		->addCondition('on_date','>=',$from_date)
	            		->addCondition('on_date','<',$m->api->nextDate($to_date));
	            return $exp->sum('net_amount');

	    	})->sortable(true);

	    $amount_field[] = $fields[]= 'total_expense';

	    $fields[]= 'balance';

	    $or = $this->api->db->dsql()->orExpr();

	    foreach ($amount_field as $af) {
	    	$or->where($af,'>',0);
	    }
	    $distributor_model->_dsql()->having($or);


        if($this->api->stickyGET('distributor')){
        	$distributor_model->addCondition('id',$_GET['distributor']);
        }

		$grid = $this->add('xMLM/Grid_CreditReportSum',array('amount_fields'=>$amount_field, 'from_date'=>$from_date,'to_date'=>$to_date));

		$grid->setModel($distributor_model,$fields);
		$grid->addSno();
		$grid->addPaginator(20);
		$grid->addGrandTotals($amount_field);


		if($form->isSubmitted()){
			$grid->js()->reload(array(
					'from_date'=>$form['from_date']?:0,
					'to_date'=>$form['to_date']?:0,
					'distributor'=>$form['distributor']?:0,
				))->execute();
		}
	}

}