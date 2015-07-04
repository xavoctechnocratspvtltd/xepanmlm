<?php

class page_xMLM_page_owner_dashboard extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Distributors Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Dashboard');
		

		$this->add('View')->set('No of Active Users');
		$this->add('View')->set('Display no of kits distributed by category');

	}
}