<?php
namespace Swoole\Libs;

class router{
	// 对象
	protected static $instance;
	// controller对象
	protected static $controller = array();
	// 路由协议
	protected static $protocol = array();

    final protected function __construct($protocol = array())
    {
    	$this->protocol = $protocol;
    }

    final protected function __clone()
    {
    }

    // 获取单例
    public static function getInstance($protocol)
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($protocol);
        }
        return self::$instance;
    }

	// 路由分发
	public function adapter($code){
		if(isset($this->protocol[$code])){
			$protocol = $this->protocol[$code];
			// 获取控制器和方法
			$c = $protocol['c'];
			$a = $protocol['a'];
			if(isset($this->controller[$code])){
				$controller = $this->controller[$code];
			}else{
				$app_name = explode('/', APP_PATH);
				$class = '\\'.ucfirst($app_name[1]).'\\Controller\\'.$c.'Controller';
				$this->controller[$code] = new $class;
				$controller = $this->controller[$code];
			}
			// 组装action
			$action = $a.'Action';
			$controller->initialize();
			$controller->$action();
		}else{
			$controller = new \Swoole\Controller\Controller();
			$controller->initialize();
			$controller->notFound();
		}
	}
}