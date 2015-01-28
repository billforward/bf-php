<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

Class Bf_ApiConfigurationTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

	public function testEdit() {
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

		//-- Get the organization we log in with (assume first found)
		$orgs = Bf_Organisation::getMine();

		$firstOrg = $orgs[0];
		$firstOrgID = $firstOrg->id;

		// echo "\nInitial Org from API:\n\n";
		// var_export($firstOrg);

		// we are going to add an API configuration for Authorize.Net
		$configType = "AuthorizeNetConfiguration";

		// Create (upon our organisation) API configuration for Authorize.net
		$AuthorizeNetLoginID = '4X8R8UAawK67';
		$AuthorizeNetTransactionKey = '3Udsn9w8G29qNt3Q';

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

		// echo "\n\nEdited model Org:\n\n";
		// var_export($firstOrg);

		$savedOrg = $firstOrg
		->save();

		// echo "\n\nResponse from API after updating Org:\n\n";
		// var_export($savedOrg);

		$newConfig = Bf_BillingEntity::fromCollectionFindFirstWhoMatchesProperties($savedOrg->apiConfigurations,
			array(
			'@type' => 'AuthorizeNetConfiguration'
			));

		$expected = $AuthorizeNetLoginID;
		$actual = $newConfig->APILoginID;

		$this->assertEquals(
			$expected,
			$actual,
			"Entity field matches known value."
			);
	}
}