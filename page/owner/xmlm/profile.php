<?php

class page_xMLM_page_owner_xmlm_profile extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Update Profile Here');
		$distributor=$this->add('xMLM/Model_Distributor');
		$form=$this->add('Form_Stacked');
		$form->setModel($distributor,array('name','email','username','password','mobile_number','address'));
		$form->addSubmit('Update');
		$form->add('Controller_FormBeautifier');

		if($form->isSubmitted()){
			$distributor->update();
			$form->js()->univ()->successMessage('Updated Successfullly')->execute();
		}
	}
}