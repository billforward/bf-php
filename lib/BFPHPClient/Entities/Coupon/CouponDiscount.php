<?php

class Bf_CouponDiscount extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'CouponDiscount');
	}
}
Bf_CouponDiscount::initStatics();
