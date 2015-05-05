<?php

namespace xMLM;

class Model_BVSlab extends \Model_Document {
	public $table ="xmlm_bvslab";
	public $status=array();
	public $root_document_name ='xMLM\BVSlab';

	function init(){
		parent::init();

		$this->addField('name')->type('int')->caption("Total Business volume");
		$this->addField('percentage')->type('int');

		$this->setOrder('name');

	}

}