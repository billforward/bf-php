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

	/**
	 * Returns (if existent; otherwise NULL) the Bf_PricingComponent whose name matching the one 
	 * provided.
	 * @param string the name upon which to match
	 * @return Bf_PricingComponent The matching Bf_PricingComponent (if any; otherwise NULL)
	 */
	public function getPricingComponentWithName($name) {
		$properties = array(
			'name' => $name
			);
		return $this->getPricingComponentWithProperties($properties);
	}

	/**
	 * Returns (if existent; otherwise NULL) the Bf_PricingComponent who has properties matching those 
	 * provided.
	 * @param array the Bf_PricingComponent properties upon which to match
	 * @return Bf_PricingComponent The matching Bf_PricingComponent (if any; otherwise NULL)
	 */
	public function getPricingComponentWithProperties(array $properties) {
		$pricingComponents = $this->getPricingComponents();

		return Bf_BillingEntity::fromCollectionFindFirstWhoMatchesProperties($pricingComponents, $properties);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('product-rate-plans', 'productRatePlan');
	}
}
Bf_ProductRatePlan::initStatics();
