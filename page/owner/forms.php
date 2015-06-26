<?php

class page_xMLM_page_owner_forms extends page_xMLM_page_owner_main {

	function page_index(){
		// parent::init();

		$this->app->title='Form Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Forms/KYC Management');


		$forms_alloted = $this->add('xMLM/Model_FormAllot');

		$crud = $this->add('CRUD');

		$crud->setModel($forms_alloted,array('created_at','distributor_id','from_no','to_no'),array('created_at','distributor','from_no','to_no'));

		$crud->add('xHR/Controller_Acl');

	}

}