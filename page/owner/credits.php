<?php


class page_xMLM_page_owner_credits extends page_xMLM_page_owner_main {
	
	function page_index(){
		$this->add('xMLM/Controller_Acl');

		$this->app->title='Credits Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Credits Management');

		$tabs = $this->add('Tabs');
		$req_tab = $tabs->addTabURL("xMLM/page/owner/xmlm/credits/requests","Requests");
		$req_tab = $tabs->addTabURL("xMLM/page/owner/xmlm/credits/approved","Approved");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_purchase","Processed");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_rejected","Rejected");
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_consumed","Consumed");		
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_report","Report");		
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_sales","Income Report");		
		$req_tab = $tabs->addTabURL("xMLM_page_owner_xmlm_credits_creditincome","Credit Income");		

	}
}