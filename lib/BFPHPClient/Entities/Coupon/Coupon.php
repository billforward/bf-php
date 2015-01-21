<?php

class Bf_Coupon extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'Coupon');
	}

	/**
	 * Constructs a Coupon model.
	 * @param union[string ($id | $name) | Bf_ProductRatePlan $ratePlan] Identifier of the product discounted by the coupon. <string>: ID or name of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @param union[string ($id | $name) | Bf_Product $product | NULL] (Default: NULL) Identifier of the product discounted by the coupon. <string>: ID or name of the Bf_Product. <Bf_Product>: The Bf_Product. <NULL>: Refer to the product recruited by the Bf_ProductRatePlan.
	 * @return Bf_Coupon The created coupon.
	 */
	public static function construct($ratePlan, $product = NULL) {
		$ratePlanIdentifier = Bf_ProductRatePlan::getIdentifier($ratePlan);
		if (is_null($product)) {
			// get rate plan
			$ratePlanEntity = Bf_ProductRatePlan::fetchIfNecessary($ratePlan);
			$product = $ratePlanEntity->productID;
		}
		$productIdentifier = Bf_Product::getIdentifier($product);

		$model = new static();

		$model->product = $product;
		$model->productRatePlan = $ratePlan;

		return $model;
	}
	
	public function setDiscounts() {

	}
}
Bf_Coupon::initStatics();
