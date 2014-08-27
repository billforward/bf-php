<?php

class Bf_PricingComponentValueChange extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-component-value-changes', 'PricingComponentValueChange');
	}
}
Bf_PricingComponentValueChange::initStatics();
