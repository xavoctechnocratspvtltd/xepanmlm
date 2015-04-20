<?php

namespace xMLM;

class Model_Configuration extends \SQL_Model {
	public $table ="xmlm_configurations";

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('admin_charge')->caption('Admin Charge in %');
		$this->addField('other_charge_name');
		$this->addField('other_charge')->caption('Other Charge in %');
		$this->addField('registration_type')->enum(array('Free','Paid'))->mandatory(true);
		$this->addField('required_tail')->type('boolean');
		$this->addField('allow_extreme_left_right')->type('boolean');

	}
}