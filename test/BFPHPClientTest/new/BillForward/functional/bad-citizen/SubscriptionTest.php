<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_SubscriptionTest extends \PHPUnit_Framework_TestCase {
	protected static $models = NULL;
	protected static $created = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
		self::makeRequiredEntities();
	}

	public static function makeRequiredEntities() {
		$useExistingOrMakeNew = function($entityClass, $model) {
			$name = $model->name;
			try {
				$existing = $entityClass::getByID($name);
				if ($existing) {
					return $existing;
				}
			} catch(Bf_NoMatchingEntityException $e) {
				return $entityClass::create($model);
			}
		};

		$models = array(
			'account' => Models::Account(),
			'uom' => array(
				Models::UnitOfMeasure(),
				Models::UnitOfMeasure2(),
				Models::UnitOfMeasure2(),
				),
			'product' => Models::MonthlyRecurringProduct(),
			'pricingComponentTierLists' => array(
				Models::PricingComponentTiers(),
				Models::PricingComponentTiers2(),
				Models::PricingComponentTiers2()
				)
			);
		$created = array(
			'account' => Bf_Account::create($models['account']),
			'uom' => array(
				$useExistingOrMakeNew(Bf_UnitOfMeasure::getClassName(), $models['uom'][0]),
				$useExistingOrMakeNew(Bf_UnitOfMeasure::getClassName(), $models['uom'][1]),
				),
			'product' => $useExistingOrMakeNew(Bf_Product::getClassName(), $models['product'])
			);
		// having created product, make rate plan for it
		$models['pricingComponents'] = array(
			Models::PricingComponent($created['uom'][0], $models['pricingComponentTierLists'][0]),
			Models::PricingComponent2($created['uom'][1], $models['pricingComponentTierLists'][1]),
			Models::PricingComponent3($created['uom'][1], $models['pricingComponentTierLists'][1])
			);
		$models['ratePlan'] = Models::ProductRatePlan($created['product'], $models['pricingComponents']);
		$created['ratePlan'] = Bf_ProductRatePlan::create($models['ratePlan']);

		$models['subscription'] = Models::Subscription($created['ratePlan'], $created['account']);
		$created['subscription'] = Bf_Subscription::create($models['subscription']);

		self::$models = $models;
		self::$created = $created;
	}

    public function testGetAll() {    	
		$subscriptions = Bf_Subscription::getAll();

		$firstSub = $subscriptions[0];

		$expected = Bf_Subscription::getResourcePath()->getEntityName();
		$actual = $firstSub['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
    }

	public function testContent()
    {
    	$subscription = self::$created['subscription'];
    	$ratePlanModel = self::$models['subscription'];

		$expected = $ratePlanModel->creditEnabled;
		$actual = $subscription->creditEnabled;

		$this->assertEquals(
			$expected,
			$actual,
			"Subscription's creditEnabled matches known value."
			);
    }
}