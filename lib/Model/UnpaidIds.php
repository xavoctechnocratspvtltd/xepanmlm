<?php

namespace xMLM;

class Model_UnpaidIds extends Model_Distributor{
	public $actions = array(
			'allow_add'=>array(),
			'allow_del'=>array(),
			'allow_edit'=>array()
			);
	
	function init(){
		parent::init();

		$this->addCondition('status','unpaid');
	}
}