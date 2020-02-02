<?php
require 'vendor/autoload.php';

use Command\ShopwareImportCommand;

try {
  $i = new ShopwareImportCommand();
  $i->configure($argv)
    ->execute();
}
catch(\Exception $e) {
  echo "Exception: ";
  echo $e->getMessage();
}
