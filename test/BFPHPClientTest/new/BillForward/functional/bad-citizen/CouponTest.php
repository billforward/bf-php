<?php
namespace BFPHPClientTest;
class CouponTest extends \PHPUnit_Framework_TestCase {
	protected static $entities = NULL;

	public static function setUpBeforeClass() {
		TestBase::initialize();
		self::$entities = self::makeRequiredEntities();
	}

	public static function makeRequiredEntities() {
		$useExistingOrMakeNew = function($entityClass, $model) {
			$name = $model->name;
			$existing = $entityClass::getByID($name);
			if ($existing) {
				return $existing;
			}
			return $entityClass::getByID($name);
		};

		$models = array(
			'account' => Models::Account(),
			'uom' => array(
				Models::UnitOfMeasure(),
				Models::UnitOfMeasure2(),
				),
			'product' => Models::MonthlyProduct(),
			'pricingComponentTierLists' => array(
				Models::PricingComponentTiers(),
				Models::PricingComponentTiers2()
				)
			);
		$created = array(
			'account' => \Bf_Account::create($models['account']),
			'uom' => array(
				$useExistingOrMakeNew(\Bf_UnitOfMeasure::getClassName(), $models['uom'][0]),
				$useExistingOrMakeNew(\Bf_UnitOfMeasure::getClassName(), $models['uom'][1]),
				),
			'product' => \Bf_Product::create($models['product'])
			);
		// having created product, make rate plan for it
		$models['pricingComponents'] = array(
			Models::PricingComponent($created['uom'][0], $models['pricingComponentTierLists'][0]),
			Models::PricingComponent2($created['uom'][1], $models['pricingComponentTierLists'][1])
			);
		$models['ratePlan'] = Models::ProductRatePlan($created['product'], $models['pricingComponents']);
		$created['ratePlan'] = \Bf_ProductRatePlan::create($models['ratePlan']);

		$models['subscription'] = Models::Subscription($created['ratePlan'], $created['account']);
		$created['subscription'] = \Bf_Subscription::create($models['subscription']);

		return $created;
	}

	public function testCreate()
    {	
    	var_export(self::$entities['subscription']);

    	/*

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);*/
    }
}
