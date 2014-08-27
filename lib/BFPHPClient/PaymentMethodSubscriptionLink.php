<?php

class Bf_PaymentMethodSubscriptionLink extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('payment-method-subscription-links', 'PaymentMethodSubscriptionLink');
	}
}
Bf_PaymentMethodSubscriptionLink::initStatics();
