<?php

namespace xMLM;

class Model_CreditMovement extends \Model_Document {
	public $table ="xmlm_credits";

	public $status = array('approved','Purchase','Consumed','Collapsed','Canceled','Request');
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
		$this->addField('credits_given_on')->type('datetime')->defaultValue(null);
		$this->addField('narration')->mandatory(true);

		$this->add('filestore/Field_Image','attachment_id');//->mandatory(true);

		$this->add('Controller_Validator');
		$this->is(array(
							'credits|number|>0'
						)
				);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function approve_page($page){
		$form = $page->add('Form');
		$form->addField('text','transaction_details');
		$form->addSubmit('Approve');

		if($form->isSubmitted()){
			$this->approve($form['transaction_details']);
			return true;
		}
	}

	function approve($transaction_details){
		$this->setStatus('approved',$transaction_details);
	}

	function cancel_page($page){
		$form = $page->add('Form');
		$form->addField('text','reason');
		$form->addSubmit('Cancel');

		if($form->isSubmitted()){
			$this->cancel($form['reason']);
			return true;
		}
	}

	function cancel($reason){
		$this->distributor()->nitifyViaEmail("Credit Request Canceled","Dear Distributor,<br> Your credit request has been canceled by administrator due to following reason: <br/> '$reason' <br/><br/> -- Reagrds <br/> System");
		$this->setStatus('Canceled',$reason);
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
		$this['credits_given_on']=$this->api->now;
		$this->save();
		$this->distributor()->set('credit_purchase_points',$this->dsql()->expr('credit_purchase_points+'.$this['credits']))->save();
	}

	function distributor(){
		return $this->ref('distributor_id');
	}

	function email_authorities($total=false){
		$email = $this->add('xMLM/Model_Configuration')->tryLoadANy()->get('credit_manager_email_id');
		if(!$email) return;

		if(!$total){
			$subject = "Credit Request :: ". $this['distributor']. ' :: ' . $this['distributor_id'];
			$email_body = "Hi<br/> There is a Credit Request pending from ". $this['distributor'].' <br/><br/> Please check <br/><br/>--Regards <br/><a href="http://xepan.org">xEpan</a> System';
		}else{
			$pendings = $this->add('xMLM/Model_Credit_Request')->count()->getOne();
			$subject = "Credit Requests Pending :: ". $pendings;
			$email_body = "Hi<br/> There are $pendings Credit Requests pending <br/><br/> Please check <br/><br/>--Regards <br/><a href='http://xepan.org'>xEpan</a> System";
		}

		$tm=$this->add( 'TMail_Transport_PHPMailer' );	
		try{
			$tm->send($email, $email,$subject, $email_body);
		}catch( \phpmailerException $e ) {
			$this->api->js(null,'$("#form-'.$_REQUEST['form_id'].'")[0].reset()')->univ()->errorMessage( $e->errorMessage() . " " . $email )->execute();
		}catch( \Exception $e ) {
			throw $e;
		}
	}

}