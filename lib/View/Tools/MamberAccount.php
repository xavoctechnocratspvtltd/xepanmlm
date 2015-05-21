<?php

namespace xMLM;

class View_Tools_MamberAccount extends \componentBase\View_Component{
	public $html_attributes=array(); // ONLY Available in server side components
	function init(){
		parent::init();

		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		if(! $dis = $this->add('xMLM/Model_Distributor')->loadLoggedIn()){
			$this->add('View_Error')->set('Please Login First '.$this->api->auth->model->id);
		}else{
			$days = $this->api->my_date_diff($dis['created_at'],$this->api->today);

			if(!$dis['greened_on'] and $days['days_total']>=$config['days_allowed_for_green'] and $dis['is_active']){
				$dis['is_active']=false;
				$dis->save();
			}

			if(!$dis['is_active']){
				$this->add('View_Error')->set('Your ID is not activated, please contact company');
				return;
			}

			if(!$dis['last_password_change']){
				$this->add('View_Info')->set('Please change your Password');
				$form = $this->add('Form');
				$form->addField('password','old_password');
				$form->addField('password','new_password');
				$form->addField('password','re_new_password');

				$form->addSubmit('Update');

				if($form->isSubmitted()){
					if($form['old_password'] != $dis['password'])
						$form->displayError('old_password','Password is not correct');

					if(strcmp($form['new_password'],$form['re_new_password'])!=0)
						$form->displayError('re_new_password','Password must match...');

					if(strlen($form['new_password']) < 6)
						$form->displayError('new_password','Must be greater than 6 characters in length');

					$dis['last_password_change'] = $this->api->now;
					$dis['password'] = $dis['re_password'] = $form['new_password'];
					$dis->save();
					$this->js()->reload()->execute();

				}

				return;
			}

			$tab=$this->add('Tabs');
			$login_tab=$tab->addTabURL('xMLM_page_owner_xmlm_dashboard','Dashboard');
			$profile_tab=$tab->addTabURL('xMLM_page_owner_xmlm_profile','Update Profile');
			$joining_tab=$tab->addTabURL('xMLM_page_owner_xmlm_newjoining','New Joining');
			$payout_tab=$tab->addTabURL('xMLM_page_owner_xmlm_mypayout','My Payout');
			$tree_tab=$tab->addTabURL('xMLM_page_owner_xmlm_treeview','Tree View');
			$downline_tab=$tab->addTabURL('xMLM_page_owner_xmlm_downlineview','Downline View');
			// $invoice_tab=$tab->addTabURL('xMLM_page_owner_xmlm_invoice','Invoice');
			$credits_tab=$tab->addTabURL('xMLM_page_owner_xmlm_credits','Credits Management');
			$credits_tab=$tab->addTabURL('xMLM_page_owner_xmlm_mybookings','My Booking(s)');
			// $logout_tab=$tab->addTabURL('xMLM_page_owner_xmlm_logout','Logout');
		}
	}

	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}