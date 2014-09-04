<?php
namespace BFPHPClientTest;
echo "Running Bf_Account tests for BillForward PHP Client Library.\n";

use Bf_Account;
Class Bf_AccountTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetAll()
    {	
    	// short alias
    	$client = self::$client;
    	
		$accounts = Bf_Account::getAll();

		$firstAccount = $accounts[0];

		$expected = Bf_Account::getResourcePathStatic()->getEntityName();
		$actual = $firstAccount['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

	public function testGetByID()
    {	
    	// short alias
    	$client = self::$client;
    	$config = self::$config;

    	$loginAccountId = $config
    	->getUsualLoginAccountID();
    	
		$account = Bf_Account::getById($loginAccountId);

		$userID = $account['userID'];

		$expected = $config
    	->getUsualLoginUserID();
		$actual = $userID;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that account's user ID matches known value."
			);
    }

    public function testLoginAccountFound() {
		$client = self::$client;
    	
    	// order by userID so that we are likely to see our login user's account
		$accounts = Bf_Account::getAll(array(
			'order_by' => 'userID'
			));

		$found = NULL;
		foreach ($accounts as $account) {
			if (array_key_exists('userID', $account)) {
				$found = $account
				->id;
				break;
			}
		}

		$this
		->assertFalse(is_null($found), "Asserting that account is found with a userID set.");
	}
}
