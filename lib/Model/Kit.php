<?php

namespace xMLM;

class Model_Kit extends \xShop\Model_Item {
	
	function init(){
		parent::init();

		// only items with specifications named PV, BV and RP
		$specification = $this->add('xShop\Model_Specification');
		$specification->addCondition('name','PV');
		$spec_assos_j1 = $this->join('xshop_item_spec_ass.item_id',null,null,'pv_j');
		$spec_assos_j1->addField('pv_specification_id','specification_id');
		$spec_assos_j1->addField('pv_value','value')->display(array('form'=>'Readonly'));
		$this->addCondition('pv_specification_id',$specification->fieldQuery('id'));

		$specification = $this->add('xShop\Model_Specification');
		$specification->addCondition('name','BV');
		$spec_assos_j2 = $this->join('xshop_item_spec_ass.item_id',null,null,'bv_j');
		$spec_assos_j2->addField('bv_specification_id','specification_id');
		$spec_assos_j2->addField('bv_value','value')->display(array('form'=>'Readonly'));
		$this->addCondition('bv_specification_id',$specification->fieldQuery('id'));

		$specification = $this->add('xShop\Model_Specification');
		$specification->addCondition('name','RP');
		$spec_assos_j3 = $this->join('xshop_item_spec_ass.item_id',null,null,'rp_j');
		$spec_assos_j3->addField('rp_specification_id','specification_id');
		$spec_assos_j3->addField('rp_value','value')->display(array('form'=>'Readonly'));
		$this->addCondition('rp_specification_id',$specification->fieldQuery('id'));

		$specification = $this->add('xShop\Model_Specification');
		$specification->addCondition('name','Capping');
		$spec_assos_j4 = $this->join('xshop_item_spec_ass.item_id',null,null,'cp_j');
		$spec_assos_j4->addField('cap_specification_id','specification_id');
		$spec_assos_j4->addField('cap_value','value')->display(array('form'=>'Readonly'))->caption('Capping')->type('money');
		$this->addCondition('cap_specification_id',$specification->fieldQuery('id'));

		$specification = $this->add('xShop\Model_Specification');
		$specification->addCondition('name','Introduction Income');
		$spec_assos_j4 = $this->join('xshop_item_spec_ass.item_id',null,null,'cp_j');
		$spec_assos_j4->addField('intro_specification_id','specification_id');
		$spec_assos_j4->addField('intro_value','value')->display(array('form'=>'Readonly'))->caption('Introduction Income')->type('money');
		$this->addCondition('intro_specification_id',$specification->fieldQuery('id'));

		$this->addField('purchase_points_required')->mandatory(true);

	}

	function requiredPurchasePoints(){
		return $this['sale_price'];
	}

	function getPV(){
		return $this->specification('PV');
	}

	function getBV(){
		return $this->specification('BV');
	}

	function getRP(){
		return $this->specification('RP');
	}

	function getIntro(){
		return $this->specification('Introduction Income');
	}

	function getCapping(){
		return $this->specification('Capping');
	}


}