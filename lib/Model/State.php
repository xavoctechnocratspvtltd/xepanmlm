<?php

namespace xMLM;

class Model_State extends \Model_Table {
	public $table='xmlm_states';
	
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xMLM/District','state_id');
		$this->hasMany('xMLM/Distributor','state_id');
	}
}