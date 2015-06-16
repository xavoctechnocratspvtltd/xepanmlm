<?php

class page_xMLM_page_owner_bookings extends page_xMLM_page_owner_main {
	function page_index(){
		// parent::init();

		$this->app->title='Properties Booking';
		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-dashboard icon-gauge"></i> Bookings');

		$tab=$this->add('Tabs');
		$tab->addTabURL('./request','Request');
		$tab->addTabURL('./approved','Approved');
		$tab->addTabURL('./availed','Availed');
		$tab->addTabURL('./reject','Rejected');
		$tab->addTabURL('./cancel','Canceled');
	}

	function page_request(){
		$request_model=$this->add('xMLM/Model_Booking_Request');
		$crud=$this->add('CRUD',array('grid_class'=>'xMLM/Grid_MyBooking'));
		$crud->setModel($request_model);
		$crud->add('xHR/Controller_Acl');

		
	}

	function page_approved(){
		$approved_model=$this->add('xMLM/Model_Booking_Approved');
		$crud=$this->add('CRUD',array('grid_class'=>'xMLM/Grid_MyBooking'));
		$crud->setModel($approved_model);
		$crud->add('xHR/Controller_Acl');
	}

	function page_availed(){
		$availed_model=$this->add('xMLM/Model_Booking_Availed');
		$crud=$this->add('CRUD',array('grid_class'=>'xMLM/Grid_MyBooking'));
		$crud->setModel($availed_model);
		$crud->add('xHR/Controller_Acl');
	}

	function page_reject(){
		$rejected_model=$this->add('xMLM/Model_Booking_Rejected');
		$crud=$this->add('CRUD',array('grid_class'=>'xMLM/Grid_MyBooking'));
		$crud->setModel($rejected_model);
		$crud->add('xHR/Controller_Acl');
	}

	function page_cancel(){
		$cancel_model=$this->add('xMLM/Model_Booking_Canceled');
		$crud=$this->add('CRUD',array('grid_class'=>'xMLM/Grid_MyBooking'));
		$crud->setModel($cancel_model);
		$crud->add('xHR/Controller_Acl');
		// $grid=$this->add('xMLM/Grid_MyBooking');
		// $a=array(
		// 	array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
		// 	array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
		// 	array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
		// 	array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
		// 	);

		// $grid->setSource($a);
		// $grid->removeColumn('edit');
	}
}