<?php
namespace app\servers\events;

use app\packs\IPack;
use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;
use app\servers\helper\Helper;
use app\servers\helper\ObjectHelper;

class ServerEvent extends Event
{
    # 获取到客户端发送的数据时执行的方法
    public function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data)
    {
        try{
            if(isset($this->inputBufferMap[$fd]['data'])){
                $this->inputBufferMap[$fd]['data'] = $this->inputBufferMap[$fd]['data'].$data;
            }else{
                $this->inputBufferMap[$fd]['data'] = $data;
            }

            # 获取端口配置 如果没有移除接收到的所有数据
            $portConfig = ConfigHelper::getPortConfigByPort($server->connection_info($fd)['server_port']);
            if(empty($portConfig)){
                unset($this->inputBufferMap[$fd]);
                return;
            }

            /* @var $pack IPack */
            $pack = ObjectHelper::directLoad($portConfig['pack_class']);
            $input = $pack->decode($this->inputBufferMap[$fd]['data']);
            if($input == -1){
                return;
            }
            elseif($input < -1){
                unset($this->inputBufferMap[$fd]);
                throw new EchoException("解包失败------\n".$this->inputBufferMap[$fd]['data']);
            }
            unset($this->inputBufferMap[$fd]);
            $controllerNamespace = '\\app\\controllers\\'.$input['controller'];
            $controller = ObjectHelper::directLoad($controllerNamespace)->init($server,$fd,$reactor_id,$input);
            $controller->{$input['action']}();

            $controller->__destruct();
        }catch (\Exception $e){
            unset($this->inputBufferMap[$fd]);
            echo "接收数据发生意外---------\n".$e->getMessage();
        }
    }


    public function onStart(\swoole_server $server){
        /* @var $server \swoole_server */
        $pidArr = ['manager_pid' => $server->manager_pid,'master_pid' => $server->master_pid];
        file_put_contents($this->config['system']['base_dir'].'/bin/log/pid.log',json_encode($pidArr));
    }

    public function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        # 移除当前客户端所有数据 一定要记得清理这个
        if(isset($this->inputBufferMap[$fd])){
            unset($this->inputBufferMap[$fd]);
        }
    }

    public function onConnect(\swoole_server $server, int $fd, int $reactorId){

    }

    public function onTask(\swoole_server $server, int $task_id, int $src_worker_id, $data){
        # 这里是发起任务后的回调函数 就是在task进程里面了 开始做任务吧 不能再使用异步IO了 返回字符串或者使用 $server->finish方法表示回调到下方onFinish
        if(!isset($data['class'])){
            return json_encode(['code' => 1,'msg' => '没有调用类']);
        }
        if(!isset($data['action'])){
            return json_encode(['code' => 1,'msg' => '没有调用方法']);
        }

        try{
            $class = ObjectHelper::directLoad($data['class'])->init();
            if(isset($data['params'])){
                $response = $class->{$data['action']}($data['params']);
            }else{
                $response = $class->{$data['action']}();
            }
            $class->__destruct();
        }catch (\Exception $e){
            return json_encode(['code' => 2,'msg' => $e->getMessage(),'line' => $e->getLine(),'file' => $e->getFile()]);
        }

        return $response;
    }

    # 任务进程返回结果时执行的方法
    public function onFinish(\swoole_server $server, int $task_id, string $data){
        # 这里做任务执行完毕的处理 想怎么完就怎么完 这里是work进程里面了 可以使用异步IO
    }

    public function onManagerStart(\swoole_server $server){
        Helper::setProcessName($this->config['system']['process_prefix'].'manager');
    }

    public function onWorkerStart(\swoole_server $server, int $worker_id){
        #require __DIR__.'/../function/function.php';
        getServer()->loadPool();
        if($worker_id >= $server->setting['work_num']) {
            Helper::setProcessName($this->config['system']['process_prefix'].'task');
            return;
        }

        Helper::setProcessName($this->config['system']['process_prefix'].'work');
    }

}