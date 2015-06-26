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
        }
        return parent::validate();
    }
}
