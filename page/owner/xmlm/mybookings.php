<?php

class page_xMLM_page_owner_xmlm_mybookings extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Bookings Here');
	}
}