<?php
/**
 * Page class
 */
class page_xMLM_page_cron_freelookfinish extends Page
{
	function init()
    {
		parent::init();

		// get all distributors who has created at before 11 days (including time)
		// updated_ansestos = false
		// is_active = true
		// greened_on must not ne null/empty
			// loop through
				// update ansesstors
				// add to introducer's sessionIntros


		$distributors = $this->add('xMLM/Model_Distributor');
		$distributors->addCondition('ansestors_updated',false);
		$distributors->addCondition('is_active',true);
		// $distributors->addCondition('greened_on','<',date('Y-m-d H:i:s',strtotime($this->api->now.' -11 day')));
		
		foreach ($distributors as $dist) {
			$kit=$distributors->kit();
			$distributors->updateAnsestors($kit->getPV(),$kit->getBV());
			$introducer = $distributors->introducer();
			$introducer->addSessionIntro($kit->getIntro());
			$distributors['ansestors_updated'] = true;

			/*When Distributor Id Is Green Send mail to Multiple Email In Array */

			$tm=$this->add( 'TMail_Transport_PHPMailer' );
			// $msg=$this->add( 'GiTemplate' );
			// $msg->loadTemplate( 'mail/registerdistributerwhenidorange');

			$cc = array();
			$emails = $this->add('xMLM/Model_Configuration')->tryLoadANy()->get('when_id_becomes_green');
			$emails=explode(",", $emails);
			$email = $emails[0];
			// unset($emails[0]);
			$emails = array_values($emails);
			// throw new \Exception("coming", 1);
			$cc = $emails;

			
			
			$config_model=$this->add('xMLM/Model_Configuration');
			$config_model->tryLoadAny();

			if($config_model['green_email_matter']){
				// $distributer_mail=$this['email'];
				$subject= $config_model['green_email_subject']." ".$distributors['name'];
				$email_body=$config_model['green_email_matter']?:"Distributor Send Mail Layout Is Empty";
		
				//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
				$email_body = str_replace("{{sponsor_name}}", $distributors['sponsor'], $email_body);
				$email_body = str_replace("{{introducer_name}}", $distributors['introducer'], $email_body);
				$email_body = str_replace("{{name}}", $distributors['name'], $email_body);
				$email_body = str_replace("{{first_name}}", $distributors['first_name'], $email_body);
				$email_body = str_replace("{{last_name}}", $distributors['last_name'], $email_body);
				$email_body = str_replace("{{Username}}", $distributors['username'], $email_body);
				$email_body = str_replace("{{password}}", $distributors['password'], $email_body);
				$email_body = str_replace("{{mobile_number}}", $distributors['mobile_number']?$distributors['mobile_number']:" ", $email_body);
				$email_body = str_replace("{{email}}", $distributors['email']?$distributors['email']:" ", $email_body);
				$email_body = str_replace("{{date_of_birth}}", $distributors['date_of_birth']?$distributors['date_of_birth']:" ", $email_body);
				$email_body = str_replace("{{address}}", $distributors['address']?$distributors['address']:" ", $email_body);
				$email_body = str_replace("{{state}}", $distributors['state']?$distributors['state']:" ", $email_body);
				$email_body = str_replace("{{district}}", $distributors['district']?$distributors['district']:" ", $email_body);
				$email_body = str_replace("{{address}}", $distributors['address']?$distributors['address']:" ", $email_body);
				// $email_body = str_replace("{{users_id}}", $this['users_id']?$this['user_id']:" ", $email_body);
				$email_body = str_replace("{{pan_no}}", $distributors['pan_no']?$distributors['pan_no']:" ", $email_body);
				$email_body = str_replace("{{pin_code}}", $distributors['pin_code']?$distributors['pin_code']:" ", $email_body);
				$email_body = str_replace("{{user_type}}", $distributors['type']?$distributors['type']:" ", $email_body);
				$email_body = str_replace("{{bank}}", $distributors['bank']?$distributors['bank']:" ", $email_body);
				$email_body = str_replace("{{account_no}}", $distributors['account_no']?$distributors['account_no']:" ", $email_body);
				$email_body = str_replace("{{IFCS_Code}}", $distributors['IFCS_Code']?$distributors['IFCS_Code']:" ", $email_body);
				$email_body = str_replace("{{branch_name}}", $distributors['branch_name']?$distributors['branch_name']:" ", $email_body);
				$email_body = str_replace("{{kyc_no}}", $distributors['kyc_no']?$distributors['kyc_no']:" ", $email_body);
				$email_body = str_replace("{{nominee_name}}", $distributors['nominee_name']?$distributors['nominee_name']:" ", $email_body);
				$email_body = str_replace("{{relations_with_nominee}}", $distributors['relations_with_nominee']?$distributors['relations_with_nominee']:" ", $email_body);
				$email_body = str_replace("{{nominee_age}}", $distributors['nominee_age']?$distributors['nominee_age']:" ", $email_body);
				$email_body = str_replace("{{nominee_email}}", $distributors['nominee_email']?$distributors['nominee_email']:" ", $email_body);
				$email_body = str_replace("{{kit}}", $distributors['kit_item']?$distributors['kit_item']:" ", $email_body);
				$email_body = str_replace("{{leg}}", $distributors['Leg']?$distributors['Leg']:" ", $email_body);

				// return $email_body;
			}	
			// if(!$email) throw new \Exception("not found ".$email, 1);
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
			// throw new \Exception("Error Processing Request", 1);
			
			/*When Distributor Id Is Green Send mail to Distributor*/

			$tmail=$this->add( 'TMail_Transport_PHPMailer' );
			$config_model=$this->add('xMLM/Model_Configuration');
			$config_model->tryLoadAny();

			$subject= $config_model['green_distributor_email_subject']." ".$distributors['name'];
			$distributer_mail=$distributors['email'];
			// throw new \Exception($distributer_mail, 1);
			if($config_model['green_distributor_mail_matter']){
				$email_body = $config_model['green_distributor_mail_matter']?:"Orange Distributor Send Mail Layout Is Empty";

				//REPLACING VALUE INTO ORDER DETAIL TEMPLATES
				$email_body = str_replace("{{sponsor_name}}", $distributors['sponsor'], $email_body);
				$email_body = str_replace("{{introducer_name}}", $distributors['introducer'], $email_body);
				$email_body = str_replace("{{name}}", $distributors['name'], $email_body);
				$email_body = str_replace("{{first_name}}", $distributors['first_name'], $email_body);
				$email_body = str_replace("{{last_name}}", $distributors['last_name'], $email_body);
				$email_body = str_replace("{{Username}}", $distributors['username'], $email_body);
				$email_body = str_replace("{{password}}", $distributors['password'], $email_body);
				$email_body = str_replace("{{mobile_number}}", $distributors['mobile_number']?$distributors['mobile_number']:" ", $email_body);
				$email_body = str_replace("{{email}}", $distributors['email']?$distributors['email']:" ", $email_body);
				$email_body = str_replace("{{date_of_birth}}", $distributors['date_of_birth']?$distributors['date_of_birth']:" ", $email_body);
				$email_body = str_replace("{{address}}", $distributors['address']?$distributors['address']:" ", $email_body);
				$email_body = str_replace("{{state}}", $distributors['state']?$distributors['state']:" ", $email_body);
				$email_body = str_replace("{{district}}", $distributors['district']?$distributors['district']:" ", $email_body);
				$email_body = str_replace("{{address}}", $distributors['address']?$distributors['address']:" ", $email_body);
				// $email_body = str_replace("{{users_id}}", $this['users_id']?$this['user_id']:" ", $email_body);
				$email_body = str_replace("{{pan_no}}", $distributors['pan_no']?$distributors['pan_no']:" ", $email_body);
				$email_body = str_replace("{{pin_code}}", $distributors['pin_code']?$distributors['pin_code']:" ", $email_body);
				$email_body = str_replace("{{user_type}}", $distributors['type']?$distributors['type']:" ", $email_body);
				$email_body = str_replace("{{bank}}", $distributors['bank']?$distributors['bank']:" ", $email_body);
				$email_body = str_replace("{{account_no}}", $distributors['account_no']?$distributors['account_no']:" ", $email_body);
				$email_body = str_replace("{{IFCS_Code}}", $distributors['IFCS_Code']?$distributors['IFCS_Code']:" ", $email_body);
				$email_body = str_replace("{{branch_name}}", $distributors['branch_name']?$distributors['branch_name']:" ", $email_body);
				$email_body = str_replace("{{kyc_no}}", $distributors['kyc_no']?$distributors['kyc_no']:" ", $email_body);
				$email_body = str_replace("{{nominee_name}}", $distributors['nominee_name']?$distributors['nominee_name']:" ", $email_body);
				$email_body = str_replace("{{relations_with_nominee}}", $distributors['relations_with_nominee']?$distributors['relations_with_nominee']:" ", $email_body);
				$email_body = str_replace("{{nominee_age}}", $distributors['nominee_age']?$distributors['nominee_age']:" ", $email_body);
				$email_body = str_replace("{{nominee_email}}", $distributors['nominee_email']?$distributors['nominee_email']:" ", $email_body);
				$email_body = str_replace("{{kit}}", $distributors['kit_item']?$distributors['kit_item']:" ", $email_body);
				$email_body = str_replace("{{leg}}", $distributors['Leg']?$distributors['Leg']:" ", $email_body);

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


			$distributors->saveAndUnload();			
		}

	}
}