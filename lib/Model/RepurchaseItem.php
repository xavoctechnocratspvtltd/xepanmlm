<?php

namespace xMLM;

class Model_RepurchaseItem extends \Model_Document {
	public $table ="xmlm_repurchase_items";
	public $status=array();
	public $root_document_name ='xMLM\RepurchaseItem';

	function init(){
		parent::init();

		$this->addField('name')->caption('Repurchase item name')->mandatory('Repurchase item name is required');
		$this->addField('pv')->type('int')->mandatory('PV is required')->system(true)->defaultValue(0);
		$this->addField('bv')->type('int')->mandatory('BV is required')->defaultValue(0);
		$this->addField('mrp')->type('money')->caption('MRP')->mandatory('MRP is required');

	}

}