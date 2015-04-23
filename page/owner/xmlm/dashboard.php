<?php
class page_xMLM_page_owner_xmlm_dashboard extends page_xMLM_page_owner_xmlm_main {
	function init(){
		parent::init();

		
        $cols=$this->add('Columns');
        $col1=$cols->addColumn(4)->addClass('well text-center')->setStyle(array('background-color'=>'#E84765'));       
        $col2=$cols->addColumn(4)->addClass('well text-center')->setStyle(array('background-color'=>'#44AA38'));       
        $col3=$cols->addColumn(4)->addClass('well text-center')->setStyle(array('background-color'=>'#AEB024'));       
        $col1->add('H2')->set('Total Distributors')->addClass('panel panel-default');
        $col2->add('H2')->set('Active Distributors')->addClass('panel panel-default');
        $col3->add('H2')->set('Current PV')->addClass('panel panel-default');

        $col=$this->add('Columns');
        $graph_col=$col->addColumn(6);
        $welcome_col=$col->addColumn(6);

        $graph_col->add('view')->setElement('img')->setAttr(array('src'=>'epan-components/xMLM/templates/images/welcome.png'));
        $welcome_col->add('view')->setElement('img')->setAttr(array('src'=>'epan-components/xMLM/templates/images/dashboard.png'));
        

	}
}