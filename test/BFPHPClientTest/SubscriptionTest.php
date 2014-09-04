<?php
namespace BFPHPClientTest;
echo "Running Bf_Subscription tests for BillForward PHP Client Library.\n";

use BillForwardClient;
use Bf_Subscription;
Class Bf_SubscriptionTest extends \PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;
	protected static $usualSubscription = NULL;

	public static function setUpBeforeClass() {
		self::$config = new TestConfig();
		self::$client = self::$config->getClient();
	}

	protected function recycleUsualSubscription() {
		if (is_null(self::$usualSubscription)) {
			return $this->getUsualSubscription();
		} else {
			return self::$usualSubscription;
		}
    }

    protected function getUsualSubscription() {
    	// short alias
    	$client = self::$client;
    	$config = self::$config;
    	
    	$testSubscriptionId = $config->getUsualSubscriptionID();

		$sub = Bf_Subscription::getById($testSubscriptionId);

		self::$usualSubscription = $sub;

		return $sub;
    }

    public function testGetAll() {
    	// short alias
    	$client = self::$client;
    	
		$subscriptions = Bf_Subscription::getAll();

		$firstSub = $subscriptions[0];

		$expected = Bf_Subscription::getResourcePath()->getEntityName();
		$actual = $firstSub['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);
    }

	public function testContent()
    {	
    	// short alias
    	$config = self::$config;

    	$sub = $this->recycleUsualSubscription();

		$name = $sub
		->name;

		$expected = $config->getUsualSubscriptionName();
		$actual = $name;

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that subscription's name matches known value."
			);
    }
}