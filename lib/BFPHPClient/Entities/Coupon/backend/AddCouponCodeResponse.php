<?php

class Bf_AddCouponCodeResponse extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'AddCouponCodeResponse');
	}

	public static function applyCouponToSubscription(Bf_Coupon $coupon, $subscription, $options = NULL, $customClient = NULL) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$endpoint = "/$subscriptionIdentifier/coupon";

		$responseEntity = Bf_Coupon::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}
}
Bf_AddCouponCodeResponse::initStatics();
