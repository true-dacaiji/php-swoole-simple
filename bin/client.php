<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 8888, 5)) {
    exit("connect failed. Error: {$client->errCode}\n");
}

$data = json_encode(['controller' => 'test','action' => 'testTask','data' => 'word'])."\r\n\r\n";
$sendData = pack('N',4 + strlen($data)).$data;
$client->send($sendData);
$response = $client->recv();
$response = substr($response,4,strlen($response));
print_r(json_decode($response,true));
$client->close();


