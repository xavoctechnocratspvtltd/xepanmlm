<?php

class page_xMLM_page_owner_xmlm_mybookings extends page_xMLM_page_owner_xmlm_main{
	function page_index(){
		$this->add('View_Info')->set('Bookings Here');
		$tabs=$this->add('Tabs');
		$booking=$tabs->addTabURL('./booking','Bookings');
		$new_booking=$tabs->addTabURL('./request','Request');
	}
	
	function page_booking(){
		$booking_model=$this->add('xMLM/Model_Booking');
		$grid=$this->add('xMLM/Grid_MyBooking',array('sno_caption'=>'no'));
		$grid->setModel($booking_model);
		
		$order = $grid->addOrder();
		// $order->move('status','after','no_of_childern');
		$order->move('status','after','no_of_childern');
		$order->move('booking_through','after','voucher_no');
		$order->now();

		$booking_model->setOrder('id','desc');

	}

	
}