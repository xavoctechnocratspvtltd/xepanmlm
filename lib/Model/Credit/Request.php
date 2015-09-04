<?php

namespace xMLM;

class Model_Credit_Request extends Model_CreditMovement{
	public $root_documnet_name="xMLM\Model_CreditMovement";
	public $actions=array(
			'can_approve'=>array(),
			// 'can_mark_processed'=>array('caption'=>'Process'),
			'can_reject'=>array(),
			'allow_add'=>false,
			'allow_edit'=>true,
			'can_manage_attachments'=>false,
		);

	function init(){
		parent::init();
		$this->addCondition('status','Request');
		$this->addHook('beforeSave',$this);
	}
	function beforeSave(){
		$request_model=$this->add('xMLM/Model_Credit_Request');
		$request_model->addCondition('id',$this['id']);
		$request_model->tryLoadAny();
		if($request_model->loaded()){
			// throw new \Exception("loaded", 1);
			if($request_model['credits'] < $this['credits'])
				throw new \Exception("In-Sufficient Credits Amount ");
		}
		// throw new \Exception("not loaded", 1);
		
		
		
	}
}