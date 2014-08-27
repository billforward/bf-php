<?php
// Custom SPL autoloader for BillForward SDK

if (!function_exists('curl_init')) {
  throw new Exception('BillForward needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('BillForward needs the JSON PHP extension.');
}

spl_autoload_register(function($className) {
    static $classMap;

    if (!isset($classMap)) {
    	// MACROS TO BE EXPANDED BY BUILD..
    	// base dir is directory that contains Billforward.php
    	$baseDir = %BASEDIR%
    	// class map points to all classes inside BFPHPClient
        $classMap = %CLASSMAP%
    }

    if (isset($classMap[$className])) {
        include $classMap[$className];
    }
});
