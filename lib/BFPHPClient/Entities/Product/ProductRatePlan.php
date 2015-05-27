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
		// if this is just a model made on our end, we might not the serialized product yet
		// alternatively: newer BillForward may omit serialized product and necessitate a fetch
		if (!$this->product) {
			if (!$this->productID) {
				throw new Bf_PreconditionFailedException("This Bf_ProductRatePlan has neither a 'product' specified, nor a 'productID' by which to obtain said product.");
			}
			$this->product = Bf_Product::getByID($this->productID);
		}
		return $this->product;
	}

	/**
	 * Gets Bf_ProductRatePlans by name or ID
	 * @note This is merely a getByID that returns a collection (supports the case where globally, multiple rate plans exist with the same name)
	 * @param union[string ($id | $name) | Bf_ProductRatePlan $entity] The ProductRatePlan that you wish to GET. <string>: ID or name of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @return Bf_ProductRatePlan[]
	 */
	public static function getByName($ratePlan, $options = NULL, $customClient = NULL) {
		$ratePlanIdentifier = Bf_ProductRatePlan::getIdentifier($ratePlan);

		$endpoint = sprintf("/%s",
			rawurlencode($ratePlanIdentifier)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_ProductRatePlans for a given Bf_Product
	 * @param union[string ($id | $name) | Bf_Product $entity] The Product whose rate plans you wish to GET. <string>: ID or name of the Bf_Product. <Bf_Product>: The Bf_Product.
	 * @return Bf_ProductRatePlan[]
	 */
	public static function getForProduct($product, $options = NULL, $customClient = NULL) {
		$productIdentifier = Bf_Product::getIdentifier($product);

		$endpoint = sprintf("/product/%s", rawurlencode($productIdentifier));

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets (by name or ID) Bf_ProductRatePlans belonging to a Bf_Product by name or ID
	 * @param union[string ($id | $name) | Bf_Product $entity] The Product whose rate plans you wish to GET. <string>: ID or name of the Bf_Product. <Bf_Product>: The Bf_Product.
	 * @param union[string ($id | $name) | Bf_ProductRatePlan $entity] The ProductRatePlan that you wish to GET from that Product. <string>: ID or name of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @return Bf_ProductRatePlan
	 */
	public static function getByProductAndRatePlanID($product, $ratePlan, $options = NULL, $customClient = NULL) {
		$productIdentifier = Bf_Product::getIdentifier($product);
		$ratePlanIdentifier = Bf_ProductRatePlan::getIdentifier($ratePlan);

		$endpoint = sprintf("/product/%s/rate-plan/%s",
			rawurlencode($productIdentifier),
			rawurlencode($ratePlanIdentifier)
			);

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Fetches Bf_Subscriptions for this Bf_ProductRatePlan.
	 * @return Bf_Subscription[]
	 */
	public function getSubscriptions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getByRatePlanID($this->id, $options, $customClient);
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

	/**
	 * Retrieves a quote for the price of the specified quantities of pricing components of the product rate plan
	 * @see Bf_Quote::getQuote()
	 * @return Bf_APIQuote The price quote
	 */
	public function getQuote(
		array $namesToValues,
		array $quoteOptions = array(
			'couponCodes' => array(),
			'quoteFor' => 'InitialPeriod'
			)) {
		return Bf_Quote::getQuote($this, $namesToValues, $quoteOptions);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('product-rate-plans', 'productRatePlan');
	}
}
Bf_ProductRatePlan::initStatics();
