<?php
namespace xMLM;
class Model_Location extends \Model_Document{
	public $table="xmlm_localtions";
	public $status=array();
	public $root_document_name="xMLM\Locations";
	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
			'can_view'=>array(),
		);
	function init(){
		parent::init();

		// $this->hasOne('xMLM/Distributor','distributor_id')->Caption('Distributor Name');
		$this->addField('name');
		$this->hasMany('xMLM/City','location_id');
		$this->hasMany('xMLM/Property','location_id');

		$this->add('dynamic_model/Controller_AutoCreator');
	}
}