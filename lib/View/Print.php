<?php

namespace xMLM;

class View_Print extends \View {

	function init(){
		parent::init();

		$link = $this->add('View')->setElement('link');
		$link->setAttr('rel',"stylesheet");
		$link->setAttr('type',"text/css");
		$link->setAttr('href',"epan-components/xMLM/templates/css/xMLM-menu.css");
		$link->setAttr('media','print');
	}

	function setModel($payout_model){
		$view = $this->add('View');
		$distributor_model = $this->add('xMLM/Model_Distributor')->load($payout_model['distributor_id']);
		
		$parse_print_body = $this->add('xMLM/Model_Configuration')->tryLoadAny()->get('payout_print_format');
		
		//Set Distributor Info
		$parse_print_body = str_replace("{{distributor_id}}", $distributor_model['id'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_name}}", $distributor_model['name'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_address}}", $distributor_model['address'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_city}}", $distributor_model['city'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_state}}", $distributor_model['state'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_pin_code}}", $distributor_model['pin_code'], $parse_print_body);
		$parse_print_body = str_replace("{{distributor_mobile_number}}", $distributor_model['mobile_number'], $parse_print_body);
		
		//Payout Information
		$parse_print_body = str_replace("{{payout_date}}", date_format(date_create($payout_model['on_date']), 'Y-m-d'), $parse_print_body);
		$parse_print_body = str_replace("{{pairs}}", $payout_model['pairs'], $parse_print_body);
		$parse_print_body = str_replace("{{pair_income}}", $payout_model['pair_income'], $parse_print_body);
		$parse_print_body = str_replace("{{tds}}", $payout_model['tds'], $parse_print_body);
		$parse_print_body = str_replace("{{admin_charge}}", $payout_model['admin_charge'], $parse_print_body);
		$parse_print_body = str_replace("{{net_amount}}", $payout_model['net_amount'], $parse_print_body);
		$parse_print_body = str_replace("{{bonus}}", $payout_model['bonus'], $parse_print_body);
		$parse_print_body = str_replace("{{carried_amount}}", $payout_model['carried_amount'], $parse_print_body);
		$parse_print_body = str_replace("{{introduction_income}}", $payout_model['introduction_income'], $parse_print_body);
		$parse_print_body = str_replace("{{previous_carried_amount}}", $payout_model['previous_carried_amount'], $parse_print_body);
							
		$view->setHtml($parse_print_body);

		parent::setModel($payout_model);
	}
}