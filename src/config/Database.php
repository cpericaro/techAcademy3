<?php

$env = parse_ini_file(dirname(__DIR__, 2) . '/.env');

try {
  $conn = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']}", $env['DB_USER'], $env['DB_PASSWORD']);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
// https://www.w3schools.com/php/php_mysql_connect.asp
?>