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
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$endpoint = sprintf("%s/coupons",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_Coupon::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	public static function getApplicableCouponsForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$endpoint = sprintf("%s/applicable-coupons",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_Coupon::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}
}
Bf_GetCouponsRequest::initStatics();
