<?php
namespace xMLM;
class Model_Booking_Rejected extends Model_Booking{
	public $actions=array(
			'allow_edit'=>array(),
			'allow_add'=>array(),
			'allow_del'=>array(),
			'can_view'=>array(),
			'can_approve'=>array(),
			
		);
	function init(){
		parent::init();

		$this->addCondition('status','rejected');
	}
}