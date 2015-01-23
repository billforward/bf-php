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
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$serial = $coupon->getSerialized();
		$client = $coupon->getClient();

		$endpoint = "$subscriptionIdentifier/coupon";

		$responseEntity = Bf_Coupon::getClassName();

		$constructedEntity = static::postAndGrabFirst($endpoint, $serial, $client, $responseEntity);
		return $constructedEntity;
	}

	public static function applyCouponCodeToSubscription($couponCode, $subscription) {
		$coupon = new Bf_Coupon();
		$coupon->couponCode = $couponCode;

		return static::applyCouponToSubscription($coupon, $subscription);
	}
}
Bf_AddCouponCodeRequest::initStatics();
