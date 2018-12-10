<?php
use app\servers\process\TestProcess;
use app\servers\process\HelloProcess;

$config['process'] = [
    'ipc_type'      => 0,       # 进程间通信模式 详情见swoole process\pool
    'msgqueue_key'  => 1,       # 消息队列的key ipc_type 设置为 SWOOLE_IPC_MSGQUEUE 时生效
    'item'  => [
        '0' => [
            'class'  => TestProcess::class,
            'action' => 'run',
            'name'   => 'testProcess'       #进程名
        ],
        '1' => [
            'class'  => HelloProcess::class,
            'action' => 'word',
            'name'   => 'helloProcess'       #进程名
        ],
    ]
];


