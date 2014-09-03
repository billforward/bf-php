<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_ProductRatePlan tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Subscription;
use Bf_ProductRatePlan;
use Bf_PricingComponent;
use BFPHPClientTest\TestConfig;
Class Bf_ProductRatePlan_OneOffTest extends \PHPUnit_Framework_TestCase {
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
		$testUomID = $config->getUsualUnitOfMeasureID();

		$pricingComponentsArray = array(
			new Bf_PricingComponent(array(
			'@type' => 'flatPricingComponent',
			'chargeModel' => 'flat',
			'name' => 'Devices used',
			'unitOfMeasureID' => $testUomID,
			'chargeType' => 'subscription',
			'upgradeMode' => 'immediate',
			'downgradeMode' => 'immediate',
			'defaultQuantity' => 10,
			))
		);
    	
		$prp = new Bf_ProductRatePlan(array(
			'currency' => 'USD',
			'name' => 'Cool Plan',
			'pricingComponents' => $pricingComponentsArray,
			'productID' => $testProductID,
			));

		//var_export($prp->getSerialized());

		// TODO API: 'productID' should be 'required'.
		// TODO API: 'chargeModel' on pricingComponent should be 'required' (at least in cascade).
		// TODO API: 'defaultQuantity' on pricingComponent should be 'required' in cascade, since it is required otherwise.

		$createdPrp = $prp->create();

		var_export($createdPrp);
	}
}