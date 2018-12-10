<?php
namespace app\servers\task;

use app\servers\mysql\SyncMysqlClient;
use app\servers\redis\SyncRedisClient;

class Task
{
    /* @var SyncMysqlClient */
    protected $db;
    /* @var SyncRedisClient */
    protected $redis;

    public function init(){
        $this->db = new SyncMysqlClient('test');
        $this->redis = (new SyncRedisClient('test'))->getClient();
        return $this;
    }

    public function __destruct(){
        $this->db->closeSync();
        $this->redis->close();
        $this->redis = null;
        $this->db = null;
    }
}
