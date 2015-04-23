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
		$this->hasOne('xMLM/Sponsor','sponsor_id')->display(array('form'=>'xMLM/Distributor'));//->mandatory(true);
		$this->hasOne('xMLM/Introducer','introducer_id')->display(array('form'=>'xMLM/Distributor'));//->mandatory(true);


		$user_j = $this->join('users','user_id');
		$user_j->addField('username')->sortable(true)->group('b~4~Distributor Login')->mandatory(true);
		$user_j->addField('password')->type('password')->group('b~4')->mandatory(true);
		$user_j->addField('name')->group('a~4~Distributor Info')->mandatory(true)->mandatory(true);
		$user_j->addField('email')->sortable(true)->group('a~4');

		$customer_j = $this->join('xshop_memberdetails','customer_id');
		$customer_j->addField('users_id')->system(true);
		$customer_j->addField('mobile_number')->group('a~4');
		$customer_j->addField('address')->type('text')->group('a~12');
		$customer_j->addField('is_active')->type('boolean')->defaultValue(true);


		$user_j->addField('type')->setValueList(array(100=>'SuperUser',80=>'BackEndUser',50=>'FrontEndUser'))->defaultValue(50)->group('a~6')->sortable(true)->mandatory(false);
		$this->addCondition('type',50);

		// Other technical fields for MLM purpose here
		$this->hasOne('xMLM/Kit','kit_item_id')->mandatory(true);
		
		$this->hasOne('xMLM/Distributor','left_id')->defaultValue(0);
		$this->hasOne('xMLM/Distributor','right_id')->defaultValue(0);

		$this->addField('re_password')->type('password')->group('b~4');

		$this->addField('name_of_bank')->group('e~6~Bank Info');//->system(true);
		$this->addField('IFCS_Code')->group('e~6~bl');//->system(true);
		$this->addField('account_no')->group('e~6');//->system(true);
		$this->addField('branch_name')->group('e~6~bl');//->system(true);
		$this->addField('nominee_name')->group('f~6~Nominee Details');//->system(true);
		$this->addField('relation_with_nominee')->group('f~4');//->system(true);
		$this->addField('nominee_age')->group('f~2');//->system(true);

		$this->addField('Leg')->setValueList(array('A'=>'Left','B'=>'Right'))->mandatory(true);
		$this->addField('path')->type('text')->system(true);

		$this->addField('session_left_pv')->defaultValue(0);
		$this->addField('session_right_pv')->defaultValue(0);

		$this->addField('total_left_pv')->defaultValue(0);
		$this->addField('total_right_pv')->defaultValue(0);

		$this->addField('carried_amount')->type('money')->defaultValue(0);
		$this->addField('credit_amount')->type('money')->defaultValue(0);
		$this->addField('temp')->system(true)->defaultValue(0);

		$this->addField('greened_on')->type('datetime')->defaultValue(null);

		$this->hasMany('xMLM/Sponsor','sponsor_id',null,'SponsoredDistributors');
		$this->hasMany('xMLM/Introducer','introducer_id',null,'IntroducedDistributors');
		$this->hasMany('xMLM/CreditMovement','distributor_id');

		$this->addHook('beforeSave',array($this,'beforeSaveDistributor'));
		$this->addHook('afterSave',array($this,'afterSaveDistributor'));

		$this->add('Controller_Validator');
		$this->is(array(
							'username|to_trim|unique',
							'email|email',
							'email|unique'
						)
				);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSaveDistributor(){
		if($this['password']!=$this['re_password'])
			throw $this->exception('Passwords Must Match','ValidityCheck')->setField('re_password');

		if(!$this->loaded()){
			// Its New Entry
			$dist= $this->add('xMLM/Model_Distributor')->loadLoggedIn();
			if(!$dist and !$this->api->auth->model->isDefaultSuperUser()){
				throw $this->exception('You do not have rights to add distributor','Growl');
			}

			// Check For available purchase points


			$sponsor = $this->sponsor();
			if($sponsor[($this['Leg']=='A'?'left':'right').'_id']){
				throw $this->exception('Leg Already Filled','ValidityCheck')->setField('Leg');
			}
			$this['path'] = $sponsor->path() . $this['Leg'];
			$this->memorize('leg',$this['Leg']);
		}
	}

	function afterSaveDistributor(){
		if($leg = $this->recall('leg',false)){
			$sponsor = $this->sponsor();
			$sponsor[($leg=='A'?'left':'right').'_id'] = $this->id;
			$sponsor->save();
			$this->forget('leg');
		}
	}

	function loadLoggedIn(){
		if($this->loaded()) $this->unload();
		if(!$this->api->auth->isLoggedIn()) return false;
		
		$this->addCondition('user_id',$this->api->auth->model->id);
		$this->tryLoadAny();
		if(!$this->loaded()) return false;
		return true;
	}

	function markGreen(){

	}

	function markRed(){

	}

	function sponsor(){
		return $this->ref('sponsor_id');
	}

	function path(){
		return $this['path'];
	}


}