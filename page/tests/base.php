<?php

class page_xMLM_page_tests_base extends Page_Tester{
	
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

		$dist['name'] = $dist['username'] = $username?:strtolower($sponsor['path'] . $leg);
		$dist['password'] = $password;
		$dist['re_password'] = $password;
		$dist['email'] = $dist['username'].'@nebulavcations.com';
		$dist->save();
		return $dist;
	}

	function resetDB(){
		$rd = $root_distributor = $this->add('xMLM/Model_Distributor')->loadRoot();
		// remove all users
		$this->api->db->dsql()->table('users')->where('username','not in',array('admin','root'))->delete()->execute();
		$this->api->db->dsql()->table('xshop_memberdetails')->where($this->api->db->dsql()->orExpr()->where('users_id','<>',$root_distributor['user_id'])->where('users_id',null))->delete()->execute();
		$this->api->db->dsql()->table('xmlm_distributors')->where('id','<>',$root_distributor->id)->delete()->execute();
		$this->api->db->dsql()->table('xmlm_payouts')->delete()->execute();

		// reset root distributor
		$rd['left_id'] = $rd['right_id'] = 0;
		$rd['session_left_pv'] = $rd['session_right_pv'] = $rd['session_left_bv'] = $rd['session_right_bv']= 0;
		$rd['total_left_pv'] = $rd['total_right_pv'] = $rd['total_left_bv'] = $rd['total_right_bv']= 0;
		$rd['session_intros_amount'] = $rd['total_intros_amount'] = $rd['session_self_bv'] = $rd['total_pairs'] = $rd['carried_amount'] = 0;
		$rd['credit_purchase_points'] = 100000;
		$rd->save();

		$this->add('xMLM/Model_CreditMovement')->deleteAll();

	}

	function joining($username){
		return $this->add('xMLM/Model_Distributor')->loadBy('username',$username);
	}
}