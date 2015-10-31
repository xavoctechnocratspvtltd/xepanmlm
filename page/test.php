<?php

class page_xMLM_page_test extends Page{
	function init(){
		parent::init();
		$message= "Thank you for joining Nebula! We have send your login Credentials on your registered email ID. Please Call us \n on 18004192299 for any assistance. Happy Networking!\nBest Regards,\nNebula Team - Reach Beyond";

		$form=$this->add('Form');
		$form->addField('line','mobile_no');
		$form->addSubmit('Send');

		if($form->isSubmitted()){
		// $message= "Thank you for joining Nebula! We have send your login Credentials on your registered email ID. Please Call us on 18004192299 for any assistance. Happy Networking! Best Regards, Nebula Team - Reach Beyond";
			$this->add('Controller_Sms')->sendMessage($form['mobile_no'],$message);

			$form->js()->reload()->execute();
		}
	}
}