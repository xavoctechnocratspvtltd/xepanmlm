<?php

namespace xMLM;

class Model_Credit_Consumed extends Model_CreditMovement{
	public $root_documnet_name="xMLM\Model_CreditMovement";
	public $actions=array(
			// 'can_mark_processed'=>array(),
			// 'can_cancel'=>array(),
			// 'allow_add'=>array(),
		);

	function init(){
		parent::init();
		$this->addCondition('status','Consumed');
	}
}