<?php

namespace Command;

require 'vendor/autoload.php';

use Processor\ConfigProcessor;
use Processor\DataProcessor;
use GuzzleHttp\Client;
use Importer\ShopwareImporter;

class ShopwareImportCommand
{
  private $config = [];
  private $configParams = [];

  public function configure($params)
  {
    $p = new ConfigProcessor();

    $this->config = $p->getFromParams($params);

    if (empty($this->config["file"]) && empty($this->config["url"]))
      throw new \Exception("One of the following variables need to be set for the command to work: 'file' or 'url'");

    return $this;
  }

  public function setConfigFromFile($config)
  {
    foreach ($config as $key => $value) {
      $this->configParams[$key] = $value;
    }
    
    return $this;
  }

  public function execute()
  {
    $json = null;

    if (!empty($this->config["file"])) {

      if (!file_exists($this->config["file"]))
        throw new \Exception(sprintf("File '%s' not Found", $this->config["file"]));

      $json = file_get_contents($this->config["file"]);
    } else if (!empty($this->config["url"])) {
      $client = new Client();
      $res = $client->request('GET', $this->config["url"]);
      $json = $res->getBody()->getContents();
    }

    $p = new DataProcessor($json);
    $values = $p->validate()
      ->getValues();

    $s = new ShopwareImporter($this->configParams);
    $s->setValues($values)->doImport();
    echo sprintf("The import ran successfully. %s entries were processed, %s were imported, %s failed", $s->getTotalCount(), $s->getSuccessCount(), $s->getFailedCount());
  }
}
