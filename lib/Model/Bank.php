<?php

namespace xMLM;

class Model_Bank extends \Model_Table {
	public $table='xmlm_banks';
	
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xMLM/Distributor','district_id');
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		
		// Check name for THIS State
		$old_bank = $this->add('xMLM/Model_Bank');
		$old_bank->addCondition('name',$this['name']);
		
		if($this->loaded()){
			$old_bank->addCondition('id','<>',$this->id);
		}
		$old_bank->tryLoadAny();
		if($old_bank->loaded()){
			$this->api->js()->univ()->errorMessage('This Bank name is already taken, Chose Another')->execute();
		}
	}
}