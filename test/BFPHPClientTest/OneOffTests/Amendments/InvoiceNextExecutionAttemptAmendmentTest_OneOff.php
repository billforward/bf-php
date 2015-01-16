<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_InvoiceNextExecutionAttemptAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_InvoiceNextExecutionAttemptAmendment;
use BFPHPClientTest\TestConfig;
Class Bf_InvoiceNextExecutionAttemptAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssueImmediate() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	$invoiceID = '4F2B93F3-C774-4E51-8513-E9DE79AB263C';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$amendment = new Bf_InvoiceNextExecutionAttemptAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id
			));

		$createdAmendment = Bf_InvoiceNextExecutionAttemptAmendment::create($amendment);
		var_export($createdAmendment);
	}

	public function testIssueFuture() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	$invoiceID = '4F2B93F3-C774-4E51-8513-E9DE79AB263C';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// timestamp 2 mins from now
		$time = time()+2*60;
		$isoFormatted = gmdate(DATE_ISO8601, $time);
		// replace "+0000 with Z"
		$formattedTimezone = substr($isoFormatted, 0, strlen($isoFormatted)-5).'Z';

		$amendment = new Bf_InvoiceNextExecutionAttemptAmendment(array(
			'subscriptionID' => $invoice->subscriptionID,
			'invoiceID' => $invoice->id,
			'actioningTime' => $formattedTimezone // defaults to immediate actioning
			));

		$createdAmendment = Bf_InvoiceNextExecutionAttemptAmendment::create($amendment);
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperImmediate() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	$invoiceID = 'C9017A9A-24A9-47BD-ABD9-1B162361455C';
		$invoice = Bf_Invoice::getByID($invoiceID);

		$createdAmendment = $invoice->attemptRetry();
		var_export($createdAmendment);
	}

	public function testIssueUsingHelperFuture() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	$invoiceID = 'C9017A9A-24A9-47BD-ABD9-1B162361455C';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// timestamp 2 mins from now
		$time = time()+2*60;

		$createdAmendment = $invoice->attemptRetry($time);
		var_export($createdAmendment);
	}
}