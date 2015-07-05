<?php

namespace xMLM;


class Plugins_mlmmenuadd extends \componentBase\Plugin {

	function init(){
		parent::init();
		
		$this->addHook('menu-created',array($this,'Plugins_mlmmenuadd'));
	}

	function Plugins_mlmmenuadd($obj, $top_menu){
                $dept_model = $this->add('xHR/Model_Department');

        	$admin_m = $top_menu->addMenu('Referral Program');
                $admin_m->addItem(array('Dashboard','icon'=>'gauge-1'),'xMLM_page_owner_dashboard');
                $admin_m->addItem(array('Joining Kits','icon'=>'gauge-1'),$this->api->url('xMLM_page_owner_kits',array('department_id'=>$dept_model->getCached(array('name'=>'Company'),'id'))));
                $admin_m->addItem(array('Distributors','icon'=>'gauge-1'),'xMLM_page_owner_distributors');
                $admin_m->addItem(array('Payouts','icon'=>'gauge-1'),$this->api->url('xMLM_page_owner_payouts',array('department_id'=>$dept_model->getCached(array('name'=>'Accounts'),'id'))));
                $admin_m->addItem(array('Credits Management','icon'=>'gauge-1'),$this->api->url('xMLM_page_owner_credits',array('department_id'=>$dept_model->getCached(array('name'=>'Accounts'),'id'))));
                // $admin_m->addItem(array('Reports','icon'=>'gauge-1'),'xMLM_page_owner_reports');
                $admin_m->addItem(array('Properties Management','icon'=>'gauge-1'),'xMLM_page_owner_properties');
                $admin_m->addItem(array('Booking Management','icon'=>'gauge-1'),'xMLM_page_owner_bookings');
                $admin_m->addItem(array('Forms Management','icon'=>'gauge-1'),'xMLM_page_owner_forms');
                $admin_m->addItem(array('Repurchase Management','icon'=>'gauge-1'),'xMLM_page_owner_repurchase');
                $admin_m->addItem(array('Configuration','icon'=>'gauge-1'),'xMLM_page_owner_configuration');
                $admin_m->addItem(array('Update FreeLook','icon'=>'gauge-1'),'xMLM_page_cron_freelookfinish');
        
	}
}
