<?php

class page_xMLM_page_owner_properties extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Properties Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Properties Management');
		
		$tab=$this->add('Tabs');
		$location_tab=$tab->addTab('Location');
		$city_tab=$tab->addTab('Cities');
		$propery_tab=$tab->addTab('Properties');

		/*Location Session Started*/
		$l_crud=$location_tab->add('CRUD');
		$l_crud->setModel('xMLM/Location');
		$l_crud->add('xHR/Controller_Acl');

		$grid=$l_crud->grid;
		if($grid->hasColumn('status'))$grid->removeColumn('status');
		if($grid->hasColumn('item_name'))$grid->removeColumn('item_name');
		if($grid->hasColumn('related_document'))$grid->removeColumn('related_document');
		if($grid->hasColumn('created_by'))$grid->removeColumn('created_by');
		if($grid->hasColumn('created_date'))$grid->removeColumn('created_date');
		if($grid->hasColumn('updated_date'))$grid->removeColumn('updated_date');

		/*City Session Started*/

		$c_crud=$city_tab->add('CRUD');
		$c_crud->setModel('xMLM/City');
		$c_crud->add('xHR/Controller_Acl');
		$grid=$c_crud->grid;
		if($grid->hasColumn('status'))$grid->removeColumn('status');
		if($grid->hasColumn('item_name'))$grid->removeColumn('item_name');
		if($grid->hasColumn('related_document'))$grid->removeColumn('related_document');
		if($grid->hasColumn('created_by'))$grid->removeColumn('created_by');
		if($grid->hasColumn('created_date'))$grid->removeColumn('created_date');
		if($grid->hasColumn('updated_date'))$grid->removeColumn('updated_date');

		/*Property Session Started*/

		$crud = $propery_tab->add('CRUD',array('grid_class'=>'xMLM/Grid_Property'));
		$crud->setModel('xMLM/Property');
		$crud->add('xHR/Controller_Acl');
		$grid=$crud->grid;
		if($grid->hasColumn('item_name'))$grid->removeColumn('item_name');
		if($grid->hasColumn('created_by'))$grid->removeColumn('created_by');
		if($grid->hasColumn('related_document'))$grid->removeColumn('related_document');
		if($grid->hasColumn('created_date'))$grid->removeColumn('created_date');
		if($grid->hasColumn('updated_date'))$grid->removeColumn('updated_date');
	}
}