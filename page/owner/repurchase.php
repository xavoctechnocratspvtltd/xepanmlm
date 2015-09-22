<?php

class page_xMLM_page_owner_repurchase extends page_xMLM_page_owner_main {
	
	function page_index(){

		$this->app->title='Repurchase Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Repurchase Management');

		$tab = $this->add('Tabs');
		$item_tab = $tab->addTabURL('./item','Repurchase item');
		$entry_tab = $tab->addTabURL('./entry','Repurchase entry');
		$report_tab = $tab->addTabURL('./report','Repurchase report');
	}

	function page_item(){

		$crud = $this->add('CRUD');
		$crud->setModel('xMLM/RepurchaseItem',array('name','bv','mrp'),array('name','bv','mrp'));
		if(!$crud->isEditing()){
			$crud->grid->addPaginator(50);
			$crud->grid->addQuickSearch(array('name'));
		}
	}

	function page_entry(){
		$tabs = $this->add('Tabs');
		$tabs->addTabURL('./draft','Draft');
		$tabs->addTabURL('./approved','Approved');
	}

	function page_entry_draft(){
		// $crud=$this->add('CRUD',array('grid_class'=>'xShop/Grid_Order','add_form_beautifier'=>false));
		$draft_crud = $this->add('CRUD');
		$draft_crud->setModel('xMLM/Repurchase_Entry_Draft',array('distributor_id','repurchaseitem_id','narration'),array('distributor','repurchaseitem','narration'));
		$draft_crud->add('xHR/Controller_Acl');
		if(!$draft_crud->isEditing()){
			$draft_crud->grid->addPaginator(50);
			$draft_crud->grid->addQuickSearch(array('distributor','repurchaseitem'));
		}
	}

	function page_entry_approved(){
		$approved_crud = $this->add('CRUD',array('allow_add'=>false));
		$approved_crud->setModel('xMLM/Repurchase_Entry_Approved',array('distributor_id','repurchaseitem_id','narration'),array('distributor','repurchaseitem','narration'));
		if(!$approved_crud->isEditing()){
			$approved_crud->grid->addPaginator(50);
			$approved_crud->grid->addQuickSearch(array('distributor','repurchaseitem'));
		}
	}

	function page_report(){
		
		$crud = $this->add('CRUD',array('allow_add'=>false));
		$crud->setModel('xMLM/RepurchaseEntry');
	}

}