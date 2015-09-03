<?php

namespace xMLM;

class Model_Configuration extends \SQL_Model {
	public $table ="xmlm_configurations";

	function init(){
		parent::init();

		// $this->addField('name');
		$this->addField('admin_charge')->caption('Admin Charge in %');
		$this->addField('other_charge_name');
		$this->addField('other_charge')->caption('Other Charge in %');
		$this->addField('welcome_letter')->type('text')->display(array('form'=>'RichText'));

		$this->addField('tail_pv')->defaultValue(0);
		$this->addField('minimum_payout_amount')->defaultValue(0);
		$this->addField('include_generation')->type('boolean')->defaultValue(true);
		$this->addField('trimming_applicable')->type('boolean')->defaultValue(true);
		$this->addField('credit_manager_email_id')->defaultValue(true);
		$this->addField('when_id_becomes_green')->defaultValue(true);
		$this->addField('when_id_becomes_orange')->defaultValue(true);
		$this->addField('days_allowed_for_green')->type('int')->defaultValue(45);
		$this->addField('relations_with_nominee')->type('text')->defaultValue('Father,Mother,Spouse,Sibling,Friend,Son,Daughter');

		$this->addField('royalty_percentage')->type('number');
		$this->addField('self_buiness_4_active_royalty')->type('number');
		$this->addField('active_royalty_percentage')->type('number');
		$this->addField('welcome_email_subject')->hint('Distributor/New Joining  Email Subject : this mail send to Distributor New Joining');
		$this->addField('welcome_email_matter')->hint('Distributor/New Joining  Email Body : this mail send to Distributor New Joining,
													 {{sponsor_name}},{{introducer_name}},{{Username}},{{password}}
													 {{name}},{{first_name}},{{last_name}},
													 {{mobile_number}},{{email}},{{date_of_birth}},
													 {{address}},{{state}},{{district}}
													 {{pan_no}},{{pin_code}},
													 {{user_type}},{{bank}},{{account_no}},
													 {{IFCS_Code}},{{branch_name}},{{kyc_no}},
													 {{nominee_name}},{{relations_with_nominee}}
													 {{nominee_age}}, {{nominee_email}},
													 {{kit}}, {{leg}} '
													 )->type('text')->display(array('form'=>'RichText'));

		$this->addField('credit_movement_email_subject')->hint('Creadit Movement/Rejected Distributor  Email Subject : this mail send to Rejected Distributor');
		$this->addField('credit_movement_email_matter')->hint('Creadit Movement/Rejected Distributor  Email Body : ,
													 {{name}},{{mobile_number}},{{email}},{{status}},
													 {{credits}},{{credits_given_on}},{{state}},
													 {{district}},{{address}},{{narration}}'
													 )->type('text')->display(array('form'=>'RichText'));
		
		$this->addField('booking_approve_email_subject')->hint('Booking Approved Email Subject: this email send to member');
		$this->addField('booking_approve_email_matter')->type('text')->display(array('form'=>'RichText'))
													->hint('{{booking_in_name_of}}, {{location}} {{hotel_name}},{{hotel_email_id}},{{hotel_contact_number}},{{hotel_confirmation_code}},
														{{adults}}, {{children}},
														{{voucher_no}},{{confirmation_through}},{{check_in_date}},{{check_out_date}},
														{{distributor_name}},{{distributor_mobile_number}},{{distributor_email}},
														{{distributor_address}},{{distributor_state}},{{distributor_district}},{{distributor_pin_code}}');
		
		$this->addField('orange_email_subject')->hint('Booking Approved Email Subject: this email send to member');
		$this->addField('orange_email_matter')->type('text')->display(array('form'=>'RichText'))
													->hint('{{booking_in_name_of}}, {{location}} {{hotel_name}},{{hotel_email_id}},{{hotel_contact_number}},{{hotel_confirmation_code}},
														{{adults}}, {{children}},
														{{voucher_no}},{{confirmation_through}},{{check_in_date}},{{check_out_date}},
														{{distributor_name}},{{distributor_mobile_number}},{{distributor_email}},
														{{distributor_address}},{{distributor_state}},{{distributor_district}},{{distributor_pin_code}}');													

		$this->addField('green_email_subject')->hint('Booking Approved Email Subject: this email send to member');
		$this->addField('green_email_matter')->type('text')->display(array('form'=>'RichText'))
													->hint('{{booking_in_name_of}}, {{location}} {{hotel_name}},{{hotel_email_id}},{{hotel_contact_number}},{{hotel_confirmation_code}},
														{{adults}}, {{children}},
														{{voucher_no}},{{confirmation_through}},{{check_in_date}},{{check_out_date}},
														{{distributor_name}},{{distributor_mobile_number}},{{distributor_email}},
														{{distributor_address}},{{distributor_state}},{{distributor_district}},{{distributor_pin_code}}');													

		

		$this->add('dynamic_model/Controller_AutoCreator');

	}
}