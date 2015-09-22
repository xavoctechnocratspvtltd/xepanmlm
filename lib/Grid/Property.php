<?php
namespace xMLM;
class Grid_Property extends \Grid{
	function init(){
		parent::init();
	}
	function setModel($model){
		$m= parent::setModel($model);
		if($this->hasColumn('item_name'))$this->removeColumn('item_name');
		if($this->hasColumn('related_document'))$this->removeColumn('related_document');
		if($this->hasColumn('created_by'))$this->removeColumn('created_by');
		if($this->hasColumn('created_date'))$this->removeColumn('created_date');
		if($this->hasColumn('updated_date'))$this->removeColumn('updated_date');
		if($this->hasColumn('item_name'))$this->removeColumn('item_name');
		return $m;		
	}
	function formatRow(){
		parent::formatRow();
	}
}