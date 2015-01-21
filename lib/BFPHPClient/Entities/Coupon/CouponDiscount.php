<?php

class Bf_CouponDiscount extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'CouponDiscount');
	}

	/**
	 * Constructs a Bf_CouponDiscount model.
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
	 * @return Bf_CouponDiscount The created coupon discount.
	 */
	public static function construct($discountNature, $pricingComponent, $amount) {
		$pricingComponentIdentifier = Bf_PricingComponent::getIdentifier($pricingComponent);

		$model = new static();
		$model->pricingComponent = $pricingComponentIdentifier;

		if ($discountNature === 'percentageDiscount') {
			$model->percentageDiscount = $amount;
		} else if ($discountNature === 'cashDiscount') {
			$model->cashDiscount = $amount;
		} else if ($discountNature === 'unitsFree') {
			$model->unitsFree = $amount;
		} else {
			trigger_error("Unsupported/unrecognised 'discountNature': '$discountNature'.", E_USER_ERROR);
		}

		return $model;
	}
}
Bf_CouponDiscount::initStatics();
