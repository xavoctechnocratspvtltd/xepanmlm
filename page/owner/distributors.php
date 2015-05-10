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
		$red_tab = $tab->addTabURL('./unpaid','Unpaid Ids');
		$green_tab = $tab->addTabURL('./paid','Paid Ids');
		$inactive_tab = $tab->addTabURL('./unactive','UnActive / Blocked');
		$inactive_tab = $tab->addTabURL('./all','All Distributors');
		
		
		
	}

	function page_unpaid(){

		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$unpaid_model=$this->add('xMLM/Model_UnpaidIds');
		$crud->setModel($unpaid_model,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'),array('name','email','mobile_number','address','sponsor','introducer','username','password','re_password','item_name','created_at','is_active','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','greened_on','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			$crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
		}
	}

	function page_paid(){
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$crud->setModel('xMLM/PaidIds',array('status','name','email','mobile_number','address','sponsor','introducer','username','password','item_name','created_at','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','greened_on','left','right'));
		
		if(!$crud->isEditing()){
			$crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
		}
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));

	}

	function page_unactive(){
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		$crud->setModel('xMLM/BlockedIds',array('status','name','email','mobile_number','address','sponsor','introducer','username','password','item_name','created_at','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','greened_on','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			$crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'));
		}
	}

	function page_all(){
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor','allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));		
		$crud->setModel('xMLM/Distributor',array('status','name','email','mobile_number','address','sponsor','introducer','username','password','item_name','created_at','is_active','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount','greened_on','left','right'));
		// $crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
		if(!$crud->isEditing()){
			$crud->grid->add('misc/Export');
			$crud->grid->addPaginator(25);
			$crud->grid->addQuickSearch(array('name','sponsor','introducer','username','mobile_number','email'),null,'xMLM/Filter_Distributor');
		}
	}
}