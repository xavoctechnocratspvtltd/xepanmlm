<?php

namespace xMLM;

class Model_Credit_Request extends Model_CreditMovement{
	public $root_documnet_name="xMLM\Model_CreditMovement";
	public $actions=array(
			'can_approve'=>array(),
			// 'can_mark_processed'=>array('caption'=>'Process'),
			'can_cancel'=>array(),
			'allow_add'=>false,
			'can_manage_attachments'=>false,
		);

	function init(){
		parent::init();
		$this->addCondition('status','Request');
	}
}