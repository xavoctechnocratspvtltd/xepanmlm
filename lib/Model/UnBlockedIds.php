<?php

namespace xMLM;

class Model_UnBlockedIds extends Model_Distributor{

	function init(){
		parent::init();

		$this->addCondition('is_active',true);
	}
}