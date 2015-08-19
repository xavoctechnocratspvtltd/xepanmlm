<?php

namespace xMLM;

class Controller_Acl extends \AbstractController {

	function init(){
		parent::init();

		if($this->api->auth->model->isSuperUser()){
			//Add ACL Setting Button if Logegd in User is Super User
			$b=$this->owner->add('Button')->set('ACL Settings')->addClass('atk-swatch-blue');
			$b->add('VirtualPage')
				->bindEvent('ACL Setting','click')
				->set(function($page){
					$acl = $page->add('xMLM/Model_Acl');
					$acl->addCondition('name',$this->api->page);

					$crud = $page->add('CRUD');
					$crud->setModel($acl);
				});
		}else{
			$user_id = $this->api->auth->model->id;
			$employee = $this->add('xHR/Model_Employee');
			$employee->loadFromLogin();

			$acl = $this->add('xMLM/Model_Acl');
			$acl->addCondition('name',$this->api->page);
			$acl->addCondition('employee_id',$employee->id);
			if($acl->count()->getOne() != 1){
				$this->owner->add('View_Error')->set('You are not authorise to access this page');
				throw $this->Exception('','StopInit');
				// throw new \Exception("Not Authorise to access this page");
			}
			
		}



	}	
}