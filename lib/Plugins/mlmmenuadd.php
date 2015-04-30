<?php

namespace xMLM;


class Plugins_mlmmenuadd extends \componentBase\Plugin {

	function init(){
		parent::init();
		
		$this->addHook('menu-created',array($this,'Plugins_mlmmenuadd'));
	}

	function Plugins_mlmmenuadd($obj, $top_menu){
		$admin_m = $top_menu->addMenu('MLM');
        $admin_m->addItem(array('Dashboard','icon'=>'gauge-1'),'xMLM_page_owner_dashboard');
        $admin_m->addItem(array('Joining Kits','icon'=>'gauge-1'),'xMLM_page_owner_kits');
        $admin_m->addItem(array('Distributors','icon'=>'gauge-1'),'xMLM_page_owner_distributors');
        $admin_m->addItem(array('Payouts','icon'=>'gauge-1'),'xMLM_page_owner_payouts');
        $admin_m->addItem(array('Credits Management','icon'=>'gauge-1'),'xMLM_page_owner_credits');
        $admin_m->addItem(array('Reports','icon'=>'gauge-1'),'xMLM_page_owner_reports');
        $admin_m->addItem(array('Configuration','icon'=>'gauge-1'),'xMLM_page_owner_configuration');
        
	}
}
