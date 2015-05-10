<?php

class page_xMLM_page_owner_xmlm_credits_canceled extends page_xMLM_page_owner_main{
	
	function page_index(){
		$credit_model = $this->add('xMLM/Model_Credit_Canceled');

		$crud = $this->add('CRUD');
		$crud->setModel($credit_model,array('created_at','distributor','credits','narration'));

		if($crud->isEditing('add')){
			$crud->form->getElement('distributor_id')->getModel()->addCondition('status','paid');
		}

		$crud->add('xHR/Controller_Acl',array('override'=>array('can_view'=>'All')));
	}
}