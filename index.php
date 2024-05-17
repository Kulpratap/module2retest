<?php
// this file is used for autloading and clean URL
session_start();
require __DIR__ . '/vendor/autoload.php';
use app\php\Config;
new Config();
$requestUri = $_SERVER['REQUEST_URI'];
// Define routes and corresponding PHP files
$str = substr($requestUri, 1);
$urlComponents = explode("/", $str);
if (empty($urlComponents[0])) {
  $urlComponents[0] = "home";
}
$fileName = './app/php/' . $urlComponents[0] . '.php';
if (!file_exists($fileName)) {
  $fileName = './app/database/' . $urlComponents[0] . '.php';
  if (!file_exists($fileName)) {
    $fileName = './app/php/error404.php';
  }
}
include_once ($fileName);
