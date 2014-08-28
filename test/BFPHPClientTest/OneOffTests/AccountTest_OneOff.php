<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Account tests for BillForward PHP Client Library.\n";

use Bf_Account;
use BFPHPClientTest\TestConfig;
Class Bf_Account_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
    	
    	// creates a new default account
		$account = new Bf_Account($client);

		$response = $account
		->create();
	}

	public function testCreateWithController() {
		$client = self::$client;
    	
    	// creates a new default account
    	$createdAccount = $client
    	->accounts
    	->create();
	}
}