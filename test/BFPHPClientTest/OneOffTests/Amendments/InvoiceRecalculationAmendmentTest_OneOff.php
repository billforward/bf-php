<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_InvoiceRecalculationAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_InvoiceRecalculationAmendment;
use BFPHPClientTest\TestConfig;
Class Bf_InvoiceRecalculationAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssue() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// specifically this needs to point to a pending invoice.
    	$invoiceID = '4F2B93F3-C774-4E51-8513-E9DE79AB263C';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$amendment = new Bf_InvoiceRecalculationAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id,
			'newInvoiceState' => 'Pending' // well, probably you want Paid, but this lets us run the test again. :)
			));

		$createdAmendment = Bf_InvoiceRecalculationAmendment::create($amendment);
		var_export($createdAmendment);
	}
}