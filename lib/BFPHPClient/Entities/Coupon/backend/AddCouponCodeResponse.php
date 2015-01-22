<?php

class Bf_AddCouponCodeResponse extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'AddCouponCodeResponse');
	}
}
Bf_AddCouponCodeResponse::initStatics();
