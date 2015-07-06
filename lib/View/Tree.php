<?php

namespace xMLM;

class View_Tree extends \View {
	
	public $start_distributor=null;
	public $start_id=null;
	public $level=4;

	function init(){
		parent::init();
		
		$this->distributor = $distributor=$this->add('xMLM/Model_Distributor');
		$distributor->loadLoggedIn();
		
		
		if($this->api->stickyGET('start_id')){
			$this->start_id = $_GET['start_id'];
		}

		if(!$this->start_id){
			$this->start_id = $distributor->id;
		}else{
			if(!$this->api->auth->model->isBackEndUser()){
				if(!$distributor->isInDown($this->add('xMLM/Model_Distributor')->tryLoad($this->start_id))){
					$this->add('View_Error')->set('You are not Authorized to look out of your Tree');
					$this->start_id = $distributor?$distributor->id: null;
				}
			}
		}

		$this->start_distributor = $this->add('xMLM/Model_Distributor')->load($this->start_id);

		$form = $this->add('Form');
		$user_field = $form->addField('line','username');
		$user_field->afterField()->add('Button')->set(array(' ','icon'=>'search'));

		if($form->isSubmitted()){
			$model = $this->add('xMLM/Model_Distributor')->tryLoadBy('username',$form['username']);
			if(!$model->loaded())
				$form->displayError('username','No, User found with this username');
			if(!$this->api->auth->model->isBackEndUser()){
				if(!$distributor->isInDown($model)){
					$form->displayError('username','Looks like, Not in your Downline');
				}
			}
			$this->js()->reload(array('start_id'=>$model->id))->execute();
		}
	}
	
	function renderModel($model,$level){
		$output="";
		$reload_js = $this->js()->reload(array('start_id'=>$model->id));
		$t=$this->template->cloneRegion('Node');
		$t->setHTML('username','<a href="#xepan" onclick="'.$reload_js->render().'">'.$model['username'].'</a>');
		$t->set('class',($model['greened_on'] && $model['ansestors_updated'])?'atk-effect-success':($model['greened_on']?'atk-effect-warning':'atk-effect-danger'));
		$t->set('title',
				$model['name'].
				"<br/>Jn: ". date("d M Y", strtotime($model['created_at'])). 
				"<br/>Gr: ". date("d M Y", strtotime($model['greened_on'])). 
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
					<div class='atk-box-small atk-size-mega atk-swatch-green'>Downline</div>
					<table border=1>
						<tr>
							<td>&nbsp;</td>
							<td>Left</td>
							<td>Right</td>
						</tr>
						<tr>
							<td>Total</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'A%')->count()->getOne() ."</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'B%')->count()->getOne() ."</td>
						</tr>
						<tr>
							<td>Orange</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'A%')->addCondition('ansestors_updated',false)->count()->getOne() ."</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'B%')->addCondition('ansestors_updated',false)->count()->getOne() ."</td>
						</tr>
						<tr>
							<td>Green</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'A%')->addCondition('greened_on','<>',null)->addCondition('ansestors_updated',true)->count()->getOne() ."</td>
							<td>". $model->newInstance()->addCondition('path','like',$model['path'].'B%')->addCondition('greened_on','<>',null)->addCondition('ansestors_updated',true)->count()->getOne() ."</td>
						</tr>
					</table>
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

		if($this->start_id){

			$reload_parent_js = $this->js()->reload(array('start_id'=>$this->start_distributor['sponsor_id']));
			$distributor_tree_js = $this->js()->reload(array('start_id'=>$this->distributor->id));

			$r=$this->renderModel($this->add('xMLM/Model_Distributor','d')->load($this->start_id),$this->level);
	        $this->template->setHTML('Tree',$r);
	        if($this->start_id != $this->distributor->id && $this->start_distributor['sponsor_id']){
		        $this->template->setHTML('ParentURL',$reload_parent_js->render());
		        $this->template->setHTML('MyURL',$distributor_tree_js->render());
	        }
		    else{
		    	$this->template->del('Parent');
		    }
	        $this->template->del('Node');
	        $this->js(true)->_selector('.main_div')->xtooltip();
		}
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