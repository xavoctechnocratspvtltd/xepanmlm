<?php
/**
 * Model implementation
 */
namespace xMLM;

class Model_FormAllot extends \Model_Document
{
	public $table="xmlm_formallots";
	public $status=array();
	public $root_document_name='xMLM\FormAllot';
	/**
	 * init model
	 *
	 * @return void
	 */
	function init()
	{
		parent::init();
		
		$this->hasOne('xMLM/Distributor','distributor_id')->display(array('form'=>'autocomplete/Basic'))->mandatory(true);

		$this->addField('from_no')->mandatory(true);
		$this->addField('to_no')->mandatory(true);
		// $this->addField('created_at')->type('datetime')->mandatory(true)->defaultValue($this->api->now);

		$this->addHook('beforeSave',$this);

		// $this->add('dynamic_model/Controller_AutoCreator');

	}

	function beforeSave(){

		$entry_no_check = $this->add('xMLM/Model_FormAllot');

		$cond1 = $this->dsql()->andExpr()
			->where('from_no','<=',$this['from_no'])
			->where('to_no','>=',$this['from_no']);

		$cond2 = $this->dsql()->andExpr()
			->where('from_no','<=',$this['to_no'])
			->where('to_no','>=',$this['to_no']);

		$cond3 = $this->dsql()->andExpr()
			->where('from_no','>',$this['from_no'])
			->where('to_no','<',$this['from_no']);

		$entry_no_check->addCondition($this->dsql()->orExpr()->where($cond1)->where($cond2)->where($cond3));


		if($this->loaded())
			$entry_no_check->addCondition('id','<>',$this->id);

		$entry_no_check->tryLoadAny();

		if($entry_no_check->loaded())
			throw $this->exception('Either \'From\' or \'To\' Number is not valid','ValidityCheck')->setField('from_no');
			

	}

}