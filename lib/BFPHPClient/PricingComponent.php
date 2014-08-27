<?php

class Bf_PricingComponent extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('tiers', Bf_PricingComponentTier::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-components', 'PricingComponent');
	}
}
Bf_PricingComponent::initStatics();
