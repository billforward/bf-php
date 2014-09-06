<?php
namespace BFPHPClientTest;
use BillForwardClient;
use Bf_Account;
use Bf_Address;
use Bf_ApiConfiguration;
use Bf_Organisation;
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
	private $useStandardSandbox = false;

	private $usualLoginAccountID;
	private $usualLoginUserID;
	private $usualAccountID;
	private $usualProfileID;
	private $usualAddressID;
	private $usualOrganisationID;
	private $usualProductID;
	private $usualProductRatePlanID;
	private $usualSubscriptionID;
	private $usualUnitOfMeasureID;
	private $usualFlatPricingComponentID;
	private $usualTieredPricingComponentID;
	private $usualPaymentMethodID;
	private $usualPaymentMethodLinkID;

	public function __construct() {
		$this->access_token = '';
        $this->urlRoot = 'https://api-sandbox.billforward.net/2014.223.0/';

        $this->usualLoginAccountID = '';
		$this->usualLoginUserID = '';
		$this->usualOrganisationID = '';
		$this->usualAccountID = '';
		$this->usualProfileID = '';
		$this->usualAddressID = '';
		$this->usualPaymentMethodLinkID = '';
		$this->usualPaymentMethodID = '';
		$this->usualProductID = '';
		$this->usualProductRatePlanID = '';
		$this->usualFlatPricingComponentID = '';
		$this->usualTieredPricingComponentID = '';
		$this->usualSubscriptionID = '';
		$this->usualUnitOfMeasureID = '';

		$this->client = new BillForwardClient($this->access_token, $this->urlRoot);
        BillForwardClient::setDefaultClient($this->client);
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

	/**
	 * Get Bf_Profile ID of our go-to profile.
	 * @return string
	 */
	public function getUsualProfileID() {
		return $this->usualProfileID;
	}

	/**
	 * Get Bf_Address ID of our go-to address.
	 * @return string
	 */
	public function getUsualAddressID() {
		return $this->usualAddressID;
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

	public function getUsualFlatPricingComponentID() {
		return $this->usualFlatPricingComponentID;
	}

	public function getUsualTieredPricingComponentID() {
		return $this->usualTieredPricingComponentID;
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
			throw new \Exception('Login account not found.');
		}
		//var_export($foundLoginAccount);

		//-- Get the organization we log in with (assume first found)
		$orgs = Bf_Organisation::getMine();

		$firstOrg = $orgs[0];
		$firstOrgID = $firstOrg->id;


		// Create (upon our organisation) API configuration for Authorize.net
		$AuthorizeNetLoginID = '4X8R8UAawK67';
		$AuthorizeNetTransactionKey = '3Udsn9w8G29qNt3Q';

		// saving this twice to the same organisation seems to make a copy.
		// so probably you sohuld clear out your `api_configurations` in SQL before running this a second time.
		$apiConfiguration = new Bf_ApiConfiguration(array(
			 "@type" => "AuthorizeNetConfiguration",
	         "APILoginID" => $AuthorizeNetLoginID,
	         "transactionKey" => $AuthorizeNetTransactionKey,
	         "environment" => "Sandbox"
			));

		$firstOrg
		->apiConfigurations = array($apiConfiguration);

		$savedOrg = $firstOrg
		->save();

		//-- Make account with expected profile, profile with expected address
		$address = new Bf_Address(array(
			'addressLine1' => 'address line 1',
		    'addressLine2' => 'address line 2',
		    'addressLine3' => 'address line 3',
		    'city' => 'London',
		    'province' => 'London',
		    'country' => 'United Kingdom',
		    'postcode' => 'SW1 1AS',
		    'landline' => '02000000000',
		    'primaryAddress' => true
			));
		// make one-item list of addresses
		$addresses = array($address);

		$email = $this->getUsualAccountsProfileEmail();
		$profile = new Bf_Profile(array(
			'email' => $email,
			'firstName' => 'Test',
			'addresses' => $addresses,
			));
		
		$account = new Bf_Account(array(
			'profile' => $profile,
			));

		$createdAcc = Bf_Account::create($account);
		$createdAccID = $createdAcc->id;
		$createdProfileID = $createdAcc->getProfile()->id;
		$createdAddresses = $createdAcc->getProfile()->getAddresses();
		$firstAddress = $createdAddresses[0];

		$createdAddressID = $firstAddress->id;


		//-- make payment method, and associate it with account
			//-- make Authorize.net token to associate payment method to

		$customerProfileID = 28476855;
		$customerPaymentProfileID = 25879733;
    	
		// this 'last 4 digits of credit card number' field (currently optional) is required for refunds
		$cardLast4Digits = 1337;

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

		$productDescription = $this->getUsualProductDescription();

		//-- Make product
		$product = new Bf_Product(array(
			'productType' => 'non-recurring',
			'state' => 'prod',
			'name' => 'Month of Paracetamoxyfrusebendroneomycin',
			'description' => $productDescription,
			'durationPeriod' => 'days',
			'duration' => 28,
			));
		$createdProduct = Bf_Product::create($product);
		$createdProductID = $createdProduct->id;

		//-- Make product rate plan
			//-- Make pricing components for product rate plan
				//-- Make tiers for pricing component
		$tierFlat = new Bf_PricingComponentTier(array(
			'lowerThreshold' => 1,
			'upperThreshold' => 1,
			'pricingType' => 'fixed',
			'price' => 1,
			));

		$tier1 = new Bf_PricingComponentTier(array(
			'lowerThreshold' => 1,
			'upperThreshold' => 1,
			'pricingType' => 'fixed',
			'price' => 10,
			));
		$tier2 = new Bf_PricingComponentTier(array(
			'lowerThreshold' => 2,
			'upperThreshold' => 10,
			'pricingType' => 'unit',
			'price' => 5,
			));
		$tier3 = new Bf_PricingComponentTier(array(
			'lowerThreshold' => 11,
			'upperThreshold' => 100,
			'pricingType' => 'unit',
			'price' => 2,
			));
		$tiersFlat = array($tierFlat);
		$tiers = array($tier1, $tier2, $tier3);

		$pricingComponentsArray = array(
			new Bf_PricingComponent(array(
			'@type' => 'flatPricingComponent',
			'chargeModel' => 'flat',
			'name' => 'Devices used, fixed',
			'description' => 'How many devices you use, I guess',
			'unitOfMeasureID' => $createdUomID,
			'chargeType' => 'subscription',
			'upgradeMode' => 'immediate',
			'downgradeMode' => 'immediate',
			'defaultQuantity' => 1,
			'tiers' => $tiersFlat
			)),
			new Bf_PricingComponent(array(
			'@type' => 'tieredPricingComponent',
			'chargeModel' => 'tiered',
			'name' => 'Devices used, tiered',
			'description' => 'How many devices you use, but with a tiering system',
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
			'name' => $this->getUsualPrpName(),
			'pricingComponents' => $pricingComponentsArray,
			'productID' => $createdProductID,
			));
		$createdPrp = Bf_ProductRatePlan::create($prp);
		$createdProductRatePlanID = $createdPrp->id;
		$createdFlatPricingComponentID = $createdPrp->pricingComponents[0]->id;
		$createdTieredPricingComponentID = $createdPrp->pricingComponents[1]->id;

		//-- Make pricing component value instance of pricing component
		$prcFlat = new Bf_PricingComponentValue(array(
			'pricingComponentID' => $createdFlatPricingComponentID,
			'value' => 1,
			'crmID' => ''
			));
		$prcTiered = new Bf_PricingComponentValue(array(
			'pricingComponentID' => $createdTieredPricingComponentID,
			'value' => 5,
			'crmID' => ''
			));
		$pricingComponentValuesArray = array($prcFlat, $prcTiered);
		
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

		$subName = $this->getUsualSubscriptionName();
		//-- Make subscription
		$sub = new Bf_Subscription(array(
			'type' => 'Subscription',
			'productID' => $createdProductID,
			'productRatePlanID' => $createdProductRatePlanID,
			'accountID' => $createdAccID,
			'name' => $subName,
			'description' => 'Memorable Subscription Description',
			'paymentMethodSubscriptionLinks' => $paymentMethodSubscriptionLinks,
			'pricingComponentValues' => $pricingComponentValuesArray
			));
		$createdSub = Bf_Subscription::create($sub);

		echo "\n";
		echo sprintf("\$this->usualLoginAccountID = '%s';\n", $foundLoginAccount->id);
		echo sprintf("\$this->usualLoginUserID = '%s';\n", $foundLoginAccount->userID);
		echo sprintf("\$this->usualOrganisationID = '%s';\n", $firstOrgID);
		echo sprintf("\$this->usualAccountID = '%s';\n", $createdAccID);
		echo sprintf("\$this->usualProfileID = '%s';\n", $createdProfileID);
		echo sprintf("\$this->usualAddressID = '%s';\n", $createdAddressID);
		echo sprintf("\$this->usualPaymentMethodLinkID = '%s';\n", $createdAuthorizeNetTokenID);
		echo sprintf("\$this->usualPaymentMethodID = '%s';\n", $createdPaymentMethodID);
		echo sprintf("\$this->usualProductID = '%s';\n", $createdProductID);
		echo sprintf("\$this->usualProductRatePlanID = '%s';\n", $createdProductRatePlanID);
		echo sprintf("\$this->usualFlatPricingComponentID = '%s';\n", $createdFlatPricingComponentID);
		echo sprintf("\$this->usualTieredPricingComponentID = '%s';\n", $createdTieredPricingComponentID);
		echo sprintf("\$this->usualSubscriptionID = '%s';\n", $createdSub->id);
		echo sprintf("\$this->usualUnitOfMeasureID = '%s';\n", $createdUomID);
	}
}