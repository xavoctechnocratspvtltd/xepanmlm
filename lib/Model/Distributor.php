<?php

namespace xMLM;

class Model_Distributor extends \Model_Document {
	public $table ="xmlm_distributors";
	public $status=array('paid','unpaid');
	public $root_document_name="xMLM\Distributor";
	public $actions = array(
			'allow_del'=>array(),
			'allow_edit'=>array()
			);
	
	function init(){
		parent::init();

		$this->getElement('status')->DefaultValue('unpaid');
		$this->addField('customer_id')->system(true);
		$this->addField('user_id')->system(true);
		$this->hasOne('xMLM/Sponsor','sponsor_id');
		$this->hasOne('xMLM/Introducer','introducer_id');


		$user_j = $this->join('users','user_id');
		$user_j->addField('username')->sortable(true)->group('b~6~Distributor Loign');
		$user_j->addField('password')->type('password')->group('b~6');
		$user_j->addField('name')->group('a~4~Basic Info')->mandatory(true);
		$user_j->addField('email')->sortable(true)->group('a~4');

		$customer_j = $user_j->join('xshop_memberdetails.users_id');
		$customer_j->addField('users_id')->system(true);
		$customer_j->addField('mobile_number')->group('a~4');
		$customer_j->addField('address')->type('text')->group('a~12');
		$customer_j->addField('is_active')->type('boolean');


		$user_j->addField('type')->setValueList(array(100=>'SuperUser',80=>'BackEndUser',50=>'FrontEndUser'))->defaultValue(50)->group('a~6')->sortable(true)->mandatory(false);
		$this->addCondition('type',50);

		// Other technical fields for MLM purpose here
		$this->hasOne('xMLM/Kit','kit_item_id');

		$this->addField('Path')->type('text')->system(true);
		$this->addField('session_left_pv');
		$this->addField('session_right_pv');

		$this->addField('total_left_pv');
		$this->addField('total_right_pv');

		$this->addField('carried_amount')->type('money');

		$this->addField('temp')->system(true);

		$this->hasMany('xMLM/Sponsor','sponsor_id',null,'SponsoredDistributors');
		$this->hasMany('xMLM/Introducer','introducer_id',null,'IntroducedDistributors');
		// $this->debug();
	}

	function updateSelfPath(){

	}

	function markGreen(){

	}

	function markRed(){

	}


}