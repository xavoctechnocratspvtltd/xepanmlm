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
	
	public $title_field = 'username';

	function init(){
		parent::init();

		$this->getElement('status')->DefaultValue('unpaid');
		$this->addField('customer_id')->type('int')->system(true);
		$this->addField('user_id')->type('int')->system(true);
		$this->hasOne('xMLM/Sponsor','sponsor_id')->display(array('form'=>'xMLM/Distributor'));//->mandatory(true);
		$this->hasOne('xMLM/Introducer','introducer_id')->display(array('form'=>'xMLM/Distributor'));//->mandatory(true);


		$user_j = $this->join('users','user_id');
		$user_j->addField('username')->sortable(true)->group('b~4~Distributor Login')->mandatory(true);
		$user_j->addField('password')->type('password')->group('b~4')->mandatory(true);
		$user_j->addField('name')->mandatory(true)->mandatory(true)->system(true);
			$this->addField('first_name')->group('a~4~Distributor Info')->mandatory(true)->mandatory(true);
			$this->addField('last_name')->group('a~4')->mandatory(true)->mandatory(true);
			$this->addField('date_of_birth')->type('date')->group('a~4')->mandatory(true)->mandatory(true);
		$user_j->addField('email')->sortable(true)->group('a~4');
		$user_j->addField('user_is_active','is_active')->system(true)->defaultValue(true);

		$customer_j = $this->join('xshop_memberdetails','customer_id');
		$customer_j->addField('users_id')->type('int')->system(true);
		$customer_j->addField('mobile_number')->group('a~4');
		$this->addField('pan_no')->group('a~4');
		$customer_j->addField('address')->type('text')->group('a~12')->system(true);
			
			$this->addField('block_no')->group('a~4');
			$this->addField('landmark')->group('a~4');
			$this->addField('building_no')->group('a~4');
			$this->addField('pin_code')->group('a~4');

			$this->hasOne('xMLM/State','state_id')->group('a~4');
			$this->hasOne('xMLM/District','district_id')->group('a~4');

		$customer_j->addField('is_active')->type('boolean')->defaultValue(true);


		$user_j->addField('type')->setValueList(array(100=>'SuperUser',80=>'BackEndUser',50=>'FrontEndUser'))->defaultValue(50)->group('a~6')->sortable(true)->mandatory(false);
		$this->addCondition('type',50);

		// Other technical fields for MLM purpose here
		$this->hasOne('xMLM/Kit','kit_item_id')->defaultValue(null);
		$this->addField('capping')->type('int')->system(true);

		$this->hasOne('xMLM/Left','left_id','username')->defaultValue(0);
		$this->hasOne('xMLM/Right','right_id','username')->defaultValue(0);

		$this->addField('re_password')->type('password')->group('b~4');

		$this->hasOne('xMLM/Bank','bank_id')->group('e~6~Bank Info');//->system(true);
		
		$this->addField('IFCS_Code')->group('e~6~bl');//->system(true);
		$this->addField('account_no')->group('e~6');//->system(true);
		$this->addField('branch_name')->group('e~6~bl');//->system(true);
		$this->addField('nominee_name')->group('f~6~Nominee Details');//->system(true);
		$this->addField('relation_with_nominee')->enum(array('Father', 'Mother', 'Spouse', 'Sibling', 'Friend', 'Son', 'Daughter'))->group('f~2');//->system(true);
		$this->addField('nominee_email')->group('f~2');//->system(true);
		$this->addField('nominee_age')->group('f~2');//->system(true);

		$this->addField('Leg')->setValueList(array('A'=>'Left','B'=>'Right'))->mandatory(true);
		$this->addField('path')->type('text')->system(true);

		$this->addField('session_intros_amount')->type('money')->defaultValue(0);
		$this->addField('total_intros_amount')->type('money')->defaultValue(0);

		$this->addField('session_left_pv')->type('int')->defaultValue(0);
		$this->addField('session_right_pv')->type('int')->defaultValue(0);


		$this->addField('total_left_pv')->type('int')->defaultValue(0);
		$this->addField('total_right_pv')->type('int')->defaultValue(0);

		$this->addField('session_self_bv')->type('int')->defaultValue(0);
		$this->addField('session_left_bv')->type('int')->defaultValue(0);
		$this->addField('session_right_bv')->type('int')->defaultValue(0);
		
		$this->addField('total_left_bv')->type('int')->defaultValue(0);
		$this->addField('total_right_bv')->type('int')->defaultValue(0);
		
		$this->addField('total_pairs')->type('int')->defaultValue(0);

		$this->addField('carried_amount')->type('money')->defaultValue(0);
		$this->addField('credit_purchase_points')->type('money')->defaultValue(0);
		$this->addField('temp')->system(true)->defaultValue(0);

		$this->addField('greened_on')->type('datetime')->defaultValue(null);

		$this->hasMany('xMLM/Sponsor','sponsor_id',null,'SponsoredDistributors');
		$this->hasMany('xMLM/Introducer','introducer_id',null,'IntroducedDistributors');
		$this->hasMany('xMLM/CreditMovement','distributor_id');

		$this->addHook('beforeSave',array($this,'beforeSaveDistributor'));
		$this->addHook('afterSave',array($this,'afterSaveDistributor'));
		$this->addHook('beforeDelete',array($this,'beforeDeleteDistributor'));

		$this->add('Controller_Validator');
		$this->is(array(
							'username|to_trim|unique',
							'email|email'
						)
				);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSaveDistributor(){
		if($this['password']!=$this['re_password'])
			throw $this->exception('Passwords Must Match','ValidityCheck')->setField('re_password');


		$mobile_number = $this['mobile_number'];
		if($this['mobile_number'] AND preg_match('/^\d{10}$/', $mobile_number)){
		}else{
			throw $this->exception('Mobile Number must be 10 digit long only '. $mobile_number,'ValidityCheck')->setField('mobile_number');
		}

		$diff = $this->api->my_date_diff($this->api->today,$this['date_of_birth']);
		if($diff['years']<18)
			throw $this->exception('Applicant must be above 18','ValidityCheck')->setField('date_of_birth');


		// throw new \Exception("Error Processing Request", 1);
		

		$this['name'] = $this['first_name'].' '. $this['last_name'];

		// Check For available purchase points
		if($this->dirty['kit_item_id'] AND $this['kit_item_id'] !==""){
			$kit=$this->kit();
			if($kit AND !$this->validateKitPurchasePoints($this->kit())){
				throw $this->exception($this->newInstance()->loadLoggedIn()->get('name').' :: You do not have sufficient credits','Growl');
			}
			$this['status']='paid';
			$this['greened_on']=$this['created_at'];
			$this['capping']=$kit->getCapping();
			if($this->loaded()){
				$this->updateAnsestors($kit->getPV(),$kit->getBV());
				$introducer = $this->introducer();
				$introducer->addSessionIntro($kit->getIntro());
			}
		}

		if(!$this->loaded()){
			// Its New Entry
			$dist= $this->add('xMLM/Model_Distributor')->loadLoggedIn();
			if(!$dist and !$this->api->auth->model->isDefaultSuperUser()){
				throw $this->exception('You do not have rights to add distributor','Growl');
			}

			if($sponsor = $this->sponsor()){
				if($sponsor[($this['Leg']=='A'?'left':'right').'_id']){
					throw $this->exception('Leg Already Filled','ValidityCheck')->setField('Leg');
				}
				$this['path'] = $sponsor->path() . $this['Leg'];
				$this->memorize('leg',$this['Leg']);
			}
		}
	}

	function afterSaveDistributor(){
		if($leg = $this->recall('leg',false)){
			$sponsor = $this->sponsor();
			$sponsor[($leg=='A'?'left':'right').'_id'] = $this->id;
			$sponsor->save();
			if($this['greened_on']){
				$kit=$this->kit();
				$this->updateAnsestors($kit->getPV(),$kit->getBV());
				$introducer = $this->introducer();
				$introducer->addSessionIntro($kit->getIntro());
			}
			$this->welcomeDistributor();
			$this->forget('leg');
		}
	}

	function beforeDeleteDistributor(){
		if($this['greened_on'] OR $this['left_id'] OR $this['right_id'])
			throw $this->exception('Cannot Delete','Growl');
	}

	function forceDelete(){
		if(!isset($this->api->deleted_distributor)) $this->api->deleted_distributor =array();
		if(in_array($this->id, $this->api->deleted_distributor)) return;
		
		if($this['sponsor_id'])	$this->newInstance()->tryLoad($this['sponsor_id'])->forceDelete();
		if($this['introducer_id']) $this->newInstance()->tryLoad($this['introducer_id'])->forceDelete();
		if($this['left_id']) $this->newInstance()->tryLoad($this['left_id'])->forceDelete();
		if($this['right_id']) $this->newInstance()->tryLoad($this['right_id'])->forceDelete();

		$this->api->deleted_distributor[] = $this->id;

		$this->delete();
	}

	function welcomeDistributor(){

	}

	function creditMovements(){
		return $this->add('xMLM/Model_CreditMovement')->addCondition('distributor_id',$this->id);
	}

	function consumePurchasePoints($points,$narration){
		$this['credit_purchase_points'] = $this['credit_purchase_points'] - $points;
		$this->save();
		$credit_movement = $this->creditMovements();
		$credit_movement['credits'] = $points;
		$credit_movement['narration'] = $narration;
		$credit_movement['status'] = 'Consumed';
		$credit_movement->save();
	}

	function updateKit($kit, $from_distributor=false){
		if(!$from_distributor) $from_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

	}

	function validateKitPurchasePoints($kit, $from_distributor=false){
		$kitpoints = $kit->requiredPurchasePoints();

		if($this->api->auth->model->isBackEndUser() ){
			// throw new \Exception("Is BackEnd User", 1);
			return true;	
		} 

		if($from_distributor){
			if(!$from_distributor instanceof \xMLM\Model_Distributor){
				return false;
			}

			if($from_distributor['credit_purchase_points'] < $kitpoints){
				return false;		
			}
			$from_distributor->consumePurchasePoints($kitpoints,"Joining of ".$this->id." [".$this['username']."]");
			return true;
		}

		if(!($logged_in_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn())){
			// throw new \Exception("Distributor not loggedin", 1);
			return false;
		}

		if($logged_in_distributor['credit_purchase_points'] < $kitpoints){
			// throw new \Exception("Not sufficient credit points", 1);
			return false;
		}
		$logged_in_distributor->consumePurchasePoints($kitpoints,"Joining of ".$this->id." [".$this['username']."]");
		return true;
	}

	function login(){
		$this->api->auth->login($this['username']);
	}

	function loadLoggedIn(){

		if($this->loaded()) $this->unload();
		if(!$this->api->auth->isLoggedIn()) return false;
		
		$this->addCondition('user_id',$this->api->auth->model->id);
		$this->tryLoadAny();
		if(!$this->loaded()) return false;
		return $this;
	}

	function updateAnsestors($pv_points,$bv_points){
		// throw new \Exception("Error Processing Request", 1);
		
		$path = $this['path'];
		$q="
				UPDATE xmlm_distributors d
				Inner Join 
				(SELECT 
					id,
					path,
					LEFT('$path',LENGTH(path)) desired,
					MID('$path',LENGTH(path)+1,1) next_char
				 FROM xmlm_distributors 
				 HAVING
				 next_char = 'A' AND desired=path
				 ) lefts on lefts.id = d.id
				SET
					session_left_pv = session_left_pv + $pv_points,
					session_left_bv = session_left_bv + $bv_points,
					total_left_pv = total_left_pv + $pv_points,
					total_left_bv = total_left_bv + $bv_points
		";
		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();

		$q="
				UPDATE xmlm_distributors d
				Inner Join 
				(SELECT 
					id,
					path,
					LEFT('$path',LENGTH(path)) desired,
					MID('$path',LENGTH(path)+1,1) next_char
				 FROM xmlm_distributors 
				 HAVING
				 next_char = 'B' AND desired=path
				 ) rights on rights.id = d.id
				SET
					session_right_pv = session_right_pv + $pv_points,
					session_right_bv = session_right_bv + $bv_points,
					total_right_pv = total_right_pv + $pv_points,
					total_right_bv = total_right_bv + $bv_points
		";

		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
	}

	function addSessionIntro($intro_amount){
		$this['session_intros_amount'] = $this['session_intros_amount'] + $intro_amount;
		$this->save();
	}

	function markGreen(){

	}

	function markRed(){

	}

	function sponsor(){
		if($this['sponsor_id'])
			return $this->ref('sponsor_id');
		return false;
	}

	function introducer(){
		if($this['introducer_id'])
			return $this->ref('introducer_id');
		return false;
	}

	function path(){
		return $this['path'];
	}

	function kit(){
		if($this['kit_item_id'])
			return $this->ref('kit_item_id');
		return false;
	}

	function isInDown($downline_distributor){
		$down_path = $downline_distributor['path'];
		$my_path =$this['path'];

		return strpos($down_path, $my_path) !== false;
	}

	function loadRoot(){
		return $this->loadBy('path','0');	
	}

}