<?php

class Bf_Coupon extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('coupons', 'Coupon');
	}
}
Bf_Coupon::initStatics();
