<?php

namespace xMLM;

class Model_CreditMovement extends \Model_Document {
	public $table ="xmlm_credits";

	public $status = array('Purchase','Consumed','Collapsed','Canceled','Request');
	public $root_document_name = 'xMLM\CreditMovement';

	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
		);

	function init(){
		parent::init();

		$this->hasOne('xMLM\Distributor','distributor_id')->mandatory(true)->caption('Paid Distributors');
		$this->addField('credits')->type('money')->mandatory(true);
		$this->addField('narration')->mandatory(true);

		$this->add('Controller_Validator');
		$this->is(array(
							'credits|number|>0'
						)
				);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function mark_processed_page($p){
		$form = $p->add('Form_Stacked');
		$form->addField('text','remark');
		$form->addSubmit('Process');
		if($form->isSubmitted()){
			$this->mark_processed();
			$this->setStatus('Purchase',$form['remark']);
			return true;
		}
	}

	function mark_processed(){
		$this->distributor()->set('credit_purchase_points',$this->dsql()->expr('credit_purchase_points+'.$this['credits']))->save();
	}

	function distributor(){
		return $this->ref('distributor_id');
	}

}