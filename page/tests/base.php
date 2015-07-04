<?php

class page_xMLM_page_tests_base extends Page_Tester{

	function init(){
		ini_set('memory_limit', '2048M');
    	set_time_limit(0);
		parent::init();
	}
	
	function newJoining($sponsor, $leg, $introducer, $kit=null, $username=null,$password='123',$pan=''){
		$dist = $this->add('xMLM/Model_Distributor');
		$dist['sponsor_id'] = $sponsor->id;
		$dist['Leg']=$leg; // A/B
		$dist['introducer_id'] = $introducer->id;

		if(!$kit){
			$kit= $this->add('xMLM/Model_Kit')->loadBy('name','Standard Kit');
		}

		if($kit !=='free')
			$dist['kit_item_id'] = $kit->id;

		$dist['first_name'] = $dist['username'] = $username?:'___'.strtolower($sponsor['path'] . $leg);
		$dist['password'] = $password;
		$dist['re_password'] = $password;
		$dist['email'] = $dist['username'].'@nebulavcations.com';
		$dist->save();
		return $dist;
	}

	function resetDB(){
		$rd = $root_distributor = $this->add('xMLM/Model_Distributor')
			->addCondition('path','0')
			->tryLoadAny();

		$this->api->db->dsql()->table('xmlm_payouts')->truncate()->execute();

		if($rd['left_id']){
			try{
				$lid = $rd->ref('left_id');
				$lid->forceDelete = true;
				$lid->forceDelete();
			}catch(\Exception $e){
				// echo $rd['left_id'];
				throw $e;
			}
		}
		if($rd['right_id']){
			$rid= $rd->ref('right_id');
			$rid->forceDelete=true;
			$rid->forceDelete();
		}

		if($rd->loaded()){
			$rd->forceDelete = true;
			$rd->forceDelete();	
		} 

		// remove all users
		$this->add('Model_Users')
			->addCondition('username','<>',array('admin','root'))
			->each(function($obj){
				$obj->forceDelete = true;
				$obj->forceDelete();
			});

			

		// // reset root distributor
		// $rd['left_id'] = $rd['right_id'] = 0;
		// $rd['session_left_pv'] = $rd['session_right_pv'] = $rd['session_left_bv'] = $rd['session_right_bv']= 0;
		// $rd['total_left_pv'] = $rd['total_right_pv'] = $rd['total_left_bv'] = $rd['total_right_bv']= 0;
		// $rd['session_intros_amount'] = $rd['total_intros_amount'] = $rd['session_self_bv'] = $rd['total_pairs'] = $rd['carried_amount'] = 0;
		// $rd['credit_purchase_points'] = 100000;
		// $rd->save();

		$this->add('xMLM/Model_CreditMovement')->deleteAll();

	}

	function setUpRootDistributor(){
		$gold_kit = $this->add('xMLM/Model_Kit')->addCondition('name','like','%Deluxe%')->loadAny();

        $root_dist = $this->add('xMLM/Model_Distributor');
        $root_dist->addCondition('path','0');
        $root_dist->tryLoadAny();
        if(!$root_dist->loaded()){
            // echo "Saving root";
            
            // Admin has rights to entry without having credits ... 
        	$form_allot = $this->add('xMLM/Model_FormAllot')->set(array('from_no'=>1,'to_no'=>1))->save();

            $this->api->auth->login($this->add('Model_Users')->getDefaultSuperUser()->get('username'));
            $root_dist['first_name']="Root";
            $root_dist['last_name']="Distributor";
            $root_dist['path']='0';
            $root_dist['email']='info@nebulavacations.com';
            $root_dist['username']='root';
            $root_dist['password']='root';
            $root_dist['re_password']='root';
            $root_dist['kit_item_id']=$gold_kit->id;
            $root_dist['is_active']=true;
            $root_dist['credit_purchase_points']=100000;
            $root_dist['kyc_no']=1;
            $root_dist->save();
            $root_dist = $this->add('xMLM/Model_Distributor')->loadRoot();
            $this->api->db->dsql()->table('xshop_memberdetails')->where('id',$root_dist['customer_id'])->set('users_id',$root_dist['user_id'])->update();

            $form_allot['distributor_id'] = $root_dist->id;
            $form_allot->save();

        }else{
            // echo "Not Saving root";
        }
        return $root_dist;
	}

	function joining($username){
		return $this->add('xMLM/Model_Distributor')->loadBy('username',$username);
	}
}