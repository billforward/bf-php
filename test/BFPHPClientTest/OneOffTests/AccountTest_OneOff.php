<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Account tests for BillForward PHP Client Library.\n";

use Bf_Account;
use Bf_Profile;
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
		$account = new Bf_Account();

		$response = Bf_Account::create($account);
	}

	public function testCreateWithProfile()
    {	
    	// creates a new default account
    	$profile = new Bf_Profile(array(
    		));
		$account = new Bf_Account(array(
			'profile' => $profile
			));

    	var_export($account); print "\n";

    	$createdAccount = Bf_Account::create($account);

    	var_export($createdAccount); print "\n";

    	// TODO API: API should not ignore our profile when it has no params!
    }
}