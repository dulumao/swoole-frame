<?php
namespace Swoole\Model;
/**
 * Mongodb模型实现
 */
class MongoModel {
	// 当前mongo连接
	protected $mongo;
	// 当前mongo数据库
	protected $db;
	// 当前mongo集合
	protected $collection = array();
	// 查新更新条件
	protected $condition = array();
	// 查询字段
	protected $field;
	// 查询开始字段
	protected $cursor_start = array();
	// 查询限制长度字段
	protected $cursor_length;
	// 排序
	protected $cursor_sort;

	public function __construct($name , $mongo_server){
		// 回调自定义的初始化方法
		$this->_initialize();    
        try {     
            $this->mongo = new \MongoClient($mongo_server, array('connect'=>true));     
        }catch (MongoConnectionException $e){     
            $this->error = $e->getMessage();     
            return false;     
        }
	}

	// 回调方法 初始化模型
    protected function _initialize() {}

    // 设置字段
    public function field($field,$except=false){
    	if($field){
    		$fields = array();	
    		if($except){
    			foreach ($field as $v) {
    				$fields[$v] = false;
    			}
    		}
    		$this->field = $fields;
    	}else{
    		$this->field = array();
    	}
    	return $this;
    }

    // 设置条件
    public function where($condition = array()){
    	$this->condition = $condition;
    	return $this;
    }

    // 设置序列
    public function order($sort){
    	$this->cursor_sort = $sort;
    	return $this;
    }

    public function limit($start , $length = null){
    	$this->cursor_start = $start;
    	$this->cursor_length = $length;
    	return $this;
    }

    // 查找所有
    public function select(){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection;
    	// 获取集合     
        $cursor = $this->mongo->$db->$collection->find($this->condition , $this->field);  
        if (!empty($this->cursor_start)){     
            $cursor->skip($this->cursor_start);     
        }     
        if (!empty($this->cursor_length)){     
            $cursor->limit($this->cursor_length);     
        }     
        if (!empty($this->cursor_sort)){     
            $cursor->sort($this->cursor_sort);     
        } 
        // 设置结果集    
        $result = array();    
        try {     
            while ($cursor->hasNext()){     
                $result[] = $cursor->getNext();     
            }
        }catch (MongoConnectionException $e){     
            $this->error = $e->getMessage();     
            return false;     
        }catch (MongoCursorTimeoutException $e){     
            $this->error = $e->getMessage();     
            return false;     
        }    
        return $result;
    }

    // 查找满足条件的一条记录
    public function find(){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection; 
    	try {
        	return $this->mongo->$db->$collection->findOne($this->condition, $this->field);  
    	}catch(MongoCursorException $e){
    		$this->error = $e->getMessage();     
            return false;
    	}
    }

    public function count($condition = array()){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection; 
    	if(empty($condition)){
    		$condition = $this->condition;
    	}
    	// 获取结果
    	return $this->mongo->$db->$collection->count($condition); 
    }

    // insert操作
    public function add($record){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection;     
        try {
        	// 执行插入操作     
            $this->mongo->$db->$collection->insert($record);     
            return true;     
        }catch (MongoCursorException $e){     
            $this->error = $e->getMessage();     
            return false;     
        } 
    }

    public function delete($options=array()){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection;  
		// 安全删除 
        $options['safe'] = 1;     
        try {     
            $this->mongo->$db->$collection->remove($this->condition, $options);     
            return true;     
        }catch (MongoCursorException $e){     
            $this->error = $e->getMessage();     
            return false;     
        } 
    }

    // 更新操作
    public function save($data , $options = array()){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection;
    	// 设置安全操作     
        $options['safe'] = 1;     
        if (!isset($options['multiple'])){     
            $options['multiple'] = 0;          
        }     
        try {     
            $this->mongo->$db->$collection->update($this->condition, $data, $options);     
            return true;     
        }catch (MongoCursorException $e){     
            $this->error = $e->getMessage();     
            return false;     
        }
    }

    // 设置单个字段的值
    public function setField($field , $value){
    	$data[$field] = $value;
    	return $this->save($data);
    }

    // 查询单个字段的值
    public function getField($field){
    	// 获取当前数据库
    	$db = $this->db;
    	// 获取当前集合
    	$collection = $this->collection; 
    	try {
        	$result = $this->mongo->$db->$collection->findOne($this->condition, $field);  
        	return isset($result[$field]) ? $result[$field] : null;
    	}catch(MongoCursorException $e){
    		$this->error = $e->getMessage();     
            return false;
    	}
    }

    // 自增加某个字段
    public function setInc($field , $step = 1){

    }


    public function setDec($field , $step = 1){

    }

}