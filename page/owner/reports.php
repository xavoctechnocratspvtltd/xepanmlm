<?php

class page_xMLM_page_owner_reports extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='xMLM Reports';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> xMLM Reports');
	}
}