<?php

class Bf_Coupon extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'Coupon');
	}

	/**
	 * Constructs a Bf_Coupon model.
	 * @param union[string ($id | $name) | Bf_ProductRatePlan $ratePlan] The product discounted by the coupon. <string>: ID or name of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @param union[string ($id | $name) | Bf_Product $entity | NULL] (Default: NULL) The product discounted by the coupon. <string>: ID or name of the Bf_Product. <Bf_Product>: The Bf_Product. <NULL>: Refer to the product recruited by the Bf_ProductRatePlan.
	 * @return Bf_Coupon The created coupon.
	 */
	public static function construct($ratePlan, $product = NULL) {
		if (is_null($product)) {
			// get rate plan
			$ratePlanEntity = Bf_ProductRatePlan::fetchIfNecessary($ratePlan);
			$product = $ratePlanEntity->productID;
		}
		$productIdentifier = Bf_Product::getIdentifier($product);
		$ratePlanIdentifier = Bf_ProductRatePlan::getIdentifier($ratePlan);

		$model = new static();

		$model->product = $product;
		$model->productRatePlan = $ratePlan;

		return $model;
	}

	/**
	 * Adds a collection of percentage CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addPercentageDiscounts($pricingComponentsToAmounts) {
		return $this->addDiscounts('percentageDiscount', $pricingComponentsToAmounts);
	}

	/**
	 * Adds a collection of cash CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addCashDiscounts($pricingComponentsToAmounts) {
		return $this->addDiscounts('cashDiscount', $pricingComponentsToAmounts);
	}

	/**
	 * Adds a collection of 'free units' CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addFreeUnits($pricingComponentsToAmounts) {
		return $this->addDiscounts('unitsFree', $pricingComponentsToAmounts);
	}

	/**
	 * Adds a collection of Bf_CouponDiscounts to this Bf_Coupon model.
	 * @param string ENUM['percentageDiscount', 'cashDiscount', 'unitsFree'] Nature of the discount being conferred.
	 ***
	 *  <percentageDiscount>
	 *  Discounts from the price of the specified pricing component: a percentage.
	 *  Example: $amount = 31 // 31% discount
	 *
	 *  <cashDiscount>
	 *  Discounts from the price of the specified pricing component: a lump sum.
	 *  Example: $amount = 31 // $31 discount
	 *
	 *  <unitsFree>
	 *  Discounts from the price of the specified pricing component: a quantity of units.
	 *  Example: $amount = 31 // 31 ice creams free
	 ***
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	protected function addDiscounts($discountNature, $pricingComponentsToAmounts) {
		$newDiscounts = array();
		foreach($pricingComponentsToAmounts as $pricingComponentIdentifier => $amount) {
			$newDiscount = Bf_CouponDiscount::construct($discountNature, $pricingComponentIdentifier, $amount);
			array_push($newDiscounts, $newDiscount);
		}
		$currentDiscounts = $this->discounts;
		$concatenatedDiscounts = array_merge($currentDiscounts, $newDiscounts);

		$this->discounts = $concatenatedDiscounts;
	}
}
Bf_Coupon::initStatics();
