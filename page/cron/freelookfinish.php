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
		$distributors->addCondition('greened_on','<',date('Y-m-d H:i:s',strtotime($this->api->now.' -11 day')));

		foreach ($distributors as $dist) {
			$kit=$distributors->kit();
			$distributors->updateAnsestors($kit->getPV(),$kit->getBV());
			$introducer = $distributors->introducer();
			$introducer->addSessionIntro($kit->getIntro());			
		}

	}
}