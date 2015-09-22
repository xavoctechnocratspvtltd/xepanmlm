<?php
namespace xMLM;

class Form_Field_PanNumber extends \Form_Field_Line {
    
    function validate(){
        // empty value is allowed
        $last_name = $this->owner->get('last_name');
        if($this->value!=''){
            if(strtolower($this->value[4]) != strtolower($last_name[0]) OR (strlen($this->value) !=10)){
                $this->displayFieldError('Pan No Does not looks correct ');
            }

            $model = $this->owner->model;

            $check=  $this->add('xMLM/Model_Distributor');
            $check->addCondition('pan_no',$this->value);

            if($model->loaded()){
                $check->addCondition('id','<>',$model->id);
            }

            $check->tryLoadAny();

            if($check->loaded()){
                $this->displayFieldError('Pan no is already used');
            }
        }



        return parent::validate();
    }
}
