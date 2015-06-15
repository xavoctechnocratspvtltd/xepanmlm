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
		$tab->addTabURL('./reject','Rejected / Canceled');
	}

	function page_request(){
		$grid=$this->add('xMLM/Grid_MyBooking');
		$a=array(
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			);
		$grid->setSource($a);
		$grid->addColumn('Button','approved');
		$grid->addColumn('Button','reject');
		$grid->removeColumn('edit');
		$grid->removeColumn('cancel');
	}

	function page_approved(){
		$grid=$this->add('xMLM/Grid_MyBooking');
		$a=array(
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			);

		$grid->setSource($a);
		$grid->addColumn('Button','availed');
		$grid->addColumn('Button','reject');
		$grid->removeColumn('edit');
		$grid->removeColumn('cancel');
	}

	function page_availed(){
		$grid=$this->add('xMLM/Grid_MyBooking');
		$a=array(
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			);

		$grid->setSource($a);
		$grid->removeColumn('edit');
		$grid->removeColumn('cancel');
	}

	function page_reject(){
		$grid=$this->add('xMLM/Grid_MyBooking');
		$a=array(
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			);

		$grid->setSource($a);
		$grid->removeColumn('edit');
	}
}