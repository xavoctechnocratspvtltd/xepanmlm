<?php
namespace xMLM;
class Model_Booking_Approved extends Model_Booking{
	public $actions=array(
			'allow_edit'=>array(),
			'allow_add'=>array(),
			'allow_del'=>array(),
			'can_view'=>array(),
			'can_mark_processed'=>array('caption'=>'Availed'),
			
		);
	function init(){
		parent::init();

		$this->addCondition('status','approved');
	}
}