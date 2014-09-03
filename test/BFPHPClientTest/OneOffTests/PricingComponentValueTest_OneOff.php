<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_PricingComponent tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Subscription;
use Bf_PricingComponent;
use Bf_PricingComponentValue;
use BFPHPClientTest\TestConfig;
Class Bf_PricingComponentValue_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	/*public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		//$testProductID = '376E2FFB-4C2F-4B49-AAF9-B93CF6A1C9CB';
		$testPricingComponentId = $config->getUsualPricingComponentID();
    	
		$prc = new Bf_PricingComponentValue($client, [
			//'@type' => 'flatPricingComponent',
			'pricingComponentID' => $testPricingComponentId,
			'value' => 2,
			]);
		$createdPrcv = $prc
		->create();

		// TODO API: 'productID' should be 'required'.
		// TODO API: received 'you must specify a default quantity' when I tried example request.

		var_export($createdPrcv);
	}*/

	// not actually a one-off; just didn't want to make another class
	public function testGetByID() {
		$client = self::$client;
		$config = self::$config;

		$subID = $config->getUsualSubscriptionID();

		$gottenSub = Bf_Subscription::getByID($subID);

		var_export($gottenSub);
    	
		$gottenPrc = $gottenSub
		->pricingComponentValues[0];

		var_export($gottenPrc);
	}
}