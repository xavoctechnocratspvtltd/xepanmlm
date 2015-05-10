<?php

namespace xMLM;

class Model_Bank extends \Model_Table {
	public $table='xmlm_banks';
	
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xMLM/Distributor','district_id');
	}
}