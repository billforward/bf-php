<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Payment Method Suscription Link tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_PaymentMethod;
use Bf_PaymentMethodSubscriptionLink;
use Bf_Subscription;
use BFPHPClientTest\TestConfig;
Class Bf_PaymentMethodSubscriptionLink_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		$testSubscriptionID = $config->getUsualSubscriptionID();
		$testPaymentMethodID = $config->getUsualPaymentMethodID();
    	
		$paymentMethodSubscriptionLink = new Bf_PaymentMethodSubscriptionLink($client, array(
		'subscriptionID' => $testSubscriptionID,
		'paymentMethodID' => $testPaymentMethodID,
			));

		$response = $paymentMethodSubscriptionLink
		->create();

		var_export($response);
	}
}