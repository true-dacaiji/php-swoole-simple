<?php
namespace app\servers\mysql;

use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;

class MysqlClient extends \Swoole\Coroutine\MySQL
{
    protected $mysqlConfig = [];
    /* @var $stem \Swoole\Coroutine\Mysql\Statement */
    protected $stem;

    public function __construct($name){
        $config = ConfigHelper::getConfig();
        if(!isset($config['mysql'][$name])){
            throw new EchoException("mysql配置{$name}不存在\n");
        }
        $this->mysqlConfig = $config['mysql'][$name];
    }


    public function onBegin($successBack,$errorBack){

        if(!$this->connected){
            $this->connect($this->mysqlConfig);
        }
        $res = $this->query('begin');
        if($res === false){
            throw new EchoException("开启事务失败");
        }

        $result = null;
        try{
            $result = $successBack($this);
            $this->query("commit");
        }catch (\Exception $e){
            $this->query("rollback");
            if($errorBack != null){
                $result = $errorBack($this,$e);
            }
        }
        return $result;
    }

    /**
     * 该方面不能用于执行查询大量数据的SQL 容易导致内存溢出 需要使用prepare
     * @param $sql string
     * @param $timeout float
     * @throws EchoException
     * @return array|bool
     */
    public function query($sql, $timeout = 0.0){
        if(!$this->connected){
            $this->connect($this->mysqlConfig);
        }
        $res = parent::query($sql,$timeout);
        if($res === false){
            throw new EchoException("Mysql Query Error \n".$this->error);
        }
        $result['result'] = $res;
        $result['affect_rows'] = $this->affected_rows;
        $result['last_insert_id'] = $this->insert_id;
        return $result;
    }

    /**
     * 如果大数据的查询 需要使用这个来处理 返回的是Statement 可自行循环数据处理 避免内存溢出
     * @param $sql string
     * @param $bindParams array
     * @param $timeout float
     * @throws EchoException
     * @return \PDOStatement|\Swoole\Coroutine\Mysql\Statement|bool
     */
    public function prepare($sql,$bindParams = [],$timeout = 0.0){

        if(!$this->connected){
            $this->connect($this->mysqlConfig);
        }
        $this->stem = parent::prepare($sql);
        if($this->stem === false){
            return false;
        }
        $this->stem->execute($bindParams,$timeout);
        return $this->stem;
    }

}

