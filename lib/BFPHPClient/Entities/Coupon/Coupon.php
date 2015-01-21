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
	 * @return Bf_Coupon The created coupon model.
	 */
	public static function construct($ratePlan, $product = NULL) {
		$model = new static();

		$model->setRatePlan($ratePlan, $product);

		return $model;
	}

	/**
	 * Assigns to this Bf_Coupon a Bf_ProductRatePlan and Bf_Product.
	 * @param union[string ($id | $name) | Bf_ProductRatePlan $ratePlan] The product discounted by the coupon. <string>: ID or name of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @param union[string ($id | $name) | Bf_Product $entity | NULL] (Default: NULL) The product discounted by the coupon. <string>: ID or name of the Bf_Product. <Bf_Product>: The Bf_Product. <NULL>: Fetch first result for Bf_ProductRatePlan (if identifying rate plan by name, please ensure this rate plan's name is unique), and use its Bf_Product.
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function setRatePlan($ratePlan, $product = NULL) {
		if (is_null($product)) {
			// get rate plan
			$ratePlanEntity = Bf_ProductRatePlan::fetchIfNecessary($ratePlan);
			$product = $ratePlanEntity->productID;
		}
		$productIdentifier = Bf_Product::getIdentifier($product);
		$ratePlanIdentifier = Bf_ProductRatePlan::getIdentifier($ratePlan);

		$this->product = $product;
		$this->productRatePlan = $ratePlan;

		return $this;
	}

	/**
	 * Adds a collection of percentage Bf_CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addPercentageDiscounts($percentageDiscounts) {
		return $this->addDiscounts('percentageDiscount', $percentageDiscounts);
	}

	/**
	 * Adds a collection of cash Bf_CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addCashDiscounts($cashDiscounts) {
		return $this->addDiscounts('cashDiscount', $cashDiscounts);
	}

	/**
	 * Adds a collection of 'free units' Bf_CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addFreeUnits($freeUnits) {
		return $this->addDiscounts('unitsFree', $freeUnits);
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
	protected function addDiscounts($discountNature, $componentToAmountMap) {
		$newDiscounts = array();
		foreach($componentToAmountMap as $pricingComponentIdentifier => $amount) {
			$newDiscount = Bf_CouponDiscount::construct($discountNature, $pricingComponentIdentifier, $amount);
			array_push($newDiscounts, $newDiscount);
		}
		$currentDiscounts = $this->discounts;
		$concatenatedDiscounts = array_merge($currentDiscounts, $newDiscounts);

		$this->discounts = $concatenatedDiscounts;
	}

	/**
	 * Gets Bf_Coupons for a given subscription ID
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription upon which to search. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_Coupon[]
	 */
	public static function getForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$endpoint = "/subscription/$subscriptionIdentifier";

		return static::getCollection($endpoint, $options, $customClient);
	}
}
Bf_Coupon::initStatics();
