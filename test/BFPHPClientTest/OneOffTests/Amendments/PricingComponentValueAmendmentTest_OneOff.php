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
use BFPHPClientTest\TestConfig;
Class Bf_PricingComponentValueAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssue() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	$invoiceID = 'A142C507-4FAD-48F5-9DE6-29D824ACF17A';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$subscription = Bf_Subscription::getByID($invoice->subscriptionID);

		var_export($subscription);

		// needs to be a tiered component.
		$pricingComponentValue_0 = $subscription->getPCVCorrespondingToPricingComponentWithName('Devices used, tiered');
		$newValue_0 = 100;

		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id,
			'oldValue' => $pricingComponentValue_0->value,
			'newValue' => $newValue_0,
			'mode' => 'immediate',
			'invoicingType' => 'Immediate', 
			'logicalComponentID' => $pricingComponentValue_0->pricingComponentID
			));

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		var_export($createdAmendment);
	}
}