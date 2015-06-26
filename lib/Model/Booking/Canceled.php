<?php
namespace xMLM;
class Model_Booking_Canceled extends Model_Booking{
	public $actions=array(
			'allow_edit'=>false,
			'allow_add'=>false,
			'allow_del'=>array(),
			'can_view'=>false,
			'can_approve'=>false,
			'can_manage_attachments'=>false,
			
		);
	function init(){
		parent::init();

		$this->addCondition('status','canceled');
	}
}