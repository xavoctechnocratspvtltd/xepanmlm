<?php


class page_xMLM_page_owner_configuration extends page_xMLM_page_owner_main {
	
	function page_index(){
		// parent::init();

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
		$form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('admin_charge','other_charge_name','other_charge','tail_pv','minimum_payout_amount','include_generation','trimming_applicable','days_allowed_for_green','relations_with_nominee'/*,'distributor_join_emails','credit_manager_email_id','when_id_becomes_green','when_id_becomes_orange','credit_request_approve_email','credit_request_processing_email'*/));
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

		$mt->addTabURL('./welComeMailConfig','Wel-Come mail Configuration');
		$mt->addTabURL('./creditRequestEmail','Credit movement mail');
		$mt->addTabURL('./bookingApproveEmail','Booking approve mail');
		$mt->addTabURL('./distributorConfig','Distributor Configuration');
		$mt->addTabURL('./creditApproveEmail','Credit Request Approved mail');
		$mt->addTabURL('./creditProcessedEmail','Credit Request Processed mail');


		//Payout Print Format
		$payout_tab = $tabs->addTab('Print Format');
		$pt=$payout_tab->add('Tabs');
		$pt->addTabURL('./payoutprint','Payout Print Format');


        /*SMS Setting*/

		$sms_tab = $tabs->addTab('SMS Settings');
		$sms_form = $sms_tab->add('Form_Stacked');
		$sms_form->setModel($this->api->current_website,
														array('gateway_url','sms_user_name_qs_param',
															'sms_username','sms_password_qs_param',
															'sms_password','sms_number_qs_param',
															'sm_message_qs_param','sms_prefix',
															'sms_postfix'
														)
							);
		
		$sms_form->addSubmit('Update');
		$sms_form->add('Controller_FormBeautifier');

		if($sms_form->isSubmitted()){
			$sms_form->save();
			$sms_form->js(null,$sms_form->js()->reload())->univ()->successMessage('Updated')->execute();
		}



	}

	function page_payoutprint(){
		$print_form = $this->add('Form_Stacked');
		$print_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('payout_print_format'));
		$print_form->addSubmit('update');
		if($print_form->isSubmitted()){
			$print_form->Update();
			$print_form->js(null,$print_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_welComeMailConfig(){
		$mail_form = $this->add('Form_Stacked');
		$mail_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('distributor_join_emails','welcome_email_subject','welcome_email_matter'));
		$mail_form->addSubmit('update');
		if($mail_form->isSubmitted()){
			$mail_form->Update();
			$mail_form->js(null,$mail_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}
	function page_creditRequestEmail(){
		$credit_form = $this->add('Form_Stacked');
		$credit_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_manager_email_id','credit_movement_email_subject','credit_movement_email_matter'));
		$credit_form->addSubmit('update');
		if($credit_form->isSubmitted()){
			$credit_form->Update();
			$credit_form->js(null,$credit_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_bookingApproveEmail(){
		$booking_form = $this->add('Form_Stacked');
		$booking_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('booking_approve_email_subject','booking_approve_email_matter'));
		$booking_form->addSubmit('update');
		if($booking_form->isSubmitted()){
			$booking_form->Update();
			$booking_form->js(null,$booking_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_distributorConfig(){

		$dis_mail=$this->add('Tabs');	
		$dis_mail->addTabURL('../orageAdminEmail','Orange Admin mail');
		$dis_mail->addTabURL('../greenAdminEmail','Green Admin mail');
		$dis_mail->addTabURL('../orangeDistributorEmail','Orange Distributor mail');
		$dis_mail->addTabURL('../greenDistributorEmail','Green Distributor mail');
	}
		

	function page_orageAdminEmail(){
		$orange_admin_form =$this->add('Form_Stacked');
		$orange_admin_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('when_id_becomes_orange','orange_email_subject','orange_email_matter'));
		$orange_admin_form->addSubmit('update');
		if($orange_admin_form->isSubmitted()){
			$orange_admin_form->Update();
			$orange_admin_form->js(null,$orange_admin_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_greenAdminEmail(){
		$green_admin_form = $this->add('Form_Stacked');
		$green_admin_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('when_id_becomes_green','green_email_subject','green_email_matter'));
		$green_admin_form->addSubmit('update');
		if($green_admin_form->isSubmitted()){
			$green_admin_form->Update();
			$green_admin_form->js(null,$green_admin_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_orangeDistributorEmail(){
		$orange_form =$this->add('Form_Stacked');
		$orange_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('orange_distributor_email_subject','orange_distributor_mail_matter'));
		$orange_form->addSubmit('update');
		if($orange_form->isSubmitted()){
			$orange_form->Update();
			$orange_form->js(null,$orange_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_greenDistributorEmail(){
		$green_form = $this->add('Form_Stacked');
		$green_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('green_distributor_email_subject','green_distributor_mail_matter'));
		$green_form->addSubmit('update');
		if($green_form->isSubmitted()){
			$green_form->Update();
			$green_form->js(null,$green_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_creditApproveEmail(){
		$approve_credit_request_form = $this->add('Form_Stacked');
		$approve_credit_request_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_request_approve_email','credit_approve_email_subject','credit_approve_email_matter'));
		$approve_credit_request_form->addSubmit('update');
		if($approve_credit_request_form->isSubmitted()){
			$approve_credit_request_form->Update();
			$approve_credit_request_form->js(null,$approve_credit_request_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}

	function page_creditProcessedEmail(){
		$processed_credit_request_form = $this->add('Form_Stacked');
		$processed_credit_request_form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny(),array('credit_request_processing_email','credit_processed_email_subject','credit_processed_email_matter'));
		$processed_credit_request_form->addSubmit('update');
		if($processed_credit_request_form->isSubmitted()){
			$processed_credit_request_form->Update();
			$processed_credit_request_form->js(null,$processed_credit_request_form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}
	}
}