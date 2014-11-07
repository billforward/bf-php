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
use Bf_ProductRatePlan;
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

    /*public function testCalculatePrice() {
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
    }*/

    /*public function testCalculatePriceNewUser() {
        $config = self::$config;

        $productRatePlanID = $config->getUsualProductRatePlanID();

        $productRatePlan = Bf_ProductRatePlan::getByID($productRatePlanID);
        $productID = $productRatePlan->productID;

        // find pricing component by name
        $flatPricingComponent = $productRatePlan->getPricingComponentWithName('Devices used, fixed');
        $tieredPricingComponent = $productRatePlan->getPricingComponentWithName('Devices used, tiered');

        $newFlatQuantity = 15;
        $flatPricingComponentValue = new Bf_PricingComponentValue(array(
            'pricingComponentID' => $flatPricingComponent->id,
            'value' => $newFlatQuantity,
            ));
        $newTieredQuantity = 100;
        $tieredPricingComponentValue = new Bf_PricingComponentValue(array(
            'pricingComponentID' => $tieredPricingComponent->id,
            'value' => $newTieredQuantity,
            ));

        $updatedPricingComponentValues = array(
            $flatPricingComponentValue,
            $tieredPricingComponentValue
            );

        $requestEntity = new Bf_PriceRequest(array(
            'productRatePlanID' => $productRatePlanID,
            'productID' => $productID,
            'updatedPricingComponentValues' => $updatedPricingComponentValues
            ));

        //var_export($requestEntity);
        $calculation = Bf_PricingCalculator::requestPriceCalculation($requestEntity);

        var_export($calculation);
    }*/

    public function testCalculatePriceNewUserWithHelper() {
        $config = self::$config;

        $productRatePlanID = $config->getUsualProductRatePlanID();

        $componentNameToValueMap = array(
            'Devices used, fixed' => 15,
            'Devices used, tiered' => 100,
            );

        $requestEntity = Bf_PriceRequest::forPricingComponentsByName($componentNameToValueMap, $productRatePlanID);

        //var_export($requestEntity);
        $calculation = Bf_PricingCalculator::requestPriceCalculation($requestEntity);

        var_export($calculation);
    }

    public function testCalculateUpgradePriceWithHelper() {
        $config = self::$config;

        $subscriptionID = '549790F5-E2DE-4BB4-A75A-A8D17EDF3C06';

        // map pricing component names to values
        $componentNameToValueMap = array(
            'CPU' => 10,
            'Bandwidth' => 20
            );

        $requestEntity = Bf_AmendmentPriceRequest::forPricingComponentsByName($componentNameToValueMap, $subscriptionID);

        $requestEntity->printJson();
        $calculation = Bf_PricingCalculator::requestUpgradePrice($requestEntity);

        var_export($calculation);
    }

    /*public function testCalculatePriceNewUserWithHelper2() {
        $config = self::$config;

        $productRatePlanID = $config->getUsualProductRatePlanID();
        $productRatePlan = Bf_ProductRatePlan::getByID($productRatePlanID); 

        $componentNameToValueMap = array(
            'Devices used, fixed' => 15,
            'Devices used, tiered' => 100,
            );

        $requestEntity = Bf_PriceRequest::forPricingComponentsByName($componentNameToValueMap, null, $productRatePlan);

        //var_export($requestEntity);
        $calculation = Bf_PricingCalculator::requestPriceCalculation($requestEntity);

        var_export($calculation);
    }*/

    /*public function unfinishedTestCalculatePrice() {
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