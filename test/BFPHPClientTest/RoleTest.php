<?php
namespace BFPHPClientTest;
echo "Running Bf_Role tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Account;
Class Bf_RoleTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testRolePrivilege()
    {	
    	// short alias
    	$client = self::$client;
    	$config = self::$config;

    	$testAccountId = $config
    	->getUsualLoginAccountID();
    	
		$account = Bf_Account::getById($testAccountId);

		$roles = $account
		->getRoles();

		$firstRole = $roles[0];

		$rolePrivilege = $firstRole['role'];

		$expected = 'admin';
		$actual = $rolePrivilege;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that role's privilege matches known value."
			);
    }
}

