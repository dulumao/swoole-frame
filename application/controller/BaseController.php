<?php
namespace Application\Controller;

class BaseController extends \Swoole\Controller\Controller{
	public function initialize(){
		$this->checkLogin();
	}

	// 检测是否登录
	protected function checkLogin(){
		
	}
}