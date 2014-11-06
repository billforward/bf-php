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

	public function testIssueUsingHelperPeriodEnd() {
		$client = self::$client;

		$subscriptionID = '54099787-12F0-422E-8FAC-1504AF034A24';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', 'AtPeriodEnd');
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperSpecifiedTime() {
		$client = self::$client;

		$subscriptionID = '54099787-12F0-422E-8FAC-1504AF034A24';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 50,
			'Bandwidth' => 70
			);

		$time = time()+2*60; // time 2 mins from now
		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', $time);
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperImmediate() {
		$client = self::$client;

		$subscriptionID = '54099787-12F0-422E-8FAC-1504AF034A24';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 14,
			'Bandwidth' => 14
			);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate');
		var_export($createdAmendment);
	}
}