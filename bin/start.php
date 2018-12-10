<?php
require_once './autoload.php';
date_default_timezone_set('Asia/Shanghai');
$service = new \app\service();

if($argc == 1){
    echo " 无效的参数\n command -> start|stop|reload\n start -d 守护进程启动 \n reload 重启所有work与task进程 \n";
    die;
}
if($argv[1] == 'start'){
    if(isset($argv[2])){
        $service->start($argv[2]);
    }else{
        $service->start();
    }
}
elseif($argv[1] == 'stop'){
    $service->stop();
}
elseif($argv[1] == 'reload'){
    $service->reload();
}
else{
    echo " 无效的参数\n command -> start|stop|reload\n start -d 守护进程启动 \n reload 重启所有work与task进程 \n";
    die;
}

