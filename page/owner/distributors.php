<?php


class page_xMLM_page_owner_distributors extends page_xMLM_page_owner_main {
	
	function init(){
		parent::init();

		$this->app->title='Distributors Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Distributors Management');

		$tab = $this->add('Tabs');
		$red_tab = $tab->addTab('Unpaid Ids');
		$green_tab = $tab->addTab('Paid Ids');
		
		$crud = $red_tab->add('CRUD',array('grid_class'=>'xMLM/Grid_Distributor'));
		
		$unpaid_model=$this->add('xMLM/Model_UnpaidIds');

		$crud->setModel($unpaid_model,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'),array('name','email','mobile_number','address','sponsor','introducer','username','password','re_password','item_name','created_at','is_active','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount'));
		$crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));

		$crud = $green_tab->add('CRUD');
		$crud->setModel('xMLM/PaidIds',array('status','name','email','mobile_number','address','sponsor','introducer','username','password','item_name','created_at','is_active','kit_item','session_left_pv','session_right_pv','total_left_pv','total_right_pv','carried_amount'));
		$crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));

	}
}