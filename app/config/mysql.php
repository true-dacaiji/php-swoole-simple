<?php

$config['mysql'] = [];
$config['mysql']['max_pool_number'] = 5;  # 链接次链接最大个数 可不设置 默认5

$config['mysql']['test'] = [
    'host'              => '192.168.0.230',
    'port'              => '3306',
    'user'              => 'root',
    'password'          => 'qingtaokeDB520',
    'database'          => 'dakatui',
];

$config['mysql']['test2'] = [
    'host'      => '127.0.0.1',
    'port'      => '3306',
    'user'      => 'root',
    'password'  => 'test',
    'database'  => 'test2'
];
