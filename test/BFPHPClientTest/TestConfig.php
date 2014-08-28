<?php
namespace BFPHPClientTest;
use BfClient;
use Bf_Account;
use Bf_ApiConfiguration;
use Bf_Profile;
use Bf_Product;
use Bf_PaymentMethod;
use Bf_PaymentMethodSubscriptionLink;
use Bf_PricingComponent;
use Bf_PricingComponentTier;
use Bf_PricingComponentValue;
use Bf_ProductRatePlan;
use Bf_UnitOfMeasure;
use Bf_Subscription;
use Bf_AuthorizeNetToken;
class TestConfig {
	private $access_token = NULL;
	private $urlRoot = NULL;
	private $client = NULL;
	private $useStandardSandbox = true;

	private $usualLoginAccountID;
	private $usualLoginUserID;
	private $usualAccountID;
	private $usualOrganisationID;
	private $usualProductID;
	private $usualProductRatePlanID;
	private $usualSubscriptionID;
	private $usualUnitOfMeasureID;
	private $usualPricingComponentID;
	private $usualPaymentMethodID;
	private $usualPaymentMethodLinkID;

	public function __construct() {
		$this->access_token = '';
        $this->urlRoot = 'https://api-sandbox.billforward.net/2014.223.0/';

        $this->usualLoginAccountID = '';
        $this->usualLoginUserID = '';
        $this->usualOrganisationID = '';
        $this->usualAccountID = '';
        $this->usualPaymentMethodID = '';
        $this->usualPaymentMethodLinkID = '';
        $this->usualProductID = '';
        $this->usualProductRatePlanID = '';
        $this->usualPricingComponentID = '';
        $this->usualSubscriptionID = '';
        $this->usualUnitOfMeasureID = '';

		$this->client = new BfClient($this->access_token, $this->urlRoot);
	}

	public function getClient() {
		return $this->client;
	}

	/**
	 * Gets account ID associated with our go-to login user. Generally we try not
	 * to involve this account in things like subscribing; make
	 *  a separate go-to account for such things.
	 * This account has 'admin' role and a userID.
	 * @return string
	 */
	public function getUsualLoginAccountID() {
		return $this->usualLoginAccountID;
	}

	/**
	 * Gets ID of our go-to login user.
	 * @return string
	 */
	public function getUsualLoginUserID() {
		return $this->usualLoginUserID;
	}

	/**
	 * Get Bf_Account ID of our go-to account. This account has a profile.
	 * @return string
	 */
	public function getUsualAccountID() {
		return $this->usualAccountID;
	}

	public function getUsualOrganisationID() {
		return $this->usualOrganisationID;
	}

	public function getUsualProductID() {
		return $this->usualProductID;
	}

	public function getUsualProductRatePlanID() {
		return $this->usualProductRatePlanID;
	}

	public function getUsualSubscriptionID() {
		return $this->usualSubscriptionID;
	}

	public function getUsualUnitOfMeasureID() {
		return $this->usualUnitOfMeasureID;
	}

	public function getUsualPricingComponentID() {
		return $this->usualPricingComponentID;
	}

	public function getUsualPaymentMethodID() {
		return $this->usualPaymentMethodID;
	}

	public function getUsualPaymentMethodLinkID() {
		return $this->usualPaymentMethodLinkID;
	}

	public function getUsualAccountsProfileEmail() {
		return 'full@account.is.moe';
	}

	public function getUsualPrpName() {
		return 'Cool Plan';
	}

	public function getUsualSubscriptionName() {
		return 'Memorable Subscription';
	}

	public function getUsualProductDescription() {
		return 'It can cure the common cold, and being struck by lightning';
	}

	/**
	 * If you've nuked your local database, run this to get some 'go-to' data to play with
	 */
	public function buildSampleData() {
		$client = $this->client;

		//-- Find the account we login with (assume first found with associated user)
		// order by userID so that we are likely to see our login user's account
		$accounts = $client
		->accounts
		->getAll([
			'order_by' => 'userID'
			]);

		$foundLoginAccount = NULL;
		foreach ($accounts as $account) {
			if (array_key_exists('userID', $account)) {
				$foundLoginAccount = $account;
				break;
			}
		}
		if (is_null($foundLoginAccount)) {
			throw new \Exception('Login account not found.');
		}
		//var_export($foundLoginAccount);

		//-- Get the organization we log in with (assume first found)
		$orgs = $client
		->organisations
		->getMine();

		$firstOrg = $orgs[0];
		$firstOrgID = $firstOrg->id;


		// Create (upon our organisation) API configuration for Authorize.net
		$AuthorizeNetLoginID = '4X8R8UAawK67';
		$AuthorizeNetTransactionKey = '3Udsn9w8G29qNt3Q';

		// saving this twice to the same organisation seems to make a copy.
		// so probably you sohuld clear out your `api_configurations` in SQL before running this a second time.
		$apiConfiguration = new Bf_ApiConfiguration($client, [
			 "@type" => "AuthorizeNetConfiguration",
	         "APILoginID" => $AuthorizeNetLoginID,
	         "transactionKey" => $AuthorizeNetTransactionKey,
	         "environment" => "Sandbox"
			]);

		$firstOrg
		->apiConfigurations = [$apiConfiguration];

		$savedOrg = $firstOrg
		->save();

		//-- Make account with expected profile
		$email = $this->getUsualAccountsProfileEmail();
		$profile = new Bf_Profile($client, [
			'email' => $email,
			'firstName' => 'Test',
			]);
		
		$account = new Bf_Account($client, [
			'profile' => $profile,
			]);

		$createdAcc = $account
		->create();
		$createdAccID = $createdAcc->id;


		//-- make payment method, and associate it with account
			//-- make Authorize.net token to associate payment method to

		$customerProfileID = 28476855;
		$customerPaymentProfileID = 25879733;
    	
		// this 'last 4 digits of credit card number' field (currently optional) is required for refunds
		$cardLast4Digits = 1337;

		$authorizeNetToken = new Bf_AuthorizeNetToken($client, [
			'accountID' => $createdAccID,
			'customerProfileID' => $customerProfileID,
			'customerPaymentProfileID' => $customerPaymentProfileID,
			'lastFourDigits' => $cardLast4Digits,
			]);

		$createdAuthorizeNetToken = $authorizeNetToken
		->create();
		$createdAuthorizeNetTokenID = $createdAuthorizeNetToken
		->id;

		$paymentMethod = new Bf_PaymentMethod($client, [
			'linkID' => $createdAuthorizeNetTokenID,
			'accountID' => $createdAccID,
			'name' => 'Authorize.Net',
			'description' => 'Pay via Authorize.Net',
			'gateway' => 'authorizeNet',
			'userEditable' => 0,
			'priority' => 100,
			'reusable' => 1,
			]);
		$createdPaymentMethod = $paymentMethod
		->create();
		$createdPaymentMethodID = $createdPaymentMethod->id;

		$paymentMethods = [$createdPaymentMethod];

		// add these payment methods to our model of the created account
		$createdAcc
		->paymentMethods = $paymentMethods;
		// save changes to real account
		$createdAcc = $createdAcc
		->save();

		var_export($createdAcc);

		//-- Make unit of measure
		$uom = new Bf_UnitOfMeasure($client, [
			'name' => 'Devices',
			'displayedAs' => 'Devices',
			'roundingScheme' => 'UP',
			]);
		$createdUom = $uom
		->create();
		$createdUomID = $createdUom->id;

		$productDescription = $this->getUsualProductDescription();

		//-- Make product
		$product = new Bf_Product($client, [
			'productType' => 'non-recurring',
			'state' => 'prod',
			'name' => 'Month of Paracetamoxyfrusebendroneomycin',
			'description' => $productDescription,
			'durationPeriod' => 'days',
			'duration' => 28,
			]);
		$createdProduct = $product
		->create();
		$createdProductID = $createdProduct->id;

		//-- Make product rate plan
			//-- Make pricing components for product rate plan
				//-- Make tiers for pricing component
		$tier = new Bf_PricingComponentTier($client, [
			'lowerThreshold' => 1,
			'upperThreshold' => 1,
			'pricingType' => 'unit',
			'price' => 1,
			]);
		$tiers = [$tier];

		$pricingComponentsArray = [
			new Bf_PricingComponent($client, [
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
			])
		];

		$prp = new Bf_ProductRatePlan($client, [
			'currency' => 'USD',
			'name' => $this->getUsualPrpName(),
			'pricingComponents' => $pricingComponentsArray,
			'productID' => $createdProductID,
			]);
		$createdPrp = $prp
		->create();
		$createdProductRatePlanID = $createdPrp->id;
		$createdPricingComponentID = $createdPrp->pricingComponents[0]->id;

		//-- Make pricing component value instance of pricing component
		$prc = new Bf_PricingComponentValue($client, [
			'pricingComponentID' => $createdPricingComponentID,
			'value' => 2,
			'crmID' => ''
			]);
		$pricingComponentValuesArray = [$prc];

		
		//-- Make Bf_PaymentMethodSubscriptionLinks
		// refer by ID to our payment method.
		$paymentMethodReference = new Bf_PaymentMethod($client, [
				'id' => $createdPaymentMethodID 
				]);

		$paymentMethodSubscriptionLink = new Bf_PaymentMethodSubscriptionLink($client, [
			// 'paymentMethodID' => $createdPaymentMethodID,
			'paymentMethod' => $paymentMethodReference,
			'organizationID' => $firstOrgID,
			]);
		$paymentMethodSubscriptionLinks = [$paymentMethodSubscriptionLink];

		$subName = $this->getUsualSubscriptionName();
		//-- Make subscription
		$sub = new Bf_Subscription($client, [
			'type' => 'Subscription',
			'productID' => $createdProductID,
			'productRatePlanID' => $createdProductRatePlanID,
			'accountID' => $createdAccID,
			'name' => $subName,
			'description' => 'Memorable Subscription Description',
			'paymentMethodSubscriptionLinks' => $paymentMethodSubscriptionLinks,
			'pricingComponentValues' => $pricingComponentValuesArray
			]);
		$createdSub = $sub
		->create();

		echo "\n";
		echo sprintf("\$this->usualLoginAccountID = '%s';\n", $foundLoginAccount->id);
		echo sprintf("\$this->usualLoginUserID = '%s';\n", $foundLoginAccount->userID);
		echo sprintf("\$this->usualOrganisationID = '%s';\n", $firstOrgID);
		echo sprintf("\$this->usualAccountID = '%s';\n", $createdAccID);
		echo sprintf("\$this->usualPaymentMethodLinkID = '%s';\n", $createdAuthorizeNetTokenID);
		echo sprintf("\$this->usualPaymentMethodID = '%s';\n", $createdPaymentMethodID);
		echo sprintf("\$this->usualProductID = '%s';\n", $createdProductID);
		echo sprintf("\$this->usualProductRatePlanID = '%s';\n", $createdProductRatePlanID);
		echo sprintf("\$this->usualPricingComponentID = '%s';\n", $createdPricingComponentID);
		echo sprintf("\$this->usualSubscriptionID = '%s';\n", $createdSub->id);
		echo sprintf("\$this->usualUnitOfMeasureID = '%s';\n", $createdUomID);
	}
}