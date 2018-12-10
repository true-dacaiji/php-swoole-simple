<?php
namespace app\packs;

class JsonPack implements IPack
{
    /**
     * @param $buffer string
     * @return integer|array -1包不完整 -2解析错误 -3controller不存在 -4action不存在
    */
    public function decode($buffer)
    {
        $explode = explode("\r\n\r\n",$buffer);
        if(count($explode) == 1){
            return -1;
        }

        $data = json_decode(substr($buffer,4),true);
        if(empty($data)){
            return -2;
        }

        if(!isset($data['controller']) || empty($data['controller'])){
            return -3;
        }

        if(!isset($data['action']) || empty($data['action'])){
            return -3;
        }
        return $data;
    }

    public function encode($buffer)
    {
        $sendLength = 4 + strlen($buffer);
        return pack('N',$sendLength).$buffer;
    }
}


