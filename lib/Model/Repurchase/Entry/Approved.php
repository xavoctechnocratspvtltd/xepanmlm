<?php

namespace xMLM;

class Model_Repurchase_Entry_Approved extends Model_RepurchaseEntry {
	public $root_document_name ='xMLM\RepurchaseEntry';

	function init(){
		parent::init();

		$this->addCondition('status','approved');
	}

}