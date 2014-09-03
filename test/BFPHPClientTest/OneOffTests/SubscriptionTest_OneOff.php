<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Subscription tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Subscription;
use BFPHPClientTest\TestConfig;
Class Bf_Subscription_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}


	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		$testProductID = $config->getUsualProductID();
		$testProductRatePlanID = $config->getUsualProductRatePlanID();
		$testAccountID = $config->getUsualAccountID();
    	
    	// creates a new default account
		$sub = new Bf_Subscription(array(
			'type' => 'Subscription',
			'productID' => $testProductID,
			'productRatePlanID' => $testProductRatePlanID,
			'accountID' => $testAccountID,
			'name' => 'Memorable Bf_Subscription',
			));

		// TODO API: why is 'productID' 'required'? surely it can (and should) grab this from PRP. Otherwise user can mismatch them.

		$response = Bf_Subscription::create($sub);
	}

	public function testStart() {
		$client = self::$client;
		$config = self::$config;

		$testSubscriptionID = $config->getUsualSubscriptionID();

		var_export($testSubscriptionID);
    	
    	// creates a new default account
		$sub = Bf_Subscription::getByID($testSubscriptionID);

		if ($sub->state != 'AwaitingPayment') {
			$response = $sub->
			activate();

			var_export($response);
		}
	}



	// TODO API: Bf_Subscription cannot obviously be updated; complains that subscription ID already exists.
	/*
	// This code 
	//-- Make subscription
	$sub = new Bf_Subscription(array(
		'type' => 'Subscription',
		'productID' => $createdProductID,
		'productRatePlanID' => $createdProductRatePlanID,
		'accountID' => $createdAccID,
		'name' => 'Memorable Bf_Subscription',
		'pricingComponentValues' => $pricingComponentValuesArray
		));
	$createdSub = Bf_Subscription::create($sub);

	//-- Make Bf_PaymentMethodSubscriptionLinks
	$paymentMethodSubscriptionLink = new Bf_PaymentMethodSubscriptionLink(array(
		'paymentMethodID' => $createdPaymentMethodID,
		'subscriptionID' => $createdPaymentMethodID,
		'organizationID' => $firstOrgID,
		));
	$paymentMethodSubscriptionLinks = array($paymentMethodSubscriptionLink);

	$createdSub
	->paymentMethodSubscriptionLinks = $paymentMethodSubscriptionLinks;
	$createdSub
	->save();
	*/
}