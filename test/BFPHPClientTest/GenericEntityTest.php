<?php
namespace BFPHPClientTest;
echo "Running Bf_GenericEntity tests for BillForward PHP Client Library.\n";

use Bf_GenericEntity;
use Bf_ResourcePath;
Class Bf_GenericEntityTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	public function testGetProfileUsingGeneric() {
		$entityPath = new Bf_ResourcePath('accounts');
		$account = new Bf_GenericEntity(null, null, $entityPath);
		$createdAccount = Bf_GenericEntity::create($account);

		var_export($createdAccount);
	}
}
