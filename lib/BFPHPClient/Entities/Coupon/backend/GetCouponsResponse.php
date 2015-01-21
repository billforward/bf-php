<?php

class Bf_GetCouponsResponse extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'GetCouponsResponse');
	}
}
Bf_GetCouponsResponse::initStatics();
