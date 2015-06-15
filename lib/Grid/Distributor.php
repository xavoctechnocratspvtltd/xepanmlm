<?php

namespace xMLM;

class Grid_Distributor extends \Grid{
	
	public $pay_vp=null;

	function init(){
		parent::init();
		$this->addClass('dist_grid');
		$this->js('reload')->reload();

		$this->pay_vp= $this->add('VirtualPage')->set(function($p){
			$p->api->stickyGET('red_distributor_id');
			$p->add('View')->set('Pay for kit')->addClass('text-center atk-size-exa atk-swatch-red atk-box-small');
			$form = $p->add('Form_Stacked');
			$form->addField('DropDown','kit')->setEmptyText("Please Select Kit")->validateNotNull()->setModel('xMLM/Model_Kit');
			$form->addField('password','your_password')->validateNotNull();
			$form->addSubmit("Pay Now");
			if($form->isSubmitted()){
				
				if(!$this->api->auth->verifyCredentials($this->api->auth->model['username'], $form['your_password']))
					$form->displayError('your_password','Password is not correct');

				$distributor = $p->add('xMLM/Model_Distributor')->load($_GET['red_distributor_id']);
				
				if($distributor['kit_item_id'])
					$form->displayError('kit','Distributor is Already assigned a kit');

				$distributor['kit_item_id'] = $form['kit'];
				$distributor->save();
				$form->js(null,$form->js()->_selector('.dist_grid')->trigger('reload'))->univ()->closeDialog()->execute();
			}
		});

		$this->add('VirtualPage')->addColumn('Tree','Tree',array('icon'=>'users'),$this)->set(function($p){
			$distributor=$p->add('xMLM/Model_Distributor');
			$distributor->tryLoad($p->id);

			if(!$distributor->loaded())
		 		$distributor = $distributor->newInstance()->loadRoot();
			$p->add('xMLM/View_Tree',array('start_id'=>$distributor->id));
		});

	}

	function setModel($model,$fields=null){
		if(is_string($model)){
			$model= str_replace('/', "/Model_", $model);
			$model = $this->add($model);
		}

		// $q= $model->dsql();

		// $kit_j = $model->leftJoin('xshop_items','kit_item_id');
		// $specification = $this->add('xShop\Model_Specification');
		// $specification->addCondition('name','Color');
		// $spec_assos_j4 = $kit_j->leftJoin('xshop_item_spec_ass.item_id',null,null,'clr_val_j');
		// $spec_assos_j4->addField('color_specification_id','specification_id');
		// $spec_assos_j4->addField('color_value','value')->display(array('form'=>'Readonly'))->caption('Color');
		// $model->addCondition('color_specification_id',$specification->fieldQuery('id'));

		$m=parent::setModel($model,$fields);

		if($this->hasColumn('item_name')) $this->removeColumn('item_name');
		if($this->hasColumn('created_at')) $this->removeColumn('created_at');
		if($this->hasColumn('is_active')) $this->removeColumn('is_active');
		if($this->hasColumn('sponsor')) $this->removeColumn('sponsor');
		if($this->hasColumn('introducer')) $this->removeColumn('introducer');
		if($this->hasColumn('left')) $this->removeColumn('left');
		if($this->hasColumn('right')) $this->removeColumn('right');
		if($this->hasColumn('kit_item')) $this->removeColumn('kit_item');
		if($this->hasColumn('status')) $this->removeColumn('status');
		if($this->hasColumn('color_value')) $this->removeColumn('color_value');

		return $m;
	}

	function formatRow(){
		$sp = "<div class='atk-size-micro atk-move-left'>sp: ".$this->model['sponsor']."</div>";
		$int = "<div class='atk-size-micro atk-move-right'>in: ".$this->model['introducer']."</div>";
		
		if($this->model['greened_on'])
			$name_class="atk-clear-fix atk-effect-success atk-size-mega";
		else
			$name_class="atk-clear-fix atk-effect-danger atk-size-mega";

		$name = "<br/><div class='$name_class'>".$this->model['name'].
				"<br/><small style='color: ". $this->model->ref('kit_item_id')->get('color_value') ."'>". $this->model['kit_item'] ."</small>".
				"</div>";

		$left = "<div class='atk-size-micro atk-move-left'>A: ".$this->model['left']."</div>";
		$right = "<div class='atk-size-micro atk-move-right'>B: ".$this->model['right']."</div>";


		$this->current_row_html['name']= $sp.$int.$name.$left.$right;
		$this->setTDParam('name','class','text-center');


		$un = "<div class='text-center'>".$this->model['username']."</div>";
		$jn = "<div class='atk-size-micro'>Joined On: ".date("d M Y",strtotime($this->model['created_at']))."</div>";
		if($this->model['greened_on'])
			$grn = "<div class='atk-size-micro'>Qualified On: ".date("d M Y",strtotime($this->model['greened_on']))."</div>";
		else{
			if($this->api->auth->isLoggedIn() && $this->api->auth->model->isFrontEndUser())
				$grn = "<div class='atk-size-micro'>gn: <a href='#green' onclick=\"$.univ.frameURL('Pay Now For Distributor ".$this->model['name']."','".($this->api->url($this->pay_vp->getURL(),array('red_distributor_id'=>$this->model->id))->getURL())."')\">Pay Now</a></div>";
			else
				$grn="";
		}

		$this->current_row_html['username'] = $un.$jn.$grn;


		parent::formatRow();
	}

}
