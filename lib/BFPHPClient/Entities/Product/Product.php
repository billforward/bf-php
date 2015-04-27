<?php

class Bf_Product extends Bf_MutableEntity {
	protected static $_resourcePath;

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
