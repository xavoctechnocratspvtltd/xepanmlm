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
        $this->on_date_field = $this->addField('xMLM/DatePicker', 'on_date', '')->setNoSave()->setAttr('placeholder','Greened On');

        if($_GET['reset_filter']){
            foreach ($this->get() as $field=>$value) {
                if ($this->isClicked('Clear') || is_null($value)) {
                    $this->forget($field);
                } else {
                    $this->memorize($field, $value);
                }
            }
        }

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
        $on_date = $this->get('on_date');

        if(!$v AND !$kit AND !$status AND !$on_date) {
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

        if($on_date){
            $and->where('greened_on','>=',$on_date);
            $and->where('greened_on','<',$this->api->nextDate($on_date));
        }

        
        if($status){
            if($status =='active'){
                $and->where('greened_on','<>',null);
            }elseif($status =='inactive'){
                $and->where('greened_on',null);
            }
        }
        
        $and->where($or);
        $q->having($and);
    }
}
