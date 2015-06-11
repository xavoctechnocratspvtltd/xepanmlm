<?php

class page_xMLM_page_owner_xmlm_mybookings extends page_xMLM_page_owner_xmlm_main{
	function page_index(){
		// parent::init();

		$this->add('View_Info')->set('Bookings Here');

		$tabs=$this->add('Tabs');
		$booking=$tabs->addTabURL('./booking','Bookings');
		$new_booking=$tabs->addTabURL('./newbooking','New Bookings');
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

	function page_newbooking(){
		$form=$this->add('Form');
		$form->add('View_Warning')->set('Prefrence 1');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('DropDown','city')->setValueList(array('udaipur'=>'udaipur','jaipur'=>'jaipur'))->setEmptyText('Please Select City');
		$form->addField('DropDown','city')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select State');
		$form->addField('DropDown','property')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select ');
		$form->addField('Number','adults');
		$form->addField('Number','children');
		$form->add('View_Error')->set('Prefrence 2');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('DropDown','city')->setValueList(array('udaipur'=>'udaipur','jaipur'=>'jaipur'))->setEmptyText('Please Select City');
		$form->addField('DropDown','city')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select State');
		$form->addField('DropDown','property')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select ');
		$form->addField('Number','adults');
		$form->addField('Number','children');
		$form->add('View_Success')->set('Prefrence 3');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addField('DropDown','city')->setValueList(array('udaipur'=>'udaipur','jaipur'=>'jaipur'))->setEmptyText('Please Select City');
		$form->addField('DropDown','city')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select State');
		$form->addField('DropDown','property')->setValueList(array('Rajasthan','Gujrat'))->setEmptyText('Please Select ');
		$form->addField('Number','adults');
		$form->addField('Number','children');
		$form->addSubmit('Submit');
		if($form->isSubmitted()){
			$form->js()->univ()->errorMessage('Value Not Proper')->execute();
		}
	}	
}