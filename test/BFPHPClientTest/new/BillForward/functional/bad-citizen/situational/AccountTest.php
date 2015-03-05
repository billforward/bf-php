<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_AccountTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

	protected static $createdAccount = NULL;

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

    public function testAddDefaultPaymentMethod()
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
	public function testUpdateCascade()
    {	
    	//--Add a Profile to an existing Account
		$account = self::$createdAccount;

		$originalName = $account->profile->firstName;
		$newName = 'Sanae';

		$account->profile->firstName = $newName;

		$updatedAccount = $account->save();

    	$expected = $newName;
		$actual = $updatedAccount->profile->firstName;

		$this->assertEquals(
			$expected,
			$actual,
			"Nested entity introduced correctly."
			);
    }
}
