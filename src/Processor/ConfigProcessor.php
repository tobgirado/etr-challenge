<?php
namespace Processor;

class ConfigProcessor{

    public function getFromParams($params){
            $keys = ["file","url"];
            $config = [];
            
            foreach ($params as $param){                
                foreach($keys as $key){
                    if(substr($param,0,strlen($key)+ 3) == "--".$key."=")
                        $config[$key] = substr($param,strlen($key)+3);
                }
            }
            return $config;
    }
}