<?php

$config['ports'] = [];

$config['ports'][] = [
    'socket_type'           => SWOOLE_SOCK_TCP,
    'bind_ip'               => '0.0.0.0',
    'listen_port'           => '8888',
    'pack_class'            => \app\packs\JsonPack::class
];


