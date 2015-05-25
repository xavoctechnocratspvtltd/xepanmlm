<?php

namespace xMLM;

class Model_State extends \Model_Table {
	public $table='xmlm_states';
	
	function init(){
		parent::init();

		$this->addField('name')->mandatory(true);
		$this->hasMany('xMLM/District','state_id');
		$this->hasMany('xMLM/Distributor','state_id');
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		
		// Check name for THIS State
		$old_state = $this->add('xMLM/Model_State');
		$old_state->addCondition('name',$this['name']);
		
		if($this->loaded()){
			$old_state->addCondition('id','<>',$this->id);
		}
		$old_state->tryLoadAny();
		if($old_state->loaded()){
			$this->api->js()->univ()->errorMessage('This STATE name is already taken, Chose Another')->execute();
		}
	}
}