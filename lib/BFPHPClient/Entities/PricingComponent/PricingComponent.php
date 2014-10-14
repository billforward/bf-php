<?php

class Bf_PricingComponent extends Bf_MutableEntity {
	public static function getAll($options = NULL, $customClient = NULL) {
		trigger_error('Get All support is denied for this entity; '
		 .'at the time of writing, no working API endpoint exists to support it. '
		 .'The entity can be GETted through cascade (i.e. GET a related entity), or by ID only.',
		 E_USER_ERROR);
	}

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
