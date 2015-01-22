<?php

class Bf_GetCouponsResponse extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'GetCouponsResponse');
	}

	public static function getCouponsForSubscription() {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$endpoint = "/subscription/$subscriptionIdentifier";

		$responseEntity = Bf_Coupon::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}
}
Bf_GetCouponsResponse::initStatics();
