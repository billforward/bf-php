<?php

/*
 * This is not a real entity in BF, but models how a GET request can be made
 * to the subscriptions controller and receive (something like) a coupon in response.
 */
class Bf_GetCouponsRequest extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'GetCouponsRequest');
	}

	public static function getCouponsForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		$endpoint = "/$subscriptionIdentifier/coupons";

		$responseEntity = Bf_Coupon::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}
}
Bf_GetCouponsRequest::initStatics();
