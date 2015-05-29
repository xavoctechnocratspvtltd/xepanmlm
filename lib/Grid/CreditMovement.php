<?php

namespace xMLM;


class Grid_CreditMovement extends \Grid {
	public $hide_distributor=true;
	public $generation_income=false;
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
		if(!$fields)
			$fields=array('distributor_id','distributor','status','created_at','credits_given_on','credit','debit','narration');
		$m = parent::setModel($model,$fields);
		
		if($this->hasColumn('attachment')) $this->removeColumn('attachment');
		// $this->addFormatter('distributor','totals_distributor');

		if(!$this->hide_distributor and $this->hasColumn('distributor')){
			$this->addFormatter('distributor','distributor');
		}
		return $m;
	}

	function updateGrandTotals()
    {
        // get model
        $m = clone $this->getIterator();
        // create DSQL query for sum and count request
        $fields = array_keys($this->totals);

        // select as sub-query
        $sub_q = $m->_dsql()->del('fields')->del('limit')->del('order')->del('group');

        // $q = $this->api->db->dsql();//->debug();
        // $q->table($sub_q->render(), 'grandTotals'); // alias is mandatory if you pass table as DSQL
        foreach ($fields as $field) {
            $sub_q->field($sub_q->sum($m->getElement($field)), $field);
        }
        $sub_q->field($sub_q->count(), 'total_cnt');

        // execute DSQL
        $data = $sub_q->getHash();

        // parse results
        $this->total_rows = $data['total_cnt'];
        unset($data['total_cnt']);
        $this->totals = $data;
    }
}