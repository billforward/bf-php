<?php
namespace BFPHPClientTest;
echo "Running Bf_ProductRatePlan tests for BillForward PHP Client Library.\n";

use \BfClient;
use \Bf_ProductRatePlan;
Class Bf_ProductRatePlanTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;
	protected static $usualProductRatePlan = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	protected function recycleUsualProductRatePlan() {
		if (is_null(self::$usualProductRatePlan)) {
			return $this->getUsualProductRatePlan();
		} else {
			return self::$usualProductRatePlan;
		}
    }

    protected function getUsualProductRatePlan() {
    	// short alias
    	$client = self::$client;
    	$config = self::$config;
    	
    	$testProductRatePlanId = $config->getUsualProductRatePlanID();

		$sub = $client
		->productRatePlans
		->getById($testProductRatePlanId);

		self::$usualProductRatePlan = $sub;

		return $sub;
    }

    public function testGetAll() {
    	// short alias
    	$client = self::$client;
    	
		$productRatePlans = $client
		->productRatePlans
		->getAll();

		$firstPrp = $productRatePlans[0];

		$expected = Bf_ProductRatePlan::getResourcePath()->getEntityName();
		$actual = $firstPrp['@type'];

		// var_export($firstPrp);

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

	public function testContent()
    {	
    	$config = self::$config;

    	$prp = $this->getUsualProductRatePlan();

		$name = $prp
		->name;

		$expected = $config->getUsualPrpName();
		$actual = $name;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that PRP's name matches known value."
			);
    }

    /*public function testUpdateModel()
    {	
    	$profile = $this->recycleUsualSubscription();
    	$changeNameTo = 'Best';

		$firstNameBefore = $profile
		->firstName;

		$profile
		->firstName = $changeNameTo;

		$firstNameAfter = $profile
		->firstName;

		$expected = $changeNameTo;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's email is able to be changed in model."
			);
    }

    public function testUpdate()
    {	
    	$profile = $this->recycleUsualSubscription();

    	$uniqueString = date(DATE_RFC2822);

    	$changeNameTo = 'Test '.$uniqueString;

		$firstNameBefore = $profile
		->firstName;

		$profile
		->firstName = $changeNameTo;

		$profile->save();

		$updated = $this->getUsualSubscription();

		$firstNameAfter = $updated
		->firstName;

		$expected = $changeNameTo;
		$actual = $firstNameAfter;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that profile's firstName is able to be changed in model."
			);
    }*/
}