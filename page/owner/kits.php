<?php


class page_xMLM_page_owner_kits extends page_xMLM_page_owner_main {
	
	function page_index(){
		// parent::init();

		$this->app->title='Startup Package Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Startup Package Management');
		
		$crud = $this->add('CRUD');
		$crud->setModel('xMLM/Kit',array('name','code','created_at','sale_price','pv_value','bv_value','rp_value','cap_value','intro_value','color_value','is_publish'));
		$crud->addClass('kitcrud');
		// $crud->js('kit')->univ()->alert('cal');

		if(!$crud->isEditing()){
			$crud->grid->addColumn('Expander','update_points');
		}

		$crud->add('xHR/Controller_Acl');
	}

	function page_update_points(){
		$item_id = $this->api->stickyGet('xshop_items_id');
		$crud = $this->add('CRUD');//,array('allow_add'=>false,'allow_del'=>false));
		$spec_asso = $this->add('xShop/Model_ItemSpecificationAssociation')->addCondition('item_id',$item_id);
		$spec_asso->getElement('specification_id')->display(array('form'=>'Readonly'));
		$crud->setModel($spec_asso);
		$crud->add('xHR/Controller_Acl');

		$grid=$crud->grid;
		if($grid->hasColumn('item_name'))$grid->removeColumn('item_name');
		if($grid->hasColumn('created_by'))$grid->removeColumn('created_by');
		if($grid->hasColumn('related_document'))$grid->removeColumn('related_document');
		if($grid->hasColumn('created_date'))$grid->removeColumn('created_date');
		if($grid->hasColumn('updated_date'))$grid->removeColumn('updated_date');
		// $crud->js('reload')->_selector('.kitcrud')->trigger('kit');

	}

}