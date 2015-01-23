<?php
namespace BFPHPClientTest;
class TestBase {
	private static $client = NULL;
	private static $initialized = false;

	public static function initialize()
    {
    	if (self::$initialized)
    		return;

    	self::setupClient();
    	self::$initialized = true;
    }

    private static function grabCredentials()
    {
    	$configPath = __DIR__ . "/./config/config.php";
    	if (is_file($configPath))
		{
		    require_once($configPath);
		} else {
			$configPath = __DIR__ . "/./config/config.example.php";
			require_once($configPath);
		}
    	return $credentials;
    }

    private static function setupClient()
    {
    	$credentials = self::grabCredentials();
    	self::$client = new \BillForwardClient($credentials['access_token'], $credentials['urlRoot']);
        \BillForwardClient::setDefaultClient(self::$client);
    }

    public static function getClient()
    {
    	self::initialize();
        return $client;
    }
}	
