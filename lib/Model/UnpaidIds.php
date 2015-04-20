<?php

namespace xMLM;

class Model_UnpaidIds extends Model_Distributor{

	function init(){
		parent::init();

		$this->addCondition('status','unpaid');
	}
}