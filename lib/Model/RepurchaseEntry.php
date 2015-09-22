<?php

namespace xMLM;

class Model_RepurchaseEntry extends \Model_Document {
	public $table ="xmlm_repurchase_entries";
	public $status=array('draft','approved');
	public $root_document_name ='xMLM\RepurchaseEntry';

	function init(){
		parent::init();

		$this->hasOne('xMLM/Distributor','distributor_id')->caption('Distributor name')->mandatory('Distributor name is required');
		$this->hasOne('xMLM/RepurchaseItem','repurchaseitem_id')->caption('Repurchase item name')->mandatory('Repurchase item is required');

		$this->addField('pv')->type('int');
		$this->addField('bv')->type('int');
		$this->addField('narration')->type('text');
		
	}


	function approve_page($page){

		$form = $page->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Ok');

		if($form->isSubmitted()){
			$this->approve($form['comments']);
			// $this->send_via_email_page($this);
			return true;
		}
		return false;
	}

	function approve($message){

		$repurchase_item = $this->ref('repurchaseitem_id');

		$this['pv'] = $repurchase_item['pv'];
		$this['bv'] = $repurchase_item['bv'];
		$this->save();

		$distributor_model = $this->ref('distributor_id');
		$distributor_model['session_self_bv'] = $distributor_model['session_self_bv'] + $this['bv'];
		$distributor_model->save();
		$distributor_model->updateAnsestors($this['pv'],$this['bv']);

		$this->setStatus('approved',$message);
		return $this;
	}


}