<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_PricingComponent tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_PricingComponent;
use Bf_PricingComponentTier;
use BFPHPClientTest\TestConfig;
Class Bf_PricingComponent_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		//$testProductID = '376E2FFB-4C2F-4B49-AAF9-B93CF6A1C9CB';
		$testUomID = $config->getUsualUnitOfMeasureID();
		$testProductRatePlanId = $config->getUsualProductRatePlanID();

		$tier = new Bf_PricingComponentTier(array(
			'lowerThreshold' => 1,
			'upperThreshold' => 1,
			'pricingType' => 'unit',
			'price' => 1,
			));
		$tiers = array($tier);
    	
		$prc = new Bf_PricingComponent(array(
			'@type' => 'flatPricingComponent',
			'productRatePlanID' => $testProductRatePlanId,
			'unitOfMeasureID' => $testUomID,
			'chargeType' => 'subscription',
			'name' => 'Devices used',
			'description' => 'How many devices you use, I guess',
			'upgradeMode' => 'immediate',
			'downgradeMode' => 'immediate',
			'defaultQuantity' => 10,
			'tiers' => $tiers
			));
		$createdPrc = Bf_PricingComponent::create($prc);

		// TODO API: 'productID' should be 'required'.
		// TODO API: received 'you must specify a default quantity' when I tried example request.
		// TODO API: change POST permission to 'admin'
		// TODO API: change POST permission of Bf_PricingComponentTier to 'admin'
		// TODO API 2

		var_export($createdPrc);
	}

	// not actually a one-off; just didn't want to make another class
	/*public function testGetByID() {
		$client = self::$client;
		$config = self::$config;

		$testPrcID = $config->getUsualPricingComponentID();
    	
		$gottenPrc = $client
		->pricingComponents
		->getByID($testPrcID);

		var_export($gottenPrc);
	}*/
}