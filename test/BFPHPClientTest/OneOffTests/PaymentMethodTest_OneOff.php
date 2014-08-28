<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Payment Method tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_PaymentMethod;
use BFPHPClientTest\TestConfig;
Class Bf_PaymentMethod_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		$testAccountID = $config->getUsualAccountID();
		$testPaymentMethodLinkID = $config->getUsualPaymentMethodLinkID();
    	
		$paymentMethod = new Bf_PaymentMethod($client, [
		'accountID' => $testAccountID,
		'linkID' => $testPaymentMethodLinkID,
		'name' => 'Credit',
		'description' => 'Pay using account credit',
		'gateway' => 'AuthorizeNet',
		'userEditable' => 1,
		'reusable' => 1,
			]);

		// TODO API: 'linkID' appears in docs example, and indeed is 'required' (see API's 'link_id'). Yet has no @ApiModelProperty(), so does not appear as 'required' in docs.
		// TODO API: when 'linkID' is null, we get a 'Duplicate record' back from server, rather than parameter validation prompt.
		// TODO API: what is 'linkID' for? It is a required field, but you can write whatever you want in it. Empty string works, but NULL doesn't
		// TODO API: presumably 'gateway' needs to be 'required'? this was not enforced..
		// TODO API: 'name' turned out to be optional; mark it as such, or enforce it
		// TODO API: 'description' turned out to be optional; mark it as such, or enforce it
		// TODO API: 'userEditable' is an optional field that is not mentioned in docs
		// TODO API: 'reusable' is an optional field that is not mentioned in docs

		$response = $paymentMethod
		->create();
	}
}