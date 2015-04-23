<?php

class page_xMLM_page_owner_xmlm_treeview extends page_xMLM_page_owner_xmlm_main{
	function init(){
		parent::init();

		$this->add('View_Info')->set('Tree View Here');
		$this->add('view')->setElement('img')->setAttr(array('src'=>'epan-components/xMLM/templates/images/downline.png'));
	}
}