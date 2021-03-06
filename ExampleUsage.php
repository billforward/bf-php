<?php

$path_to_BillForward = 'lib/BillForward.php';
require_once($path_to_BillForward);

//namespace BFPHPClientTest;
//echo "Running Bf_Account tests for BillForward PHP Client Library.\n";

//use BFPHPClient\Account;



function getUsualAccountsProfileEmail() {
	return 'full@account.is.moe';
}

function getUsualPrpName() {
	return 'Cool Plan';
}


// Grab an API token from: https://app-sandbox.billforward.net/setup/#/personal/api-keys
$access_token = 'INSERT ACCESS TOKEN HERE';
$urlRoot = 'https://api-sandbox.billforward.net/2014.223.0/';
$client = new BillForwardClient($access_token, $urlRoot);
BillForwardClient::setDefaultClient($client);

//-- Find the account we login with (assume first found with associated user)
// order by userID so that we are likely to see our login user's account
$accounts = Bf_Account::getAll(array(
	'order_by' => 'userID'
	));

$foundLoginAccount = NULL;
foreach ($accounts as $account) {
	if (array_key_exists('userID', $account)) {
		$foundLoginAccount = $account;
		break;
	}
}
if (is_null($foundLoginAccount)) {
	throw new Exception('Login account not found.');
}
//var_export($foundLoginAccount);

//-- Get the organization we log in with (assume first found)
$orgs = Bf_Organisation::getMine();

$firstOrg = $orgs[0];
$firstOrgID = $firstOrg->id;

// we are going to add an API configuration for Authorize.Net
$configType = "AuthorizeNetConfiguration";

// Create (upon our organisation) API configuration for Authorize.net
$AuthorizeNetLoginID = 'FILL IN WITH AUTHORIZE NET LOGIN ID';
$AuthorizeNetTransactionKey = 'FILL IN WITH AUTHORIZE NET TRANSACTION KEY';

// model of Authorize.Net credentials
$apiConfiguration = new Bf_ApiConfiguration(array(
	 "@type" => $configType,
     "APILoginID" => $AuthorizeNetLoginID,
     "transactionKey" => $AuthorizeNetTransactionKey,
     "environment" => "Sandbox"
	));

// when there are no api configurations, possibly there is no array altogether
if (!is_array($firstOrg->apiConfigurations)) {
	$firstOrg->apiConfigurations = array();
}

// we are going to remove any existing API configurations of the current type
$prunedConfigs = array();

foreach($firstOrg->apiConfigurations as $config) {
	if ($config['@type'] !== $configType) {
		array_push($prunedConfigs, $config);
	}
}

// add to our organization the model of the Authorize.Net credentials
array_push($prunedConfigs, $apiConfiguration);

$firstOrg
->apiConfigurations = $prunedConfigs;

$savedOrg = $firstOrg
->save();

//-- Make account with expected profile
$email = getUsualAccountsProfileEmail();
$profile = new Bf_Profile(array(
	'email' => $email,
	'firstName' => 'Test',
	));

$account = new Bf_Account(array(
	'profile' => $profile,
	));

$createdAcc = Bf_Account::create($account);
$createdAccID = $createdAcc->id;


//-- make payment method, and associate it with account
	//-- make Authorize.net token to associate payment method to

// FILL IN WITH YOUR AUTHORIZE.NET CUSTOMER PROFILE ID
$customerProfileID = 00000000;
// FILL IN WITH YOUR AUTHORIZE.NET CUSTOMER PAYMENT PROFILE ID
$customerPaymentProfileID = 00000000;
// FILL IN WITH YOUR AUTHORIZE.NET CUSTOMER'S CARD LAST 4 DIGITS
// this 'last 4 digits of credit card number' field (currently optional) is required for refunds
$cardLast4Digits = 0000;

$authorizeNetToken = new Bf_AuthorizeNetToken(array(
	'accountID' => $createdAccID,
	'customerProfileID' => $customerProfileID,
	'customerPaymentProfileID' => $customerPaymentProfileID,
	'lastFourDigits' => $cardLast4Digits,
	));

$createdAuthorizeNetToken = Bf_AuthorizeNetToken::create($authorizeNetToken);
$createdAuthorizeNetTokenID = $createdAuthorizeNetToken
->id;

$paymentMethod = new Bf_PaymentMethod(array(
	'linkID' => $createdAuthorizeNetTokenID,
	'accountID' => $createdAccID,
	'name' => 'Authorize.Net',
	'description' => 'Pay via Authorize.Net',
	'gateway' => 'authorizeNet',
	'userEditable' => 0,
	'priority' => 100,
	'reusable' => 1,
	));
$createdPaymentMethod = Bf_PaymentMethod::create($paymentMethod);
$createdPaymentMethodID = $createdPaymentMethod->id;

$paymentMethods = array($createdPaymentMethod);

// add these payment methods to our model of the created account
$createdAcc
->paymentMethods = $paymentMethods;
// save changes to real account
$createdAcc = $createdAcc
->save();

var_export($createdAcc);

//-- Make unit of measure
$uom = new Bf_UnitOfMeasure(array(
	'name' => 'Devices',
	'displayedAs' => 'Devices',
	'roundingScheme' => 'UP',
	));
$createdUom = Bf_UnitOfMeasure::create($uom);
$createdUomID = $createdUom->id;

//-- Make product
$product = new Bf_Product(array(
	'productType' => 'non-recurring',
	'state' => 'prod',
	'name' => 'Month of Paracetamoxyfrusebendroneomycin',
	'description' => 'It can cure the common cold, and being struck by lightning',
	'durationPeriod' => 'days',
	'duration' => 28,
	));
$createdProduct = Bf_Product::create($product);
$createdProductID = $createdProduct->id;

//-- Make product rate plan
	//-- Make pricing components for product rate plan
		//-- Make tiers for pricing component
$tier = new Bf_PricingComponentTier(array(
	'lowerThreshold' => 1,
	'upperThreshold' => 1,
	'pricingType' => 'unit',
	'price' => 1,
	));
$tiers = array($tier);

$pricingComponentsArray = array(
	new Bf_PricingComponent(array(
	'@type' => 'flatPricingComponent',
	'chargeModel' => 'flat',
	'name' => 'Devices used',
	'description' => 'How many devices you use, I guess',
	'unitOfMeasureID' => $createdUomID,
	'chargeType' => 'subscription',
	'upgradeMode' => 'immediate',
	'downgradeMode' => 'immediate',
	'defaultQuantity' => 10,
	'tiers' => $tiers
	))
);

$prp = new Bf_ProductRatePlan(array(
	'currency' => 'USD',
	'name' => getUsualPrpName(),
	'pricingComponents' => $pricingComponentsArray,
	'productID' => $createdProductID,
	));
$createdPrp = Bf_ProductRatePlan::create($prp);
$createdProductRatePlanID = $createdPrp->id;
$createdPricingComponentID = $createdPrp->pricingComponents[0]->id;

//-- Make pricing component value instance of pricing component
$prc = new Bf_PricingComponentValue(array(
	'pricingComponentID' => $createdPricingComponentID,
	'value' => 2,
	'crmID' => ''
	));
$pricingComponentValuesArray = array($prc);


//-- Make Bf_PaymentMethodSubscriptionLinks
// refer by ID to our payment method.
$paymentMethodReference = new Bf_PaymentMethod(array(
		'id' => $createdPaymentMethodID 
		));

$paymentMethodSubscriptionLink = new Bf_PaymentMethodSubscriptionLink(array(
	// 'paymentMethodID' => $createdPaymentMethodID,
	'paymentMethod' => $paymentMethodReference,
	'organizationID' => $firstOrgID,
	));
$paymentMethodSubscriptionLinks = array($paymentMethodSubscriptionLink);

//-- Provision subscription
$sub = new Bf_Subscription(array(
	'type' => 'Subscription',
	'productID' => $createdProductID,
	'productRatePlanID' => $createdProductRatePlanID,
	'accountID' => $createdAccID,
	'name' => 'Memorable Bf_Subscription',
	'description' => 'Memorable Bf_Subscription Description',
	'paymentMethodSubscriptionLinks' => $paymentMethodSubscriptionLinks,
	'pricingComponentValues' => $pricingComponentValuesArray
	));
$createdSub = Bf_Subscription::create($sub);

// activate provisioned subscription
$createdSub
->activate();

echo "\n";
echo sprintf("\$usualLoginAccountID = '%s';\n", $foundLoginAccount->id);
echo sprintf("\$usualLoginUserID = '%s';\n", $foundLoginAccount->userID);
echo sprintf("\$usualOrganisationID = '%s';\n", $firstOrgID);
echo sprintf("\$usualAccountID = '%s';\n", $createdAccID);
echo sprintf("\$usualPaymentMethodLinkID = '%s';\n", $createdAuthorizeNetTokenID);
echo sprintf("\$usualPaymentMethodID = '%s';\n", $createdPaymentMethodID);
echo sprintf("\$usualProductID = '%s';\n", $createdProductID);
echo sprintf("\$usualProductRatePlanID = '%s';\n", $createdProductRatePlanID);
echo sprintf("\$usualPricingComponentID = '%s';\n", $createdPricingComponentID);
echo sprintf("\$usualSubscriptionID = '%s';\n", $createdSub->id);
echo sprintf("\$usualUnitOfMeasureID = '%s';\n", $createdUomID);