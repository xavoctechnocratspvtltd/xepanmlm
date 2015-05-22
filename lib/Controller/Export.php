<?php

namespace xMLM;

class Controller_Export extends \AbstractController {
	public $fields=null;
	public $add_sno = true;
	public $totals=array();
	
	function init(){
		parent::init();
		$grid = $this->owner;
		if(!$this->fields) $this->fields = $grid->model->getActualFields();

		$btn = $grid->addButton('Export');
		if($btn->isClicked()){
			$grid->js()->univ()->successMessage($grid->model->_dsql()->field('sum(net_amount)')->getOne())->execute();
		}
	}
}