<?php
namespace xMLM;

class Form_Field_MobileNumber extends \Form_Field_Line {
    
    function validate(){
        // empty value is allowed
        if($this->value!=''){
            if(!preg_match ("/^[\d]{10}$/",$this->value)) {
                $this->displayFieldError('Mobile number must be 10 digits');
            }
            if(!in_array($this->value[0],array(9,8,7))){
				throw $this->displayFieldError("Must start with 9, 8 or 7");
			}
        }
        return parent::validate();
    }
}
