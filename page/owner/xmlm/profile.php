<?php

class page_xMLM_page_owner_xmlm_profile extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('container');

		$container->add('View')->setHTML('Manage Your Profile')->addClass('text-center atk-swatch-green atk-size-exa atk-box');

		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		$distributor->getElement('username')->display(array('form'=>'Readonly'));
		$distributor->getElement('name')->display(array('form'=>'Readonly'));
		$distributor->getElement('sponsor_id')->display(array('form'=>'Readonly'))->group('top~3~Joining Info');
		$distributor->getElement('introducer_id')->display(array('form'=>'Readonly'))->group('top~3');
		$distributor->getElement('kit_item_id')->display(array('form'=>'Readonly'))->group('top~3');
		$distributor->getElement('Leg')->display(array('form'=>'Readonly'))->group('top~3');

		$form=$container->add('Form_Stacked');
		$form->setModel($distributor,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'));
		$form->addSubmit('Update');

		if($form->isSubmitted()){
			$form->save();
			$form->js()->univ()->successMessage('Updated Successfully')->execute();
		}
		$form->add('Controller_FormBeautifier');
	}
}