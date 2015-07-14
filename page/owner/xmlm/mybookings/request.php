<?php
/**
 * Page class
 */
class page_xMLM_page_owner_xmlm_mybookings_request extends page_xMLM_page_owner_xmlm_main
{
    /**
     * Initialize the page
     *
     * @return void
     */

    function init(){
    	parent::init();

		$distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();
		$forms=$this->add('Form',null,null,array('form/empty'));
		

		$forms->addField('Readonly','distributor_caption')->set('Distributor Name')->setterGetter('group','a~3');
		$b_a=$forms->addField('Readonly','distributor_name')->set($distributor['name'])->setterGetter('group','a~3');
		$b_a->belowField()->add('View')->setHtml('&nbsp;');
		$forms->addField('Readonly','booking_in_name_of_caption')->set('Booking In Name of')->setterGetter('group','a1~3');
		$b_l=$forms->addField('line','booking_in_name_of')->set($distributor['name'])->setterGetter('group','a1~3');
		$b_l->belowField()->add('View')->setHtml('&nbsp;');
		// $b_l->js(true)->closest('div.atk-form-row')->appendTo($dis_right);
		// $b_a_c->js(true)->closest('div.atk-form-row')->appendTo($dis_left);

		$b_col=$this->add('Columns');
		$b_left=$b_col->addColumn(4);
		$b_right=$b_col->addColumn(7);
		// $b_l_c=$b_left->add('View')->set('Booking Name of');
		// $b_l_c->js(true)->closest('div.atk-form-row')->appendTo($b_left);


		// $l_col=$this->add('Columns');
		// $l_left=$l_col->addColumn(3);
		// $l_mid=$l_col->addColumn(3);
		// $l_right=$l_col->addColumn(3);

		$location = $this->add('xMLM/Model_Location');
		for($i=1;$i<=3;$i++){

			$location_fileds= $forms->addField('DropDown','location_'.$i,'Location')->setEmptyText('Please Select Location')->validateNotNull(true)->setterGetter('group',$i.'~4~Preferance -'.$i);
			$location_fileds->setModel($location);
		// $location_fileds->js(true)->closest('div.atk-form-row')->appendTo($l_left);

			$property = $this->add('xMLM/Model_Property');
			if($this->api->StickyGET('location_'.$i)){
				$property->addCondition('location_id',$_GET['location_'.$i]);
			}

			$hotel_field = $forms->addField('DropDown','hotel_'.$i,'Hotel Name')->setEmptyText('Please Select Hotel')->validateNotNull("Location is required")->setterGetter('group',$i.'~4');
			$hotel_field->setModel($property);
			// $hotel_field->js(true)->closest('div.atk-form-row')->appendTo($l_mid);

			if($_GET['location_'.$i]){
				$hotel_field->getModel()->addCondition('location_id',$_GET['location_'.$i]);
			}
		
			$checkin_field = $forms->addField('DatePicker','checkin_date_'.$i,'Checkin Date')->validateNotNull()->setterGetter('group',$i.'~4');
			$checkin_field->options=[
                    		'minDate'=>$this->js(null, 'new Date'),
                    		'onSelect'=>$checkin_field->js()->datepicker('option','minDate',$checkin_field->js()->datepicker('getDate'))->_enclose()
                		];
			// $checkin_field->js(true)->closest('div.atk-form-row')->appendTo($l_right);

			$location_fileds->js('change',$forms->js()->atk4_form('reloadField','hotel_'.$i,array($this->api->url(),'location_'.$i=>$location_fileds->js()->val())));
			// $location_fileds->js('change',$this->js()->univ()->alert("sdfsf"));
		}

		$booking=$this->add('xMLM/Model_Booking');
		$booking_through=$booking->ref('property_id')->get('booking_through');
		$forms->addField('DropDown','no_of_adults','Adults')->setValueList(array('0'=>'Please Select Adults ' ,'1'=>'Adults ( 1 )','2'=>'Adults ( 2 ) '))->setterGetter('group','b~3');
		$forms->addField('DropDown','no_of_children','Children')->setValueList(array('0'=>'Please Select Children ','1'=>'Children ( 1 ) ','2'=>'Children ( 2 ) '))->setterGetter('group','b~3');
		$forms->addField('line','voucher_no')->validateNotNull(" Voucher no is required")->setterGetter('group','b~3');
		$forms->addField('line','confirmation_through','Booking Through')->set($booking_through)->validateNotNull('Booking through is required')->setterGetter('group','b~3');

		$forms->addSubmit('Submit');
		
		$forms->add('Controller_FormBeautifier');		
		if($forms->isSubmitted()){
			
			for ($i=1; $i <=3 ; $i++) { 
				$booking=$this->add('xMLM/Model_Booking');
				

				$booking['distributor_id'] = $distributor->id;
				$booking['name'] = $forms['booking_in_name_of'];
				$booking['property_id'] = $forms['hotel_'.$i];
				$booking['check_in_date']=$forms['checkin_date_'.$i];
				$booking['check_out_date']=$forms['checkout_date_'.$i];
				$booking['no_of_nights']=$forms['no_of_nights_'.$i];

				$booking['no_of_adults']= $forms['no_of_adults'];

				$booking['no_of_childern'] = $forms['no_of_children'];
				$booking['voucher_no'] = $forms['voucher_no'];
				$booking['booking_through'] = $forms['confirmation_through'];

				$booking->save();
			}

			$forms->js(null,$forms->js()->reload()->univ()->successMessage(' Request Submitted successfully'))->execute();
		}	

		// $form=$this->add('Form',null,null,array('form/empty'));
		// $form->setLayout('view/bookingrequest');

		// $form->addField('Readonly','distributor_name')->set($distributor['name']);
		// $form->addField('line','booking_in_name_of')->set($distributor['name']);

		// $location = $this->add('xMLM/Model_Location');
		// for($i=1;$i<=3;$i++){
		// 	$location_fileds = $form->addField('DropDown','location_'.$i)->setEmptyText('Please Select Location')->validateNotNull(true);
		// 	$location_fileds->setModel($location);

		// 	$property = $this->add('xMLM/Model_Property');
		// 	if($this->api->StickyGET('location_'.$i)){
		// 		$property->addCondition('location_id',$_GET['location_'.$i]);
		// 	}

		// 	$hotel_field = $form->addField('DropDown','hotel_'.$i)->setEmptyText('Please Select Hotel')->validateNotNull("Location is required");
		// 	$hotel_field->setModel($property);

		// 	if($_GET['location_'.$i]){
		// 		$hotel_field->getModel()->addCondition('location_id',$_GET['location_'.$i]);
		// 	}
		
		// 	$checkin_field = $form->addField('DatePicker','checkin_date_'.$i)->validateNotNull();
		// 	$checkin_field->options=[
  //                   		'minDate'=>$this->js(null, 'new Date'),
  //                   		'onSelect'=>$checkin_field->js()->datepicker('option','minDate',$checkin_field->js()->datepicker('getDate'))->_enclose()
  //               		];

		// 	$checkout_field = $form->addField('DatePicker','checkout_date_'.$i)->validateNotNull();
		// 	$checkout_field->options=[
  //                   		'minDate'=>$this->js(null, 'new Date'),
  //                   		'onSelect'=>$checkout_field->js()->datepicker('option','minDate',$checkout_field->js()->datepicker('getDate'))->_enclose()
  //               		];

		// 	$no_of_nights_field = $form->addField('line','no_of_nights_'.$i)->validateNotNull();

		// 	// $diff =  "alert(daydiff(parseDate($('#first').val()), parseDate($('#second').val())));";
		// 	// $checkout_field->js('change',$no_of_nights_field->js()->val($diff)->_enclose());
		// 	// $checkin_field->js('change',$no_of_nights_field->js()->val($diff)->_enclose());

		// 	// $location_fileds->js('change',$form->js()->atk4_form('reloadField','hotel_'.$i,array($this->api->url(),'location_'.$i=>$location_fileds->js()->val())));

		// }

		// $form->addField('DropDown','no_of_adults','Adults')->setValueList(array('0'=>0,'1'=>1,'2'=>2));
		// $form->addField('DropDown','no_of_children','Children')->setValueList(array('0'=>0,'1'=>1,'2'=>2));
		// $form->addField('line','voucher_no')->validateNotNull(" Voucher no is required");
		// $form->addField('line','confirmation_through','Booking Through')->validateNotNull('Booking through is required');

		// $form->addSubmit('Submit');
		
		
		// if($form->isSubmitted()){
			
		// 	for ($i=1; $i <=3 ; $i++) { 
		// 		$booking=$this->add('xMLM/Model_Booking');
				

		// 		$booking['distributor_id'] = $distributor->id;
		// 		$booking['property_id'] = $form['hotel_'.$i];
		// 		$booking['check_in_date']=$form['checkin_date_'.$i];
		// 		$booking['check_out_date']=$form['checkout_date_'.$i];
		// 		$booking['no_of_nights']=$form['no_of_nights_'.$i];

		// 		$booking['no_of_adults']= $form['no_of_adults'];

		// 		$booking['no_of_childern'] = $form['no_of_children'];
		// 		$booking['voucher_no'] = $form['voucher_no'];
		// 		$booking['booking_through'] = $form['confirmation_through'];

		// 		$booking->save();
		// 	}

		// 	$form->js(null,$form->js()->reload()->univ()->successMessage(' Request Submitted successfully'))->execute();
			// $form->js()->univ()->errorMessage('Value Not Proper')->execute();
		// }

	}	

}