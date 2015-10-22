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

		$config = $this->add('xMLM/Model_Configuration')->tryLoadAny();

		$this->getElement('status')->DefaultValue('unpaid');
		$this->addField('customer_id')->type('int')->system(true);
		$this->addField('user_id')->type('int')->system(true);
		$this->hasOne('xMLM/Sponsor','sponsor_id')->display(array('form'=>'xMLM/Distributor'))->mandatory("Sponsor is required");
		$this->hasOne('xMLM/Introducer','introducer_id')->display(array('form'=>'xMLM/Distributor'))->mandatory("Introducer is required");


		$user_j = $this->join('users','user_id');
		$user_j->addField('username')->sortable(true)->group('b~4~Distributor Login')->mandatory(true)->display(array('form'=>'xMLM/LetterCount'));
		$user_j->addField('password')->type('password')->group('b~4')->mandatory(true)->display(array('form'=>'xMLM/Password'));
		$user_j->addField('name')->mandatory(true)->mandatory(true)->system(true)->display(array('form'=>'Alpha'))->caption('Distributor name');
			$this->addField('first_name')->group('a~4~Distributor Info')->mandatory("First name is required")->display(array('form'=>'Alpha'))->caption('First name');
			$this->addField('last_name')->group('a~4')->mandatory("Last name is required")->display(array('form'=>'Alpha'))->caption('Last name');
			$this->addField('date_of_birth')->type('date')->group('a~4')->mandatory("Date of Birth is required")->display(array('form'=>'xMLM/BDate'))->caption('Date of Birth');
		$user_j->addField('email')->sortable(true)->group('a~4')->mandatory("Email is required")->display(array('form'=>'Email'))->caption('Email id');

		$user_j->addField('user_is_active','is_active')->system(true)->defaultValue(true);
		$user_j->addField('user_epan_id','epan_id')->system(true);
		
		$this->addCondition('user_epan_id',$this->api->current_website->id);

		$customer_j = $this->join('xshop_memberdetails','customer_id');
		$customer_j->addField('users_id')->type('int')->system(true);
		$customer_j->addField('mobile_number')->group('a~4')->mandatory("Please enter Mobile number")->display(array('form'=>'xMLM/MobileNumber'))->caption('Mobile number');
		
		$customer_j->addField('member_epan_id','epan_id')->system(true);
		$this->addCondition('member_epan_id',$this->api->current_website->id);

		$this->addField('pan_no')->group('a~4')->display(array('form'=>'xMLM/PanNumber'))->caption('PAN no.');
		// $customer_j->addField('address')->type('text')->group('a~12')->system(true);
			
			// $this->addField('block_no')->group('a~4');
			// $this->addField('building_no')->group('a~4');
			// $this->addField('landmark')->group('a~4');
			$this->addField('address')->type('text')->group('a~12')->mandatory("Address is required");

			$this->addField('pin_code')->group('a~4')->display(array('form'=>'xMLM/Pincode'))->caption('PIN Code')->mandatory('Pin code is required');

			$this->hasOne('xMLM/State','state_id')->group('a~4')->mandatory("State is required")->display(array('form'=>'DropDownNormal'));
			$this->hasOne('xMLM/District','district_id')->group('a~4')->mandatory("City/District is required")->display(array('form'=>'DropDownNormal'))->caption('City/District');

		$customer_j->addField('is_active')->type('boolean')->defaultValue(true);


		$user_j->addField('type')->setValueList(array(100=>'SuperUser',80=>'BackEndUser',50=>'FrontEndUser'))->defaultValue(50)->group('a~6')->sortable(true)->mandatory(false);
		$this->addCondition('type',50);

		// Other technical fields for MLM purpose here
		$this->hasOne('xMLM/Kit','kit_item_id')->defaultValue(null)->caption('Startup Package');
		$this->addField('capping')->type('int')->system(true);

		$this->hasOne('xMLM/Left','left_id')->defaultValue(null);
		$this->hasOne('xMLM/Right','right_id')->defaultValue(null);

		// $this->addField('re_password')->type('password')->group('b~4')->mandatory(true);
		$this->addField('last_password_change')->type('datetime')->system(true)->defaultValue(null);

		$this->hasOne('xMLM/Bank','bank_id')->group('e~6~Bank Info')->mandatory("Bank is required");//->system(true);
		$this->addField('account_no')->group('e~6~bl')->mandatory("Account no is required")->display(array('form'=>'xMLM/Number'))->caption('Account no');
		
		$this->addField('IFCS_Code')->group('e~6')->mandatory("IFSC Code is required")->display(array('form'=>'AlphaNumeric'))->caption('IFSC Code');
		$this->addField('branch_name')->caption('Branch')->group('e~6~bl')->mandatory("Branch name is required")->display(array('form'=>'Alpha'));//->system(true);
		$this->addField('kyc_no')->group('kyc~4~Kyc Info')->mandatory("KYC no is required")->caption('KYC no.');
		$this->add('filestore/Field_Image','kyc_id')->group('kyc~4')->caption('KYC form');
		$this->add('filestore/Field_Image','address_proof_id')->group('kyc~4')->caption('Address proof');
		$this->addField('nominee_name')->group('f~6~Nominee Details')->mandatory("Nominee name is required")->display(array('form'=>'Alpha'))->caption('Nominee name');
		$this->addField('relation_with_nominee')->enum(explode(",", $config['relations_with_nominee']))->group('f~2')->mandatory("Relation with nominee is required")->caption('Relation with Nominee');//->system(true);
		$this->addField('nominee_email')->group('f~2')->caption('Nominee email')->display(array('form'=>'Email'));//->system(true);
		$this->addField('nominee_age')->group('f~2')->mandatory("Nominee age is required")->display(array('form'=>'xMLM/Range'))->caption("Nominee age");

		$this->addField('Leg')->setValueList(array('A'=>'Left','B'=>'Right'))->mandatory("Leg is required");
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

		$this->addField('carried_amount')->type('money')->defaultValue(0)->caption('Carried amount');
		$this->addField('credit_purchase_points')->type('money')->defaultValue(0);
		$this->addField('temp')->system(true)->defaultValue(0);


		$this->addField('greened_on')->type('datetime')->defaultValue(null)->caption('Qualified date');
		$this->addField('ansestors_updated')->type('boolean')->defaultValue(false)->system(true);

		$this->hasMany('xMLM/Sponsor','sponsor_id',null,'SponsoredDistributors');
		$this->hasMany('xMLM/Introducer','introducer_id',null,'IntroducedDistributors');
		$this->hasMany('xMLM/CreditMovement','distributor_id');
		$this->hasMany('xMLM/Booking','distributor_id');
		$this->hasMany('xMLM/RepurchaseEntry','distributor_id');

		
		$this->addHook('beforeSave',array($this,'beforeSaveDistributor'));
		$this->addHook('afterSave',array($this,'afterSaveDistributor'));
		$this->addHook('beforeDelete',array($this,'beforeDeleteDistributor'));
		$this->addHook('afterInsert',$this);

		$this->add('Controller_Validator');
		$this->is(array(
							'username|to_trim|unique',
							'password|to_trim',
							// 're_password|to_trim',
						)
				);

		$this->setOrder('greened_on','desc');
		$this->getElement('created_at')->caption('Joining date');
		// $this->api->auth->addEncryptionHook($this);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function afterInsert(){
		$m_no=$this['mobile_number'];
		$message= 'Thank you for joining Nebula! We have send your login Credentials on your registered email ID. Please Call us '\n' on 18004192299 for any assistance. Happy Networking!
    				'\n'Best Regards,
    				'\n'
    				'\n'
    				Nebula Team - Reach Beyond';
		
		$this->add('Controller_Sms')->sendMessage($m_no,$message);
	}

	function beforeSaveDistributor(){

		// if( trim($this['password']) !== trim($this['re_password']))
		// 	throw $this->exception('Passwords Must Match','ValidityCheck')->setField('re_password');

		if($this['pan_no'] and strtolower($this['pan_no'][4]) != strtolower($this['last_name'][0]) and strlen($this['pan_no']) !=10){
			throw $this->exception('Pan No Does not looks correct','ValidityCheck')->setField('pan_no');
		}

		$mobile_number = $this->get('mobile_number');

		$diff = $this->api->my_date_diff($this->api->today,$this['date_of_birth']);
		if($diff['years']<18)
			throw $this->exception('Applicant must be above 18','ValidityCheck')->setField('date_of_birth_ALERTED');		

		if(strlen($this['username']) < 3)
			throw $this->exception('Username must be more than 3 characters long '. $this['id']);//,'ValidityCheck')->setField('username');

		// throw new \Exception("Error Processing Request", 1);
		

		// Check KYC No 
		$alloted_kyc_check = $this->add('xMLM/Model_FormAllot');
		$alloted_kyc_check->addCondition('from_no','<=',$this['kyc_no']);
		$alloted_kyc_check->addCondition('to_no','>=',$this['kyc_no']);
		$alloted_kyc_check->tryLoadANy();

		if($this['kyc_no'] and !$alloted_kyc_check->loaded() and !isset($this->forceDelete))
			throw $this->exception('Form is not alloted','ValidityCheck')->setField('kyc_no');

		// Check Used KYC No
		$used_kyc_check = $this->add('xMLM/Model_Distributor');
		$used_kyc_check->addCondition('kyc_no',$this['kyc_no']);
		if($this->loaded())
			$used_kyc_check->addCondition('id','<>',$this->id);

		$used_kyc_check->tryLoadAny();

		if($this['kyc_no'] and $used_kyc_check->loaded() )
			throw $this->exception('Someone already has that KYC no. Try another','ValidityCheck')->setField('kyc_no');
		

		$this['name'] = $this['first_name'].' '. $this['last_name'];
		// $this['address'] = "Block No ". $this['block_no'] .", Building No ". $this['building_no']. ", ". $this['landmark'] . ', PIN-'. $this['pin_code'];

		// Check For available purchase points
		if($this->dirty['kit_item_id'] AND $this['kit_item_id'] !=""){
			$kit=$this->kit();
			if($kit AND !$this->validateKitPurchasePoints($this->kit())){
				throw $this->exception($this->id.' :: You do not have sufficient credits','Growl');
			}
			
			$this['status']='paid';
			$this['greened_on']=$this->api->now;
			$this['is_active']=true;
			$this['capping']=$kit->getCapping();
			
			/*When Distributor ID is Orange Send Multiple Mail In Array */
		
			$tm=$this->add( 'TMail_Transport_PHPMailer' );
			// $msg=$this->add( 'GiTemplate' );
			// $msg->loadTemplate( 'mail/registerdistributerwhenidorange');
			$cc = array();
			$emails = $this->add('xMLM/Model_Configuration')->tryLoadANy()->get('when_id_becomes_orange');
			$emails=explode(",", $emails);
			$email = $emails[0];
			// unset($emails[0]);
			$emails = array_values($emails);
			// throw new \Exception("coming", 1);
			$cc = $emails;

			if(!$email) return; //throw new \Exception("not found ".$email, 1);
			
			$config_model=$this->add('xMLM/Model_Configuration');
			$config_model->tryLoadAny();

			$subject= $config_model['orange_email_subject']." ".$this['name'];
			if($config_model['orange_email_matter']){
				// $distributer_mail=$this['email'];
				$email_body=$config_model['orange_email_matter']?:"Orange Distributor Send Mail Layout Is Empty";
		
				//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
				$email_body = str_replace("{{sponsor_name}}", $this['sponsor'], $email_body);
				$email_body = str_replace("{{introducer_name}}", $this['introducer'], $email_body);
				$email_body = str_replace("{{name}}", $this['name'], $email_body);
				$email_body = str_replace("{{first_name}}", $this['first_name'], $email_body);
				$email_body = str_replace("{{last_name}}", $this['last_name'], $email_body);
				$email_body = str_replace("{{Username}}", $this['username'], $email_body);
				$email_body = str_replace("{{password}}", $this['password'], $email_body);
				$email_body = str_replace("{{mobile_number}}", $this['mobile_number']?$this['mobile_number']:" ", $email_body);
				$email_body = str_replace("{{email}}", $this['email']?$this['email']:" ", $email_body);
				$email_body = str_replace("{{date_of_birth}}", $this['date_of_birth']?$this['date_of_birth']:" ", $email_body);
				$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
				$email_body = str_replace("{{state}}", $this['state']?$this['state']:" ", $email_body);
				$email_body = str_replace("{{district}}", $this['district']?$this['district']:" ", $email_body);
				$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
				$email_body = str_replace("{{pan_no}}", $this['pan_no']?$this['pan_no']:" ", $email_body);
				$email_body = str_replace("{{pin_code}}", $this['pin_code']?$this['pin_code']:" ", $email_body);
				$email_body = str_replace("{{user_type}}", $this['type']?$this['type']:" ", $email_body);
				$email_body = str_replace("{{bank}}", $this['bank']?$this['bank']:" ", $email_body);
				$email_body = str_replace("{{account_no}}", $this['account_no']?$this['account_no']:" ", $email_body);
				$email_body = str_replace("{{IFCS_Code}}", $this['IFCS_Code']?$this['IFCS_Code']:" ", $email_body);
				$email_body = str_replace("{{branch_name}}", $this['branch_name']?$this['branch_name']:" ", $email_body);
				$email_body = str_replace("{{kyc_no}}", $this['kyc_no']?$this['kyc_no']:" ", $email_body);
				$email_body = str_replace("{{nominee_name}}", $this['nominee_name']?$this['nominee_name']:" ", $email_body);
				$email_body = str_replace("{{relations_with_nominee}}", $this['relations_with_nominee']?$this['relations_with_nominee']:" ", $email_body);
				$email_body = str_replace("{{nominee_age}}", $this['nominee_age']?$this['nominee_age']:" ", $email_body);
				$email_body = str_replace("{{nominee_email}}", $this['nominee_email']?$this['nominee_email']:" ", $email_body);
				$email_body = str_replace("{{kit}}", $this['kit_item']?$this['kit_item']:" ", $email_body);
				$email_body = str_replace("{{leg}}", $this['Leg']?$this['Leg']:" ", $email_body);

				// return $email_body;
			}	

			if(!$email) return;
			// throw new \Exception($email_body, 1);
			try{
				$tm->send($email,$email,$subject,$email_body,null,$cc);
				// $tm->send($email, $emails,$subject, $email_body);
			}catch( \phpmailerException $e ) {
				$this->js(true)->univ()->errorMessage($e->getMessage());
			}catch( \Exception $e ) {
				throw $e;
			}
			// ======== 11 days free look period =========== in cron now
			// if($this->loaded()){
				// $this->updateAnsestors($kit->getPV(),$kit->getBV());
				// $introducer = $this->introducer();
				// $introducer->addSessionIntro($kit->getIntro());
			// }
			// if(!$this->loaded()) throw $this->exception('Model Must Be Loaded Before Email Send');
			
			/*###########################################################*/

			/*When Distributor ID is Orange Send Multiple Mail In Array */


			$tmail=$this->add( 'TMail_Transport_PHPMailer' );
			$config_model=$this->add('xMLM/Model_Configuration');
			$config_model->tryLoadAny();

			$subject= $config_model['orange_distributor_email_subject']." ".$this['name'];
			$distributer_mail=$this['email'];
			// throw new \Exception($distributer_mail, 1);
			if($config_model['orange_distributor_mail_matter']){
				$email_body = $config_model['orange_distributor_mail_matter']?:"Orange Distributor Send Mail Layout Is Empty";

				//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
				$email_body = str_replace("{{sponsor_name}}", $this['sponsor'], $email_body);
				$email_body = str_replace("{{introducer_name}}", $this['introducer'], $email_body);
				$email_body = str_replace("{{name}}", $this['name'], $email_body);
				$email_body = str_replace("{{first_name}}", $this['first_name'], $email_body);
				$email_body = str_replace("{{last_name}}", $this['last_name'], $email_body);
				$email_body = str_replace("{{Username}}", $this['username'], $email_body);
				$email_body = str_replace("{{password}}", $this['password'], $email_body);
				$email_body = str_replace("{{mobile_number}}", $this['mobile_number']?$this['mobile_number']:" ", $email_body);
				$email_body = str_replace("{{email}}", $this['email']?$this['email']:" ", $email_body);
				$email_body = str_replace("{{date_of_birth}}", $this['date_of_birth']?$this['date_of_birth']:" ", $email_body);
				$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
				$email_body = str_replace("{{state}}", $this['state']?$this['state']:" ", $email_body);
				$email_body = str_replace("{{district}}", $this['district']?$this['district']:" ", $email_body);
				$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
				// $email_body = str_replace("{{users_id}}", $this['users_id']?$this['user_id']:" ", $email_body);
				$email_body = str_replace("{{pan_no}}", $this['pan_no']?$this['pan_no']:" ", $email_body);
				$email_body = str_replace("{{pin_code}}", $this['pin_code']?$this['pin_code']:" ", $email_body);
				$email_body = str_replace("{{user_type}}", $this['type']?$this['type']:" ", $email_body);
				$email_body = str_replace("{{bank}}", $this['bank']?$this['bank']:" ", $email_body);
				$email_body = str_replace("{{account_no}}", $this['account_no']?$this['account_no']:" ", $email_body);
				$email_body = str_replace("{{IFCS_Code}}", $this['IFCS_Code']?$this['IFCS_Code']:" ", $email_body);
				$email_body = str_replace("{{branch_name}}", $this['branch_name']?$this['branch_name']:" ", $email_body);
				$email_body = str_replace("{{kyc_no}}", $this['kyc_no']?$this['kyc_no']:" ", $email_body);
				$email_body = str_replace("{{nominee_name}}", $this['nominee_name']?$this['nominee_name']:" ", $email_body);
				$email_body = str_replace("{{relations_with_nominee}}", $this['relations_with_nominee']?$this['relations_with_nominee']:" ", $email_body);
				$email_body = str_replace("{{nominee_age}}", $this['nominee_age']?$this['nominee_age']:" ", $email_body);
				$email_body = str_replace("{{nominee_email}}", $this['nominee_email']?$this['nominee_email']:" ", $email_body);
				$email_body = str_replace("{{kit}}", $this['kit_item']?$this['kit_item']:" ", $email_body);
				$email_body = str_replace("{{leg}}", $this['Leg']?$this['Leg']:" ", $email_body);

			}
				// throw new \Exception($distributer_mail, 1);
				try{
					$tmail->send($distributer_mail,$email,$subject,$email_body);
				// 	// $tm->send($email, $emails,$subject, $email_body);
					
				}catch( \phpmailerException $e ) {
					$this->js(true)->univ()->errorMessage($e->getMessage());
				}catch( \Exception $e ) {
				 	throw $e;
				}
				
		}

		if(!$this->loaded()){
			// Its New Entry
			$dist= $this->add('xMLM/Model_Distributor')->loadLoggedIn();
			if(!($dist OR $this->api->auth->model->isDefaultSuperUser())){
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
			$sponsor->saveAndUnload();
			// if($this['greened_on']){
			// 	$kit=$this->kit();
			// 	$this->updateAnsestors($kit->getPV(),$kit->getBV());
			// 	$introducer = $this->introducer();
			// 	$introducer->addSessionIntro($kit->getIntro());
			// }
			$this->welcomeDistributor();
			$this->forget('leg');

			$this->api->db->dsql()->table('xshop_memberdetails')->where('id',$this['customer_id'])->set('users_id',$this['user_id'])->update();
		}
	}

	function beforeDeleteDistributor(){
		if($this['greened_on'] OR $this['left_id'] OR $this['right_id'])
			throw $this->exception('Cannot Delete','Growl');
	}

	function forceDelete(){
		if(!$this->loaded())
			throw $this->exception('Unknown Distributor to delete');

		if(!isset($this->api->deleted_distributor)) $this->api->deleted_distributor =array();
		if(in_array($this->id, $this->api->deleted_distributor)) return;

		$this->forceDelete = true;

		$this->ref('xMLM/Booking','model')->each(function($obj){
			$obj->delete();
		});

		$this->add('xMLM/Model_CreditMovement')->addCondition('joined_distributor_id',$this->id)
			->each(function($obj){
				$obj->forceDelete = true;
				$obj->forceDelete();
			});

		$this->add('xMLM/Model_RepurchaseEntry')->addCondition('distributor_id',$this->id)
			->each(function($obj){
				$obj->forceDelete = true;
				$obj->forceDelete();
			});

		$this->add('xMLM/Model_FormAllot')
			->addCondition('distributor_id',$this->id)
			->deleteAll();


		$this->creditMovements()->each(function ($obj){
			$obj->forceDelete();
		});
		
		// Main kisi ki sponsor id main to hun .. usme se mujhe hatao ...
		$i_m_in_left = $this->add('xMLM/Model_Distributor')->tryLoadBy('left_id',$this->id);
		if($i_m_in_left->loaded()){
			$i_m_in_left['left_id']=null;
			$i_m_in_left->forceDelete = true;
			$i_m_in_left->saveAndUnload();
		}

		$i_m_in_right = $this->add('xMLM/Model_Distributor')->tryLoadBy('right_id',$this->id);
		if($i_m_in_right->loaded()){
			$i_m_in_right['right_id']=null;
			$i_m_in_right->forceDelete=true;
			$i_m_in_right->saveAndUnload();
		}

		// I am someones Introducer
		$i_am_intro =  $this->add('xMLM/Model_Distributor')->addCondition('introducer_id',$this->id);
		foreach ($i_am_intro as $intros) {
			$intros['introducer_id']=null;
			$intros->forceDelete = true;
			$intros->saveAndUnload();
		}

		// I am someones Sponsor as well
		$i_am_spn =  $this->add('xMLM/Model_Distributor')->addCondition('sponsor_id',$this->id);
		foreach ($i_am_spn as $spn) {
			$spn['sponsor_id']=null;
			$spn->forceDelete = true;
			$spn->saveAndUnload();
		}

		
		$lid= $this['left_id'];
		$rid = $this['right_id'];

		$this['sponsor_id']=null;
		$this['introducer_id']=null;
		$this['left_id']=null;
		$this['right_id'] = null;
		$this['greened_on']=null;
		try{
			if($this->loaded()) 
				$dist = $this->saveAs('xMLM/Model_Distributor');
			else
				return;
		}catch(\Exception $e){
			// echo $this->id;
			throw $e;
		}
			
		$dist->add('xMLM/Model_Distributor')->addCondition('path','like',$this['path'].'A%')
				->each(function($obj){
					$obj->forceDelete = true;
					$obj->forceDelete();
				});
		$dist->add('xMLM/Model_Distributor')->addCondition('path','like',$this['path'].'B%')
				->each(function($obj){
					$obj->forceDelete = true;
					$obj->forceDelete();
				});

		// if($this['sponsor_id'])	$this->newInstance()->tryLoad($this['sponsor_id'])->forceDelete();
		// if($this['introducer_id']) $this->newInstance()->tryLoad($this['introducer_id'])->forceDelete();


		$this->add('xShop/Model_Customer')->load($dist['customer_id'])->forceDelete();
		$dist->delete();
		$dist->api->deleted_distributor[]  = $dist->id;
	}

	function parseEmailBody(){

		$distributer_mail=$this['email'];

		$config_model=$this->add('xMLM/Model_Configuration');
		$config_model->tryLoadAny();
				
		$email_body=$config_model['welcome_email_matter']?:"Distributor Send Mail Layout Is Empty";
		
		//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
		$email_body = str_replace("{{sponsor_name}}", $this['sponsor'], $email_body);
		$email_body = str_replace("{{introducer_name}}", $this['introducer'], $email_body);
		$email_body = str_replace("{{name}}", $this['name'], $email_body);
		$email_body = str_replace("{{first_name}}", $this['first_name'], $email_body);
		$email_body = str_replace("{{last_name}}", $this['last_name'], $email_body);
		$email_body = str_replace("{{Username}}", $this['username'], $email_body);
		$email_body = str_replace("{{password}}", $this['password'], $email_body);
		$email_body = str_replace("{{mobile_number}}", $this['mobile_number']?$this['mobile_number']:" ", $email_body);
		$email_body = str_replace("{{email}}", $this['email']?$this['email']:" ", $email_body);
		$email_body = str_replace("{{date_of_birth}}", $this['date_of_birth']?$this['date_of_birth']:" ", $email_body);
		$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
		$email_body = str_replace("{{state}}", $this['state']?$this['state']:" ", $email_body);
		$email_body = str_replace("{{district}}", $this['district']?$this['district']:" ", $email_body);
		$email_body = str_replace("{{address}}", $this['address']?$this['address']:" ", $email_body);
		// $email_body = str_replace("{{users_id}}", $this['users_id']?$this['user_id']:" ", $email_body);
		$email_body = str_replace("{{pan_no}}", $this['pan_no']?$this['pan_no']:" ", $email_body);
		$email_body = str_replace("{{pin_code}}", $this['pin_code']?$this['pin_code']:" ", $email_body);
		$email_body = str_replace("{{user_type}}", $this['type']?$this['type']:" ", $email_body);
		$email_body = str_replace("{{bank}}", $this['bank']?$this['bank']:" ", $email_body);
		$email_body = str_replace("{{account_no}}", $this['account_no']?$this['account_no']:" ", $email_body);
		$email_body = str_replace("{{IFCS_Code}}", $this['IFCS_Code']?$this['IFCS_Code']:" ", $email_body);
		$email_body = str_replace("{{branch_name}}", $this['branch_name']?$this['branch_name']:" ", $email_body);
		$email_body = str_replace("{{kyc_no}}", $this['kyc_no']?$this['kyc_no']:" ", $email_body);
		$email_body = str_replace("{{nominee_name}}", $this['nominee_name']?$this['nominee_name']:" ", $email_body);
		$email_body = str_replace("{{relations_with_nominee}}", $this['relations_with_nominee']?$this['relations_with_nominee']:" ", $email_body);
		$email_body = str_replace("{{nominee_age}}", $this['nominee_age']?$this['nominee_age']:" ", $email_body);
		$email_body = str_replace("{{nominee_email}}", $this['nominee_email']?$this['nominee_email']:" ", $email_body);
		$email_body = str_replace("{{kit}}", $this['kit_item']?$this['kit_item']:" ", $email_body);
		$email_body = str_replace("{{leg}}", $this['Leg']?$this['Leg']:" ", $email_body);

		return $email_body;
	}


	function welcomeDistributor(){
		if(!$this->loaded()) throw $this->exception('Model Must Be Loaded Before Email Send');
		
		$config_model=$this->add('xMLM/Model_Configuration');
		$config_model->tryLoadAny();

		if($config_model['welcome_email_matter']){
			$distributer_mail=$this['email'];
			$subject= $config_model['welcome_email_subject']." ".$this['name'];
			$email_body = $this->parseEmailBody();

			$cc = array();
			$emails = $config_model['distributor_join_emails'];
			$emails=explode(",", $emails);
			$email = $emails[0];
			// unset($emails[0]);
			$emails = array_values($emails);
			$cc = $emails;

			// throw new \Exception(print_r($cc,true), 1);
			

			$this->sendEmail($distributer_mail,$subject,$email_body,$cc,null);
		}		
	}

	function creditMovements(){
		return $this->add('xMLM/Model_CreditMovement')->addCondition('distributor_id',$this->id);
	}

	function consumePurchasePoints($points,$narration, $joined_distributor){
		$this['credit_purchase_points'] = $this['credit_purchase_points'] - $points;
		$this->save();
		$credit_movement = $this->creditMovements();
		$credit_movement['credits'] = $points;
		$credit_movement['narration'] = $narration;
		$credit_movement['status'] = 'Consumed';
		$credit_movement['joined_distributor_id'] = $joined_distributor->id;
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
			$from_distributor->consumePurchasePoints($kitpoints,"Joining of ".$this['username'], $this);
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
		$logged_in_distributor->consumePurchasePoints($kitpoints,"Joining of ".$this['username']."",$this);
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

		$in_down = strpos($down_path, $my_path) !== false;
		return $in_down;
	}

	function loadRoot(){
		return $this->loadBy('path','0');	
	}

	function nitifyViaEmail($subject, $email_body){
		$email = $this['email'];
		if(!$email) return;
		$tm=$this->add( 'TMail_Transport_PHPMailer' );	
		try{
			$tm->send($email, $email,$subject, $email_body);
		}catch( \phpmailerException $e ) {
			$this->js(true)->univ()->errorMessage($e->getMessage());
		}catch( \Exception $e ) {
			throw $e;
		}
	}

}
