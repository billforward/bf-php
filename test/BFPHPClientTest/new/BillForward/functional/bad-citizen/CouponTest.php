<?php
class CouponTest extends PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
	}

	public static function makeRequiredEntities() {
		$accountModel = Models::Account();
		$accountCreated = Bf_Account::create($accountModel);
	}

	public function testCreate()
    {	
    	

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }
}
