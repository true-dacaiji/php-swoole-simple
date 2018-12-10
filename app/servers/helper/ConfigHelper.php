<?php
namespace app\servers\helper;

use app\servers\exception\EchoException;

class ConfigHelper
{
    private static $config = [];

    protected static function loadConfig()
    {
        $path = __DIR__.'/../../config';
        $dirRes = opendir($path);
        while ($file = readdir($dirRes)){
            if($file == '.' || $file == '..' || $file == 'php'){
                continue;
            }
            if(is_dir($path."/".$file)){
                continue;
            }
            $explodeArr = explode('.',$file);
            $suffix = end($explodeArr);
            if($suffix != 'php'){
                continue;
            }
            require $path."/".$file;
        }
        if(isset($config)){
            self::$config = $config;
        }
    }

    public static function getConfig()
    {
        if(empty(self::$config)){
            self::loadConfig();
        }

        return self::$config;
    }

    public static function checkNecessaryConfig()
    {
        if(empty(self::$config)){
            throw new EchoException("没有配置文件..........");
        }
        if(!isset(self::$config['server'])){
            throw new EchoException("server配置不存在..........");
        }
        if(!isset(self::$config['ports'])){
            throw new EchoException("ports配置不存在..........");
        }
        if(!isset(self::$config['system'])){
            throw new EchoException("system配置不存在..........");
        }
    }

    public static function getPortConfigByPort($port)
    {
        if(!isset(self::$config['ports'])){
            throw new EchoException("ports配置不存在..........");
        }

        foreach (self::$config['ports'] as $portArr){
            if($portArr['listen_port'] == $port){
                return $portArr;
            }
        }
        return [];
    }
}
