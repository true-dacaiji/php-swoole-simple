<?php
$config['redis'] = [];
$config['redis']['max_pool_number'] = 10;   # 链接次链接最大个数 可不设置 默认5

$config['redis']['test'] = [
    'host' => '192.168.0.230',
    'port' => '7399',
    'select' => 0,          # 可不设置 默认0
    #'password' => '123456'  # 可不设置 默认不使用密码
];


