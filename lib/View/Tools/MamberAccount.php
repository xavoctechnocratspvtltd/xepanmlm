<?php

namespace xMLM;

class View_Tools_MamberAccount extends \componentBase\View_Component{
	public $html_attributes=array(); // ONLY Available in server side components
	function init(){
		parent::init();

		if(! $dis = $this->add('xMLM/Model_Distributor')->loadLoggedIn()){
			$this->add('View_Error')->set('Please Login First '.$this->api->auth->model->id);
		}else{
			$tab=$this->add('Tabs');
			$login_tab=$tab->addTabURL('xMLM_page_owner_xmlm_dashboard','Dashboard');
			$profile_tab=$tab->addTabURL('xMLM_page_owner_xmlm_profile','Update Profile');
			$joining_tab=$tab->addTabURL('xMLM_page_owner_xmlm_newjoining','New Joining');
			$payout_tab=$tab->addTabURL('xMLM_page_owner_xmlm_mypayout','My Payout');
			$tree_tab=$tab->addTabURL('xMLM_page_owner_xmlm_treeview','Tree View');
			$downline_tab=$tab->addTabURL('xMLM_page_owner_xmlm_downlineview','Downline View');
			$invoice_tab=$tab->addTabURL('xMLM_page_owner_xmlm_invoice','Invoice');
			$credits_tab=$tab->addTabURL('xMLM_page_owner_xmlm_credits','Credits Management');
			$logout_tab=$tab->addTabURL('xMLM_page_owner_xmlm_logout','Logout');
		}
	}

	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}