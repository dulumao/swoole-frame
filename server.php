<?php
/**
 * File Description
 * User: Kp
 * Date: 2015/10/19
 * Time: 15:08
 */

//添加自动加载
include 'swoole/load.php';
//定义项目路径
define('APP_PATH' , './application');

use Application\Service\Auth;
use Application\Library\redisCli;
class TcpServer{
    // 配置项
    protected $set;

    public function __construct(){
        // 获取配置参数
        $this->set = include ROOT_PATH . '/config/configure.php';
        // 获取到了配置参数才开启服务
        if($this->set){
            $serv = new swoole_server("0.0.0.0", 9501 , SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
            // 设置配置
            $serv->set($this->set);
            //注册Server的事件回调函数
            $serv->on('Start', array($this , 'onStart'));
            $serv->on('WorkerStart', array($this, 'onWorkerStart'));
            $serv->on('Connect', array($this, 'onConnect'));
            $serv->on('Receive', array($this, 'onReceive'));
            $serv->on('Close', array($this, 'onClose'));
            $serv->on('Shutdown',array($this,'onShutdown'));
            $serv->on('Timer',array($this,'onTimer'));
            $serv->on('Task', array($this, 'onTask'));
            $serv->on('Finish', array($this, 'onFinish'));
            $serv->start();
        }else{
            exit('read configure error!');
        }
    }

    public function onStart(swoole_server $serv){
        echo "Server is Running" . PHP_EOL;
        //管理进程的PID，通过向管理进程发送SIGUSR1信号可实现柔性重启
        echo $serv->manager_pid . PHP_EOL;
        //主进程的PID，通过向主进程发送SIGTERM信号可安全关闭服务器
        echo $serv->master_pid . PHP_EOL;
        //将管理进程的PID写入文件方便管理进程
        file_put_contents(ROOT_PATH . '/run/manager.pid' ,$serv->manager_pid);
        file_put_contents(ROOT_PATH . '/run/master.pid' ,$serv->master_pid);
    }

    public function onWorkerStart(){
//        if($worker_id < $this->set['worker_num']){            //worker进程设置定时器
//            $serv->addtimer(100);
//        }
    }

    // 连接回调
    public function onConnect(swoole_server $serv , $fd){
        Auth::index();
        $redis = redisCli::getInstance()->createRedis();
        echo "Client {$fd} open connection". PHP_EOL;
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data){
        echo 'Receive:'.$fd."\n";
    }

    public function onClose(swoole_server $serv , $fd){
        echo "Close: $fd.\n";
    }

    public function onShutdown(swoole_server $serv){

    }

    public function onTask(swoole_server $serv){

    }

    public function onTimer(swoole_server $serv){

    }

    public function onFinish(swoole_server $serv){

    }
}
new TcpServer();