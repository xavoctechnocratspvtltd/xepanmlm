<?php

class page_xMLM_page_owner_xmlm_newjoining extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('New Joining Here');
		// if(!$this->add('xMLM/Model_Configuration')->tryLoadAny()->get('new_joining_on')){
		// 	$this->add('View_Error')->set('Joining Stopped By Admin ...');
		// 	return;
		// }
		
		$form=$this->add('Form_Stacked');

		$distributor= $this->add('xMLM/Model_Distributor');
		$form->setModel($distributor,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'));
		$form->addSubmit('Register');			
		//Controller Added
		
		if($form->isSubmitted()){
			// $pin=$this->add('mBinaryApp/Model_Pin');
			// if(($msg=$pin->validatePin($form['distributor_id'],$form['pin']))!==true)
				// $form->displayError('pin',$msg);
			

			$distributor= $this->add('xMLM/Model_Distributor');		
			// if((!$distributor->is_available($form['username']))){
			// 	$form->js()->univ()->errorMessage('Username is already taken, try another')->execute();
			// 	// $form->displayError('username','Username is already taken, try another');
			// }
			
			
			// if($distributor->is_available($form['introducer_username'])){		
			// 	$form->js()->univ()->errorMessage('This Introducer name is not exist')->execute();
			// }

			// if($form['password']!=$form['re_password'])
			// 	$form->js()->univ()->errorMessage('Password Looks wrong')->execute();
			
			// $distributor->newJoining($form->getAllFields());
			$form->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Entry Done')->execute();
			$form->add('Controller_FormBeautifier');
		}
		$form->add('Controller_FormBeautifier');
	}
}