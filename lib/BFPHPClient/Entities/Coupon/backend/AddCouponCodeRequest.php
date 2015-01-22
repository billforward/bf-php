<?php

class Bf_AddCouponCodeRequest extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'AddCouponCodeRequest');
	}

	public static function applyCouponToSubscription(Bf_Coupon $coupon, $subscription) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$endpoint = "/$subscriptionIdentifier/coupon";

		$responseEntity = Bf_Coupon::getClassName();

		$constructedEntity = static::postAndGrabFirst($endpoint, $serial, $client, $responseEntity);
		return $constructedEntity;
	}
}
Bf_AddCouponCodeRequest::initStatics();
