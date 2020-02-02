<?php

namespace Importer;

use GuzzleHttp\Client;

class ShopwareImporter
{
    private $values = [];
    private $success = [];
    private $failed = [];

    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }


    private function nameExists($value, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['name'] === $value) {
                return true;
            }
        }
        return false;
    }
    private function getIdFromArray($name, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['name'] === $name) {
                return $val["id"];
            }
        }
        return null;
    }
    public function doImport()
    {
        $defaultTax = 7;
        $user = "demo";
        $secret = "F8pkhFxaRDAheFsQvRHrkQ9Q0l06E5Mi8zT0XsZ0";

        $auth = [$user, $secret];

        $client = new Client();

        $cat = array();
        $prod = array();
        foreach ($this->values as $i => $values) {
            if (!self::nameExists($values["product_line_area"], $cat))
                array_push($cat, ["id" => null, "name" => $values["product_line_area"], "parentId" => 1]);
        };

        foreach ($cat as  $i => $category) {
            $post = $client->request('POST', 'http://localhost:8012/api/categories', ['auth' => $auth, 'json' => $category]);
            $b =  json_decode($post->getBody()->getContents());
            $cat[$i]["id"] = $b->data->id;
        };

        foreach ($this->values as $values) {
            array_push($prod, [
                "id" => null,
                "name" => $values["title"],
                "active" => true,
                "description" => $values["lang"],
                "mainDetail" => [
                    "number" => rand(),
                ],
                "categories" => [[
                    "id" => self::getIdFromArray($values["product_line_area"], $cat)
                ]],
                "tax" => [
                    "tax" => 7
                ]
            ]);
        };

        foreach ($prod as  $i => $product) {
            $post = $client->request('POST', 'http://localhost:8012/api/articles', ['auth' => $auth, 'json' => $product]);
            $b =  json_decode($post->getBody()->getContents());
            if($b->success == true)
                array_push($this->success,$product);
            if($b->success == false)    
                array_push($this->failed,$product);

            $prod[$i]["id"] = $b->data->id;
        };

        return $this;
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
