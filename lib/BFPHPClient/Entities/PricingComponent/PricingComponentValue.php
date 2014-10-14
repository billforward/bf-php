<?php

class Bf_PricingComponentValue extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-component-values', 'PricingComponentValue');
	}
}
Bf_PricingComponentValue::initStatics();
