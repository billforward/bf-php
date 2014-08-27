<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) dataset init for BillForward PHP Client Library.\n";

use BFPHPClientTest\TestConfig;
Class BuildSampleData_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	// a more valuable test would check error upon unsafe use
	/*public function testTypeSafety() {
		$client = self::$client;
		$config = self::$config;

		$prof = new Bf_Profile($client, [
			'hey' => 'huh'
			]);

		$args = [
			'profile' => $prof,
			'yo' => 'sup',
			];

		$account = new Bf_Account($client, $args);

		var_export($account->getSerialized());
	}*/

	public function testCreate() {
		$client = self::$client;
		$config = self::$config;

		$config
		->buildSampleData();
 // if we get this far, there's no errors, right?
	}
}