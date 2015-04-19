<?php
class page_xMLM_page_removecomponent extends page_componentBase_page_removecomponent {
	function init(){
		parent::init();


		// Code to run before removing component ...
		$this->remove();
		// Code to run after remove
		
		// component still available in marketplace
	}
}