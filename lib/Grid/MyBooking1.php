<?php
namespace xMLM;
class Grid_MyBooking extends \Grid{
	function init(){
		parent::init();

		

		
	}
	function setModel($model){
		$m= parent::setModel($model);

		$this->addColumn('from_date_1');
		$this->addColumn('from_date_2');
		$this->addColumn('from_date_2');
		$this->addColumn('to_date_1');
		$this->addColumn('to_date_2');
		$this->addColumn('to_date_2');
		$this->addColumn('destination_1');
		$this->addColumn('destination_2');
		$this->addColumn('destination_3');
		$this->addColumn('adults_1');
		$this->addColumn('adults_2');
		$this->addColumn('adults_3');
		$this->addColumn('children_1');
		$this->addColumn('children_2');
		$this->addColumn('children_3');
		$this->addColumn('status_1');
		$this->addColumn('status_2');
		$this->addColumn('status_3');
		$this->addColumn('Prefrences1');
		$this->addColumn('Prefrences2');
		$this->addColumn('Prefrences3');
		$this->addColumn('Button','edit');
		$this->addColumn('Button','cancel',array('icon'=>'user','descr'=>'cancel'));
		
		$this->addFormatter('Prefrences1','wrap');
		$this->addFormatter('Prefrences2','wrap');
		$this->addFormatter('Prefrences3','wrap');
		
		$this->removeColumn('from_date_1');
		$this->removeColumn('from_date_2');
		$this->removeColumn('from_date_3');
		$this->removeColumn('to_date_1');
		$this->removeColumn('to_date_2');
		$this->removeColumn('to_date_3');
		$this->removeColumn('destination_1');
		$this->removeColumn('destination_2');
		$this->removeColumn('destination_3');
		$this->removeColumn('adults_1');
		$this->removeColumn('adults_2');
		$this->removeColumn('adults_3');
		$this->removeColumn('children_1');
		$this->removeColumn('children_2');
		$this->removeColumn('children_3');
		$this->removeColumn('status_1');
		$this->removeColumn('status_2');
		$this->removeColumn('status_3');
	
		$this->addQuickSearch('from_date','to_date');
		return $m;
	}
	function formatRow(){
		$status='rejected';
		if($this->model['status_2']=='availed')  $status= "atk-effect-success";

		$this->current_row_html['Prefrences1']="From Date.: " .$this->model['from_date_1']."&nbsp&nbsp&nbsp"."To Date.:".$this->model['to_date_1']."<br/>
											<span class='atk-move-right'> Destination.:".$this->model['destination_1']."</span><br/> 
											 Adults.: ".$this->model['adults_1']."  "."Child.: ".$this->model['children_1'].'<br/>
											<span class="atk-move-right"> Status.: '.$this->model['status_1'].'</span>';
		$this->current_row_html['Prefrences2']="From Date.: " .$this->model['from_date_2']."&nbsp&nbsp&nbsp"."To Date.:".$this->model['to_date_2']."<br/>
											<span class='atk-move-right'> Destination.:".$this->model['destination_2']."</span><br/> 
											 Adults.: ".$this->model['adults_2']."  "."Child.: ".$this->model['children_2'].'<br/>
											<span class="atk-move-right"> Status.: <span class="'.$status.'">'.$this->model['status_2'].'</span></span>';
		$this->current_row_html['Prefrences3']="From Date.: " .$this->model['from_date_3']."&nbsp&nbsp&nbsp"."To Date.:".$this->model['to_date_3']."<br/>
											<span class='atk-move-right'> Destination.:".$this->model['destination_3']."</span><br/> 
											 Adults.: ".$this->model['adults_3']."  "."Child.: ".$this->model['children_3'].'<br/>
											<span class="atk-move-right"> Status.: '.$this->model['status_3'].'</span>';
		parent::formatRow();
	}
}