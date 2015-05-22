<?php

namespace xMLM;

class Controller_Export extends \AbstractController {
	public $fields=null;
	public $add_sno = true;
	public $totals=array();
	public $row_separator="\n";
	public $column_separator=",";
	public $output=null;
	public $output_type = "text/csv";
	public $output_disposition = "attachment";
    public $output_filename = "export.csv";


	function init(){
		parent::init();
		$grid = $this->owner;
		if(!$this->fields) $this->fields = $grid->model->getActualFields();
		// $this->addHook("output", array($this, "output"));
		
		if($_GET[$this->name]){
			$this->getData();
			$this->output();
		}

		$btn = $grid->addButton('Export');
		$btn->js("click")->univ()->location($this->api->url(null, array($this->name => "1")));
	}

	function getData(){
		// Add Headers First
		$header=array();
		if($this->add_sno)
			$header[] ="Sno";
		foreach ($this->fields as $f) {
			$header[] = "\"" . preg_replace("/\"/", "\"\"", $f) . "\"";
		}
		$this->output= implode($this->column_separator, $header);
		
		// Add Data
		$i=1;
		foreach ($this->owner->model->getRows() as $row) {
            $cols = array();
            
            if($this->add_sno)
            	$cols[] = $i++;

            foreach ($this->fields as $f){
            	// if(!in_array($col, $this->fields)) continue;
            	$col= $row[$f];
                $cols[] = "\"" . preg_replace("/\"/", "\"\"", $col) . "\"";
            }
            if ($this->output){
                $this->output .= $this->row_separator;
            }
            $this->output .= implode($this->column_separator, $cols);
		}
	}

	function setOutput($type=null, $disposition=null, $filename=null){
        if ($type){
            $this->output_type = $type;
        }
        if ($disposition){
            $this->output_disposition = $disposition; // inline or attachment
        }
        if ($filename){
            $this->output_filename = $filename;
        }
    }

    function output(){
    	header("Content-type: " . $this->output_type);
        header("Content-disposition: " . $this->output_disposition . "; filename=\"" . $this->output_filename . "\"");
        header("Content-Length: " . strlen($this->output));
        header("Content-Transfer-Encoding: binary");
        print $this->output;
        exit;
    }
}