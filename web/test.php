<?php
use MongoDB\Client;

require_once 'autoload.php';
$client = new MongoDB\Driver\Manager("mongodb://datastore.cambridgedev.org:27017");

 

$list_databases = new MongoDB\Driver\Command(["listDatabases" => 1]);

$result = $client->executeCommand("admin", $list_databases);

 

$databases = current($result->toArray());

foreach ($databases->databases as $database) {

  echo "Database Name: " . $database->name . "\n";

}