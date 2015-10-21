<?php
/**
 * File Description
 * User: Kp
 * Date: 2015/10/19
 * Time: 17:51
 */
//定义项目路径
define('ROOT_PATH' , dirname(__DIR__));
class Loader
{
    /**
     * 自动加载类
     * @param $class 类名
     */
    public static function autoload($class)
    {
        if ($class) {
            $filename = explode('\\' , $class);
            $filepath = rtrim(ROOT_PATH , '/') . '/'  . implode('/' , array_map('strtolower' , $filename)) . '.php';
            if (file_exists($filepath)) {
                include $filepath;
            }
        }
    }
}
/**
 * sql自动加载
 */
spl_autoload_register(array('Loader', 'autoload'));