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
		$tabs->addTabURL('./analysis','Analytical Report');
	}

	function page_gen_pay(){
		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$cols = $this->add('Columns');
		$lc= $cols->addColumn(6);
		$rc= $cols->addColumn(6);


		$form = $lc->add('Form');
		$form->addField('Readonly','on_date','Date')->set($this->api->now);

		if($config['trimming_applicable']){
			$updt_btn = $rc->add('Button')->set('Update');
			$chv = $rc->add('xMLM/View_ClosingHint');

			$tb_f = $form->addField('Number','trimming_base')->set(0)->setFieldHint("Trimming applicable over the binary payouts");
			$tp_f = $form->addField('Number','trimming_percentage')->set(0)->setFieldHint("percentage figure (Not % sign) ");
			
			$updt_btn->js('click',$chv->js()->reload(array('hint_trimming_base'=>$tb_f->js()->val(),'hint_trimming_percentage'=>$tp_f->js()->val())));
		}


		if($config['include_generation'])
			$form->addField('Checkbox','close_generation','Close Generation Income');
		$field=$form->addField('line','captcha');
		$field->setAttr('PlaceHolder','Please enter the code shown above');
		// $field->belowField()->add('H5')->set('Please enter the code shown above');
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
					$trimming_base = 0;
					$trimming_percentage = 0;

					if($config['include_generation'])
						$close_generation = $form['close_generation'];

					if($config['trimming_applicable']){
						$trimming_base = $form['trimming_base'];
						$trimming_percentage = $form['trimming_percentage'];
					}

					$payout_m->generatePayout($form['on_date'],$close_generation, $trimming_base, $trimming_percentage);

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
		$closings_field = $form->addField('DropDown','closings','Closing')->setEmptyText('Please select any closing')->setModel($payouts_list);
		$form->addField('autocomplete/Basic','distributor','Distributor name')->setModel('xMLM/Distributor');
		$form->addSubmit('Get Details');

		$payout_model = $this->add('xMLM/Model_Payout');
		$dist_j = $payout_model->join('xmlm_distributors','distributor_id');
		$dist_j->addField('greened_on')->caption('Qualified Date');
		
		if($g_on_date=$this->api->stickyGET('on_date')){
			$payout_model->addCondition('on_date',$_GET['on_date']);
		}

		$g_dist_name="";

		if($g_dist_id = $this->api->stickyGET('distributor_id')){
			$payout_model->addCondition('distributor_id',$_GET['distributor_id']);
			$g_dist_name=$this->add('xMLM/Model_Distributor')->load($g_dist_id)->get('name');
		}

		if(!$g_on_date and !$g_dist_id)
			$payout_model->addCondition('id',-1);

		$payout_grid = $this->add('xMLM/Grid_Payout',array('hide_distributor'=>false,'sno_caption'=>'No'));//,'generation_income'=>$config['include_generation']));
		$payout_grid->setModel($payout_model);
			
		if($payout_model->count()->getOne()){
			$payout_grid->addGrandTotals(array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount'));
		}

		$payout_grid->addPaginator(100);
		$payout_grid->addSno();

		// $kit_array = $this->add('xMLM/Model_Kit')->getKitUcFirst();

		$payout_grid->add('xMLM/Controller_Export',
				array('output_filename'=>'Payout_'.$g_on_date."_".$g_dist_name.'.csv','model'=>$payout_model,
					'fields'=>array('on_date','distributor','previous_carried_amount','standard_kit_count','standard_kit_income',
									'gold_kit_count','gold_kit_income','pair_income','total_pay','tds','admin_charge',
									'other_deduction','total_deduction','net_amount','carried_amount','session_left_pv',
									'session_right_pv','pairs','session_carried_left_pv','session_carried_right_pv',
									'session_business_volume','generation_level','generation_gross_amount',
									'introduction_income','generation_difference_income','bonus',
									'greened_on'
									),
					'totals'=>array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount')
					)
				);

		if($form->isSubmitted()){

				$payout_grid->js()->reload(array(
						'on_date'=>$form['closings']?:'0',
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
		$from_date_field = $form->addField('DatePicker','from_date','From date');
		$to_date_field = $form->addField('DatePicker','to_date','To date');
		
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
		$payout_grid->removeColumn('previous_carried_amount');

		if($payout_model->count()->getOne()){
		$payout_grid->addGrandTotals(array('pair_income','introduction_income','tds','admin_charge','net_amount','carried_amount'));
		}
		// $payout_grid->addGrandTotals();

		$payout_grid->addPaginator(100);
		$payout_grid->addSno();

		$payout_grid->add('xMLM/Controller_Export',
				array('output_filename'=>'Payout Report Pay_'.$from_date.'_'.$to_date.'.csv','model'=>$payout_model,
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
					'from_date'=>$form['from_date']?:0,
					'to_date'=>$form['to_date']?:0,
					// 'distributor_id'=>$form['distributor']
				))->execute();
		}

	}

	function page_inout(){
		$this->template->loadTemplate('view/page/payoutinout');
		$distributer=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form',null,'form');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('autocomplete/Basic','distributor')->setModel($distributer);
		$form->addSubmit('Get Report');

		$distributor_model = $this->add('xMLM/Model_Distributor');
		$distributor_model->getElement('name')->caption('Distributor Name');
		$fields=array('date','name'); // I had given username here, client changed it to name
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

	    	})->sortable(true)->caption('Total Payout');

	    $amount_field[] = $fields[]= 'total_expense';

	    $fields[]= 'balance';

	    $or = $this->api->db->dsql()->orExpr();

	    foreach ($amount_field as $af) {
	    	$or->where($af,'>',0);
	    }
	    $distributor_model->_dsql()->having($or);

	    $m = clone $distributor_model;
        $sub_q = $m->_dsql()->del('limit')->del('order')->del('group')->del('having');
	    
	    $this->template->set('total_income',$m->sum('total_income')->getOne());
	    $this->template->set('total_expense',$m->sum('total_expense')->getOne());

        if($this->api->stickyGET('distributor')){
        	$distributor_model->addCondition('id',$_GET['distributor']);
        }

		$grid = $this->add('xMLM/Grid_CreditReportSum',array('amount_fields'=>$amount_field, 'from_date'=>$from_date,'to_date'=>$to_date),'grid');

		$grid->setModel($distributor_model,$fields);
		$grid->addSno();
		$grid->addPaginator(20);
		$grid->addGrandTotals($amount_field);
		$grid->addFormatter('name','name');

		if($form->isSubmitted()){
			$grid->js()->reload(array(
					'from_date'=>$form['from_date']?:0,
					'to_date'=>$form['to_date']?:0,
					'distributor'=>$form['distributor']?:0,
				))->execute();
		}
	}

	function page_analysis(){

		$this->add('View')
			->setHTML('Under Construction, Tentative Data, DATA TO BE RESET <br/>[FUND = (KIT PV * 4) + Introduction Income of Kit] ... Working ON Carried Amount')
			->addClass('atk-swatch-red atk-box-small text-center atk-text-bold');

		$payouts_list=$this->add('xMLM/Model_Payout');
		$payouts_list->addExpression('name')->set('on_date');
		$payouts_list->id_field = 'name';
		$payouts_list->_dsql()->group('name')->order('on_date');

		$fund_times = 4;

		$data=array();
		$grid_data=array();

		foreach ($payouts_list as $pl) {

			$last_payout_date=$this->add('xMLM/Model_Payout');
			$last_payout_date->addCondition('on_date','<',$pl['on_date']);
			$last_payout_date->setOrder('on_date','desc');

			$last_closing_date=$last_payout_date->tryLoadAny()->get('on_date')?:"1970-01-01";

			// Total Revenue 
			$total_revenue = $this->add('xMLM/Model_Kit');
			$dist_join = $total_revenue->join('xmlm_distributors.kit_item_id');
			$dist_join->addField('greened_on');
			// $total_revenue->addField('sale_price');

			$total_revenue = $total_revenue->addCondition('greened_on','<=',$pl['on_date'])
					->addCondition('greened_on',">",$last_closing_date)
					->sum('sale_price')->getOne();
			
			// Total Joining Fund 
			$total_binay_fund = $this->add('xMLM/Model_Kit');
			$total_binay_fund->join('xmlm_distributors.kit_item_id')
					->addField('greened_on');
			$total_binay_fund = $total_binay_fund->addCondition('greened_on','<=',$pl['on_date'])
					->sum('pv_value')->getOne();
			$total_binay_fund *= $fund_times;

			$total_intro_fund = $this->add('xMLM/Model_Kit');
			$total_intro_fund->join('xmlm_distributors.kit_item_id')
					->addField('greened_on');
			$total_intro_fund = $total_intro_fund->addCondition('greened_on','<=',$pl['on_date'])
					->sum('intro_value')->getOne();


			$total_out = $this->add('xMLM/Model_Payout');
			$exp = $total_out->addCondition('on_date','<=',$pl['on_date'])
					->sum('total_pay')->getOne();

			$admin_charge = $this->add('xMLM/Model_Payout');
			$admin_charge = $total_out->addCondition('on_date','<=',$pl['on_date'])
					->sum('admin_charge')->getOne();

			// ClosingJoining Fund 
			$closing_binay_fund = $this->add('xMLM/Model_Kit');
			$closing_binay_fund->join('xmlm_distributors.kit_item_id')
					->addField('greened_on');
			$closing_binay_fund = $closing_binay_fund->addCondition('greened_on','<=',$pl['on_date'])
					->addCondition('greened_on',">",$last_closing_date)
					->sum('pv_value')->getOne();
			$closing_binay_fund *= $fund_times;

			$closing_intro_fund = $this->add('xMLM/Model_Kit');
			$closing_intro_fund->join('xmlm_distributors.kit_item_id')
					->addField('greened_on');
			$closing_intro_fund = $closing_intro_fund->addCondition('greened_on','<=',$pl['on_date'])
					->addCondition('greened_on',">",$last_closing_date)
					->sum('intro_value')->getOne();

			$closing_total_out = $this->add('xMLM/Model_Payout');
			$closing_total_out = $closing_total_out->addCondition('on_date','<=',$pl['on_date'])
					->addCondition('on_date',">",$last_closing_date)
					->sum('total_pay')->getOne();

			$closing_admin_charge = $this->add('xMLM/Model_Payout');
			$closing_admin_charge = $closing_admin_charge->addCondition('on_date','<=',$pl['on_date'])
					->addCondition('on_date',">",$last_closing_date)
					->sum('admin_charge')->getOne();



			$data[] = array('series'=>"Total Revenue",'fund'=>$pl['on_date'],'payout'=>$total_revenue);

			$data[] = array('series'=>"Total Joining Fund",'fund'=>$pl['on_date'],'payout'=>$total_binay_fund + $total_intro_fund);
			$data[] = array('series'=>"Total Payouts",'fund'=>$pl['on_date'],'payout'=>($exp - $admin_charge));
			
			$data[] = array('series'=>"Closing Fund",'fund'=>$pl['on_date'],'payout'=> $closing_binay_fund +  $closing_intro_fund);
			$data[] = array('series'=>"Closing Payouts",'fund'=>$pl['on_date'],'payout'=>($closing_total_out - $closing_admin_charge));

			$grid_data[]= array(
					'closing_date'=>$pl['on_date'],
					'total_joining_fund'=>$total_binay_fund + $total_intro_fund,
					'total_payouts' => ($exp - $admin_charge),
					'closing_fund' => $closing_binay_fund +  $closing_intro_fund,
					'closing_payouts' => ($closing_total_out - $closing_admin_charge)
					);

		}


		// echo "<pre>";
		// print_r($data);							
		// echo "</pre>";

		$chart=$this->add('chart/Chart');
		foreach($data as $dt) {
			$y=$chart->addLineData($dt['series'],$dt['fund'],(int)$dt['payout']);
		}

		$chart
		->setXAxisTitle('Closings')
		// ->setXAxis($xaxis)
		->setYAxisTitle('Cumulative')
		->setTitle('Closing Report',null,'Cumulative Analysis')
		->setChartType('line')
		;


		$this->add('View')->set("Tabular Records");
		$grid = $this->add('Grid');
		
		$grid->addColumn('closing_date');
		$grid->addColumn('total_joining_fund');
		$grid->addColumn('total_payouts');
		$grid->addColumn('closing_fund');
		$grid->addColumn('closing_payouts');

		$grid->setSource($grid_data);


	}

}