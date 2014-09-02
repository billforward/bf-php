<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) ApiConfiguration tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_Account;
use Bf_APIConfiguration;
use BFPHPClientTest\TestConfig;
Class ApiConfiguration_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testEdit() {
		$client = self::$client;

		//-- Find the account we login with (assume first found with associated user)
		// order by userID so that we are likely to see our login user's account
		$accounts = $client
		->accounts
		->getAll(array(
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
		$orgs = $client
		->organisations
		->getMine();

		$firstOrg = $orgs[0];
		$firstOrgID = $firstOrg->id;

		echo "\nInitial Org from API:\n\n";
		var_export($firstOrg);

		$AuthorizeNetLoginID = '3H8j4PFr7gt7';
		$AuthorizeNetTransactionKey = '7W8x7v37Gt472Cyn';

		$apiConfiguration = new Bf_APIConfiguration($client, array(
			 "@type" => "AuthorizeNetConfiguration",
	         "APILoginID" => $AuthorizeNetLoginID,
	         "transactionKey" => $AuthorizeNetTransactionKey,
	         "environment" => "Sandbox"
			));

		// TODO API: '@type' needs to be 'required', otherwise we get marshalling errors where ti doesn't know what class to make. and the client just receives a '-1 server error'

		$firstOrg
		->apiConfigurations = array($apiConfiguration);

		echo "\n\nEdited model Org:\n\n";
		var_export($firstOrg);

		$savedOrg = $firstOrg
		->save();

		echo "\n\nResponse from API after updating Org:\n\n";
		var_export($savedOrg);
	}
}