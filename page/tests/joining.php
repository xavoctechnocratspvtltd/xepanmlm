<?php

class page_xMLM_page_tests_joining extends page_xMLM_page_tests_base {
    public $title = 'Joining Testing';

    public $proper_responses=array(
        "Test_empty"=>'',
        "Test_configTest"=>array('admin_charge'=>10,'other_charge'=>null,'tail_pv'=>500,'minimum_payout_amount'=>500,'include_generation'=>0),
        'Test_rootCheck'=>array('root_id'=>7,'root_user_id'=>38,'session_left_pv'=>0,'session_right_pv'=>0,'session_left_bv'=>0,'session_right_bv'=>0, 'session_self_bv'=>0, 'total_left_pv'=>0,'total_right_pv'=>0,'carried_amount'=>0,'credit_purchase_points'=>100000,'total_intros_amount'=>0),        
        'Test_fundAvailability'=>array(),
    );


    function prepare(){
        return null;
    }

    function test_configTest(){
        $config=$this->add('xMLM/Model_Configuration')->tryLoadAny();
        return array(
                'admin_charge'=>$config['admin_charge'],
                'other_charge'=>$config['other_charge'],
                'tail_pv'=>$config['tail_pv'],
                'minimum_payout_amount'=>$config['minimum_payout_amount'],
                'include_generation'=>$config['include_generation'],
            );
    }

    function prepare_rootCheck(){
        $this->resetDB();
        return array($this->add('xMLM/Model_Distributor')->loadRoot());
    }

    function test_rootCheck($root_dist){

        return array(
                'root_id'=>$root_dist->id,
                'root_user_id'=>$root_dist['user_id'],
                'session_left_pv' => $root_dist['session_left_pv'],
                'session_right_pv' => $root_dist['session_right_pv'],
                'session_left_bv' => $root_dist['session_left_bv'],
                'session_right_bv' => $root_dist['session_right_bv'],
                'session_self_bv' => $root_dist['session_self_bv'],
                'total_left_pv' => $root_dist['total_left_pv'],
                'total_right_pv' => $root_dist['total_right_pv'],
                'carried_amount' => round($root_dist['carried_amount'],0),
                'credit_purchase_points' => round($root_dist['credit_purchase_points'],0),
                'total_intros_amount' => round($root_dist['total_intros_amount'],0),
                );
    }

    function prepare_initialJoinings(){
        $this->proper_responses['Test_initialJoinings'] = array(
            'total_distributors'=>5,
            'root'=>array(
                'session_left_pv'=>1000,
                'session_right_pv'=>2500,
                'carried_amount'=>0,
                ),
            '0ab'=>array(
                'session_left_pv'=>0,
                'session_right_pv'=>0,
                'carried_amount'=>0,
                )
            );
    }

    function test_initialJoinings(){
        $root_dist = $this->add('xMLM/Model_Distributor')->loadRoot();
        $root_dist->login();

        $standard_kit = $this->add('xMLM/Model_Kit')->loadBy('name','Standard Kit');
        $gold_kit = $this->add('xMLM/Model_Kit')->loadBy('name','Gold');

        $j0a = $this->newJoining($root_dist, 'A', $root_dist, $standard_kit);
        $j0ab =$this->newJoining($j0a, 'B', $j0a, 'free');

        $j0b =$this->newJoining($root_dist, 'B', $root_dist, 'free');
        $j0ba =$this->newJoining($j0b, 'A', $j0b,$gold_kit);

        $j0ab['kit_item_id'] = $standard_kit->id;
        $j0ab->save();
        
        return array(
                'total_distributors'=>$this->add('xMLM/Model_Distributor')->count()->getOne(),
                'root'=>array(
                        'session_left_pv'=>$root_dist->reload()->get('session_left_pv'),
                        'session_right_pv'=>$root_dist->get('session_right_pv'),
                        'carried_amount'=>$root_dist->get('carried_amount')
                        ),
                '0ab'=>array(
                        'session_left_pv'=>$j0ab->reload()->get('session_left_pv'),
                        'session_right_pv'=>$j0ab->get('session_right_pv'),
                        'carried_amount'=>$j0ab->get('carried_amount')
                        )
            );
    }

 
    function prepare_firstClosing(){
        $payout = $this->add('xMLM/Model_Payout');
        $payout->generatePayout('2015-05-05',false);
        $this->proper_responses['Test_firstClosing'] = array(
                'root'=>array('session_left_pv'=>0,'session_right_pv'=>1500,'net_amount'=>840,'carried_amount'=>0),
                '0a'=>array('session_left_pv'=>0,'session_right_pv'=>500, 'net_amount'=>0,'carried_amount'=>200),
                '0b'=>array('session_left_pv'=>2500,'session_right_pv'=>0, 'net_amount'=>0,'carried_amount'=>1000),
                );
        return null;
    }

    function test_firstClosing(){
        $payout = $this->add('xMLM/Model_Payout');
        $pay =array();
        foreach ($payout as $p) {
            if($p['net_amount']>0 OR $p['carried_amount']>0){
                $d = $payout->distributor();
                $pay[$d['username']] = array('session_left_pv'=>$d['session_left_pv'],'session_right_pv'=>$d['session_right_pv'],'net_amount'=>(float)$p['net_amount'],'carried_amount'=>(float)$p['carried_amount']);
            }
        }
        return $pay;
    }

    function prepare_secondJoiningSet(){
        $this->proper_responses['Test_secondJoiningSet'] = array(
            'total_distributors'=>7,
            'root'=>array(
                'session_left_pv'=>2500,
                'session_right_pv'=>2000,
                'carried_amount'=>0,
                )
            );

        return null;
    }

    function test_secondJoiningSet(){
        $root_dist = $this->joining('root');
        
        $standard_kit = $this->add('xMLM/Model_Kit')->loadBy('name','Standard Kit');
        $gold_kit = $this->add('xMLM/Model_Kit')->loadBy('name','Gold');

        $j0b = $this->joining('0b');

        $j0abb =$this->newJoining($j0b, 'B', $j0b, 'free');
        $j0abb['kit_item_id'] = $standard_kit->id;
        $j0abb->save();

        $j0aa = $this->newJoining($this->joining('0a'),'A',$root_dist,$gold_kit);

        return array(
                'total_distributors'=>$this->add('xMLM/Model_Distributor')->count()->getOne(),
                'root'=>array(
                        'session_left_pv'=>$root_dist->reload()->get('session_left_pv'),
                        'session_right_pv'=>$root_dist->get('session_right_pv'),
                        'carried_amount'=>$root_dist->get('carried_amount')
                        )
            );
    }

    function prepare_secondClosing(){
        $payout = $this->add('xMLM/Model_Payout');
        $payout->generatePayout('2015-05-06',false);
        $this->proper_responses['Test_secondClosing'] = array(
                'root'=>array('session_left_pv'=>0,'session_right_pv'=>1500,'net_amount'=>840,'carried_amount'=>0),
                '0a'=>array('session_left_pv'=>0,'session_right_pv'=>500, 'net_amount'=>0,'carried_amount'=>200),
                '0b'=>array('session_left_pv'=>2500,'session_right_pv'=>0, 'net_amount'=>0,'carried_amount'=>1000),
                );
        return null;
    }

    function test_secondClosing(){
        $payout = $this->add('xMLM/Model_Payout');
        $pay =array();
        foreach ($payout as $p) {
            if($p['net_amount']>0 OR $p['carried_amount']>0){
                $d = $payout->distributor();
                $pay[$d['username']] = array('session_left_pv'=>$d['session_left_pv'],'session_right_pv'=>$d['session_right_pv'],'net_amount'=>(float)$p['net_amount'],'carried_amount'=>(float)$p['carried_amount']);
            }
        }
        return $pay;
    }

}