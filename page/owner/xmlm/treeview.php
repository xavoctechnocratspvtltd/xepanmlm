<?php

class page_xMLM_page_owner_xmlm_treeview extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();

		$container=$this->add('View')->addClass('container');
		$cr_view = $container->add('View')->setHTML("Tree View")->addClass('text-center atk-swatch-blue atk-size-exa atk-box');

		$container->add('xMLM/View_Tree')->setModel($distributor);
	}
}