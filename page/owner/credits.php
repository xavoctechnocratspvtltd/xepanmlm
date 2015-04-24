<?php


class page_xMLM_page_owner_credits extends page_xMLM_page_owner_main {
	
	function page_index(){

		$this->app->title='Credits Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Credits Management');

		$tabs = $this->add('Tabs');
		$req_tab = $tabs->addTabURL("xMLM/page/owner/xmlm/credits/requests","Requests");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_purchase","Processed");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_canceled","Canceled");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_consumed","Consumed");

		

	}
}