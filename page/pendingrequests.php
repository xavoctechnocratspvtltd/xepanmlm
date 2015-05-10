<?php


class page_xMLM_page_pendingrequests extends Page {
	function init(){
		parent::init();

		// 
		// Code To run before installing
		
		$this->add("xMLM/Model_CreditMovement")->email_authorities(true);
		
		// Code to run after installation
	}
}