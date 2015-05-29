<?php

class page_xMLM_page_owner_xmlm_profile extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('container');

		$container->add('View')->setHTML('Manage Your Profile')->addClass('text-center atk-swatch-green atk-size-exa atk-box');

		$distributor=$this->add('xMLM/Model_Distributor');
		$this->api->auth->addEncryptionHook($distributor);
		$distributor->loadLoggedIn();
		$distributor->getElement('username')->display(array('form'=>'Readonly'));
		$distributor->getElement('name')->display(array('form'=>'Readonly'));
		$distributor->getElement('sponsor_id')->display(array('form'=>'Readonly'))->group('top~3~Joining Info');
		$distributor->getElement('introducer_id')->display(array('form'=>'Readonly'))->group('top~3');
		$distributor->getElement('kit_item_id')->display(array('form'=>'Readonly'))->group('top~3');
		$distributor->getElement('Leg')->display(array('form'=>'Readonly'))->group('top~3');

		$form=$container->add('Form_Stacked');
		// $form->setModel($distributor,array('sponsor_id','Leg','introducer_id','kit_item_id','name','email','mobile_number','pan_no','address','username','password','re_password','name_of_bank','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age'));
		$form->setModel($distributor,array('sponsor_id','Leg','introducer_id','kit_item_id','first_name','last_name','date_of_birth','email','mobile_number','pan_no','block_no','building_no','landmark','pin_code','state_id','district_id','username','password','bank_id','IFCS_Code','nominee_name','account_no','branch_name','relation_with_nominee','nominee_age','nominee_email'));
		$form->addField('password','re_password')->setterGetter('group','b~4');
		$form->add('Order')
			->move('re_password','after','password')
			->now();

		$district_field = $form->getElement('district_id');

		$state_field = $form->getElement('state_id');

		if($this->api->stickyGET($state_field->name) OR $_REQUEST[$state_field->name])
			$district_field->getModel()->addCondition('state_id',$_REQUEST[$state_field->name]);
		else{
			if(!$distributor['state_id'])
				$district_field->getModel()->addCondition('state_id',-1);
			else
				$district_field->getModel()->addCondition('state_id',$distributor['state_id']);

		}

		$state_field->js('change',$form->js()->atk4_form('reloadField','district_id',array($this->api->url(null),$state_field->name=>$state_field->js()->val())));
		
		$dob_field = $form->getElement('date_of_birth');
		$dob_field->options=array('yearRange'=> "1942:2015");

		$form->addSubmit('Update');


		if($form->isSubmitted()){
			if(strlen($form['password']) < 6)
				$form->error('password','Legth must be greater than 6');

			if($form['password']!=$form['re_password'])
				$form->error('password','Password Must Match');

			if($form['password']=='~~~~~~') $form->getModel()->getElement('password')->destroy();

			$form->save();
			$form->js()->univ()->successMessage('Updated Successfully')->execute();
		}
		$form->add('Controller_FormBeautifier');

		$form->getElement('password')->set('~~~~~~');
		$form->getElement('re_password')->set('~~~~~~');
	}

}