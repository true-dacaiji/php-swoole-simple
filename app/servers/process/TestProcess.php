<?php
namespace app\servers\process;

class TestProcess
{
    # 运行
    public function run()
    {
        while (true){
            echo "呵呵\n";
            sleep(3);
        }
    }
}

