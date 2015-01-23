<?php
class Bf_AccountTest extends PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		BFPHPClientTest\TestBase::initialize();
	}

	public function testGetAll()
    {	
    	// short alias
    	$client = self::$client;
    	
		$accounts = Bf_Account::getAll();

		$firstAccount = $accounts[0];

		$expected = Bf_Account::getResourcePath()->getEntityName();
		$actual = $firstAccount['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }
}
