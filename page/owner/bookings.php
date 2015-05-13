<?php

class page_xMLM_page_owner_bookings extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Properties Booking';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Bookings');
	}
}