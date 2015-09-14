<?php

namespace xMLM;

class Model_Credit_Purchase extends Model_CreditMovement{
	public $root_documnet_name="xMLM\Model_CreditMovement";
	public $actions=array(
			'can_view'=>array(),
			// 'can_mark_processed'=>array(),
			// 'can_cancel'=>array(),
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'can_manage_attachments'=>false,
		);

	function init(){
		parent::init();
		$this->addCondition('status','Purchase');
	}
}