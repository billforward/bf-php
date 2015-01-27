<?php

class Bf_Coupon extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'Coupon');
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

		$this->product = $productIdentifier;
		$this->productRatePlan = $ratePlanIdentifier;

		return $this;
	}

	/**
	 * Adds a percentage Bf_CouponDiscount to this Bf_Coupon model.
	 * @param union[string ($id | $name) | Bf_PricingComponent $entity] The pricing component to which the discount is applied. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @param number The magnitude of the discount being conferred.
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addPercentageDiscount($pricingComponent, $amount) {
		return $this->addDiscount('percentageDiscount', $pricingComponent, $amount);
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
	 * Adds a cash Bf_CouponDiscount to this Bf_Coupon model.
	 * @param union[string ($id | $name) | Bf_PricingComponent $entity] The pricing component to which the discount is applied. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @param number The magnitude of the discount being conferred.
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addCashDiscount($pricingComponent, $amount) {
		return $this->addDiscount('cashDiscount', $pricingComponent, $amount);
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
	 * Adds a 'free units' Bf_CouponDiscount to this Bf_Coupon model.
	 * @param union[string ($id | $name) | Bf_PricingComponent $entity] The pricing component to which the discount is applied. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @param number The magnitude of the discount being conferred.
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addFreeUnitsDiscount($pricingComponent, $amount) {
		return $this->addDiscount('unitsFree', $pricingComponent, $amount);
	}

	/**
	 * Adds a collection of 'free units' Bf_CouponDiscounts to this Bf_Coupon model.
	 * @param Dictionary<string ($id | $name), Number> Map of pricing component identifiers (ID or name) to magnitude of discount; array('Bandwidth usage' => 31)
	 * @return Bf_Coupon The modified coupon model.
	 */
	public function addFreeUnitsDiscounts($freeUnitsDiscounts) {
		return $this->addDiscounts('unitsFree', $freeUnitsDiscounts);
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
	 * @param union[string ($id | $name) | Bf_PricingComponent $entity] The pricing component to which the discount is applied. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @param number The magnitude of the discount being conferred.
	 * @return Bf_Coupon The modified coupon model.
	 */
	protected function addDiscount($discountNature, $pricingComponent, $amount) {
		if (!$this->discounts)
			$this->discounts = array();
		$newDiscount = Bf_CouponDiscount::construct($discountNature, $pricingComponent, $amount);
		array_push($this->discounts, $newDiscount);
		
		return $this;
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
		foreach($componentToAmountMap as $pricingComponentIdentifier => $amount) {
			$this->addDiscount($discountNature, $pricingComponentIdentifier, $amount);
		}
		return $this;
	}

	/**
	 * Gets Bf_Coupon by coupon code
	 * @param string The Coupon code of the sought Bf_Coupon.
	 * @return Bf_Coupon The fetched Bf_Coupon.
	 */
	public static function getByCode($couponCode, $options = NULL, $customClient = NULL) {
		return static::getByID($couponCode, $options = NULL, $customClient = NULL);
	}

	/**
	 * Gets Bf_Coupons for a given subscription ID
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription upon which to search. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_Coupon[] The fetched Bf_Coupons.
	 */
	public static function getForSubscription($subscription, $options = NULL, $customClient = NULL) {
		return Bf_GetCouponsRequest::getCouponsForSubscription($subscription, $options = NULL, $customClient = NULL);
	}

	/**
	 * Gets for this Coupon's base code a list of available unique coupon codes.
	 * @param string The base Coupon code for which to find available Unique codes.
	 * @return Bf_Coupon[] The fetched applicable coupons.
	 */
	public function getUniqueCodes($options = NULL, $customClient = NULL) {
		return static::getUniqueCodesFromBaseCode($this->couponCode, $options, $customClient);
	}

	/**
	 * Gets a list of available unique coupon codes derived from a specified base code.
	 * @param string The base Coupon code for which to find available unique codes.
	 * @return Bf_Coupon[] The fetched applicable coupons.
	 */
	public static function getUniqueCodesFromBaseCode($baseCode, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$baseCode) {
    		trigger_error("Cannot lookup empty coupon base code!", E_USER_ERROR);
		}

		$encoded = rawurlencode($baseCode);

		$endpoint = "/$encoded/codes";

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Creates unique coupon codes derived from this coupon's base code. These can be applied to subscriptions.
	 * @param string The base Coupon code for which to create unique codes.
	 * @return Bf_Coupon[] The created applicable coupons.
	 */
	public function createUniqueCodes($quantity) {
		return static::createUniqueCodesFromBaseCode($this->couponCode, $quantity);
	}

	/**
	 * Creates unique coupon codes derived from a specified base code. These can be applied to subscriptions.
	 * @param string The base Coupon code for which to create unique codes.
	 * @return Bf_Coupon[] The created applicable coupons.
	 */
	public static function createUniqueCodesFromBaseCode($baseCode, $quantity) {
		// empty IDs are no good!
		if (!$baseCode) {
    		trigger_error("Cannot lookup empty coupon code!", E_USER_ERROR);
		}

		$coupon = new Bf_Coupon();
		$coupon->quantity = $quantity;

		$encoded = rawurlencode($baseCode);

		$endpoint = "$encoded/codes";

		return static::postEntityAndGrabFirst($endpoint, $coupon, $client);
	}

	/**
	 * Applies Bf_Coupon to the specified Bf_Subscription
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_Coupon The applied coupon.
	 */
	public function applyToSubscription($subscription) {
		return Bf_AddCouponCodeRequest::applyCouponToSubscription($this, $subscription);
	}

	/**
	 * Applies to specified Bf_Subscription, a coupon by a specified code.
	 * @param string The Coupon code to apply.
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_Coupon The applied coupon.
	 */
	public static function applyCouponCodeToSubscription($couponCode, $subscription){
		return Bf_AddCouponCodeRequest::applyCouponCodeToSubscription($couponCode, $subscription);
	}

	/**
	 * Removes coupon by the specified code.
	 * @param string The Coupon code to remove.
	 * @return Bf_Coupon The removed coupon.
	 */
	public static function removeCouponCode($couponCode) {
		// empty IDs are no good!
		if (!$couponCode) {
    		trigger_error("Cannot lookup empty coupon code!", E_USER_ERROR);
		}

		$endpoint = rawurlencode($couponCode);

		$client = Bf_BillingEntity::getSingletonClient();

		$retiredEntity = static::retireAndGrabFirst($endpoint, NULL, $client);
		return $retiredEntity;
	}
}
Bf_Coupon::initStatics();
