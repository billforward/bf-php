<?php
/**
 * Custom SPL autoloader for the BillForward SDK
 *
 * @package BillForward
 */

if (!function_exists('curl_init')) {
  throw new Exception('BillForward needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('BillForward needs the JSON PHP extension.');
}

spl_autoload_register(function($className) {
    static $classMap;

    if (!isset($classMap)) {
        $classMap = require __DIR__ . DIRECTORY_SEPARATOR . 'classmap.php';
    }

    if (isset($classMap[$className])) {
        include $classMap[$className];
    }
});
