<?php

class page_xMLM_page_owner_xmlm_credits_sales extends page_xMLM_page_owner_main{
	
	function init(){
		parent::init();

		$distributer=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form');
		$form->addField('DatePicker','from_date','From date');
		$form->addField('DatePicker','to_date','To date');
		$form->addField('autocomplete/Basic','distributor')->setModel($distributer);
		$form->addSubmit('Get Report');

		$distributor_model = $this->add('xMLM/Model_Distributor');
		$fields=array('date','username','name');
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
            })->sortable(true)->caption(ucfirst(strtolower($kit['name'].' sold')));

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

	    	})->sortable(true)->caption('Total income');

	    $amount_field[] = $fields[]= 'total_income';

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