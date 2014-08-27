<?php

class Bf_PaymentMethod extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('payment-methods', 'paymentMethod');
	}
}
Bf_PaymentMethod::initStatics();