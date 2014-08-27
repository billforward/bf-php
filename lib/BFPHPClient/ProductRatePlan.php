<?php

class Bf_ProductRatePlan extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('taxation', Bf_TaxationLink::getClassName(), $json);
		$this->unserializeArrayEntities('pricingComponents', Bf_PricingComponent::getClassName(), $json);

		$this->unserializeEntity('product', Bf_Product::getClassName(), $json);
	}

	/**
	 * Gets Bf_TaxationLinks for this Bf_ProductRatePlan.
	 * @return Bf_TaxationLink[]
	 */
	public function getTaxationLinks() {
		return $this->taxation;
	}

	/**
	 * Gets Bf_PricingComponents for this Bf_ProductRatePlan.
	 * @return Bf_PricingComponent[]
	 */
	public function getPricingComponents() {
		return $this->pricingComponents;
	}

	/**
	 * Gets Bf_Product for this Bf_ProductRatePlan.
	 * @return Bf_Product
	 */
	public function getProduct() {
		return $this->product;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('product-rate-plans', 'productRatePlan');
	}
}
Bf_ProductRatePlan::initStatics();
