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
		$right_col->add('View')->set('Right')->addClass('atk-swatch-ink atk-size-exa text-center atk-box-small');
		$distributor=$this->add('xMLM/Model_Distributor');			
	
		foreach ($this->add('xMLM/Model_Kit') as $kit) {
            $kit_id= $kit->id;
            $distributor->addExpression($this->api->normalizeName($kit['name']).'_left')->set(function($m,$q)use($kit_id){
                return $m->add('xMLM/Model_Distributor',array('table_alias'=>'k'.$kit_id.'left'))->addCondition('path','like',$q->concat($q->getField('path'),'A','%'))->addCondition('kit_item_id',$kit_id)->count();
            });

            $distributor->addExpression($this->api->normalizeName($kit['name']).'_right')->set(function($m,$q)use($kit_id){
                return $m->add('xMLM/Model_Distributor',array('table_alias'=>'k'.$kit_id.'right'))->addCondition('path','like',$q->concat($q->getField('path'),'B','%'))->addCondition('kit_item_id',$kit_id)->count();
            });            
        }

        $distributor->loadLoggedIn();
		
		foreach ($this->add('xMLM/Model_Kit') as $kit) {
            $left_col->add('View')->setHTML('<div class="atk-move-left">'.$kit['name'].': </div><div class="atk-move-right">'.$distributor[$this->api->normalizeName($kit['name']).'_left'].'</div>')->addClass('atk-clear-fix');
            $right_col->add('View')->setHTML('<div class="atk-move-left">'.$kit['name'].': </div><div class="atk-move-right">'.$distributor[$this->api->normalizeName($kit['name']).'_right'].'</div>')->addClass('atk-clear-fix');
        }


		$left_grid = $left_col->add('xMLM/Grid_Distributor');
		$left_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item','color_value'))
					->addCondition('path','like',$current_distributor['path'].'A%');
		$left_grid->addQuickSearch(array('username','name','kit_item'),null,'xMLM/Filter_Distributor');
		$left_grid->addPaginator(20);
		
		$right_grid = $right_col->add('xMLM/Grid_Distributor');
		$right_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item','color_value'))
					->addCondition('path','like',$current_distributor['path'].'B%');
		$right_grid->addQuickSearch(array('username','name','kit_item'),null,'xMLM/Filter_Distributor');
		$right_grid->addPaginator(20);

		$container->add('HR');
		$container->add('View')->set('Direct Introductions')->addClass('text-center atk-swatch-green atk-size-exa atk-box');
		$intro_grid = $container->add('xMLM/Grid_Distributor');
		$intro_grid->setModel('xMLM/Distributor',array('username','name','sponsor','introducer','left','right','created_at','greened_on','kit_item','color_value'))
					->addCondition('introducer_id',$current_distributor->id);

		 			

	}
}