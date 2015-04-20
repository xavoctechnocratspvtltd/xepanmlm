<?php

namespace xMLM;

class Model_PaidIds extends Model_Distributor{

	function init(){
		parent::init();

		$this->addCondition('status','paid');
	}
}