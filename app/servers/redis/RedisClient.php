<?php
namespace app\servers\redis;

use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;

class RedisClient extends \Swoole\Coroutine\Redis
{
    protected $redisConfig = [];
    public function __construct($name){
        $config = ConfigHelper::getConfig();
        if(!isset($config['redis'][$name])){
            throw new EchoException("redis配置{$name}不存在\n");
        }
        $this->redisConfig = $config['redis'][$name];
        parent::__construct(['timeout' => 3]);
    }

    public function getClient(){
        if($this->connected){
            return $this;
        }
        $this->connect($this->redisConfig['host'],$this->redisConfig['port']);
        if(isset($this->redisConfig['password']) && !empty($this->redisConfig['password'])){
            $this->auth($this->redisConfig['password']);
        }
        $this->select($this->redisConfig['select'] ?? 0);
        return $this;
    }
}

