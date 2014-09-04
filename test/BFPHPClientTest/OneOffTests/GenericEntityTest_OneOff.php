<?php
namespace BFPHPClientTest\OneOffTests;
/**One-off tests are ones which cannot be repeated (excepting manual
 * modification of database or test).
 * For example, creating the same 'new Bf_Account' twice, fails (causes clash).
 * So we hide from the testrunner here, and only run when we want to.
 */
echo "Running (one-off) Bf_GenericEntity tests for BillForward PHP Client Library.\n";

use Bf_GenericEntity;
use Bf_Account;
use BFPHPClientTest\TestConfig;
Class Bf_GenericEntity_OneOffTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testMakeAccountUsingGeneric() {
		$entityPath = Bf_Account::getResourcePathStatic();

		$account = new Bf_GenericEntity(null, null, $entityPath);
		$createdAccount = Bf_GenericEntity::create($account);

		$expected = $entityPath->getEntityName();
		$actual = $createdAccount['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of returned generic entity matches known value."
			);
	}
}