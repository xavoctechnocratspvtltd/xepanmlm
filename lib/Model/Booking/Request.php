<?php
namespace xMLM;
class Model_Booking_Request extends Model_Booking{
	public $actions=array(
			'allow_edit'=>false,
			'allow_add'=>false,
			'allow_del'=>false,
			'can_view'=>array(),
			'can_approve'=>array(),
			'can_manage_attachments'=>false,
			'can_reject'=>array(),
			
		);
	function init(){
		parent::init();

		$this->addCondition('status','request');
	}
}