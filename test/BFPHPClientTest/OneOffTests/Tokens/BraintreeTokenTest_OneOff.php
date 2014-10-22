<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Braintree Token tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_BraintreeToken;
use BFPHPClientTest\TestConfig;
Class Bf_BraintreeToken_OneOffTest extends \PHPUnit_Framework_TestCase {
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

		// ID of tokenized card in Braintree
		$cardTokenID = "gr3jmb";
		// ID of customer in Braintree
		$customerID = "22198384";
    	
		$braintreeToken = new Bf_BraintreeToken(array(
			'accountID' => $testAccountID,
			'creditCardID' => $cardTokenID,
			'customerID' => $customerID
			));

		$createdBraintreeToken = Bf_BraintreeToken::create($braintreeToken);

		var_export($createdBraintreeToken);
	}
}