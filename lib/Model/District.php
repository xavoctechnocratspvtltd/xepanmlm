<?php

namespace xMLM;

class Model_District extends \Model_Table {
	public $table='xmlm_districts';
	
	function init(){
		parent::init();

		$this->addField('name');

		$this->hasMany('xMLM/Distributor','district_id');
	}
}