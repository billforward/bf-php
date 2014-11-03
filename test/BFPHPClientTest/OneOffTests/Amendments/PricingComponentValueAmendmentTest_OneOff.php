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
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	$invoiceID = '04875733-0440-430A-9D48-546E6F9A4F71';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// map pricing component names to values
		$componentNameToValueMap = array(
			'CPU' => 10,
			'Bandwidth' => 20
			);

		$createdAmendment = $invoice->upgrade($componentNameToValueMap, 'immediate', 'Immediate');
		var_export($createdAmendment);
	}

	public function testIssueUsingHelper2() {
		$client = self::$client;
    	
    	// gets existing invoice. sorry for magic number; it's useful to me at least. :)
    	// works at least on Paid invoices.
    	$invoiceID = '04875733-0440-430A-9D48-546E6F9A4F71';
		$invoice = Bf_Invoice::getByID($invoiceID);

		// map pricing component names to values
		$propertiesList = array(
			array(
				'id' => '2C112AD7-1A1F-4C49-8FE6-83115DCA29C5'
				),
			array(
				'id' => '2C2A5C82-9AD6-4278-B7E6-3A1E6E64F2BE'
				)
			);
		$valuesList = array(
			10,
			20
			);

		$createdAmendment = $invoice->changeValueOfPricingComponentByProperties($propertiesList, $valuesList, 'immediate', 'Immediate');
		var_export($createdAmendment);
	}
}