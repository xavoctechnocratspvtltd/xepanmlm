<?php

class page_xMLM_page_owner_dashboard extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Distributors Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Dashboard');
		

		$this->add('View')->set('No of Active Users');
		$this->add('View')->set('Display no of kits distributed by category');

		$this->add('View')->set($this->api->now)->addClass('atk-size-zetta atk-swatch-red atk-align-center');

		$form = $this->add('Form');
		$form->addField('DatePicker','change_date')->set($this->api->today);
		$form->addSubmit('Change');

		if($form->isSubmitted()){
			$this->api->setDate($form['change_date']);
			$form->js()->reload()->execute();
		}


	}
}