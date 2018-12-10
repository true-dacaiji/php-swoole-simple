<?php
namespace app\controllers;

use app\servers\controllers\Controller;
use app\servers\exception\EchoException;
use app\tasks\TestTask;

class test extends Controller
{
    public function init(\swoole_server $server, int $fd, int $reactor_id, $input){
        # 这里可以做一些初始化、比如重新定义返回response、重新解析input等
        return parent::init($server, $fd, $reactor_id, $input);
    }

    public function testMysql(){
        if(!isset($this->input['data'])){
            $this->input['data'] = ['status' => 1,'msg' => '没有发送数据','data' => []];
            $this->response->send($this->input['data']);
            return;
        }

        $sql = "select id from users limit 1";
        $res = $this->db->query($sql);
        $this->input['data'] = ['status' => 0,'msg' => 'success','data' => $res];
        $this->response->send($this->input);
    }

    public function testRedis(){
        if(!isset($this->input['data'])){
            $this->input['data'] = ['status' => 1,'msg' => '没有发送数据','data' => []];
            $this->response->send($this->input['data']);
            return;
        }

        $this->redis->setex('test_key',60,'aaaaa');

        $this->input['data'] = ['status' => 0,'msg' => 'success','data' => $this->redis->get('test_key')];
        $this->response->send($this->input);
    }

    public function testTask(){
        if(!isset($this->input['data'])){
            $this->input['data'] = ['status' => 1,'msg' => '没有发送数据','data' => []];
            $this->response->send($this->input['data']);
            return;
        }
        $this->task('test',TestTask::class,$this->input['data']);
        $this->input['data'] = ['status' => 0,'msg' => 'success','data' => '已经执行'];
        $this->response->send($this->input);
    }

    public function testDate(){
        $result = ['status' => 0,'msg' => 'success','data' => date("Y-m-d H:i:s")];
        $this->response->send($result,$this->input['controller'],$this->input['action']);
    }

    public function testBegin(){
        $this->db->onBegin(function (){
            $sql = "insert into test(test) values('bbbb')";
            $res = $this->db->query($sql);
            $this->response->send($res,$this->input['controller'],$this->input['action']);
            throw new \Exception("回滚测试");
        },function ($client,$e){
            echo $e->getMessage()."\n";
        });
    }
}

