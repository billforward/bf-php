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

	public function testGetBySubscriptionID() {
		$client = self::$client;
    	
    	// gets existing amendment. sorry for magic number; it's useful to me at least. :)
    	$subscriptionID = 'E962B901-DB0C-4BE5-8E29-AF1F6F59F666';

    	$queryParams = array(
    		'records' => 100
    		);

		$amendments = Bf_Amendment::getForSubscription($subscriptionID, $queryParams);

		var_export($amendments);
	}

	public function testGetPendingBySubscriptionID() {
		$client = self::$client;
    	
    	// gets existing amendment. sorry for magic number; it's useful to me at least. :)
    	$subscriptionID = 'E962B901-DB0C-4BE5-8E29-AF1F6F59F666';

    	// newest 'created' first
    	$queryParams = array(
    		'records' => 100,
    		'order_by' => 'created',
    		'order' => 'DESC'
    		);

		$amendments = Bf_Amendment::getForSubscription($subscriptionID, $queryParams);

		$pendingAmendments = array();
		foreach ($amendments as $key => $amendment) {
			if ($amendment->state === 'Pending') {
				array_push($pendingAmendments, $amendment);
			}
		}

		var_export($pendingAmendments);
	}

	public function testGetAll() {
		$client = self::$client;

		$amendments = Bf_Amendment::getAll(array('records' => 1));
		
		var_export($amendments);
	}
}