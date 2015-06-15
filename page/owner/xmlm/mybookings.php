<?php

class page_xMLM_page_owner_xmlm_mybookings extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();
		$this->app->pathfinder->base_location->addRelativeLocation(
		    'epan-components/xMLM', array(
		        'php'=>'lib',
		        'template'=>'templates',
		        'css'=>'templates/css',
		        'js'=>'templates/js',
		    )
		);
	}
	function page_index(){
		$this->add('View_Info')->set('Bookings Here');
		$tabs=$this->add('Tabs');
		$booking=$tabs->addTabURL('./booking','Bookings');
		$new_booking=$tabs->addTabURL('./request','Request');
	}
	function page_booking(){
		$grid=$this->add('xMLM/Grid_MyBooking');
		$a=array(
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'availed','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			array('from_date_1'=>'06/06/2015','from_date_2'=>'10/06/2015','from_date_2'=>'15/06/2015','to_date_1'=>'10/06/2015','to_date_2'=>'10/06/2015','to_date_3'=>'10/06/2015','destination_1'=>'Udaipur','destination_2'=>'Udaipur','destination_3'=>'Udaipur','adults_1'=>'2','adults_2'=>'2','adults_3'=>'2','children_1'=>'3','children_2'=>'3','children_3'=>'3','status_1'=>'rejected','status_2'=>'rejected','status_3'=>'rejected'),
			);

		$grid->setSource($a);

	}

	function page_request(){
		$distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

		$form=$this->add('Form',null,null,array('form/empty'));
		$form->setLayout('view/bookingrequest');

		$form->addField('Readonly','distributor_name')->set($distributor['name']);
		$form->addField('line','booking_in_name_of')->set($distributor['name']);

		for($i=1;$i<=3;$i++){
			$form->addField('line','location_'.$i)->validateNotNull();
			$form->addField('line','hotel_'.$i)->validateNotNull();
			$form->addField('line','checkin_date_'.$i)->validateNotNull();
			$form->addField('line','checkout_date_'.$i)->validateNotNull();
			$form->addField('line','no_of_nights_'.$i)->validateNotNull();
		}

		$form->addField('line','no_of_adults');
		$form->addField('line','no_of_children');
		$form->addField('line','voucher_no');
		$form->addField('line','confirmation_through');

		$form->addSubmit('Submit');
		if($form->isSubmitted()){
			$form->error('booking_in_name_of','Hello');
			$form->js()->univ()->errorMessage('Value Not Proper')->execute();
		}
	}	
}