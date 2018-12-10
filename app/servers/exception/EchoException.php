<?php
namespace app\servers\exception;

class EchoException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        echo "程序发生意外：".$message."\n";
        return parent::__construct();
    }
}


