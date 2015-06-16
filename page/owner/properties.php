<?php

class page_xMLM_page_owner_properties extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Properties Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Properties Management');
		
		$crud = $this->add('CRUD',array('grid_class'=>'xMLM/Grid_Property'));
		$crud->setModel('xMLM/Property');
	}
}