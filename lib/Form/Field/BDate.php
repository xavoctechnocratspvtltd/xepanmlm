<?php
namespace xMLM;

class Form_Field_BDate extends \Form_Field_DatePicker {
    
    function validate(){
        // empty value is allowed 
        $diff = $this->api->my_date_diff($this->api->today,$this->value);
        if($diff['years']<18)
            $this->displayFieldError('Applicant age must be above 18 years');

        return parent::validate();
    }
}
