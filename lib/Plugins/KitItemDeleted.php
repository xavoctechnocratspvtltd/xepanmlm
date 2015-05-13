<?php

namespace xMLM;


class Plugins_KitItemDeleted extends \componentBase\Plugin {

	function init(){
		parent::init();
		$this->addHook('xshop_item_before_delete',array($this,'Plugins_KitItemDeleted'));
	}

	function Plugins_KitItemDeleted($obj, $item){
		
		$this->add('xMLM/Model_Distributor')
			->addCondition('kit_item_id',$item->id)
			->set('kit_item_id',null)
			->saveAndUnload();

	}
}
