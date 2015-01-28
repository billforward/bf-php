<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_OrganisationTest extends \PHPUnit_Framework_TestCase {
	protected static $anOrganizationID = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();

		$someEntities = Bf_Account::getAll(array(
			'records' => 1
			));
		$anEntity = $someEntities[0];

		self::$anOrganizationID = $anEntity->organizationID;
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
		$orgs = Bf_Organisation::getMine();

		$firstOrg = $orgs[0];

		$id = $firstOrg->id;

		$expected = self::$anOrganizationID;
		$actual = $id;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that organisation's ID matches known value."
			);

		$expected = Bf_Organisation::getResourcePath()->getEntityName();
		$actual = $firstOrg['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }
}
