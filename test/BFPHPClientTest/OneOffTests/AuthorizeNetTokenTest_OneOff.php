<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Payment Method tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_AuthorizeNetToken;
use BFPHPClientTest\TestConfig;
Class Bf_PaymentMethod_OneOffTest extends \PHPUnit_Framework_TestCase {
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

		$customerProfileID = 28476855;
		$customerPaymentProfileID = 25879733;
		// err, didn't check what the actual card last 4 digits are. but this only matters at refund-time.
		$cardLast4Digits = 4444;
    	
		$authorizeNetToken = new Bf_AuthorizeNetToken(array(
			'accountID' => $testAccountID,
			'customerProfileID' => $customerProfileID,
			'customerPaymentProfileID' => $customerPaymentProfileID,
			'lastFourDigits' => $cardLast4Digits,
			));

		// TODO API: make 'lastFourDigits' required, as this is needed for refunds.

		$createdAuthorizeNetToken = $authorizeNetToken
		->create();

		var_export($createdAuthorizeNetToken);
	}
}