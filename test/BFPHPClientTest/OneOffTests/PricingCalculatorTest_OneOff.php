<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_PricingCalculator tests for BillForward PHP Client Library.\n";

use Bf_PricingCalculator;
use Bf_PriceRequest;
use Bf_AmendmentPriceRequest;
use Bf_PricingComponentValue;
use Bf_Subscription;
use BFPHPClientTest\TestConfig;
Class Bf_PricingCalculator_OneOffTest extends \PHPUnit_Framework_TestCase {
    protected static $client = NULL;
    protected static $config = NULL;

    public static function setUpBeforeClass() {
        self::$config = new TestConfig();
        self::$client = self::$config->getClient();
    }

    /*public function testCalculatePriceMinimal() {
    	$config = self::$config;

    	// $accountID = $config->getUsualAccountID();
    	$prpID = $config->getUsualProductRatePlanID();
    	$productID = $config->getUsualProductID();

    	$requestEntity = new Bf_PriceRequest(array(
    		//'accountID' => $accountID,
    		'productRatePlanID' => $prpID,
    		'productID' => $productID
    		));

    	$calculation = Bf_PricingCalculator::requestPriceCalculation($requestEntity);
    }*/

    public function testCalculatePrice() {
        $config = self::$config;

        $accountID = $config->getUsualAccountID();
        $subscriptionID = $config->getUsualSubscriptionID();
        $prpID = $config->getUsualProductRatePlanID();
        $productID = $config->getUsualProductID();
        $flatPricingComponentID = $config->getUsualFlatPricingComponentID();
        $tieredPricingComponentID = $config->getUsualTieredPricingComponentID();

        $subscription = Bf_Subscription::getByID($subscriptionID);

        $newFlatQuantity = 15;
        $newTieredQuantity = 100;
        $flatPricingComponentValue = new Bf_PricingComponentValue(array(
            'pricingComponentID' => $flatPricingComponentID,
            'value' => $newFlatQuantity,
            ));
        $tieredPricingComponentValue = new Bf_PricingComponentValue(array(
            'pricingComponentID' => $tieredPricingComponentID,
            'value' => $newTieredQuantity,
            ));

        $updatedPricingComponentValues = array(
            $flatPricingComponentValue,
            $tieredPricingComponentValue
            );

        $requestEntity = new Bf_PriceRequest(array(
            'accountID' => $accountID,
            'productRatePlanID' => $prpID,
            'productID' => $productID,
            'updatedPricingComponentValues' => $updatedPricingComponentValues
            ));

        //var_export($requestEntity);
        $calculation = Bf_PricingCalculator::requestPriceCalculation($requestEntity);

        var_export($calculation);
    }

    /*public function testCalculatePrice() {
        $config = self::$config;

        $subscriptionID = $config->getUsualSubscriptionID();
        $subscription = Bf_Subscription::getByID($subscriptionID);

        $requestEntity = new Bf_AmendmentPriceRequest(array(
            'subscription' => $subscription
            ));

        $calculation = Bf_PricingCalculator::requestAmendmentPriceAndTime($requestEntity);

        var_export($calculation);
    }*/
}