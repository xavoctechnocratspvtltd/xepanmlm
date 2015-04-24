<?php

namespace xMLM;

class View_Tree extends \View {
	
	public $start_id=null;
	public $level=3;

	function init(){
		parent::init();
		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		
		if(!$this->start_id){
			$this->start_id = $distributor->id;
		}else{
			if(!$distributor->isInDown($this->add('xMLM/Model_Distributor')->tryLoad($this->start_id)))
				$this->start_id = $distributor->id;				
		}

	}
	
	function renderModel($model,$level){
		$output="";
		$t=$this->template->cloneRegion('Node');
		$t->set('username',$model['username']);
		$t->set('class',$model['greened_on']?'atk-effect-success':'atk-effect-danger');
		$t->set('title',$model['name']."<br/>Jn: ". date("d M Y", strtotime($model['created_at'])). "<br/>Kit: ". $model['kit_item'] );

		if($model['left_id'] and $level-1 > 0){
			$t->setHTML('leftnode',$this->renderModel($model->ref('left_id'),$level-1));
		}else{
			$t->trySet('sponsor_id',$model->id);
			$t->trySet('leftnode','empty');
			// $t->tryDel('leftnode');
		}

		if($model['right_id'] and $level-1 > 0){
			$t->setHTML('rightnode',$this->renderModel($model->ref('right_id'),$level-1));
		}else{
			$t->trySet('sponsor_id',$model->id);
			$t->trySet('rightnode','empty');
			// $t->tryDel('rightnode');
		}

		$output.=$t->render();
		return $output;
	}

	function render(){
		$r=$this->renderModel($this->add('xMLM/Model_Distributor')->load($this->start_id),$this->level);
        $this->template->setHTML('Tree',$r);
        $this->template->del('Node');
        $this->js(true)->_selector('.main_div')->xtooltip();
		return parent::render();
	}

	function defaultTemplate(){
		$this->app->pathfinder->base_location->addRelativeLocation(
		    'epan-components/'.__NAMESPACE__, array(
		        'php'=>'lib',
		        'template'=>'templates',
		        'css'=>'templates/css',
		        'js'=>'templates/js',
		    )
		);
		return array('view/xMLM-treeview');
	}
}