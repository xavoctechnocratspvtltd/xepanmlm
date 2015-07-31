<?php
namespace xMLM;
class Model_Property extends \Model_Document{
	public $table="xmlm_property";
	public $status=array();
	public $root_document_name="xMLM\Properties";
	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
			'can_view'=>array(),
		);
	function init(){
		parent::init();
		$this->hasOne('xMLM/Location','location_id');
		$this->addField('name')->Caption('Hotel Name');
		$this->addField('rate')->type('money');
		$this->addField('booking_through')->Caption('Booking through');
		$this->addField('contact_number')->caption('Contact number');
		$this->addField('email_id')->caption('Email id');

		$this->hasMany('xMLM/Booking','property_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}