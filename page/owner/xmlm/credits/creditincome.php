<?php

class page_xMLM_page_owner_xmlm_credits_creditincome extends page_xMLM_page_owner_main{
	
	function init(){
		parent::init();

		$distributer=$this->add('xMLM/Model_Distributor');
		$credit_mov=$this->add('xMLM/Model_CreditMovement');
		$grid=$this->add('xMLM/Grid_CreditMovement');

		$grid->setModel($credit_mov);

		$grid->addColumn('standerd_kit');
		$grid->addColumn('gold_kit');

		$grid->removeColumn('item_name');
		$grid->removeColumn('created_by');
		$grid->removeColumn('status');
		$grid->removeColumn('created_at');
		$grid->removeColumn('credits_given_on');
		$grid->removeColumn('related_document');
		$grid->removeColumn('distributor_id');

		$grid->addSno();
		$grid->addPaginator(50);
		// $grid->addQuickSearch(array('distributor','status','credits','credits_given_on'));


	}
}