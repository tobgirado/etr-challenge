<?php
namespace Processor;
use Logger\Logger;

class ConfigProcessor{

    public function getFromParams($params){
            $keys = ["file","url"];
            $config = [
                "verbosity" => Logger::VERBOSITY_1
            ];
            
            foreach ($params as $param){ 
                switch($param) {
                    case "-v":
                        $config["verbosity"] = Logger::VERBOSITY_2;
                        break;
                    case "-vv":
                        $config["verbosity"] = Logger::VERBOSITY_3;
                        break;
                    case "-vvv":
                        $config["verbosity"] = Logger::VERBOSITY_4;
                        break;
                    default:
                        foreach($keys as $key){
                            if(substr($param,0,strlen($key)+ 3) == "--".$key."=")
                                $config[$key] = substr($param,strlen($key)+3);
                        }
                }            
            }

            return $config;
    }
}

