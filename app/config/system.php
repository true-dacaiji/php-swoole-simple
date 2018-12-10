<?php
use app\servers\events\ServerEvent;

$config['system'] = [
    'base_dir'      => __DIR__.'/../../',
    'process_prefix'=> 'swoole-',
    'event' => [
        # swoole connect事件调用配置
        'connect' => [
            'class'     => ServerEvent::class,
            'action'    => 'onConnect'
        ],
        # swoole close事件调用配置
        'close'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onClose'
        ],
        # swoole start事件调用配置
        'start'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onStart'
        ],
        # swoole receive事件调用配置
        'receive'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onReceive'
        ],
        # swoole task事件调用配置
        'task'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onTask'
        ],
        # swoole finish事件调用配置
        'finish'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onFinish'
        ],
        # swoole managerStart事件调用配置
        'managerStart'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onManagerStart'
        ],
        # swoole workerStart事件调用配置
        'workerStart'   => [
            'class'     => ServerEvent::class,
            'action'    => 'onWorkerStart'
        ],
    ]
];
