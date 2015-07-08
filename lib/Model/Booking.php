<?php
namespace xMLM;
class Model_Booking extends \Model_Document{
	public $table="xmlm_booking";
	public $status=array('request','approved','rejected','availed','canceled');
	public $root_document_name="xMLM\Booking";

	public $actions=array(
		'can_manage_attachments'=>false,
		);

	function init(){
		parent::init();

		$this->hasOne('xMLM/Distributor','distributor_id')->Caption('Distributor name');
		$this->hasOne('xMLM/Property','property_id')->Caption('Hotel name');

		$this->addField('name')->caption('Booking in name of');
		$this->addField('check_in_date')->type('date')->caption('Check in date');
		$this->addField('check_out_date')->type('date')->caption('Check out date');
		$this->addField('no_of_nights')->caption('No of nights');
		$this->addField('no_of_adults')->caption('Adults');
		$this->addField('no_of_childern')->caption('Children');
		$this->getElement('status')->defaultValue('request');
		$this->addField('voucher_no')->type('text')->caption('Voucher no');
		$this->addField('confirmation_code')->system(true);

		$this->addExpression('location')->set(function($m,$q){
			return $m->refSQL('property_id')->fieldQuery('location');
		});
		$this->addExpression('booking_through')->set(function($m,$q){
			return $m->refSQL('property_id')->fieldQuery('booking_through');
		})->caption('Booking through');

		// $this->add('dynamic_model/Controller_AutoCreator');
	}


	function approve_page($page){
		$form =$page->add('Form');
		$form->addField('line','confirmation_code')->validateNotNull();
		$form->addField('line','hotel_contact_number')->set($this->ref('property_id')->get('contact_number'));
		$form->addField('text','remark');
		$form->addSubmit('Approve');

		if($form->isSubmitted()){
			$this['confirmation_code'] = $form['confirmation_code'];
			$this->save();

			$distributor = $this->ref('distributor_id');
			$this->approve($form['remark']);
			$distributor->ref('xMLM/Booking')->addCondition('status','request')->each(function($obj){
				$obj->reject();
			});
			return true;
		}
	}

	function property(){
		return $this->ref('property_id');
	}

	function distributor(){
		return $this->ref('distributor_id');
	} 

	function parseEmailBody($email_body){

		$distributor_model = $this->distributor();
		$property_model = $this->property();
		
		//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
		$email_body = str_replace("{{booking_in_name_of}}", $this['name'], $email_body);
		$email_body = str_replace("{{location}}", $this['location']?$this['location']:" ", $email_body);
		$email_body = str_replace("{{hotel_name}}", $this['property']?$this['property']:" ", $email_body);
		$email_body = str_replace("{{hotel_email_id}}", $property_model['email_id']?$property_model['email_id']:" ", $email_body);
		$email_body = str_replace("{{hotel_contact_number}}", $property_model['contact_number']?$property_model['contact_number']:" ", $email_body);
		$email_body = str_replace("{{hotel_confirmation_code}}", $this['confirmation_code']?$this['confirmation_code']:" ", $email_body);

		$email_body = str_replace("{{adults}}", $this['no_of_adults']?$this['no_of_adults']:" ", $email_body);
		$email_body = str_replace("{{children}}", $this['no_of_childern']?$this['no_of_childern']:" ", $email_body);
		$email_body = str_replace("{{voucher_no}}", $this['voucher_no']?$this['voucher_no']:" ", $email_body);
		$email_body = str_replace("{{confirmation_through}}", $this['booking_through']?$this['booking_through']:" ", $email_body);
		$email_body = str_replace("{{check_in_date}}", $this['check_in_date']?$this['check_in_date']:" ", $email_body);
		$email_body = str_replace("{{check_out_date}}", $this['check_out_date']?$this['check_out_date']:" ", $email_body);

		$email_body = str_replace("{{distributor_name}}", $distributor_model['name'], $email_body);
		$email_body = str_replace("{{distributor_mobile_number}}", $distributor_model['mobile_number']?$distributor_model['mobile_number']:" ", $email_body);
		$email_body = str_replace("{{distributor_email}}", $distributor_model['email']?$distributor_model['email']:"", $email_body);
		$email_body = str_replace("{{distributor_address}}", $distributor_model['address']?$distributor_model['address']:" ", $email_body);
		$email_body = str_replace("{{distributor_state}}", $distributor_model['state']?$distributor_model['state']:" ", $email_body);
		$email_body = str_replace("{{distributor_district}}", $distributor_model['district']?$distributor_model['district']:" ", $email_body);
		$email_body = str_replace("{{distributor_pin_code}}", $distributor_model['pin_code']?$distributor_model['pin_code']:" ", $email_body);
		
		return $email_body;
	}

	function approve($remark){

		$config_model=$this->add('xMLM/Model_Configuration');
		$config_model->tryLoadAny();

		$subject = $config_model['booking_approve_email_subject'];
		$distributor_model =  $this->distributor();
		if(!$email = $distributor_model['email']){
			throw new \Exception("Distributor Email id not Found",'Growl');
		}

		// $email_array = explode(',', $distributor['email']);
		// $email = $email_array[0];
		// unset($email_array[0]);

		// $cc = $email_array;

		if($config_model['booking_approve_email_matter']){
			$email_body=$this->parseEmailBody($config_model['booking_approve_email_matter']);
			$this->sendEmail($email,$subject,$email_body);
		}else{
			throw new \Exception("Booking Approve Email Matter is Empty",'Growl');
		}

		$this->setStatus('approved',$remark);
		return true;
	}

	function cancel(){
		$this->setStatus('canceled');
	}

	function reject(){
		$this->setStatus('rejected');
	}
	
	// Availed
	function mark_processed_page($page){
		$form =$page->add('Form');
		$form->addField('text','remark');
		$form->addSubmit('Availed');

		if($form->isSubmitted()){
			$this->availed($form['remark']);
			return true;
		}


	}

	function availed($remark){
		$this->setStatus('availed',$remark);
	}
}