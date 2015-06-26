<?php
namespace xMLM;
class Model_Booking_Approved extends Model_Booking{
	public $actions=array(
			'allow_edit'=>false,
			'allow_add'=>false,
			'allow_del'=>false,
			'can_view'=>array(),
			'can_mark_processed'=>array('caption'=>'Availed'),
			'can_manage_attachments'=>false,
			
		);
	function init(){
		parent::init();

		$this->addCondition('status','approved');
	}
}