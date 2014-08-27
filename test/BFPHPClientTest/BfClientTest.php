<?php
namespace BFPHPClientTest;
echo "Running BfClient tests for BillForward PHP Client Library.\n";

use \BfClient;
use \Account;
Class BfClientTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetByID()
    {	
    	// short alias
    	$client = self::$client;
    	$config = self::$config;

    	$testAccountId = $config->getUsualLoginAccountID();
    	
		$account = $client
		->accounts
		->getById($testAccountId);

		$userID = $account['userID'];

		$expected = $config->getUsualLoginUserID();
		$actual = $userID;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that account's user ID matches known value."
			);
    }
}
