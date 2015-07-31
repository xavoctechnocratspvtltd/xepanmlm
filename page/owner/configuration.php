<?php


class page_xMLM_page_owner_configuration extends page_xMLM_page_owner_main {
	
	function init(){
		parent::init();
		if($this->api->auth->model['type']!=100){
			$this->add('View_Error')->set('You are not Authorized.');
		return;
		}
			

		$this->app->title='MLM Configuration';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Configuration');

		$tabs = $this->add('Tabs');

		$basic_tab = $tabs->addTab('Business Info');

		$form = $basic_tab->add('Form_Stacked');
		$form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('admin_charge','other_charge_name','other_charge','tail_pv','minimum_payout_amount','include_generation','trimming_applicable','days_allowed_for_green','relations_with_nominee','credit_manager_email_id'));
		$form->addSubmit('update');
		if($form->isSubmitted()){
			$form->Update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$bv_slab_tab = $tabs->addTab('BV Slabs Info');
		$bv_slab_tab->add('CRUD')->setModel('xMLM/BVSlab',array('name','percentage'));

		$bv_form = $bv_slab_tab->add('Form');
		$bv_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('royalty_percentage','self_buiness_4_active_royalty','active_royalty_percentage'));
		$bv_form->addSubmit('Update');
		
		if($bv_form->isSubmitted()){
			$bv_form->save();
			$bv_form->js()->univ()->successMessage('Updated')->execute();
		}

		$sate_district = $tabs->addTab('State / Districts');
		$state_crud = $sate_district->add('CRUD');

		$state_crud->setModel('xMLM/State');
		$st_dist_crud = $state_crud->addRef('xMLM/District');


		$banks_tab= $tabs->addTab('Banks');
		$banks_crud= $banks_tab->add('CRUD');
		$banks_crud->setModel('xMLM/Bank');
		$banks_crud->grid->addPaginator($ipp=50);

		$mail_tab = $tabs->addTab('Mail configuration');
		$mt=$mail_tab->add('Tabs');
		$wt=$mt->addTab('Wel-Come mail Configuration');
		$mail_form = $wt->add('Form_Stacked');
		$mail_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('welcome_email_subject','welcome_email_matter'));
		$mail_form->addSubmit('update');
		if($mail_form->isSubmitted()){
			$mail_form->Update();
			$mail_form->js(null,$mail_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$ct=$mt->addTab('Credit movement mail');
		$credit_form = $ct->add('Form_Stacked');
		$credit_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_movement_email_subject','credit_movement_email_matter'));
		$credit_form->addSubmit('update');
		if($credit_form->isSubmitted()){
			$credit_form->Update();
			$credit_form->js(null,$credit_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
		

		$bt=$mt->addTab('Booking approve mail');
		$booking_form = $bt->add('Form_Stacked');
		$booking_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('booking_approve_email_subject','booking_approve_email_matter'));
		$booking_form->addSubmit('update');
		if($booking_form->isSubmitted()){
			$booking_form->Update();
			$booking_form->js(null,$booking_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}
}