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
				
				$current_distributor = $p->add('xMLM/Model_Distributor')->loadLoggedIn();
				if($current_distributor['password'] != $form['your_password'])
					$form->displayError('your_password','Password is not correct');

				$distributor = $p->add('xMLM/Model_Distributor')->load($_GET['red_distributor_id']);
				
				if($distributor['kit_item_id'])
					$form->displayError('kit','Distributor is Already assigned a kit');

				$distributor['kit_item_id'] = $form['kit'];
				$distributor->save();
				$form->js(null,$form->js()->_selector('.dist_grid')->trigger('reload'))->univ()->closeDialog()->execute();
			}
		});

	}

	function setModel($model,$fields=null){
		$m=parent::setModel($model,$fields);
		if($this->hasColumn('created_at')) $this->removeColumn('created_at');
		if($this->hasColumn('greened_on')) $this->removeColumn('greened_on');
		if($this->hasColumn('sponsor')) $this->removeColumn('sponsor');
		if($this->hasColumn('introducer')) $this->removeColumn('introducer');
		if($this->hasColumn('left')) $this->removeColumn('left');
		if($this->hasColumn('right')) $this->removeColumn('right');

		return $m;
	}

	function formatRow(){
		$sp = "<div class='atk-size-micro atk-move-left'>sp: ".$this->model['sponsor']."</div>";
		$int = "<div class='atk-size-micro atk-move-right'>in: ".$this->model['introducer']."</div>";
		
		if($this->model['greened_on'])
			$name_class="atk-clear-fix atk-effect-success atk-size-mega";
		else
			$name_class="atk-clear-fix atk-effect-danger atk-size-mega";

		$name = "<br/><div class='$name_class'>".$this->model['name']."</div>";

		$left = "<div class='atk-size-micro atk-move-left'>A: ".$this->model['left']."</div>";
		$right = "<div class='atk-size-micro atk-move-right'>B: ".$this->model['right']."</div>";


		$this->current_row_html['name']= $sp.$int.$name.$left.$right;
		$this->setTDParam('name','class','text-center');


		$un = "<div class='text-center'>".$this->model['username']."</div>";
		$jn = "<div class='atk-size-micro'>jn: ".date("d M Y",strtotime($this->model['created_at']))."</div>";
		if($this->model['greened_on'])
			$grn = "<div class='atk-size-micro'>gn: ".date("d M Y",strtotime($this->model['greened_on']))."</div>";
		else{
			// $grn_vp = $this->js()->univ()->frameURL('Pay Now For Distributor '. $this->model['name'],)->render();
			$grn = "<div class='atk-size-micro'>gn: <a href='#green' onclick=\"$.univ.frameURL('Pay Now For Distributor ".$this->model['name']."','".($this->api->url($this->pay_vp->getURL(),array('red_distributor_id'=>$this->model->id))->getURL())."')\">Pay Now</a></div>";
		}

		$this->current_row_html['username'] = $un.$jn.$grn;


		parent::formatRow();
	}

}