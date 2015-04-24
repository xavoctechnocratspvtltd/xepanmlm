<?php

class page_xMLM_page_owner_xmlm_main extends Page {
	function init(){
		parent::init();
		$this->current_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();
		if(!$this->current_distributor){
			$this->add('View')->set('Session timeout or You are not Distributor, Logout and Login Again please')->addClass('atk-swatch-red atk-size-exa atk-box-small atk-padding');
			throw $this->exception('','StopInit');
		}
	}
}