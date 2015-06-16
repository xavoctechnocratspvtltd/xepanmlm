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
		$booking_model=$this->add('xMLM/Model_Booking');
		$grid=$this->add('xMLM/Grid_MyBooking');
		$grid->setModel($booking_model);
		
		$order = $grid->addOrder();
		// $order->move('status','after','no_of_childern');
		$order->move('status','after','no_of_childern');
		$order->move('booking_through','after','voucher_no');
		$order->now();

		$booking_model->setOrder('id','desc');

	}

	function page_request(){
		$distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

		$form=$this->add('Form',null,null,array('form/empty'));
		$form->setLayout('view/bookingrequest');

		$form->addField('Readonly','distributor_name')->set($distributor['name']);
		$form->addField('line','booking_in_name_of')->set($distributor['name']);

		$location = $this->add('xMLM/Model_Location');

		for($i=1;$i<=3;$i++){
			$location_fileds = $form->addField('DropDownNormal','location_'.$i)->setEmptyText('Please Select Location')->validateNotNull(true);
			$location_fileds->setModel($location);

			$property = $this->add('xMLM/Model_Property');
			if($this->api->StickyGET('location_'.$i)){
				$property->addCondition('location_id',$_GET['location_'.$i]);
			}

			$hotel_field = $form->addField('DropDownNormal','hotel_'.$i)->setEmptyText('Please Select Hotel')->validateNotNull();
			$hotel_field->setModel($property);
		
			$chekin_filed = $form->addField('DatePicker','checkin_date_'.$i)->validateNotNull();

			$checkout_filed = $form->addField('DatePicker','checkout_date_'.$i)->validateNotNull();

			$no_of_night_filed = $form->addField('line','no_of_nights_'.$i)->validateNotNull();

			// $location_fileds->js('change',$form->js()->atk4_form('reloadField','hotel_'.$i,array($this->api->url(),'location_'.$i=>$location_fileds->js()->val())));
			// $location_fileds->js('change',$form->js()->atk4_form('reloadField',$chekin_filed,array($this->api->url(),'location_selected'=>$location_fileds->js()->val())));
			// $location_fileds->js('change',$form->js()->atk4_form('reloadField',$checkout_filed,array($this->api->url(),'location_selected'=>$location_fileds->js()->val())));
			// $location_fileds->js('change',$form->js()->atk4_form('reloadField',$no_of_night_filed,array($this->api->url(),'location_selected'=>$location_fileds->js()->val())));

		}

		$form->addField('line','no_of_adults');
		$form->addField('line','no_of_children');
		$form->addField('line','voucher_no');
		$form->addField('line','confirmation_through');

		$form->addSubmit('Submit');
		
		
		if($form->isSubmitted()){
			
			for ($i=1; $i <=3 ; $i++) { 
				$booking=$this->add('xMLM/Model_Booking');
				

				$booking['distributor_id'] = $distributor->id;
				$booking['property_id'] = $form['hotel_'.$i];
				$booking['check_in_date']=$form['checkin_date_'.$i];
				$booking['check_out_date']=$form['checkout_date_'.$i];
				$booking['no_of_nights']=$form['no_of_nights_'.$i];

				$booking['no_of_adults']= $form['no_of_adults'];

				$booking['no_of_childern'] = $form['no_of_children'];
				$booking['voucher_no'] = $form['voucher_no'];
				$booking['booking_through'] = $form['confirmation_through'];

				$booking->save();
			}

			$form->js(null,$form->js()->reload()->univ()->successMessage(' Update Information'))->execute();
			// $form->js()->univ()->errorMessage('Value Not Proper')->execute();
		}
	}	
}