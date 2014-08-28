<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_ProductRatePlan tests for BillForward PHP Client Library.\n";

use BfClient;
use Bf_UnitOfMeasure;
use BFPHPClientTest\TestConfig;
Class Bf_UnitOfMeasure_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
    	
		$uom = new Bf_UnitOfMeasure($client, [
			'name' => 'Devices',
			'displayedAs' => 'Devices',
			'roundingScheme' => 'UP',
			]);

		$createdUom = $uom->create();

		var_export($createdUom);
	}
}