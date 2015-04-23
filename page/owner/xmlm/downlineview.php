<?php

class page_xMLM_page_owner_xmlm_downlineview extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Downline View Here');
		
		$tabs=$this->add('Tabs');
		$tab1=$tabs->addTab('Active');
		$tab2=$tabs->addTab('Deactive');
		$active_form=$tab1->add('Form');
		$active_form->addField('DatePicker','from_date');
		$active_form->addField('DatePicker','to_date');
		$active_form->addSubmit('GET LIST');


		$deactive_form=$tab2->add('Form');
		$deactive_form->addField('DatePicker','from_date');
		$deactive_form->addField('DatePicker','to_date');
		$deactive_form->addSubmit('GET LIST');

		$view_active=$tab1->add('View');
		$active_tabs=$view_active->add('Tabs');
		$active_tab_left=$active_tabs->addTab('Left');
		$active_tab_right=$active_tabs->addTab('Right');

		$view_deactive=$tab2->add('View');
		$deactive_tabs=$view_deactive->add('Tabs');
		$deactive_tab_left=$deactive_tabs->addTab('Left');
		$deactive_tab_right=$deactive_tabs->addTab('Right');

		$active_left_grid=$active_tab_left->add('Grid');
		$active_left_grid->addQuickSearch(array('name','username'));
		$active_left_destributor=$this->add('xMLM/Model_Distributor');
		$active_left_destributor->addCondition('is_active',true);
		$active_left_grid->setModel($active_left_destributor);


		$active_right_grid=$active_tab_right->add('Grid');
		$active_right_grid->addQuickSearch(array('name','username'));
		$active_right_destributor=$this->add('xMLM/Model_Distributor');
		$active_right_destributor->addCondition('is_active',true);
		$active_right_grid->setModel($active_right_destributor);

		$deactive_left_grid=$deactive_tab_left->add('Grid');
		$deactive_left_grid->addQuickSearch(array('name','username'));
		$deactive_left_destributor=$this->add('xMLM/Model_Distributor');
		$deactive_left_destributor->addCondition('is_active',false);
		$deactive_left_grid->setModel($deactive_left_destributor);

		$deactive_right_grid=$deactive_tab_right->add('Grid');
		$deactive_right_grid->addQuickSearch(array('name','username'));
		$deactive_right_destributor=$this->add('xMLM/Model_Distributor');
		$deactive_right_destributor->addCondition('is_active',false);
		$deactive_right_grid->setModel($deactive_right_destributor);
	}
}