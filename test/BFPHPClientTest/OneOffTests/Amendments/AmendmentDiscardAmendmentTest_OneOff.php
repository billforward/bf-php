<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_AmendmentDiscardAmendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_Amendment;
use Bf_AmendmentDiscardAmendment;
use BFPHPClientTest\TestConfig;
Class Bf_AmendmentDiscardAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testIssue() {
		$client = self::$client;
    	
    	// gets existing amendment. sorry for magic number; it's useful to me at least. :)
    	$amendmentID = 'CDC3FB81-EDB8-402D-B6EB-EADB9710C02F';

		$fetched_amendment = Bf_Amendment::getByID($amendmentID);

		// create model of amendment
		$amendment = new Bf_AmendmentDiscardAmendment(array(
			'amendmentToDiscardID' => $amendmentID,
			'subscriptionID' => $fetched_amendment->subscriptionID
			));

		$createdAmendment = Bf_AmendmentDiscardAmendment::create($amendment);
		var_export($createdAmendment);
	}
}