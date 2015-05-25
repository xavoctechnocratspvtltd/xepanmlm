<?php

namespace xMLM;

class Model_District extends \Model_Table {
	public $table='xmlm_districts';
	
	function init(){
		parent::init();

		$this->hasOne('xMLM/State','state_id')->mandatory(true);
		$this->addField('name')->mandatory(true);

		$this->hasMany('xMLM/Distributor','district_id');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		
		// Check name for THIS State
		$old_dis = $this->add('xMLM/Model_District');
		$old_dis->addCondition('state_id',$this['state_id']);
		$old_dis->addCondition('name',$this['name']);
		
		if($this->loaded()){
			$old_dis->addCondition('id','<>',$this->id);
		}
		$old_dis->tryLoadAny();
		if($old_dis->loaded()){
			$this->api->js()->univ()->errorMessage('This CITY Name is already taken, Chose Another')->execute();
		}
	}
}