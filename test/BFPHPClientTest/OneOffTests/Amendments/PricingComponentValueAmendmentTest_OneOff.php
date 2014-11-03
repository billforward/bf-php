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

	/*public function testIssue() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	//--Upgrade/downgrade pricing component values on an invoice
		// Get invoice for which you want to change pricing compononent values
		$invoiceID = 'B468EC60-A7B9-4329-A6C6-FCE69AD3894B';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// get subscription to which the invoice pertains
		$subscription = Bf_Subscription::getByID($invoice->subscriptionID);

		// get the tiered pricing component whose value you wish to change.
		// needs to be a tiered component.
		$pricingComponentValue_0 = $subscription->getValueOfPricingComponentWithName('Devices used, tiered');
		$newValue_0 = 100;

		// create model of amendment
		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id,
			'oldValue' => $pricingComponentValue_0->value,
			'newValue' => $newValue_0,
			'mode' => 'immediate',
			'invoicingType' => 'Immediate', 
			'logicalComponentID' => $pricingComponentValue_0->pricingComponentID
			));

		// send amendment model to API to be created.
		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);

		// look at created amendment
		var_export($createdAmendment);
	}*/

	/*public function testIssueUsingHelper() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	$invoiceID = 'E422C79D-4351-4D93-A103-320A5E4E1174';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$createdAmendment = $invoice->changeValueOfPricingComponentWhoseNameMatches('Devices used, tiered', 51, 'immediate', 'Immediate');
		var_export($createdAmendment);
	}*/

	public function testIssueUsingHelper2() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	$invoiceID = '43600BBC-5682-4F5C-8DB1-BFFD9BE08C29';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$createdAmendment = $invoice->upgrade($componentNameToValueMap, 'immediate', 'Immediate');
		var_export($createdAmendment);
	}
}