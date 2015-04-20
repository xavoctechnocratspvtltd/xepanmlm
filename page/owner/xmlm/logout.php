<?php

class page_xMLM_page_owner_xmlm_logout extends page_xMLM_page_owner_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Logout Here');
	}
}