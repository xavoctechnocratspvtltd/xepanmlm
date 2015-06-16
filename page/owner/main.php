<?php

class page_xMLM_page_owner_main extends page_componentBase_page_owner_main {
	function init(){
		parent::init();
		$this->app->pathfinder->base_location->addRelativeLocation(
		    'epan-components/xMLM', array(
		        'php'=>'lib',
		        'template'=>'templates',
		        'css'=>'templates/css',
		        'js'=>'templates/js',
		    )
		);
	}


	function page_config(){
		$this->add('H1')->set('Default Config Page');
	}
}