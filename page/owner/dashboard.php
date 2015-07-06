<?php

class page_xMLM_page_owner_dashboard extends page_xMLM_page_owner_main {
	function init(){
		parent::init();

		$this->app->title='Distributors Management';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> MLM Dashboard');
		

		//Setting up System Date
		$col = $this->add('Columns');
		$col1 = $col->addColumn('4');

		$date_view = $col1->add('View')->set($this->api->now)->addClass('atk-size-zetta atk-swatch-red atk-align-center');
		$form = $col1->add('Form')->addClass('atk-box');
		$form->addField('DatePicker','change_date')->set($this->api->today);
		$form->addSubmit('Change');

		if($form->isSubmitted()){
			$this->api->setDate($form['change_date']);
			$form->js(null,$date_view->js()->reload())->reload()->execute();
		}

		//For Space
		$this->add('View')->setStyle('margin-top','30px');

		$col = $this->add('Columns');
		$col1 = $col->addColumn(3);
		$col2 = $col->addColumn(3);
		$col3 = $col->addColumn(6);
	
		//Distributors Data		
		$distributor_model= $this->add('xMLM/Model_Distributor');
		$distributor_view = $col1->add('View_Tile')->addClasS('atk-swatch-blue')->addClass('atk-padding-small');
		$distributor_view->setTitle('No. of Distributors ');
		$distributor_view->setContent($distributor_model->count()->getOne());
		$active_view = $this->add('View')->set('Active Distributors: '.$this->add('xMLM/Model_UnBlockedIds')->count()->getOne())->addClass('pull-left');
		$blocked_view = $this->add('View')->set('Blocked Distributors: '.$distributor_model->addCondition('is_active',false)->count()->getOne())->addClass('pull-left');
		$distributor_view->setFooter($active_view->getHtml().$blocked_view->getHtml());

		//Kit Data
		$kit_view = $col2->add('View_Tile')->addClasS('atk-swatch-yellow')->addClass('atk-padding-small');
		$kit_view->setTitle('No. of Kits ');
		$kit_view->setContent($this->add('xMLM/Model_Kit')->count()->getOne());
		
		//Properties Data
		$state_model = $this->add('xMLM/Model_Location');
		$state_model->addExpression('properties_count')->set($state_model->refSQL('xMLM/Property')->count());

		$row_2 = $col3->add('Columns')->addClass('atk-swatch-gray');
		$row_2_col1 = $row_2->addColumn('4');
		$row_2_col2 = $row_2->addColumn('8');

		$location_view = $row_2_col1->add('View_Tile')->addClasS('atk-swatch-gray')->addClass('atk-padding-small')->addClass('pull-left');
		$location_view->setTitle('Properties');
		$location_view->setContent($this->add('xMLM/Model_Property')->count()->getOne());
		$location_view->setFooter("Location: ".$state_model->count()->getOne());
		
		$properties_grid = $row_2_col2->add('Grid');
		$state_model->getElement('name')->caption('Location');
		$properties_grid->setModel($state_model,array('name','properties_count'));
		$properties_grid->addPaginator($ipp=5);

		$booking_model = $this->add('xMLM/Model_Booking');
		$this->add('View')->set('Total Booking: '.$booking_model->count()->getOne())->addClass('atk-size-zetta atk-align-center atk-swatch-gray atk-padding-small')->setStyle('margin','30px 0 0 0');

		$row_3 = $this->add('Columns');//->addClass('atk-swatch-gray');
		$row_3_col1 = $row_3->addColumn('3');
		$row_3_col2 = $row_3->addColumn('3');
		$row_3_col3 = $row_3->addColumn('3');
		$row_3_col4 = $row_3->addColumn('3');


		$booking_req_view = $row_3_col1->add('View_Tile')->addClass('atk-swatch-yellow');
		$booking_req_view->setTitle('Booking Request');
		$booking_req_view->setContent($this->add('xMLM/Model_Booking_Request')->count()->getOne());

		$booking_req_view = $row_3_col2->add('View_Tile')->addClass('atk-swatch-blue');
		$booking_req_view->setTitle('Booking Approved');
		$booking_req_view->setContent($this->add('xMLM/Model_Booking_Approved')->count()->getOne());
		
		$booking_req_view = $row_3_col3->add('View_Tile')->addClass('atk-swatch-green');
		$booking_req_view->setTitle('Booking Availed');
		$booking_req_view->setContent($this->add('xMLM/Model_Booking_Availed')->count()->getOne());

		$booking_req_view = $row_3_col4->add('View_Tile')->addClass('atk-swatch-red');
		$booking_req_view->setTitle('Booking Canceled');
		$booking_req_view->setContent($this->add('xMLM/Model_Booking_Canceled')->count()->getOne());

	}
}