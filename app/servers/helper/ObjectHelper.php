<?php
namespace app\servers\helper;

class ObjectHelper
{
    private static $objectPool = [];

    public static function directLoad($class)
    {
        if(isset(self::$objectPool[$class])){
            return self::$objectPool[$class];
        }
        $object = new $class;
        self::$objectPool[$class] = $object;
        unset($object);
        return self::$objectPool[$class];
    }

    public static function initLoad($class)
    {
        if(isset(self::$objectPool[$class])){
            return self::$objectPool[$class]->init();
        }
        $object = new $class;
        self::$objectPool[$class] = $object;
        unset($object);
        return self::$objectPool[$class]->init();
    }

    public static function dropObject($class)
    {
        if(isset(self::$objectPool[$class])){
            unset(self::$objectPool[$class]);
        }
    }

    public static function moveAll()
    {
        self::$objectPool = [];
    }
}


