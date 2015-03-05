<?php
namespace BFPHPClientTest;
class TestBase {
	private static $client = NULL;
	private static $initialized = false;
    private static $situation = NULL;

	public static function initialize()
    {
    	if (self::$initialized)
    		return;

    	self::setupClient();
        self::getSituation();
    	self::$initialized = true;
    }

    private static function grabConfig() {
        $configPath = __DIR__ . "/./config/config.php";
        if (is_file($configPath))
        {
            require_once($configPath);
        } else {
            $configPath = __DIR__ . "/./config/config.example.php";
            require_once($configPath);
        }
        return $config;
    }

    private static function grabSituationalConfig()
    {
        $config = self::grabConfig();
        return $config['situational'];
    }

    private static function grabCredentials()
    {
    	$config = self::grabConfig();
    	return $config['credentials'];
    }

    public static function getSituation() {
        if (is_null(self::$situation)) {
            self::$situation = self::grabSituationalConfig();
        }
        return self::$situation;
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
