<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_AccountTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

    public function testEnsureGatewayExists() {
    	//-- Get the organization we log in with (assume first found)
		$orgs = Bf_Organisation::getMine();

		$firstOrg = $orgs[0];
		$firstOrgID = $firstOrg->id;

		// we are going to add an API configuration for Authorize.Net
		$configType = "AuthorizeNetConfiguration";

		// Create (upon our organisation) API configuration for Authorize.net
		$AuthorizeNetLoginID = TestBase::getSituation('AuthorizeNetLoginID');
		$AuthorizeNetTransactionKey = TestBase::getSituation('AuthorizeNetTransactionKey');

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
    }

	protected static $createdAccount = NULL;

	/**
     * @depends testEnsureGatewayExists
     */
    public function testCreateWithProfile()
    {	
    	$createdAccount = Bf_Account::create(Models::Account());

    	$actual = $createdAccount->profile;

    	$this->assertNotNull(
			$actual,
			"Nested entity introduced correctly."
			);

    	self::$createdAccount = $createdAccount;
    }

    /**
     * @depends testCreateWithProfile
     */
    public function testAddDefaultPaymentMethod()
    {
    	$account = self::$createdAccount;

    	$customerProfileID = TestBase::getSituation('customerProfileID');
		$customerPaymentProfileID = TestBase::getSituation('customerPaymentProfileID');
		// err, didn't check what the actual card last 4 digits are. but this only matters at refund-time.
		$cardLast4Digits = TestBase::getSituation('cardLast4Digits');
    	
		$authorizeNetToken = new Bf_AuthorizeNetToken(array(
			'accountID' => $account->id,
			'customerProfileID' => $customerProfileID,
			'customerPaymentProfileID' => $customerPaymentProfileID,
			'lastFourDigits' => $cardLast4Digits
			));

		$createdAuthorizeNetToken = Bf_AuthorizeNetToken::create($authorizeNetToken);
		$createdAuthorizeNetTokenID = $createdAuthorizeNetToken->id;

		$isDefault = true;

		$paymentMethodModel = new Bf_PaymentMethod(array(
			'linkID' => $createdAuthorizeNetToken->id,
			'accountID' => $account->id,
			'name' => 'Authorize.Net',
			'description' => $cardLast4Digits,
			'gateway' => 'authorizeNet',
			'userEditable' => 0,
			'priority' => 100,
			'reusable' => 1,
			'defaultPaymentMethod' => $isDefault
			));

		$createdPaymentMethod = Bf_PaymentMethod::create($paymentMethodModel);

		$expected = $isDefault;
    	$actual = $createdPaymentMethod->defaultPaymentMethod;

    	$this->assertEquals(
    		$expected,
			$actual,
			"Payment method begins as default."
			);
    }

    /**
     * @depends testAddDefaultPaymentMethod
     */
	public function testUpdateCascade()
    {	
    	//--Add a Profile to an existing Account
		$accountID = self::$createdAccount->id;
		$fetchedAccount = Bf_Account::getByID($accountID);

		$firstPaymentMethod = $fetchedAccount->paymentMethods[0];

		$expected1 = true;
		$actual1 = $firstPaymentMethod->defaultPaymentMethod;

		$this->assertEquals(
    		$expected1,
			$actual1,
			"Payment method begins as default before update of account."
			);

		$originalName = $fetchedAccount->profile->firstName;
		$newName = 'Sanae';

		$fetchedAccount->profile->firstName = $newName;

		$updatedAccount = $fetchedAccount->save();

    	$expected2 = $newName;
		$actual2 = $updatedAccount->profile->firstName;

		$this->assertEquals(
			$expected2,
			$actual2,
			"Nested entity change introduced correctly by cascade update."
			);

		$firstPaymentMethodAfterUpdate = $updatedAccount->paymentMethods[0];

		$expected3 = true;
		$actual3 = $firstPaymentMethodAfterUpdate->defaultPaymentMethod;

		$this->assertEquals(
    		$expected3,
			$actual3,
			"Payment method remains as default after update of account."
			);
    }
}
