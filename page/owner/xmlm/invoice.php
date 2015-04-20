<?php

class page_xMLM_page_owner_xmlm_invoice extends page_xMLM_page_owner_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Invoice Here');

		$this->add('xMLM/View_Invoice');
	}
}