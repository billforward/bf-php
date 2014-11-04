<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_PricingComponentValueAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_Subscription;
use Bf_PricingComponentValueAmendment;
use Bf_UnitOfMeasure;
use BFPHPClientTest\TestConfig;
Class Bf_PricingComponentValueAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssueUsingHelper() {
		$client = self::$client;

		$subscriptionID = 'E962B901-DB0C-4BE5-8E29-AF1F6F59F666';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 5,
			'Bandwidth' => 7
			);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'immediate', 'Immediate');
	}
}