<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_Product tests for BillForward PHP Client Library.\n";

use \BfClient;
use \Bf_Product;
use BFPHPClientTest\TestConfig;
Class Bf_Product_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testCreate() {
		$client = self::$client;
    	
		$product = new Bf_Product($client, [
		'productType' => 'non-recurring',
		'state' => 'prod',
		'name' => 'Month of Paracetamoxyfrusebendroneomycin',
		'description' => 'It can cure the common cold, and being struck by lightning',
		'durationPeriod' => 'days',
		'duration' => 28,
			]);

		// TODO API: 'description' should be allowed to be null.
		// TODO API: 'duration' should be 'required', rather than defaulting to 0.

		$response = $product
		->create();

		var_export($response);
	}
}