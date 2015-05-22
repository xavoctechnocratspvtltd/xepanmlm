<?php

class page_xMLM_page_owner_xmlm_downlineview extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$container=$this->add('View')->addClass('container');

		$current_distributor = $this->add('xMLM/Model_Distributor')->loadLoggedIn();

		$container->add('View')->set('Downline View')->addClass('text-center atk-swatch-green atk-size-exa atk-box');

		$cols = $container->add('Columns');
		$left_col = $cols->addColumn(6);
		$right_col = $cols->addColumn(6);

		$left_col->add('View')->set('Left')->addClass('atk-swatch-ink atk-size-exa text-center atk-box-small');
		$left_grid = $left_col->add('xMLM/Grid_Distributor');
		$left_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item'))
					->addCondition('path','like',$current_distributor['path'].'A%');
		$left_grid->addQuickSearch(array('username','name','kit_item'));
		$left_grid->addPaginator(50);
		
		$right_col->add('View')->set('Right')->addClass('atk-swatch-ink atk-size-exa text-center atk-box-small');
		$right_grid = $right_col->add('xMLM/Grid_Distributor');
		$right_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item'))
					->addCondition('path','like',$current_distributor['path'].'B%');
		$right_grid->addQuickSearch(array('username','name','kit_item'));
		$right_grid->addPaginator(50);

		$container->add('HR');
		$container->add('View')->set('Direct Introductions')->addClass('text-center atk-swatch-green atk-size-exa atk-box');
		$intro_grid = $container->add('xMLM/Grid_Distributor');
		$intro_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item'))
					->addCondition('introducer_id',$current_distributor->id);

	}
}