<?php

class Bf_CouponUniqueCodesResponse extends Bf_BillingEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'CouponUniqueCodesResponse');
	}
}
Bf_CouponUniqueCodesResponse::initStatics();
