<?php

class page_xMLM_page_owner_forms extends page_xMLM_page_owner_main {

	function page_index(){
		// parent::init();

		$this->app->title='Form Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Forms/KYC Management');

		$form = $this->add('Form');
		

	}

}