<?php

namespace xMLM;


class Grid_CreditMovement extends \Grid {

	function init(){
		parent::init();

		$this->add('VirtualPage')->addColumn('Attachement','Attach',array('icon'=>'attach'),$this)->set(function($p){
			$req= $p->add('xMLM/Model_CreditMovement')->tryLoad($p->id);
			if($req['attachment_id']){
				$p->add('HtmlElement')
					->setElement('img')
					->setAttr('src',$req['attachment'])
					->setAttr('width','100%');

			}else{
				$p->add('View_Error')->set('No Attachment Found');
			}
		});
	}

	function setModel($model,$fields=null){
		$m = parent::setModel($model,$fields);
		
		if($this->hasColumn('attachment')) $this->removeColumn('attachment');

		return $m;
	}
}