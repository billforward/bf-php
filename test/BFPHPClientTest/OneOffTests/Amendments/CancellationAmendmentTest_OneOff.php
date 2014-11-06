<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_CancellationAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_Subscription;
use Bf_CancellationAmendment;
use BFPHPClientTest\TestConfig;
Class Bf_CancellationAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssue() {
		$client = self::$client;
    	
    	$subscriptionID = '54099787-12F0-422E-8FAC-1504AF034A24';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		$amendment = new Bf_CancellationAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'serviceEnd' => 'Immediate', // or 'AtPeriodEnd'
			));

		$createdAmendment = Bf_CancellationAmendment::create($amendment);
		var_export($createdAmendment);
	}

	public function testIssueViaHelperImmediateUntilServiceEnd() {
		$client = self::$client;
    	
    	$subscriptionID = 'DA35D225-B11B-4DCD-9626-3B490A655A4D';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		$createdAmendment = $subscription->cancel('Immediate');
		var_export($createdAmendment);
	}

	public function testIssueViaHelperDeferredImmediate() {
		$client = self::$client;
    	
    	$subscriptionID = '9E9A31FD-59B2-4BA9-B572-91AA3B8E08EF';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		$time = time()+60;

		$createdAmendment = $subscription->cancel('Immediate', $time);
		var_export($createdAmendment);
	}

	public function testIssueViaHelperDeferredUntilPeriodEndImmediate() {
		$client = self::$client;
    	
    	$subscriptionID = 'BD26F39B-C3D6-4D7D-A806-9DCABEC75128';
		$subscription = Bf_Subscription::getByID($subscriptionID);

		$createdAmendment = $subscription->cancel('Immediate', 'AtPeriodEnd');
		var_export($createdAmendment);
	}
}