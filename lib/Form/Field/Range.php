<?php
namespace xMLM;


class Form_Field_Range extends \Form_Field_Line {
    public $min = null;
    public $max = null;

    function setRange($min,$max){
        $this->min = $min;
        $this->max = $max;
        return $this;
    }
   

    function validate(){
        // empty value is allowed
        if($this->value!=''){
            if(!is_numeric($this->value)) {
                $this->displayFieldError('Not a valid number');
            }
            if( ($this->min!==null && $this->value < $this->min) ||
                ($this->max!==null && $this->value > $this->max)){
                $this->displayFieldError('Number not in valid range');
            }

            if($this->value > 100 or $this->value < 0){
                $this->displayFieldError('Nominee age must not be above 100 years');
            }
        }
        return parent::validate();
    }
}
