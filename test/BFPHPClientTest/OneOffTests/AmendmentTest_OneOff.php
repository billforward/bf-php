<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Amendment tests for BillForward PHP Client Library.\n";

use Bf_Invoice;
use Bf_AmendmentDiscardAmendment;
use Bf_Amendment;
use BFPHPClientTest\TestConfig;
Class Bf_AmendmentDiscardAmendment_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetByID() {
		$client = self::$client;
    	
    	// gets existing amendment. sorry for magic number; it's useful to me at least. :)
    	$amendmentID = '82EAD04F-9058-4B3E-B45D-E0B9D5F73225';

		$amendment = Bf_Amendment::getByID($amendmentID);

		var_export($amendment);
	}

	public function testGetAll() {
		$client = self::$client;

		$amendments = Bf_Amendment::getAll(array('records' => 1));
		
		var_export($amendments);
	}
}