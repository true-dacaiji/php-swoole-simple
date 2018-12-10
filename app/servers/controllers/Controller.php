<?php
namespace app\servers\controllers;

# 所有controller的基类 所有的controller必须基础这个类
use app\servers\helper\ConfigHelper;
use app\servers\helper\ObjectHelper;
use app\servers\mysql\MysqlClient;
use app\servers\redis\RedisClient;
use app\servers\response\Response;

class Controller
{
    /* @var \swoole_server */
    protected $server;

    protected $config = [];

    protected $input;

    protected $fd;

    protected $reactor_id;

    /* @var MysqlClient */
    protected $db;

    /* @var RedisClient */
    protected $redis;

    /* @var Response */
    protected $response;

    public function init(\swoole_server $server,int $fd, int $reactor_id, $input){
        $this->config = ConfigHelper::getConfig();
        $this->server = $server;
        $this->input = $input;
        $this->fd = $fd;
        $this->reactor_id = $reactor_id;

        $this->response = ObjectHelper::directLoad(Response::class)->init($server,$fd);
        $this->db = getServer()->mysqlPool['test']->getClient();
        $this->redis = getServer()->redisPool['test']->getClient()->getClient();
        return $this;
    }

    public function __destruct(){
        getServer()->mysqlPool['test']->pushClient($this->db);
        getServer()->redisPool['test']->pushClient($this->redis);
    }

    /**
     * @param $action string 调用任务的action
     * @param $class string 调用任务的class名
     * @param $params array 自定义数据
     * @param $wordId int 要投递给那个task进程的ID 范围是0 - ($server->task_worker_num -1)
     * @return false|bool
     */
    public function task($action,$class,$params,$wordId = -1){
        $options = [
            'action' => $action,
            'class'  => $class,
            'params' => $params
        ];
        return $this->server->task($options,$wordId);
    }
}