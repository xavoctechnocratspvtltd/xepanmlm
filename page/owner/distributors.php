<?php


class page_xMLM_page_owner_distributors extends page_xMLM_page_owner_main {
	
	function page_index(){

		$this->app->title='Distributors Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Distributors Management');

		$bg=$this->add('View_BadgeGroup');
		$data=$this->add('xMLM/Model_Distributor')->count()->getOne();
		$v=$bg->add('View_Badge')->set('Total Distributors')->setCount($data)->setCountSwatch('ink');

		$data=$this->add('xMLM/Model_Distributor')->addCondition('greened_on','<>',null)->count()->getOne();
		$v=$bg->add('View_Badge')->set('Total Paid IDs')->setCount($data)->setCountSwatch('ink');

		$data=$this->add('xMLM/Model_Distributor')->addCondition('greened_on',null)->count()->getOne();
		$v=$bg->add('View_Badge')->set('Total UNPaid IDs')->setCount($data)->setCountSwatch('ink');

		$data=$this->add('xMLM/Model_Distributor')->addCondition('is_active',false)->count()->getOne();
		$v=$bg->add('View_Badge')->set('Total Blocked IDs')->setCount($data)->setCountSwatch('ink');

		$tab = $this->add('Tabs');
		$red_tab = $tab->addTabURL('./unpaid','Unpaid distributors');
		$green_tab = $tab->addTabURL('./paid','Paid distributors');
		$inactive_tab = $tab->addTabURL('./unactive','Inactive / Blocked distributors');
		$inactive_tab = $tab->addTabURL('./all','All distributors');
		
		
		
	}

	function page_unpaid(){		
		$export_model=$this->add('xMLM/Model_UnpaidIds');
		$export_model->getElement('name')->caption('Distributor name');
		$export_model->getElement('greened_on')->caption('Qualified date');
		$v = $this->add('View');
		$exp = $v->add('xMLM/Controller_Export',array('output_filename'=>'Unpaid_Ids_'.date('l jS \of F Y h:i:s A').'.csv','model'=>$export_model,'fields'=>array('name','email','mobile_number','address','sponsor','introducer','left','right','username','created_at','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount')));
		$exp->btn->addClass('atk-box atk-swatch-yellow');
		
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$unpaid_model=$this->add('xMLM/Model_UnpaidIds');
		// $unpaid_model->getElement('name')->caption('Distributor name');
		$crud->setModel($unpaid_model,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'),array('name','email','mobile_number','address','sponsor','introducer','username','re_password','item_name','created_at','is_active','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			// $crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
			
		}


	}

	function page_paid(){
		$export_model=$this->add('xMLM/Model_PaidIds');
		$export_model->getElement('name')->caption('Distributor name');
		$export_model->getElement('greened_on')->caption('Qualified date');
		$v = $this->add('View');
		$exp = $v->add('xMLM/Controller_Export',array('output_filename'=>'Paid_Ids_'.date('l jS \of F Y h:i:s A').'.csv','model'=>$export_model,'fields'=>array('name','email','mobile_number','address','sponsor','introducer','left','right','kit_item','username','created_at','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount')));
		$exp->btn->addClass('atk-box atk-swatch-yellow');
		$paid_model=$this->add('xMLM/Model_PaidIds');
		// $paid_model->getElement('name')->caption('Distributor Name');
		// $paid_model->getElement('greened_on')->caption('Qualified Date');
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$crud->setModel($paid_model,array('status','name','email','mobile_number','address','sponsor','introducer','username','item_name','created_at','kit_item','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','left','right'));
		
		if(!$crud->isEditing()){
			// $crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
			$crud->grid->removeColumn('greened_on');
		}


		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));

	}

	function page_unactive(){
		$export_model=$this->add('xMLM/Model_BlockedIds');
		$export_model->getElement('name')->caption('Distributor name');
		$export_model->getElement('greened_on')->caption('Qualified date');

		$v = $this->add('View');
		$exp = $v->add('xMLM/Controller_Export',array('output_filename'=>'UnActive_or_Blocked_Ids_'.date('l jS \of F Y h:i:s A').'.csv','model'=>$export_model,'fields'=>array('name','email','mobile_number','address','sponsor','introducer','left','right','kit_item','username','created_at','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount')));
		$exp->btn->addClass('atk-box atk-swatch-yellow');
		
		$unactive_model=$this->add('xMLM/Model_BlockedIds');
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$crud->setModel('xMLM/BlockedIds',array('status','name','email','mobile_number','address','sponsor','introducer','username','item_name','created_at','kit_item','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			// $crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
		}
	}

	function page_all(){
		$export_model=$this->add('xMLM/Model_Distributor');
		$export_model->getElement('name')->caption('Distributor name');
		$export_model->getElement('greened_on')->caption('Qualified date');
		$v = $this->add('View');
		$exp = $v->add('xMLM/Controller_Export',array('output_filename'=>'All_Distributors_'.date('l jS \of F Y h:i:s A').'.csv','model'=>$export_model,'fields'=>array('name','email','mobile_number','address','sponsor','introducer','left','right','kit_item','username','created_at','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount')));
		$exp->btn->addClass('atk-box atk-swatch-yellow');
		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->_dsql()->del('order')->order('created_at desc');

		$distributor->actions=array('allow_add'=>false,'allow_edit'=>array(),'allow_del'=>false);

		// $distributor->getElement('name')->caption('Distributor Name');
		// $distributor->getElement('greened_on')->caption('Qualified Date');
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor'));

		$crud->setModel($distributor,array('first_name','last_name','email','mobile_number','address','is_active','bank_id','account_no','IFCS_Code','branch_name'),array('status','name','email','mobile_number','address','sponsor','introducer','username','item_name','created_at','is_active','kit_item','greened_on','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','greened_on','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			// $crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'),null,'xMLM/Filter_Distributor');
			$crud->grid->removeColumn('greened_on');
		}

		$crud->add('xHR/Controller_Acl');
	}
}