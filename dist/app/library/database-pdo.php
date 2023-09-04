<?php
require_once 'secrets.php';

$pdoConnection = new PDO(
  'mysql:dbname=' . SECRET_DB_NAME . ';host=' . SECRET_DB_SERVER . ';port=3306;charset=utf8',
  SECRET_DB_USER,
  SECRET_DB_PASS,
  [
    PDO::ATTR_TIMEOUT                  => 15,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  ]
);

// $pdoConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // without this, it will return ints as strings; but it breaks tests, and the setters in classes stop returning the data type they are told
