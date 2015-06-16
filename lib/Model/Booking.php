<?php
namespace xMLM;
class Model_Booking extends \Model_Document{
	public $table="xmlm_booking";
	public $status=array('request','approved','rejected','availed','canceled');
	public $root_document_name="xMLM\Booking";
	function init(){
		parent::init();

		$this->hasOne('xMLM/Distributor','distributor_id')->Caption('Distributor Name');
		$this->hasOne('xMLM/Property','property_id')->Caption('Hotel Name');

		$this->addField('name')->caption('Booking in Name of');
		$this->addField('check_in_date')->type('date');
		$this->addField('check_out_date')->type('date');
		$this->addField('no_of_nights')->type('money');
		$this->addField('no_of_adults')->type('money');
		$this->addField('no_of_childern')->type('money');
		$this->getElement('status')->defaultValue('request');
		$this->addField('voucher_no')->type('text');

		$this->addExpression('location')->set(function($m,$q){
			return $m->refSQL('property_id')->fieldQuery('location');
		});
		$this->addExpression('booking_through')->set(function($m,$q){
			return $m->refSQL('property_id')->fieldQuery('booking_through');
		});

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function approve(){

	}

	function cancel(){

	}

	function reject(){

	}
	
	function mark_processed(){

	}
}