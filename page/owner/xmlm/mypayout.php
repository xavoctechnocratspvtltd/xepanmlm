<?php

class page_xMLM_page_owner_xmlm_mypayout extends page_xMLM_page_owner_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('My Payout Here');

		$grid=$this->add('Grid');
		$grid->setModel('xMLM/Payout');
	}
}