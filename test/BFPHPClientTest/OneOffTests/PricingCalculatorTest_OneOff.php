<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_PricingCalculator tests for BillForward PHP Client Library.\n";

use Bf_PricingCalculator;
use BFPHPClientTest\TestConfig;
Class Bf_PricingCalculator_OneOffTest extends \PHPUnit_Framework_TestCase {
    protected static $client = NULL;
    protected static $config = NULL;

    public static function setUpBeforeClass() {
        self::$config = new TestConfig();
        self::$client = self::$config->getClient();
    }

    public function testCalculatePrice() {
    	$config = self::$config;

    	$accountID = $config->getUsualAccountID();
    	$prpID = $config->getUsualProductRatePlanID();
    	$productID = $config->getUsualProductID();

    	$calculator = new Bf_PricingCalculator(array(
    		'accountID' => $accountID,
    		'productRatePlanID' => $prpID,
    		'productID' => $productID
    		));

    	$calculation = Bf_PricingCalculator::calculatePrice($calculator);

    	var_export($calculation);
    }
}