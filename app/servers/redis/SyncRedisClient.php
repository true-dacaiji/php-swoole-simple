<?php
namespace app\servers\redis;

use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;

class SyncRedisClient extends \Redis
{
    protected $redisConfig = [];
    public function __construct($name){
        $config = ConfigHelper::getConfig();
        if(!isset($config['redis'][$name])){
            throw new EchoException("redis配置{$name}不存在\n");
        }
        $this->redisConfig = $config['redis'][$name];
    }

    public function getClient(){
        $redis = new \Redis();
        $redis->connect($this->redisConfig['host'],$this->redisConfig['port']);
        if(isset($this->redisConfig['password']) && !empty($this->redisConfig['password'])){
            $redis->auth($this->redisConfig['password']);
        }
        $redis->select($this->redisConfig['select'] ?? 0);
        return $redis;
    }
}

