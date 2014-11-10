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
use Bf_PricingCalculator;
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

		$subscriptionID = '8D0D3253-F6A6-4990-9C4D-F160BB66A91A';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 50,
			'Bandwidth' => 70
			);

		$time = time()+1*60; // time 2 mins from now
		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', $time);
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperImmediate() {
		$client = self::$client;

		$subscriptionID = '8D0D3253-F6A6-4990-9C4D-F160BB66A91A';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 1,
			'Bandwidth' => 1
			);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate');
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperImmediateWithOverride() {
		$client = self::$client;

		$subscriptionID = '8D0D3253-F6A6-4990-9C4D-F160BB66A91A';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 1,
			'Bandwidth' => 1
			);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', 'Immediate', 'Delayed');
		var_export($createdAmendment);
	}

	public function testPredictPrice() {
		$client = self::$client;

		$subscriptionID = '8D0D3253-F6A6-4990-9C4D-F160BB66A91A';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 150,
			'Bandwidth' => 150
			);

		$asOfTime = time();

		$calculation = Bf_PricingCalculator::requestUpgradePrice($componentNameToValueMap, $subscriptionID, $asOfTime);
		var_export($calculation);

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', $asOfTime);
		var_export($createdAmendment);
	}

	public function testPredictPricePast() {
		$client = self::$client;

		$subscriptionID = '8D0D3253-F6A6-4990-9C4D-F160BB66A91A';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 150,
			'Bandwidth' => 150
			);

		// one hour ago
		$asOfTime = time()-60*60*1;

		$calculation = Bf_PricingCalculator::requestUpgradePrice($componentNameToValueMap, $subscriptionID, $asOfTime);
		var_export($calculation);
		// $calculation->cost is the price of the upgrade invoice that will be issued if you action this upgrade.

		$createdAmendment = $subscription->upgrade($componentNameToValueMap, 'Immediate', $asOfTime);
		var_export($createdAmendment);
	}
}