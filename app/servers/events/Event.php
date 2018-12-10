<?php
namespace app\servers\events;

# 所有swoole_server事件的基类
use app\servers\helper\ConfigHelper;

class Event
{
    protected $config = [];
    protected $inputBufferMap = [];

    public function __construct()
    {
        $this->config = ConfigHelper::getConfig();
    }
}
