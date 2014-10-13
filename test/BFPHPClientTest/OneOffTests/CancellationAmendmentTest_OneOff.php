<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_CancellationAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
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
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	$invoiceID = 'AEC4DF60-D472-41A9-B0A5-F94E5612DFFE';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$amendment = new Bf_CancellationAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id,
			'serviceEnd' => 'Immediate', // or 'AtPeriodEnd'
			'source' => 'PHP library test'
			));

		$createdAmendment = Bf_CancellationAmendment::create($amendment);
		var_export($createdAmendment);
	}
}