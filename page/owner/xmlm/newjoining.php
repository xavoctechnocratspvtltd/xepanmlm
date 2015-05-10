<?php

class page_xMLM_page_owner_xmlm_newjoining extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('container');

		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		$credits = $distributor['credit_purchase_points'];
		$cr_view = $container->add('View')->setHTML("New Joining <small>[$credits Credits]</small>")->addClass('text-center atk-swatch-blue atk-size-exa atk-box');
		// if(!$this->add('xMLM/Model_Configuration')->tryLoadAny()->get('new_joining_on')){
		// 	$this->add('View_Error')->set('Joining Stopped By Admin ...');
		// 	return;
		// }
		
		$form=$container->add('Form_Stacked');

		$distributor= $this->add('xMLM/Model_Distributor');
		$form->setModel($distributor,array('sponsor_id','Leg','introducer_id','kit_item_id','first_name','last_name','date_of_birth','email','mobile_number','pan_no','block_no','building_no','landmark','pin_code','state_id','district_id','username','password','re_password','bank_id','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age','nominee_email'));
		$form->getElement('kit_item_id')->setEmptyText('Free/Red Entry');
		$form->addSubmit('Register');			
		
		$form->getElement('password')->js(true)->_load('pwstrength-bootstrap-1.2.5.min')->pwstrength();
		$dob_field = $form->getElement('date_of_birth');
		$dob_field->options=array('yearRange'=> "1942:2015");

		if($this->api->stickyGET('sponsor_id')){
			$form->getElement('sponsor_id')->set($_GET['sponsor_id']);
			$form->getElement('Leg')->set($_GET['Leg']);
		}
		
		if($form->isSubmitted()){

			if(strlen($form['password']) < 6)
				$form->error('password','Legth must be greater than 6');

			$distributor= $this->add('xMLM/Model_Distributor');		
			$form->save();
			$form->js(null,array($form->js()->reload(),$cr_view->js()->reload()))->univ()->successMessage('Entry Done')->execute();
		}
		$form->add('Controller_FormBeautifier');
	}
}