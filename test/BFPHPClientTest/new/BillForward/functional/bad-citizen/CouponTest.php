<?php
class CouponTest extends PHPUnit_Framework_TestCase {
	protected static $client = NULL;
	protected static $config = NULL;

	public static function setUpBeforeClass() {
		BFPHPClientTest\TestBase::initialize();
	}

	public static function makeRequiredEntities() {
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
			'account' => Bf_Account::create($models['account']),
			'uom' => array(
				Bf_UnitOfMeasure::create($models['uom'][0]),
				Bf_UnitOfMeasure::create($models['uom'][1])
				),
			'product' => Bf_Product::create($models['product'])
			);
		// having created product, make rate plan for it
		$models['pricingComponents'] = array(
			Models::PricingComponent($created['uom'][0], $models['pricingComponentTierLists'][0]),
			Models::PricingComponent($created['uom'][1], $models['pricingComponentTierLists'][1])
			);
		$models['ratePlan'] = Models::ProductRatePlan($created['product'], $models['pricingComponents']);
		$created['ratePlan'] = $models['ratePlan'];

		$models['subscription'] = Models::Subscription($created['ratePlan'], $created['account']);
		$created['subscription'] = $models['subscription'];

		return $created;
	}

	public function testCreate()
    {	
    	/*

		$this->assertEquals(
			$expected,
			$actual,
			"Asserting that type of any returned entity matches known value."
			);*/
    }
}
