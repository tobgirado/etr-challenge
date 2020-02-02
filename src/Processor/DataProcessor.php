<?php

namespace Processor;

class DataProcessor{
    private $values;
    private $parsedValues;
    static $required_fields = ["title", "product_line_area"];
    static $default_lang = "en";
    static $allowed_langs = ["en", "de"];

    public function __construct($json){
        $this->values = $this->parse($json);
    }

    public function validate(){

        if(!is_array($this->values))
            throw new \Exception("The input should be an array");
        if(count($this->values) == 0)
            throw new \Exception("There are no products to iterate");       
        if(array_keys($this->values) !== range(0, count($this->values) - 1)) 
            throw new \Exception("Input should be an array, object given");   

        foreach($this->values as $i => $value){
            foreach(self::$required_fields as $required_field) {
                if(empty($value[$required_field]))
                    throw new \Exception(sprintf("Entry in index %s is missing the required field '%s'. Received: %s", $i, $required_field, json_encode($value)));
                if(!empty($value["lang"]))
                    if (!in_array($value["lang"], self::$allowed_langs))
                        throw new \Exception(sprintf("Language '%s' in entry in index %s is not allowed. Allowed languages are %s", $value["lang"], $i, join(", ", self::$allowed_langs)));                                                                                        
            }  
            $this->parsedValues[$i] = $value;
            if(empty($value["lang"]))
            $this->parsedValues[$i]["lang"] =self::$default_lang;         
        }
        return $this;
    }

    public function getValues(){
        
        return $this->parsedValues;
    }

    private function parse($json){
        $values =  json_decode($json,true);
        
        if(is_null($values))
            throw new \Exception(sprintf("The following JSON string cannot be decoded: '%s'", $json));
        return $values;
    }

}