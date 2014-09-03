<?php
namespace BFPHPClientTest;
echo "Running Bf_Organisation tests for BillForward PHP Client Library.\n";

use \BillForwardClient;
use \Bf_Organisation;
Class Bf_OrganisationTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	/*// Can't run; no privilege!
	public function testGetAll()
    {	
    	// short alias
    	$client = self::$client;
    	
		$organisations = $client
		->organisations
		->getAll();

		$firstOrg = $organisations[0];

		$expected = Bf_Organisation::getResourcePath()->getEntityName();
		$actual = $firstOrg['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }*/

	public function testGetMine()
    {	
    	// short alias
		$client = self::$client;
		$config = self::$config;
    	
		$orgs = Bf_Organisation::getMine();

		$firstOrg = $orgs[0];

		$id = $firstOrg
		->id;

		$expected = $config
		->getUsualOrganisationID();
		$actual = $id;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that organisation's ID matches known value."
			);
    }
}
