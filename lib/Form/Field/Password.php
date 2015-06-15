<?php
namespace xMLM;

class Form_Field_Password extends \Form_Field_Password {
    
    function validate(){
        // empty value is allowed        
        if($this->value!=''){
            if(strlen($this->value) < 6) {
                $this->displayFieldError('Must not be less than 6 characters');
            }
        }
        if($this->value != $this->owner->get('re_password'))
            $this->owner->getElement('re_password')->displayFieldError('must be same as password');
        return parent::validate();
    }

    function render(){
    	$this->js(true)->_load('pwstrength-bootstrap-1.2.5.min')->pwstrength();
    	parent::render();
    }
}
