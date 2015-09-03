<?php

namespace xMLM;

class Model_CreditMovement extends \Model_Document {
	public $table ="xmlm_credits";

	public $status = array('approved','Purchase','Consumed','Collapsed','Rejected','Request');
	public $root_document_name = 'xMLM\CreditMovement';

	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array()
		);

	function init(){
		parent::init();

		$this->hasOne('xMLM\Distributor','distributor_id')->mandatory(true)->caption('Distributor name');
		$this->hasOne('xMLM\Distributor','joined_distributor_id')->mandatory(true)->caption('Used for distributor');
		$this->addField('credits')->type('money')->mandatory("Credits is required")->caption("Credit amount");
		$this->addField('credits_given_on')->type('datetime')->defaultValue(null);
		$this->addField('narration')->mandatory('Remarks is required')->caption("Remarks");

		$this->add('filestore/Field_Image','attachment_id');//->mandatory(true);

		$this->addHook('beforeDelete',array($this,'beforeCreditDelete'));

		$this->add('Controller_Validator');
		$this->is(array(
							'credits|number|>0'
						)
				);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeCreditDelete(){
		if($act = $this->add('xCRM/Model_Activity')->loadWhoseRelatedDocIs($this))
			$act->forceDelete();
	}

	function approve_page($page){
		$form = $page->add('Form');
		$form->addField('text','transaction_details');
		$form->addSubmit('ok');

		if($form->isSubmitted()){
			$this->approve($form['transaction_details']);
			$tm=$this->add( 'TMail_Transport_PHPMailer' );
			// $msg=$this->add( 'GiTemplate' );
			// $msg->loadTemplate( 'mail/registerdistributerwhenidorange');
			$dist=$this->distributor();
			// $distributer_mail=$this->distributor()->get('email');

			$cc = array();
			$emails = $this->add('xMLM/Model_Configuration')->tryLoadANy()->get('credit_request_approve_email');
			$emails=explode(",", $emails);
			$email = $emails[0];
			// unset($emails[0]);
			$emails = array_values($emails);
			// throw new \Exception("coming", 1);
			$cc = $emails;

			if(!$email) throw new \Exception("not found ".$email, 1);
			
			$config_model=$this->add('xMLM/Model_Configuration');
			$config_model->tryLoadAny();

			if($config_model['credit_movement_email_matter']){
				// $distributer_mail=$this['email'];
				$subject= $config_model['credit_movement_email_subject']." ".$this['name'];
				$email_body=$config_model['credit_movement_email_matter']?:"Credit Request Approve Send Mail Layout Is Empty";
		
				//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
				$email_body = str_replace("{{name}}", $dist['name'], $email_body);
				$email_body = str_replace("{{mobile_number}}", $dist['mobile_number']?$dist['mobile_number']:" ", $email_body);
				$email_body = str_replace("{{email}}", $dist['email']?$dist['email']:" ", $email_body);
				$email_body = str_replace("{{status}}", $this['status']?$this['status']:" ", $email_body);
				$email_body = str_replace("{{credits}}", $this['credits']?$this['credits']:" ", $email_body);
				$email_body = str_replace("{{credits_given_on}}", $this['credits_given_on']?$this['credits_given_on']:" ", $email_body);
				$email_body = str_replace("{{state}}", $dist['state']?$dist['state']:" ", $email_body);
				$email_body = str_replace("{{district}}", $dist['district']?$dist['district']:" ", $email_body);
				$email_body = str_replace("{{address}}", $dist['address']?$dist['address']:" ", $email_body);
				$email_body = str_replace("{{narration}}", $this['narration']?$this['narration']:" ", $email_body);

			}	
			if(!$email) return;
				throw new \Exception($email_body, 1);
				
			try{
				$tm->send($email,$email,$subject,$email_body,null,$cc);
			}catch( \phpmailerException $e ) {
				$this->js(true)->univ()->errorMessage($e->getMessage());
			}catch( \Exception $e ) {
				throw $e;
			}
			return true;


		}
	}

	function approve($transaction_details){
		$this->setStatus('approved',$transaction_details);
	}

	function reject_page($page){
		$form = $page->add('Form');
		$form->addField('text','reason');
		$form->addSubmit('ok');

		if($form->isSubmitted()){
			$this->reject($form['reason']);
			return true;
		}
	}

	function parseEmailBody(){

		$dist=$this->distributor();
		$distributer_mail=$this->distributor()->get('email');

		$config_model=$this->add('xMLM/Model_Configuration');
		$config_model->tryLoadAny();
				
		$email_body=$config_model['credit_movement_email_matter']?:"Rejected Distributor Send Mail Layout Is Empty";
		
		//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
		$email_body = str_replace("{{name}}", $dist['name'], $email_body);
		$email_body = str_replace("{{mobile_number}}", $dist['mobile_number']?$dist['mobile_number']:" ", $email_body);
		$email_body = str_replace("{{email}}", $dist['email']?$dist['email']:" ", $email_body);
		$email_body = str_replace("{{status}}", $this['status']?$this['status']:" ", $email_body);
		$email_body = str_replace("{{credits}}", $this['credits']?$this['credits']:" ", $email_body);
		$email_body = str_replace("{{credits_given_on}}", $this['credits_given_on']?$this['credits_given_on']:" ", $email_body);
		$email_body = str_replace("{{state}}", $dist['state']?$dist['state']:" ", $email_body);
		$email_body = str_replace("{{district}}", $dist['district']?$dist['district']:" ", $email_body);
		$email_body = str_replace("{{address}}", $dist['address']?$dist['address']:" ", $email_body);
		$email_body = str_replace("{{narration}}", $this['narration']?$this['narration']:" ", $email_body);

		return $email_body;
	}


	function reject($reason){
		// $this->distributor()->nitifyViaEmail("Credit Request Canceled","Dear Distributor,<br> Your credit request has been canceled by administrator due to following reason: <br/> '$reason' <br/><br/> -- Reagrds <br/> System");
		if(!$this->loaded()) throw $this->exception('Model Must Be Loaded Before Email Send');
		
		$dist=$this->distributor();

		
		$config_model=$this->add('xMLM/Model_Configuration');
		$config_model->tryLoadAny();

		$distributer_mail=$this->distributor()->get('email');
		$subject= $config_model['credit_movement_email_subject']." ".$dist['name'];
		$email_body = $this->parseEmailBody();
			// echo "string". $email_body;
			// exit;

		$this->sendEmail($distributer_mail,$subject,$email_body,null,null);
		$this->setStatus('Rejected',$reason);
			return true;		
	}

	function mark_processed_page($p){
		$form = $p->add('Form_Stacked');
		$form->addField('text','remark');
		$form->addSubmit('Ok');
		if($form->isSubmitted()){
			$this->mark_processed();
			$this->setStatus('Purchase',$form['remark']);
			return true;
		}
	}

	function mark_processed(){
		
		$this['credits_given_on']=$this->api->now;
		$this->save();
		$dis=$this->distributor();
		$dis->set('credit_purchase_points',$this->dsql()->expr('credit_purchase_points+'.$this['credits']))->save();
	}

	function distributor(){
		return $this->ref('distributor_id');
	}

	function email_authorities($total=false){
		$cc = array();
		$emails = $this->add('xMLM/Model_Configuration')->tryLoadANy()->get('credit_manager_email_id');
		$emails=explode(",", $emails);
		$email = $emails[0];
		unset($emails[0]);
		$emails = array_values($emails);
		$cc = $emails;

		if(!$email) return;

		if(!$total){
			$config_model=$this->add('xMLM/Model_Configuration');
			$subject = $config_model['credit_movement_email_subject']. ":: ". $this['distributor']. ' :: ' . $this['distributor_id'];
			// $email_body = $config_model['credit_movement_email_matter'];			
			$email_body=$this->parseEmailBody();
		}else{
			$pendings = $this->add('xMLM/Model_Credit_Request')->count()->getOne();
			$config_model=$this->add('xMLM/Model_Configuration');
			$subject ="Credit Request Pending::".$pendings; //$config_model['credit_movement_email_subject'];
			$email_body="Hi<br/>There are $pendings Credit Request Pending<br/><br/> Please Check<br/><br/>--Regards <br/><a href='http://xepan.org'>xEpan</a> System";//$this->parseEmailBody();
		}

		$tm=$this->add( 'TMail_Transport_PHPMailer' );	
		try{
			$tm->send($email,$email,$subject, $email_body,$cc);
		}catch( \phpmailerException $e ) {
			$this->api->js(null,'$("#form-'.$_REQUEST['form_id'].'")[0].reset()')->univ()->errorMessage( $e->errorMessage() . " " . $email )->execute();
		}catch( \Exception $e ) {
			throw $e;
		}
	}

}
