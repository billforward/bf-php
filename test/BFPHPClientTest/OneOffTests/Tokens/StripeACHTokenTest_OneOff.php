<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Stripe ACH Token tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_StripeACHToken;
use BFPHPClientTest\TestConfig;
Class Bf_StripeToken_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		$testAccountID = $config->getUsualAccountID();

		$bankAccountID = "btok_123123";
		$customerID = "cus_506rwzFR2JnBGK";
		$hasBeenVerified = false;
    	
		$stripeToken = new Bf_StripeACHToken(array(
			'accountID' => $testAccountID,
			'bankAccountID' => $bankAccountID,
			'stripeCustomerID' => $customerID,
			'verified' => $hasBeenVerified
			));

		$createdStripeToken = Bf_StripeACHToken::create($stripeToken);

		var_export($createdStripeToken);
	}
}