<?php

class Bf_Product extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('metadata', Bf_MetadataJson::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('products', 'product');
	}

	/**
	 * Fetches Bf_Subscriptions for this Bf_Product.
	 * @return Bf_Subscription[]
	 */
	public function getSubscriptions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getByProductID($this->id, $options, $customClient);
	}

	/**
	 * Fetches Bf_ProductRatePlans for this Bf_Product.
	 * @return Bf_ProductRatePlan[]
	 */
	public function getRatePlans($options = NULL, $customClient = NULL) {
		return Bf_ProductRatePlan::getForProduct($this->id, $options, $customClient);
	}
}
Bf_Product::initStatics();
