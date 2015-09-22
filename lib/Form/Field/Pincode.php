<?php
namespace xMLM;


class Form_Field_Pincode extends \Form_Field_Line {
   

    function validate(){
        // empty value is allowed
        if($this->value!=''){
            if(!is_numeric($this->value)) {
                $this->displayFieldError('Must be a valid number');
            }
            if(!preg_match ("/^[\d]{6}$/",$this->value)) {
                $this->displayFieldError('Pincode must be 6 digits');
            }
        }
        return parent::validate();
    }
}
