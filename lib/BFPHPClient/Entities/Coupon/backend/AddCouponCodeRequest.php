<?php

/*
 * This entity models how a POST request can be made
 * to the subscriptions controller and receive (something like) a coupon in response.
 */
class Bf_AddCouponCodeRequest extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'AddCouponCodeRequest');
	}

	public static function applyCouponToSubscription(Bf_Coupon $coupon, $subscription) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		// make new Bf_Coupon using only the `couponCode` param
		$requestEntity = new Bf_Coupon(array(
			'couponCode' => $coupon->couponCode
			), $coupon->getClient());

		$endpoint = sprintf("%s/coupons",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_Coupon::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	public static function applyCouponCodeToSubscription($couponCode, $subscription) {
		$coupon = new Bf_Coupon();
		$coupon->couponCode = $couponCode;

		return static::applyCouponToSubscription($coupon, $subscription);
	}
}
Bf_AddCouponCodeRequest::initStatics();
