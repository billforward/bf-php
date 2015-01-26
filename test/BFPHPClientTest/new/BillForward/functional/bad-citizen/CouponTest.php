<?php
use BFPHPClientTest\TestBase;
use BFPHPClientTest\Models;

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

		$models['subscription'] = Models::Subscription($created['ratePlan'], $created['account']);
		$created['subscription'] = Bf_Subscription::create($models['subscription']);

		return $created;
	}

	public function testCreate()
    {	
    	$subscription = self::$entities['subscription'];

    	//--Discount pricing component by 100% for 3 billing periods
		// Create model of coupon
		// unique name for test
    	$uniqueString = time();
    	$couponCode = "TEST_$uniqueString";

		$coupon = new Bf_Coupon(array(
			'name' => '3 Months free',
			'couponCode' => $couponCode,
			'coupons' => 100,
			'uses' => 3
		));

		$coupon->setRatePlan('Gold membership');

		$coupon->addPercentageDiscount("CPU", 20);

		$createdCoupon = Bf_Coupon::create($coupon);

		$subscription->activate();

		$newAdvanceComponents = array(
			'CPU' => 40
			);
		$subscription->upgrade($newAdvanceComponents);

		$appliedCoupon = $createdCoupon->applyToSubscription($subscription);
    }

    public function testCreateCompound()
    {	
    	$subscription = self::$entities['subscription'];

		// Create model of coupon
		// unique name for test
    	$uniqueString = time();
    	$couponCode = "TEST2_$uniqueString";

		$coupon = new Bf_Coupon(array(
			'name' => '3 Months free',
			'couponCode' => $couponCode,
			'coupons' => 100,
			'uses' => 3
		));

		$coupon->setRatePlan('Gold membership');

		// $5 off CPU
		$coupon->addCashDiscount("CPU", 5);
		// 5 Mbps free P2P Traffic
		$coupon->addFreeUnitsDiscount("P2P Traffic", 5);
		// 10 Mbps free bandwidth
		$coupon->addFreeUnitsDiscount("Bandwidth", 10);

		$createdCoupon = Bf_Coupon::create($coupon);
    }
}
