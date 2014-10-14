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
            'Bf_APIConfiguration' => $baseDir . '/BFPHPClient/Entities/Organisation/APIConfiguration.php',
            'Bf_Account' => $baseDir . '/BFPHPClient/Entities/Account/Account.php',
            'Bf_Address' => $baseDir . '/BFPHPClient/Entities/Account/Address.php',
            'Bf_Amendment' => $baseDir . '/BFPHPClient/Entities/Amendments/Amendment.php',
            'Bf_AmendmentDiscardAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/AmendmentDiscardAmendment.php',
            'Bf_AmendmentPriceRequest' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/AmendmentPriceRequest.php',
            'Bf_AmendmentPriceRequestCodeType' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/AmendmentPriceRequestCodeType.php',
            'Bf_AuthorizeNetToken' => $baseDir . '/BFPHPClient/Entities/Tokens/AuthorizeNetToken.php',
            'Bf_BillingEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/BillingEntity.php',
            'Bf_CancellationAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/CancellationAmendment.php',
            'Bf_ComponentCost' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/ComponentCost.php',
            'Bf_ComponentDiscount' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/ComponentDiscount.php',
            'Bf_InsertableEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/InsertableEntity.php',
            'Bf_Invoice' => $baseDir . '/BFPHPClient/Entities/Invoice/Invoice.php',
            'Bf_InvoiceLine' => $baseDir . '/BFPHPClient/Entities/Invoice/InvoiceLine.php',
            'Bf_InvoiceNextExecutionAttemptAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/InvoiceNextExecutionAttemptAmendment.php',
            'Bf_InvoicePayment' => $baseDir . '/BFPHPClient/Entities/Invoice/InvoicePayment.php',
            'Bf_InvoiceRecalculationAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/InvoiceRecalculationAmendment.php',
            'Bf_IssueInvoiceAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/IssueInvoiceAmendment.php',
            'Bf_MutableEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/MutableEntity.php',
            'Bf_Organisation' => $baseDir . '/BFPHPClient/Entities/Organisation/Organisation.php',
            'Bf_PaymentMethod' => $baseDir . '/BFPHPClient/Entities/Account/PaymentMethod.php',
            'Bf_PaymentMethodSubscriptionLink' => $baseDir . '/BFPHPClient/Entities/Subscription/PaymentMethodSubscriptionLink.php',
            'Bf_PriceCalculation' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/PriceCalculation.php',
            'Bf_PriceRequest' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/PriceRequest.php',
            'Bf_PricingCalculator' => $baseDir . '/BFPHPClient/PricingCalculator.php',
            'Bf_PricingComponent' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponent.php',
            'Bf_PricingComponentTier' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentTier.php',
            'Bf_PricingComponentValue' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentValue.php',
            'Bf_PricingComponentValueChange' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentValueChange.php',
            'Bf_Product' => $baseDir . '/BFPHPClient/Entities/Product.php',
            'Bf_ProductRatePlan' => $baseDir . '/BFPHPClient/Entities/Subscription/ProductRatePlan.php',
            'Bf_Profile' => $baseDir . '/BFPHPClient/Entities/Account/Profile.php',
            'Bf_RawAPIOutput' => $baseDir . '/BFPHPClient/BillForwardClient.php',
            'Bf_ResourcePath' => $baseDir . '/BFPHPClient/Entities/Abstract/ResourcePath.php',
            'Bf_Role' => $baseDir . '/BFPHPClient/Entities/Account/Role.php',
            'Bf_RuleSatisfaction' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/RuleSatisfaction.php',
            'Bf_Subscription' => $baseDir . '/BFPHPClient/Entities/Subscription/Subscription.php',
            'Bf_TaxLine' => $baseDir . '/BFPHPClient/Entities/Invoice/TaxLine.php',
            'Bf_TaxationLink' => $baseDir . '/BFPHPClient/Entities/Invoice/TaxationLink.php',
            'Bf_UnitOfMeasure' => $baseDir . '/BFPHPClient/Entities/UnitOfMeasure.php',
            'BillForwardClient' => $baseDir . '/BFPHPClient/BillForwardClient.php',

        );
    }

    if (isset($classMap[$className])) {
        require_once $classMap[$className];
    }
});
