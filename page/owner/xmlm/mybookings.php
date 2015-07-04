<?php

class page_xMLM_page_owner_xmlm_mybookings extends page_xMLM_page_owner_xmlm_main{
	function page_index(){

		$container=$this->add('View')->addClass('container');
		$container->add('View')->set('Booking Management')->addClass('text-center atk-swatch-green atk-size-exa atk-box atk-push');

		$tabs=$this->add('Tabs');
		$booking=$tabs->addTabURL('./booking','Bookings');
		$new_booking=$tabs->addTabURL('./request','Request');
	}
	
	function page_booking(){
		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		

		$booking_model=$this->add('xMLM/Model_Booking');
		$booking_model->addCondition('distributor_id',$distributor->id);

		$grid=$this->add('xMLM/Grid_MyBooking',array('sno_caption'=>'no'));
		$grid->setModel($booking_model);
		
		$grid->removeColumn('distributor');

		$order = $grid->addOrder();
		// $order->move('status','after','no_of_childern');
		$order->move('status','after','no_of_childern');
		$order->move('booking_through','after','voucher_no');
		$order->now();

		$booking_model->setOrder('id','desc');

	}

	
}