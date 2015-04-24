<?php

namespace xMLM;

class Model_CreditMovement extends \Model_Document {
	public $table ="xmlm_credits";

	public $status =array();
	public $root_document_name='xMLM\CreditMovement';

	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
		);

	function init(){
		parent::init();

		$this->hasOne('xMLM\Distributor','distributor_id')->mandatory(true);
		$this->addField('credits')->type('money')->mandatory(true);

		$this->addField('action')->enum(array('Purchase','Consumed','Collapsed','Request'))->mandatory(true);

		$this->addField('narration');



		$this->add('dynamic_model/Controller_AutoCreator');
	}

}