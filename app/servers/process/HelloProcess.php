<?php
namespace app\servers\process;

class HelloProcess
{
    public function word()
    {
        while (true){
            echo "hello word\n";
            sleep(3);
        }
    }
}
