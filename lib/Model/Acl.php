<?php

namespace xMLM;

class Model_Acl extends \Model_Table {
	public $table ="xmlm_pageacl";

	function init(){
		parent::init();

		$this->hasOne('xHR/Employee','employee_id');
		$this->addField('name')->caption('Page Name');

		$this->addHook('beforeSave',$this);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}


	function beforeSave(){
		$old_acl = $this->add('xMLM/Model_Acl');
		$old_acl->addCondition('name',$this['name']);
		$old_acl->addCondition('employee_id',$this['employee_id']);
		$old_acl->tryLoadAny();

		if($this->loaded()){
			$old_acl->addCondition('id','<>',$this->id);
		}

		if($old_acl->loaded()){
			$this->api->js()->univ()->errorMessage('Employee is Already Added')->execute();
		}

	}


}