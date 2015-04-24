<?php

class page_xMLM_page_owner_xmlm_profile extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View')->set('Update Profile')->addClass('text-center atk-swatch-green atk-size-exa atk-box');

		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		$distributor->getElement('username')->display(array('form'=>'Readonly'));
		$distributor->getElement('name')->display(array('form'=>'Readonly'));

		$form=$this->add('Form_Stacked');
		$form->setModel($distributor,array('name','email','username','password','re_password','mobile_number','address'));
		$form->addSubmit('Update');

		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->successMessage('Updated Successfullly')->execute();
		}
		$form->add('Controller_FormBeautifier');
	}
}