<?php


class page_xMLM_page_owner_credits extends page_xMLM_page_owner_main {
	
	function page_index(){

		$this->app->title='Credits Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Credits Management');

		$credit_model = $this->add('xMLM/Model_CreditMovement');

		$crud = $this->add('CRUD');
		$crud->setModel($credit_model);

		if($crud->isEditing('add')){
			$crud->form->getElement('distributor_id')->getModel()->addCondition('status','paid');
		}

		$crud->add('xHR/Controller_Acl',array('can_view'=>'All'));

	}
}