<?php
namespace Application\Library;
/**
 * 单例redis
 * User: Kp
 * Date: 2015/10/19
 * Time: 17:20
 */
class redisCli
{
    protected static $instance;
    protected $redis;

    final protected function __construct()
    {
    }

    final protected function __clone()
    {
    }

    // 获取单例
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // 创建redis连接对象
    public function createRedis($host = '127.0.0.1', $port = 6379, $auth = '')
    {
        // 判断是否存在redis
        if (!class_exists('Redis')) {
            throw new Exception("Class Redis not exists");
        }
        if(!isset($this->redis)){
            $this->redis = new \Redis();
            if ($this->redis->pconnect($host, $port)) {
                if (!empty($auth)) {
                    $this->redis->auth($auth);
                }
            }
        }
        return $this->redis;
    }
}