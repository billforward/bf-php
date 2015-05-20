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
            'Bf_APIErrorResponseException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_APIException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_Account' => $baseDir . '/BFPHPClient/Entities/Account/Account.php',
            'Bf_AddCouponCodeRequest' => $baseDir . '/BFPHPClient/Entities/Coupon/backend/AddCouponCodeRequest.php',
            'Bf_Address' => $baseDir . '/BFPHPClient/Entities/Account/Address.php',
            'Bf_Amendment' => $baseDir . '/BFPHPClient/Entities/Amendments/Amendment.php',
            'Bf_AmendmentDiscardAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/AmendmentDiscardAmendment.php',
            'Bf_AmendmentPriceNTime' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/AmendmentPriceNTime.php',
            'Bf_AmendmentPriceRequest' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/AmendmentPriceRequest.php',
            'Bf_AmendmentPriceRequestCodeType' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/AmendmentPriceRequestCodeType.php',
            'Bf_AuthorizeNetToken' => $baseDir . '/BFPHPClient/Entities/Tokens/AuthorizeNetToken.php',
            'Bf_BillingEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/BillingEntity.php',
            'Bf_BraintreeToken' => $baseDir . '/BFPHPClient/Entities/Tokens/BraintreeToken.php',
            'Bf_CancellationAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/CancellationAmendment.php',
            'Bf_ComponentChange' => $baseDir . '/BFPHPClient/Entities/PricingComponent/ComponentChange.php',
            'Bf_ComponentCost' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/ComponentCost.php',
            'Bf_ComponentDiscount' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/ComponentDiscount.php',
            'Bf_Coupon' => $baseDir . '/BFPHPClient/Entities/Coupon/Coupon.php',
            'Bf_CouponDiscount' => $baseDir . '/BFPHPClient/Entities/Coupon/CouponDiscount.php',
            'Bf_CouponUniqueCodesResponse' => $baseDir . '/BFPHPClient/Entities/Coupon/backend/CouponUniqueCodesResponse.php',
            'Bf_CreditNote' => $baseDir . '/BFPHPClient/Entities/CreditNote.php',
            'Bf_EmptyArgumentException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_EntityLacksIdentifierException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_GetCouponsRequest' => $baseDir . '/BFPHPClient/Entities/Coupon/backend/GetCouponsRequest.php',
            'Bf_InsertableEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/InsertableEntity.php',
            'Bf_InvocationException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_Invoice' => $baseDir . '/BFPHPClient/Entities/Invoice/Invoice.php',
            'Bf_InvoiceExecutionRequest' => $baseDir . '/BFPHPClient/Entities/SyncRequests/InvoiceExecutionRequest.php',
            'Bf_InvoiceLine' => $baseDir . '/BFPHPClient/Entities/Invoice/InvoiceLine.php',
            'Bf_InvoiceNextExecutionAttemptAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/InvoiceNextExecutionAttemptAmendment.php',
            'Bf_InvoicePayment' => $baseDir . '/BFPHPClient/Entities/Invoice/InvoicePayment.php',
            'Bf_InvoiceRecalculationAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/InvoiceRecalculationAmendment.php',
            'Bf_InvoiceRecalculationRequest' => $baseDir . '/BFPHPClient/Entities/SyncRequests/InvoiceRecalculationRequest.php',
            'Bf_IssueInvoiceAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/IssueInvoiceAmendment.php',
            'Bf_MalformedEntityReferenceException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_MalformedInputException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_MigrationRequest' => $baseDir . '/BFPHPClient/Entities/SyncRequests/MigrationRequest.php',
            'Bf_MigrationResponse' => $baseDir . '/BFPHPClient/Entities/SyncResponses/MigrationResponse.php',
            'Bf_MutableEntity' => $baseDir . '/BFPHPClient/Entities/Abstract/MutableEntity.php',
            'Bf_NoAPIResponseException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_NoMatchingEntityException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_Notification' => $baseDir . '/BFPHPClient/Entities/Notification/Notification.php',
            'Bf_Organisation' => $baseDir . '/BFPHPClient/Entities/Organisation/Organisation.php',
            'Bf_PauseRequest' => $baseDir . '/BFPHPClient/Entities/SyncRequests/PauseRequest.php',
            'Bf_PaymentMethod' => $baseDir . '/BFPHPClient/Entities/Account/PaymentMethod.php',
            'Bf_PaymentMethodSubscriptionLink' => $baseDir . '/BFPHPClient/Entities/Subscription/PaymentMethodSubscriptionLink.php',
            'Bf_PreconditionFailedException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_PriceCalculation' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/PriceCalculation.php',
            'Bf_PriceRequest' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/PriceRequest.php',
            'Bf_PricingCalculator' => $baseDir . '/BFPHPClient/PricingCalculator.php',
            'Bf_PricingComponent' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponent.php',
            'Bf_PricingComponentMigrationValue' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentMigrationValue.php',
            'Bf_PricingComponentTier' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentTier.php',
            'Bf_PricingComponentValue' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentValue.php',
            'Bf_PricingComponentValueAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/PricingComponentValueAmendment.php',
            'Bf_PricingComponentValueChange' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentValueChange.php',
            'Bf_PricingComponentValueMigrationAmendmentMapping' => $baseDir . '/BFPHPClient/Entities/PricingComponent/PricingComponentValueMigrationAmendmentMapping.php',
            'Bf_Product' => $baseDir . '/BFPHPClient/Entities/Product/Product.php',
            'Bf_ProductRatePlan' => $baseDir . '/BFPHPClient/Entities/Product/ProductRatePlan.php',
            'Bf_ProductRatePlanMigrationAmendment' => $baseDir . '/BFPHPClient/Entities/Amendments/ProductRatePlanMigrationAmendment.php',
            'Bf_Profile' => $baseDir . '/BFPHPClient/Entities/Account/Profile.php',
            'Bf_RawAPIOutput' => $baseDir . '/BFPHPClient/BillForwardClient.php',
            'Bf_ResourcePath' => $baseDir . '/BFPHPClient/Entities/Abstract/ResourcePath.php',
            'Bf_Role' => $baseDir . '/BFPHPClient/Entities/Account/Role.php',
            'Bf_RuleSatisfaction' => $baseDir . '/BFPHPClient/Entities/PricingCalculator/RuleSatisfaction.php',
            'Bf_SDKException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_SetupException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_StripeACHToken' => $baseDir . '/BFPHPClient/Entities/Tokens/StripeACHToken.php',
            'Bf_StripeToken' => $baseDir . '/BFPHPClient/Entities/Tokens/StripeToken.php',
            'Bf_Subscription' => $baseDir . '/BFPHPClient/Entities/Subscription/Subscription.php',
            'Bf_SubscriptionCancellation' => $baseDir . '/BFPHPClient/Entities/SyncRequests/SubscriptionCancellation.php',
            'Bf_SubscriptionCharge' => $baseDir . '/BFPHPClient/Entities/Subscription/SubscriptionCharge.php',
            'Bf_SubscriptionReviveRequest' => $baseDir . '/BFPHPClient/Entities/SyncRequests/SubscriptionReviveRequest.php',
            'Bf_TaxLine' => $baseDir . '/BFPHPClient/Entities/Invoice/TaxLine.php',
            'Bf_TaxationLink' => $baseDir . '/BFPHPClient/Entities/Invoice/TaxationLink.php',
            'Bf_UnitOfMeasure' => $baseDir . '/BFPHPClient/Entities/PricingComponent/UnitOfMeasure.php',
            'Bf_UnserializationException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_UnsupportedMethodException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_UnsupportedParameterException' => $baseDir . '/BFPHPClient/Exceptions/SDKExceptions.php',
            'Bf_Webhook' => $baseDir . '/BFPHPClient/Entities/Notification/Webhook.php',
            'Bf_WebhookSubscription' => $baseDir . '/BFPHPClient/Entities/Notification/WebhookSubscription.php',
            'BillForwardClient' => $baseDir . '/BFPHPClient/BillForwardClient.php',

        );
    }

    if (isset($classMap[$className])) {
        require_once $classMap[$className];
    }
});
