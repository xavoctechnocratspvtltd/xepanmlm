<?php
namespace xMLM;


class Form_Field_LetterCount extends \Form_Field_Line {
    public $min = null;

    function setRange($min,$max){
        $this->min = $min;
        $this->max = $max;
        return $this;
    }
   

    function validate(){
        // empty value is allowed
        if($this->value!=''){
            if( !(strlen($this->value) >= 3 ) ){
                $this->displayFieldError('Username must be of 3 letters');
            }
        }
        return parent::validate();
    }
}
