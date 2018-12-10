<?php
namespace app\tasks;

use app\servers\task\Task;

class TestTask extends Task
{
    public function init(){
        # 可以做一些其他的事
        return parent::init();
    }

    public function test($params){
        if($params == 'redis'){
            $this->redis->setex('test_task_key',60,'aaaaaaaaaa');
            return $this->redis->get('test_task_key');
        }

        $res = $this->db->query('select id,user_type from users limit 1');
        return json_encode($res);
    }
}

