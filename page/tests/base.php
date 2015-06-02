<?php

class page_xMLM_page_tests_base extends Page_Tester{

	function init(){
		ini_set('memory_limit', '2048M');
    	// set_time_limit(60);
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

		$dist['first_name'] = $dist['username'] = $username?:strtolower($sponsor['path'] . $leg);
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
				$rd->ref('left_id')->forceDelete();
			}catch(\Exception $e){
				// echo $rd['left_id'];
				throw $e;
			}
		}
		if($rd['right_id']){
			$rd->ref('right_id')->forceDelete();
		}

		if($rd->loaded()) $rd->forceDelete();

		// remove all users
		$this->add('Model_Users')
			->addCondition('username','<>',array('admin','root'))
			->each(function($obj){$obj->forceDelete();});

			

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
		$gold_kit = $this->add('xMLM/Model_Kit')->addCondition('name','like','%Gold%')->loadAny();

        $root_dist = $this->add('xMLM/Model_Distributor');
        $root_dist->addCondition('path','0');
        $root_dist->tryLoadAny();
        if(!$root_dist->loaded()){
            // echo "Saving root";
            
            // Admin has rights to entry without having credits ... 

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
            $root_dist->save();
            $root_dist = $this->add('xMLM/Model_Distributor')->loadRoot();
            $this->api->db->dsql()->table('xshop_memberdetails')->where('id',$root_dist['customer_id'])->set('users_id',$root_dist['user_id'])->update();

        }else{
            // echo "Not Saving root";
        }
        return $root_dist;
	}

	function joining($username){
		return $this->add('xMLM/Model_Distributor')->loadBy('username',$username);
	}
}