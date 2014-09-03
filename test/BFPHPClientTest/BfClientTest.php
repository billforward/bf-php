<?php
namespace BFPHPClientTest;
echo "Running BillForwardClient tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Account;
Class BillForwardClientTest extends \PHPUnit_Framework_TestCase {
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
    	
		$account = Bf_Account::getById($testAccountId);

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
