<?php
namespace app\servers\mysql;

use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;

class SyncMysqlClient
{
    protected $mysqlConfig = [];
    /* @var $syncClient \PDO */
    protected $syncClient;
    /* @var $stem \PDOStatement */
    protected $stem;

    public function __construct($name){
        $config = ConfigHelper::getConfig();
        if(!isset($config['mysql'][$name])){
            throw new EchoException("mysql配置{$name}不存在\n");
        }
        $this->mysqlConfig = $config['mysql'][$name];
        try{
            if(is_object($this->syncClient)){
                return;
            }
            $dsh = "mysql:host={$this->mysqlConfig['host']};port={$this->mysqlConfig['port']};dbname={$this->mysqlConfig['database']}";
            $this->syncClient = new \PDO($dsh,$this->mysqlConfig['user'],$this->mysqlConfig['password']);
        }catch (\Exception $e){
            throw new EchoException("创建同步mysql客户端失败");
        }
    }

    # 关闭同步客户端
    public function closeSync(){
        $this->syncClient = null;
        $this->stem = null;
    }

    public function onBegin($successBack,$errorBack){
        try{
            $this->syncClient->beginTransaction();
            $result = $successBack($this);
            $this->syncClient->commit();
        }catch (\Exception $e){
            $this->syncClient->rollBack();
            if($errorBack != null){
                $result = $errorBack($this,$e);
            }
        }
    }

    /**
     * 该方面不能用于执行查询大量数据的SQL 容易导致内存溢出 需要使用prepare
     * @param $sql string
     * @throws EchoException
     * @return array|bool
     */
    public function query($sql){
        $result = ['affect_rows' => 0,'result' => [],'last_insert_id' => 0];
        try{
            if(strpos($sql,'select') !== false){
                $this->stem = $this->syncClient->query($sql);
                $result['result'] = $this->stem->fetchAll(\PDO::FETCH_ASSOC);
                $this->stem = null;
                return $result;
            }
            $rows = $this->syncClient->exec($sql);
            $result['affect_rows'] = $rows;
            if(strpos($sql,'insert') !== false){
                $result['last_insert_id'] = $this->syncClient->lastInsertId();
            }
        }catch (\Exception $e){
            $stem = null;
            throw new EchoException("PDO Query Error\n".$this->syncClient->errorInfo());
        }
        return $result;
    }

    /**
     * 如果大数据的查询 需要使用这个来处理 返回的是Statement 可自行循环数据处理 避免内存溢出
     * 注意 再使用完Statement后 必须将Statement设置为null 这样保证数据库链接断开
     * @param $sql string
     * @param $bindParams array
     * @throws EchoException
     * @return \PDOStatement
     */
    public function prepare($sql,$bindParams = []){
        try{
            $this->stem = $this->syncClient->prepare($sql);
            $this->stem->execute($bindParams);
            return $this->stem;
        }catch (\Exception $e){
            throw new EchoException("PDO Query Error\n".$this->syncClient->errorInfo());
        }
    }
}

