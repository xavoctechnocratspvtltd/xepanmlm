<?php

namespace xMLM;

class Model_State extends \Model_Table {
	public $table='xmlm_states';
	
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xMLM/Distributor','district_id');
	}
}