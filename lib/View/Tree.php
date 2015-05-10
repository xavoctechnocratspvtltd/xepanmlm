<?php

namespace xMLM;

class View_Tree extends \View {
	
	public $start_distributor=null;
	public $start_id=null;
	public $level=4;

	function init(){
		parent::init();
		$distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		
		
		if($_GET['start_id']){
			$this->start_id = $_GET['start_id'];
		}

		if(!$this->start_id){
			$this->start_id = $distributor->id;
		}else{
			if(!$distributor->isInDown($this->add('xMLM/Model_Distributor')->tryLoad($this->start_id))){
				$this->start_id = $this->add('xMLM/Model_Distributor')->loadRoot()->get('id');				
			}
		}

		$this->start_distributor = $this->add('xMLM/Model_Distributor')->load($this->start_id);
	}
	
	function renderModel($model,$level){
		$output="";
		$reload_js = $this->js()->reload(array('start_id'=>$model->id));
		$t=$this->template->cloneRegion('Node');
		$t->setHTML('username','<a href="#xepan" onclick="'.$reload_js->render().'">'.$model['username'].'</a>');
		$t->set('class',$model['greened_on']?'atk-effect-success':'atk-effect-danger');
		$t->set('title',
				$model['name'].
				"<br/>Jn: ". date("d M Y", strtotime($model['created_at'])). 
				"<br/>Kit: ". $model['kit_item'] .
				"<br/>Intro: ". $model['introducer'] .
				"<br/><table border=1>
					<tr>
						<th> Session </th><th> Left </th><th> Right </th>
					</tr>
					<tr>
						<th>PV</th><td>".$model['session_left_pv']."</td><td>".$model['session_right_pv']."</td>
					</tr>
					<tr>
						<th>BV</th><td>".$model['session_left_bv']."</td><td>".$model['session_right_bv']."</td>
					</tr>
					</table>
					<div class='atk-box-small atk-swatch-green'>Session Intros: ".$model['session_intros_amount']." /-</div>
					"
				);

		if($model['left_id'] and $level-1 > 0){
			$t->setHTML('leftnode',$this->renderModel($model->ref('left_id'),$level-1));
		}else{
			$t->trySet('sponsor_id',$model->id);
			if($model['left_id'])
				$t->trySetHTML('leftnode','<i class="icon-down-circled2 atk-size-mega"></i>');
			else
				$t->trySetHTML('leftnode','<i class="icon-user atk-size-mega"></i>');
			// $t->tryDel('leftnode');
		}

		if($model['right_id'] and $level-1 > 0){
			$t->setHTML('rightnode',$this->renderModel($model->ref('right_id'),$level-1));
		}else{
			$t->trySet('sponsor_id',$model->id);
			if($model['right_id'])
				$t->trySetHTML('rightnode','<i class="icon-down-circled2 atk-size-mega"></i>');
			else
				$t->trySetHTML('rightnode','<i class="icon-user atk-size-mega"></i>');
			// $t->tryDel('rightnode');
		}

		$output.=$t->render();
		return $output;
	}

	function render(){
		$reload_parent_js = $this->js('click')->reload(array('start_id'=>$this->start_distributor['sponsor_id']));

		$r=$this->renderModel($this->add('xMLM/Model_Distributor','d')->load($this->start_id),$this->level);
        $this->template->setHTML('Tree',$r);
        if($this->start_distributor['sponsor_id'])
	        $this->template->setHTML('ParentURL',$reload_parent_js->render());
	    else
	    	$this->template->del('Parent');
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