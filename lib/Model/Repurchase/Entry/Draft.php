<?php

namespace xMLM;

class Model_Repurchase_Entry_Draft extends Model_RepurchaseEntry {
	public $root_document_name ='xMLM\RepurchaseEntry';
	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
			'can_approve'=>array(),
			'can_manage_attachments'=>false
		);

	function init(){
		parent::init();

		$this->addCondition('status','draft');
	}

}