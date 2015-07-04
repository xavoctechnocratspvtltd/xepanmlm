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
		$this->addField('no_of_adults')->caption('No of adults');
		$this->addField('no_of_childern')->caption('No of children');
		$this->getElement('status')->defaultValue('request');
		$this->addField('voucher_no')->type('text')->caption('Voucher no');

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
		$form->addField('text','remark');
		$form->addSubmit('Approve');

		if($form->isSubmitted()){
			$distributor = $this->ref('distributor_id');
			$this->approve($form['remark']);
			$distributor->ref('xMLM/Booking')->addCondition('status','request')->each(function($obj){
				$obj->reject();
			});
			return true;
		}
	}

	function approve($remark){
		$this->setStatus('approved',$remark);
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