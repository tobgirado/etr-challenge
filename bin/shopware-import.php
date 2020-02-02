<?php

require 'vendor/autoload.php';

use Command\ShopwareImportCommand;
use Logger\Logger;

$logger = new Logger();

try {
  if(!file_exists("config/config.php"))
    throw new \Exception("The file 'config/config.php' could not be found. In order to create one, simply copy 'config/config-sample.php'");  
  $config = include("config/config.php");
  
  $i = new ShopwareImportCommand($logger);
  $i->setConfigFromFile($config)
    ->configure($argv)
    ->execute();
}
catch(\Exception $e) {
    $logger->error(sprintf("Exception: %s", $e->getMessage()));
}


