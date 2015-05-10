<?php

namespace xMLM;

class Model_BlockedIds extends Model_Distributor{

	function init(){
		parent::init();

		$this->addCondition('is_active',false);
	}
}