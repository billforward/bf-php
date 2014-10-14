<?php

class Bf_PricingComponentTier extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-component-tiers', 'pricingComponentTier');
	}
}
Bf_PricingComponentTier::initStatics();
