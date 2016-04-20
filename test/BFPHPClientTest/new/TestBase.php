<?php
namespace BFPHPClientTest;
class TestBase {
	private static $client = NULL;
	private static $initialized = false;
    private static $config = NULL;
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
        if (is_null(self::$config)) {
            $configPath = __DIR__ . "/./config/config.php";
            if (is_file($configPath))
            {
                require_once($configPath);
            } else {
                $configPath = __DIR__ . "/./config/config.example.php";
                require_once($configPath);
            }
            self::$config = $config;
        }
        
        return self::$config;
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

    public static function getSituation($key = NULL) {
        if (is_null(self::$situation)) {
            self::$situation = self::grabSituationalConfig();
        }
        return is_null($key) ? self::$situation : self::$situation[$key];
    }

    private static function setupClient()
    {
    	$credentials = self::grabCredentials();
    	self::$client = new \BillForwardClient($credentials['access_token'], $credentials['urlRoot'], $credentials['curlProxy']);
        \BillForwardClient::setDefaultClient(self::$client);
    }

    public static function getClient()
    {
    	self::initialize();
        return $client;
    }
}	
