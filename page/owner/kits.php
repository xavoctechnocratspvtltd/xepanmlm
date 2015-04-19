<?php


class page_xMLM_page_owner_kits extends page_xMLM_page_owner_main {
	
	function page_index(){
		// parent::init();

		$this->app->title='Kit Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Kit Management');
		
		$crud = $this->add('CRUD');
		$crud->setModel('xMLM/Kit',array('name','code','created_at','sale_price','pv_value','bv_value','rp_value','purchase_point_required','is_publish'));
		$crud->addClass('kitcrud');
		// $crud->js('kit')->univ()->alert('cal');

		if(!$crud->isEditing()){
			$crud->grid->addColumn('Expander','update_points');
		}
	}

	function page_update_points(){
		$item_id = $this->api->stickyGet('xshop_items_id');
		$crud = $this->add('CRUD',array('allow_add'=>false,'allow_del'=>false));
		$spec_asso = $this->add('xShop/Model_ItemSpecificationAssociation')->addCondition('item_id',$item_id);
		$spec_asso->getElement('specification_id')->display(array('form'=>'Readonly'));
		$crud->setModel($spec_asso);

		// $crud->js('reload')->_selector('.kitcrud')->trigger('kit');

	}

}