<?php

namespace Importer;

use GuzzleHttp\Client;

class ShopwareImporter
{
    private $config;
    private $values = [];
    private $success = [];
    private $failed = [];

    static $required_fields = ["SHOPWARE_URL","SHOPWARE_CLIENT_ID","SHOPWARE_CLIENT_SECRET"];

    public function __construct($configParams)
    {
        foreach(self::$required_fields as $required_field) {
            if(empty($configParams[$required_field])) 
                throw new \Exception(sprintf("Required configuration value '%s' is missing from the configuration file", $required_field));             
        }
        $this->config = $configParams;       
        
    }

    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    public function doImport()
    {
        $defaultTax = 7;
     
        $auth = [$this->config["SHOPWARE_CLIENT_ID"],$this->config["SHOPWARE_CLIENT_SECRET"]];

        $client = new Client();

        $categories = $this->getAsTree();

        foreach($categories as $category) {
            $post = $client->request('POST', $this->config["SHOPWARE_URL"] . 'categories', [
                        'auth' => $auth, 
                        'json' => [
                            "id" => null,
                            "name" => $category["name"],
                            "parentId" => 1
                        ]
                    ]);

            $b =  json_decode($post->getBody()->getContents());
            $category["id"] = $b->data->id;

            foreach($category["products"] as $product) {
                try {
                    $post = $client->request('POST', $this->config["SHOPWARE_URL"] . 'articles', [
                                'auth' => $auth, 
                                'json' => [
                                    "id" => null,
                                    "name" => $product["title"],
                                    "active" => true,
                                    "description" => $product["lang"],
                                    "mainDetail" => [
                                        "number" => rand(),
                                    ],
                                    "categories" => [[
                                        "id" => $category["id"]
                                    ]],
                                    "tax" => [
                                        "tax" => 7
                                    ]
                                ]
                            ]);

                    $b =  json_decode($post->getBody()->getContents());
                    if($b->success == true)
                        $this->success[] = $product;
                    if($b->success == false)
                        throw new \Exception("Product was not added");
                }
                catch(\Exception $e) {
                    $this->failed[] = [
                        "product" => $product,
                        "error" => $e->getMessage()
                    ];
                }
            }

        }

        return $this;
    }

    private function getAsTree(){
        $tree = [];

        foreach($this->values as $value) {
            if(!array_key_exists($value['product_line_area'], $tree)) {
                $tree[$value['product_line_area']] = [
                    "name" => $value['product_line_area'],
                    "id" => null,
                    "products" => []
                ];
            }

            $tree[$value['product_line_area']]["products"][] = $value;
        }

        return array_values($tree);
    }

    public function getTotalCount()
    {
        return count($this->values);
    }

    public function getSuccessCount()
    {
        return count($this->success);
    }

    public function getFailedCount()
    {
        return count($this->failed);
    }
}