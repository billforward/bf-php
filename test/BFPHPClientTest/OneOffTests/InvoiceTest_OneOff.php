<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Invoice tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use BFPHPClientTest\TestConfig;
Class Bf_InvoiceTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetAll()
    {	
    	// short alias
    	$client = self::$client;
    	
		$invoices = Bf_Invoice::getAll(array('records' => 1));

		$firstInvoice = $invoices[0];

		$expected = Bf_Invoice::getResourcePath()->getEntityName();
		$actual = $firstInvoice['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

	public function testGetByID()
    {	
    	// short alias
    	$client = self::$client;
    	$config = self::$config;

    	$invoices = Bf_Invoice::getAll(array('records' => 1));

		$firstInvoice = $invoices[0];

    	$invoiceId = $firstInvoice->id;
    	
		$invoice = Bf_Invoice::getById($invoiceId);

		$currency = $invoice
		->currency;

		$expected = 'USD';
		$actual = $currency;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that invoice's currency matches known value."
			);
    }
}
