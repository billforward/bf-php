<?php

class Bf_CouponUniqueCodesRequest extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'CouponUniqueCodesRequest');
	}
}
Bf_CouponUniqueCodesRequest::initStatics();
