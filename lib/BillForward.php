<?php
// Custom SPL autoloader for BillForward SDK

if (!function_exists('curl_init')) {
  throw new Exception('BillForward needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('BillForward needs the JSON PHP extension.');
}

spl_autoload_register(function($className) {
    static $classMap;

    if (!isset($classMap)) {
    	// THESE VARIABLES HAVE BEEN WRITTEN BY MACRO EXPANSION IN BUILD
    	// base dir is directory that contains Billforward.php
    	$baseDir = dirname(__FILE__);
    	// class map points to all classes inside BFPHPClient
        $classMap = array(
            'BfClient' => $baseDir . '/BFPHPClient/BfClient.php',
            'Bf_APIConfiguration' => $baseDir . '/BFPHPClient/APIConfiguration.php',
            'Bf_Account' => $baseDir . '/BFPHPClient/Account.php',
            'Bf_AccountController' => $baseDir . '/BFPHPClient/AccountController.php',
            'Bf_Address' => $baseDir . '/BFPHPClient/Address.php',
            'Bf_AuthorizeNetToken' => $baseDir . '/BFPHPClient/AuthorizeNetToken.php',
            'Bf_BillingEntity' => $baseDir . '/BFPHPClient/BillingEntity.php',
            'Bf_Controller' => $baseDir . '/BFPHPClient/Controller.php',
            'Bf_InsertableEntity' => $baseDir . '/BFPHPClient/InsertableEntity.php',
            'Bf_MutableEntity' => $baseDir . '/BFPHPClient/MutableEntity.php',
            'Bf_Organisation' => $baseDir . '/BFPHPClient/Organisation.php',
            'Bf_OrganisationController' => $baseDir . '/BFPHPClient/OrganisationController.php',
            'Bf_PaymentMethod' => $baseDir . '/BFPHPClient/PaymentMethod.php',
            'Bf_PaymentMethodSubscriptionLink' => $baseDir . '/BFPHPClient/PaymentMethodSubscriptionLink.php',
            'Bf_PricingComponent' => $baseDir . '/BFPHPClient/PricingComponent.php',
            'Bf_PricingComponentController' => $baseDir . '/BFPHPClient/PricingComponentController.php',
            'Bf_PricingComponentTier' => $baseDir . '/BFPHPClient/PricingComponentTier.php',
            'Bf_PricingComponentValue' => $baseDir . '/BFPHPClient/PricingComponentValue.php',
            'Bf_PricingComponentValueChange' => $baseDir . '/BFPHPClient/PricingComponentValueChange.php',
            'Bf_Product' => $baseDir . '/BFPHPClient/Product.php',
            'Bf_ProductController' => $baseDir . '/BFPHPClient/ProductController.php',
            'Bf_ProductRatePlan' => $baseDir . '/BFPHPClient/ProductRatePlan.php',
            'Bf_ProductRatePlanController' => $baseDir . '/BFPHPClient/ProductRatePlanController.php',
            'Bf_Profile' => $baseDir . '/BFPHPClient/Profile.php',
            'Bf_RawAPIOutput' => $baseDir . '/BFPHPClient/BfClient.php',
            'Bf_ResourcePath' => $baseDir . '/BFPHPClient/ResourcePath.php',
            'Bf_Role' => $baseDir . '/BFPHPClient/Role.php',
            'Bf_Subscription' => $baseDir . '/BFPHPClient/Subscription.php',
            'Bf_SubscriptionController' => $baseDir . '/BFPHPClient/SubscriptionController.php',
            'Bf_TaxationLink' => $baseDir . '/BFPHPClient/TaxationLink.php',
            'Bf_UnitOfMeasure' => $baseDir . '/BFPHPClient/UnitOfMeasure.php',
            'Bf_UnitOfMeasureController' => $baseDir . '/BFPHPClient/UnitOfMeasureController.php',

        );
    }

    if (isset($classMap[$className])) {
        require_once $classMap[$className];
    }
});
