<?php
class page_xMLM_page_owner_xmlm_dashboard extends page_xMLM_page_owner_xmlm_main {
    function init(){
		parent::init();

        $date_view = $this->add('View')->set($this->api->now)->addClass('atk-swatch-red atk-align-center atk-size-zetta');
            $form = $this->add('Form')->addClass('atk-box'); 
            $form->addField('DatePicker','change_date');
            $form->addSubmit('Change Date');
        if($form->isSubmitted()){
            $form->api->setDate($form['change_date']);
            $form->js(null,$date_view->js()->reload())->univ()->successMessage('Date Changed')->execute();
        }


       	$container=$this->add('View')->addClass('container');
        $distributor=$this->add('xMLM/Model_Distributor');
        
        $distributor->addExpression('total_left')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'tl'))->addCondition('path','like',$q->concat($q->getField('path'),'A','%'))->count();
        });

        $distributor->addExpression('total_right')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'tr'))->addCondition('path','like',$q->concat($q->getField('path'),'B','%'))->count();
        });

        $distributor->addExpression('green_left')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'gl'))->addCondition('path','like',$q->concat($q->getField('path'),'A','%'))->addCondition('greened_on','<>',null)->count();
        });

        $distributor->addExpression('green_right')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'gr'))->addCondition('path','like',$q->concat($q->getField('path'),'B','%'))->addCondition('greened_on','<>',null)->count();
        });

        $distributor->addExpression('red_left')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'rl'))->addCondition('path','like',$q->concat($q->getField('path'),'A','%'))->addCondition('greened_on',null)->count();
        });

        $distributor->addExpression('red_right')->set(function($m,$q){
            return $m->add('xMLM/Model_Distributor',array('table_alias'=>'rr'))->addCondition('path','like',$q->concat($q->getField('path'),'B','%'))->addCondition('greened_on',null)->count();
        });

        foreach ($this->add('xMLM/Model_Kit') as $kit) {
            $kit_id= $kit->id;
            $distributor->addExpression($this->api->normalizeName($kit['name']).'_left')->set(function($m,$q)use($kit_id){
                return $m->add('xMLM/Model_Distributor',array('table_alias'=>'k'.$kit_id.'left'))->addCondition('path','like',$q->concat($q->getField('path'),'A','%'))->addCondition('kit_item_id',$kit_id)->count();
            });

            $distributor->addExpression($this->api->normalizeName($kit['name']).'_right')->set(function($m,$q)use($kit_id){
                return $m->add('xMLM/Model_Distributor',array('table_alias'=>'k'.$kit_id.'right'))->addCondition('path','like',$q->concat($q->getField('path'),'B','%'))->addCondition('kit_item_id',$kit_id)->count();
            });            
        }

        $distributor->loadLoggedIn();

        if($distributor['greened_on'])
            $swatch='green';
        else
            $swatch='red';

        $welcome_view = $container->add('View')->setHTML(
                "Welcome ".$distributor['name']." <small>[".$distributor['username']."]</small><br/>".
                "<div class='atk-size-reset'>Joined Since " . date('d-M-Y',strtotime($distributor['created_at'])) .'</div>'
                )->addClass("text-center atk-swatch-$swatch atk-size-exa atk-box");
        
        $cols = $container->add('Columns');
        $sponsor_col = $cols->addColumn(4);
        $credit_col = $cols->addColumn(4);
        $introducer_col = $cols->addColumn(4);

        $sponsor_col->add('View')->set('Sponsor: '. $distributor['sponsor'])->addClass($distributor->sponsor()?$distributor->sponsor()->get('greened_on')?'atk-effect-success':'atk-effect-danger':'');
        $introducer_col->add('View')->set('Introducer: '.$distributor['introducer'])
            ->addClass($distributor->introducer()?$distributor->introducer()->get('greened_on')?'atk-effect-success':'atk-effect-danger':'')
            ->addClass('atk-align-right');
        $credit_col->add('View')->set('Credit Balance: '.$distributor['credit_purchase_points'])->addClass('text-center');


        $container->add('View');
        $container->add('HR');
        $count_cols= $container->add('Columns')->addClass('atk-cells');

        $left_col = $count_cols->addColumn(6);
        $right_col = $count_cols->addColumn(6);

        $left_col->add('View')->set('Left')->addClass('atk-swatch-ink atk-size-exa text-center atk-box-small');
        $right_col->add('View')->set('Right')->addClass('atk-swatch-ink atk-size-exa text-center atk-box-small');

        $left_col->add('View')->setHTML('<div class="atk-move-left">Total Distributors: </div><div class="atk-move-right">'.$distributor['total_left'].'</div>')->addClass('atk-clear-fix');
        $left_col->add('View')->setHTML('<div class="atk-move-left">Green Distributors: </div><div class="atk-move-right">'.$distributor['green_left'].'</div>')->addClass('atk-clear-fix');
        $left_col->add('View')->setHTML('<div class="atk-move-left">Red Distributors: </div><div class="atk-move-right">'.$distributor['red_left'].'</div><hr/>')->addClass('atk-clear-fix');
        
        $right_col->add('View')->setHTML('<div class="atk-move-left">Total Distributors: </div><div class="atk-move-right">'.$distributor['total_right'].'</div>')->addClass('atk-clear-fix');
        $right_col->add('View')->setHTML('<div class="atk-move-left">Green Distributors: </div><div class="atk-move-right">'.$distributor['green_right'].'</div>')->addClass('atk-clear-fix');
        $right_col->add('View')->setHTML('<div class="atk-move-left">Red Distributors: </div><div class="atk-move-right">'.$distributor['red_right'].'</div><hr/>')->addClass('atk-clear-fix');

        foreach ($this->add('xMLM/Model_Kit') as $kit) {
            $left_col->add('View')->setHTML('<div class="atk-move-left">'.$kit['name'].': </div><div class="atk-move-right">'.$distributor[$this->api->normalizeName($kit['name']).'_left'].'</div>')->addClass('atk-clear-fix');
            $right_col->add('View')->setHTML('<div class="atk-move-left">'.$kit['name'].': </div><div class="atk-move-right">'.$distributor[$this->api->normalizeName($kit['name']).'_right'].'</div>')->addClass('atk-clear-fix');
        }


	}
}
