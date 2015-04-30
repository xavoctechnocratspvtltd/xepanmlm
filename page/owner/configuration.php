<?php


class page_xMLM_page_owner_configuration extends page_xMLM_page_owner_main {
	
	function init(){
		parent::init();

		$this->app->title='MLM Configuration';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Configuration');

		$form = $this->add('Form_Stacked');
		$form->setModel($this->add('xMLM/Model_Configuration')->tryLoadAny());
		$form->addSubmit('update');
		if($form->isSubmitted()){
			$form->Update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Update Successfully')->execute();
		}

		$this->add('H2')->set('Business Volume Slab');

		$this->add('CRUD')->setModel('xMLM/BVSlab',array('name','percentage'));

	}
}