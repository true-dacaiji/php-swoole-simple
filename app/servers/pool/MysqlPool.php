<?php
namespace app\servers\pool;

use app\servers\helper\ConfigHelper;
use app\servers\mysql\MysqlClient;


class MysqlPool
{
    protected $pool = [];

    protected $config = [];

    public function __construct($name){
        if(getServer()->server->taskworker === true) return;
        $this->config = ConfigHelper::getConfig();
        $max = isset($this->config['mysql']['max_pool_number']) ? 5 : $this->config['mysql']['max_pool_number'];
        $this->pool = new \Swoole\Coroutine\Channel($max);

        for ($i = 0; $i < $max; $i++){
            $this->pool->push(new MysqlClient($name));
        }
    }

    public function getClient(){
        return $this->pool->pop();
    }


    public function pushClient($client){
        return $this->pool->push($client);
    }
}
