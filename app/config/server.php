<?php
# 更多设置见 Swoole 扩展 set方法
$config['server'] = [
    'reactor_num'           => 2,
    'work_num'              => 2,
    'task_worker_num'       => 2,
    'max_request'           => 100,
    'dispatch_mode'         => 1,
    'log_file'              => __DIR__.'/../../bin/log/runtime.log'
];



