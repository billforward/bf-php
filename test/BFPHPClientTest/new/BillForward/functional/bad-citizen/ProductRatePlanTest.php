<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

class Bf_ProductRatePlanTest extends \PHPUnit_Framework_TestCase {
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
			'uom' => array(
				$useExistingOrMakeNew(Bf_UnitOfMeasure::getClassName(), $models['uom'][0]),
				$useExistingOrMakeNew(Bf_UnitOfMeasure::getClassName(), $models['uom'][1]),
				),
			'product' => Bf_Product::create($models['product'])
			);
		// having created product, make rate plan for it
		$models['pricingComponents'] = array(
			Models::PricingComponent($created['uom'][0], $models['pricingComponentTierLists'][0]),
			Models::PricingComponent2($created['uom'][1], $models['pricingComponentTierLists'][1]),
			Models::PricingComponent3($created['uom'][1], $models['pricingComponentTierLists'][1])
			);
		$models['ratePlan'] = Models::ProductRatePlan($created['product'], $models['pricingComponents']);
		$created['ratePlan'] = Bf_ProductRatePlan::create($models['ratePlan']);

		self::$models = $models;
		self::$created = $created;
	}

    public function testGetByID() {
    	$ratePlan = self::$created['ratePlan'];
    	$ratePlanId = $ratePlan->id;

		$fetchedRatePlan = Bf_ProductRatePlan::getByID($ratePlanId);

		$expected = Bf_ProductRatePlan::getResourcePath()->getEntityName();
		$actual = $fetchedRatePlan['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
    }

    public function testGetAll() {    	
		$productRatePlans = Bf_ProductRatePlan::getAll();

		$firstRatePlan = $productRatePlans[0];

		$expected = Bf_ProductRatePlan::getResourcePath()->getEntityName();
		$actual = $firstRatePlan['@type'];

		$this->assertEquals(
			$expected,
			$actual,
			"Type of any returned entity matches known value."
			);
    }

	public function testContent()
    {
    	$ratePlan = self::$created['ratePlan'];
    	$ratePlanModel = self::$models['ratePlan'];

		$expected = $ratePlanModel->name;
		$actual = $ratePlan->name;

		$this->assertEquals(
			$expected,
			$actual,
			"Rate plan's name matches known value."
			);
    }
}