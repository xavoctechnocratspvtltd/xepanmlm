<?php

namespace xMLM;


class Filter_Distributor extends \Filter_Base
{

    function init()
    {
        parent::init();

        $this->search_field = $this->addField('Line', 'q', '')->setAttr('placeholder','Search')->setNoSave();
        $this->type_field = $this->addField('Dropdown', 'kit', '')
                ->setEmptyText('All Kits')
                ->setNoSave()
                ->setModel('xMLM/Kit');
        $this->status_field = $this->addField('Dropdown', 'status', '')->setEmptyText('Any Status')->setValueList(array('active'=>'Active','inactive'=>'InActive'))->setNoSave();
        $this->from_date_field = $this->addField('DatePicker', 'from_date', '')->setNoSave();
        $this->to_date_field = $this->addField('DatePicker', 'to_date', '')->setNoSave();

    }

    /**
     * Process received filtering parameters after init phase
     *
     * @return void
     */
    function postInit()
    {
        parent::postInit();
        
        $v = trim($this->get('q'));
        $kit= $this->get('kit');
        $status = $this->get('status');
        $from_date = $this->get('from_date');
        $to_date = $this->get('to_date');

        if(!$v AND !$kit AND !$status AND !$from_date AND !$to_date) {
            return;
        }

        if($this->view->model->hasMethod('addConditionLike')){
            return $this->view->model->addConditionLike($v, $this->fields);
        }
        if($this->view->model) {
            $q = $this->view->model->_dsql();
        } else {
            $q = $this->view->dq;
        }
        
        $and = $q->andExpr();

        $or = $q->orExpr();
        foreach($this->fields as $field) {
            $or->where($field, 'like', '%'.$v.'%');
        }

        if($kit)
            $and->where('kit_item_id',$kit);

        if($from_date)
            $and->where('created_at','>=',$from_date);
        
        if($to_date)
	        $and->where('created_at','<',$this->api->nextDate($to_date));

        if($status)
            $and->where('is_active',$status=='active'?1:0);
        
        $and->where($or);
        $q->having($and);
    }
}
