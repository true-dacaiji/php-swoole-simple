<?php

namespace app\servers\helper;

class Helper
{
    public static function setProcessName($name){
        if(function_exists('cli_set_process_title')){
            cli_set_process_title($name);
            return;
        }
        swoole_set_process_name($name);
    }
}
