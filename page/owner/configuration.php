<?php


class page_xMLM_page_owner_configuration extends page_xMLM_page_owner_main {
	
	function init(){
		parent::init();

		$this->add('xMLM/Controller_Acl');

		if($this->api->auth->model['type']!=100){
			$this->add('View_Error')->set('You are not Authorized.');
		return;
		}
			

		$this->app->title='MLM Configuration';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Configuration');

		$tabs = $this->add('Tabs');

		$basic_tab = $tabs->addTab('Business Info');

		$form = $basic_tab->add('Form_Stacked');
		$form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('admin_charge','other_charge_name','other_charge','tail_pv','minimum_payout_amount','include_generation','trimming_applicable','days_allowed_for_green','relations_with_nominee','credit_manager_email_id','when_id_becomes_green','when_id_becomes_orange','credit_request_approve_email','credit_request_processing_email'));
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

		$dis_tab=$mt->addTab('Distributor Configuration');
		$dis_mail=$dis_tab->add('Tabs');	
		
		$orange_admin=$dis_mail->addTab('Orange Admin mail');
		$orange_admin_form =$orange_admin->add('Form_Stacked');
		$orange_admin_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('orange_email_subject','orange_email_matter'));
		$orange_admin_form->addSubmit('update');
		if($orange_admin_form->isSubmitted()){
			$orange_admin_form->Update();
			$orange_admin_form->js(null,$orange_admin_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$green_admin=$dis_mail->addTab('Green Admin mail');
		$green_admin_form = $green_admin->add('Form_Stacked');
		$green_admin_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('green_email_subject','green_email_matter'));
		$green_admin_form->addSubmit('update');
		if($green_admin_form->isSubmitted()){
			$green_admin_form->Update();
			$green_admin_form->js(null,$green_admin_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$orange_dis=$dis_mail->addTab('Orange Distributor mail');
		$orange_form =$orange_dis->add('Form_Stacked');
		$orange_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('orange_distributor_email_subject','orange_distributor_mail_matter'));
		$orange_form->addSubmit('update');
		if($orange_form->isSubmitted()){
			$orange_form->Update();
			$orange_form->js(null,$orange_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$green_dis=$dis_mail->addTab('Green Distributor mail');
		$green_form = $green_dis->add('Form_Stacked');
		$green_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('green_distributor_email_subject','green_distributor_mail_matter'));
		$green_form->addSubmit('update');
		if($green_form->isSubmitted()){
			$green_form->Update();
			$green_form->js(null,$green_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}





		$approve_credit_request=$mt->addTab('Credit Request Approved mail');
		$approve_credit_request_form = $approve_credit_request->add('Form_Stacked');
		$approve_credit_request_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_approve_email_subject','credit_approve_email_matter'));
		$approve_credit_request_form->addSubmit('update');
		if($approve_credit_request_form->isSubmitted()){
			$approve_credit_request_form->Update();
			$approve_credit_request_form->js(null,$approve_credit_request_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$processed_credit_request=$mt->addTab('Credit Request Processed mail');
		$processed_credit_request_form = $processed_credit_request->add('Form_Stacked');
		$processed_credit_request_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_processed_email_subject','credit_processed_email_matter'));
		$processed_credit_request_form->addSubmit('update');
		if($processed_credit_request_form->isSubmitted()){
			$processed_credit_request_form->Update();
			$processed_credit_request_form->js(null,$processed_credit_request_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}
}