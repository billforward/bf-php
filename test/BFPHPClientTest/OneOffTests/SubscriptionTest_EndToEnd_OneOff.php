<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) End-to-End Bf_Subscription tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_Subscription;
use Bf_Product;
use Bf_ProductRatePlan;
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

		$testAccountID = $config->getUsualAccountID();

		$product = new Bf_Product($client, array(
			'productType' => 'non-recurring',
			'state' => 'prod',
			'name' => 'Month of Paracetamoxyfrusebendroneomycin',
			'description' => 'It can cure the common cold, and being struck by lightning',
			'durationPeriod' => 'days',
			'duration' => 28,
			));
		$createdProduct = $product
		->create();

		$testProductID = $createdProduct->id;

		$prp = new Bf_ProductRatePlan($client, array(
			'productID' => $testProductID,
			'currency' => 'USD',
			));
		$createdPrp = $prp
		->create();

		$testProductRatePlanID = $createdPrp->id;
    	
    	// creates a new default account
		$sub = new Bf_Subscription($client, array(
			'type' => 'Subscription',
			'productID' => $testProductID,
			'productRatePlanID' => $testProductRatePlanID,
			'accountID' => $testAccountID,
			'name' => 'Test Bf_Subscription',
			));

		$createdSub = $sub
		->create();

		// if we get this far, there's no errors, right?
	}
}