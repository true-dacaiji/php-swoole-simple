<?php
namespace app;

use app\servers\helper\ConfigHelper;
use app\servers\helper\Helper;
use app\servers\pool\MysqlPool;
use app\servers\pool\RedisPool;

require __DIR__.'/servers/function/function.php';

class service
{
    /* @var self */
    protected static $myServer;
    /* @var \swoole_server */
    public $server;

    public $mysqlPool;

    public $redisPool;

    public function __construct(){
        self::$myServer = &$this;
    }

    public function start($daemonize = ''){
        $config = ConfigHelper::getConfig();
        ConfigHelper::checkNecessaryConfig();
        Helper::setProcessName($config['system']['process_prefix'].'master');
        $port = $config['ports'][0];
        $this->server = new \swoole_server($port['bind_ip'],$port['listen_port'],SWOOLE_PROCESS,$port['socket_type']);
        if($daemonize == '-d'){
            $config['server']['daemonize'] = 1;
        }

        # 加载链接池
        #$this->loadPool();
        $this->server->set($config['server']);
        foreach ($config['system']['event'] as $event => $item){
            $eventObj = new $item['class'];
            $this->server->on($event,[$eventObj,$item['action']]);
        }
        $this->server->start();
    }

    public function loadPool(){
        # 加载mysql连接池
        $this->setMysqlPool('test',MysqlPool::class);
        # 加载一个redis链接池
        $this->setRedisPool('test',RedisPool::class);
    }

    protected function setMysqlPool($name,$class){
        if(!isset($this->mysqlPool[$name])){
            $this->mysqlPool[$name] = new $class($name);
        }
    }

    protected function setRedisPool($name,$class){
        if(!isset($this->redisPool[$name])){
            $this->redisPool[$name] = new $class($name);
        }
    }

    public function stop(){
        $pidStr = file_get_contents(__DIR__.'/../bin/log/pid.log');
        if(empty($pidStr)){
            echo "没有启动进程\n";
            return;
        }

        $pidArr = json_decode($pidStr,true);
        posix_kill($pidArr['manager_pid'],SIGTERM);
        posix_kill($pidArr['master_pid'],SIGKILL);
    }

    public function reload(){
        $pidStr = file_get_contents(__DIR__.'/../bin/log/pid.log');
        if(empty($pidStr)){
            echo "没有启动进程\n";
            return;
        }

        $pidArr = json_decode($pidStr,true);
        posix_kill($pidArr['manager_pid'],SIGUSR1);
    }

    public static function &getInstance(){
        return self::$myServer;
    }
}

