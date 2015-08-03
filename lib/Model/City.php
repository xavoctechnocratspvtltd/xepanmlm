<?php
namespace xMLM;
class Model_City extends \Model_Document{
	public $table='xmlm_cities';
	public $status=array();
	public $root_document_name="xMLM\Cities";
	public $actions=array(
			'allow_add'=>array(),
			'allow_edit'=>array(),
			'allow_del'=>array(),
			'can_view'=>array(),
		);
	function init(){
		parent::init();

		$this->hasOne('xMLM/Location','location_id');
		$this->addField('name');

		$this->hasMany('Property','city_id');

		$this->add('dynamic_model/Controller_AutoCreator');
	}	
}