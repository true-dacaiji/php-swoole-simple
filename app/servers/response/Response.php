<?php
namespace app\servers\response;

use app\packs\IPack;
use app\servers\exception\EchoException;
use app\servers\helper\ConfigHelper;
use app\servers\helper\ObjectHelper;

class Response
{
    /* @var \swoole_server */
    private $server;
    private $fd;

    public function init(\swoole_server $server,int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
        return $this;
    }

    public function send($data,$controller = '',$action = '')
    {
        # 获取端口配置 如果没有移除接收到的所有数据
        $portConfig = ConfigHelper::getPortConfigByPort($this->server->connection_info($this->fd)['server_port']);
        if(empty($portConfig)){
            throw new EchoException('向客户端发送数据 没有找到对应端口配置........');
        }

        $responseData['data'] = $data;
        if(!empty($controller)){
            $responseData['controller'] = $controller;
        }
        if(!empty($action)){
            $responseData['action'] = $action;
        }

        /* @var $pack IPack */
        $pack = ObjectHelper::directLoad($portConfig['pack_class']);
        $buffer = $pack->encode(json_encode($data));
        $this->server->send($this->fd,$buffer);
    }

    public function sendTo($fd,$data)
    {
        # 获取端口配置 如果没有移除接收到的所有数据
        $portConfig = ConfigHelper::getPortConfigByPort($this->server->connection_info($this->fd)['server_port']);
        if(empty($portConfig)){
            throw new EchoException('向客户端发送数据 没有找到对应端口配置........');
        }

        $responseData['data'] = $data;
        if(!empty($controller)){
            $responseData['controller'] = $controller;
        }
        if(!empty($action)){
            $responseData['action'] = $action;
        }

        /* @var $pack IPack */
        $pack = ObjectHelper::directLoad($portConfig['pack_class']);
        $buffer = $pack->encode(json_encode($data));
        $this->server->send($fd,$buffer);
    }
}