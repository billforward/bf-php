<?php

class Bf_PricingCalculator extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		trigger_error('Create support is denied for this entity; '
		 .'This entity exists solely to build and send requests (using its helper functions). ',
		 E_USER_ERROR);
	}
	
	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		trigger_error('Get by ID support is denied for this entity; '
		 .'This entity exists solely to build and send requests (using its helper functions). ',
		 E_USER_ERROR);
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		trigger_error('Get All support is denied for this entity; '
		 .'This entity exists solely to build and send requests (using its helper functions). ',
		 E_USER_ERROR);
	}

	public function save() {
		trigger_error('Save support is denied for this entity; '
		 .'This entity exists solely to build and send requests (using its helper functions). ',
		 E_USER_ERROR);
	}

	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('rule-satisfactions', Bf_RuleSatisfaction::getClassName(), $json);
		$this->unserializeArrayEntities('component-costs', Bf_RuleSatisfaction::getClassName(), $json);
		$this->unserializeArrayEntities('component-discounts', Bf_ComponentDiscount::getClassName(), $json);
		$this->unserializeArrayEntities('price-requests', Bf_PriceRequest::getClassName(), $json);
	}

	/**
	 * Gets Bf_RuleSatisfactions for this Bf_PricingCalculator.
	 * @return Bf_RuleSatisfaction[]
	 */
	public function getRuleSatisfactions() {
		$escapedName = 'rule-satisfactions';
		return $this->$escapedName;
	}

	/**
	 * Gets Bf_ComponentCosts for this Bf_PricingCalculator.
	 * @return Bf_ComponentCost[]
	 */
	public function getComponentCosts() {
		$escapedName = 'component-costs';
		return $this->$escapedName;
	}

	/**
	 * Gets Bf_ComponentDiscounts for this Bf_PricingCalculator.
	 * @return Bf_ComponentDiscount[]
	 */
	public function getComponentDiscounts() {
		$escapedName = 'component-discounts';
		return $this->$escapedName;
	}

	/**
	 * Gets Bf_PriceRequests for this Bf_PricingCalculator.
	 * @return Bf_PriceRequest[]
	 */
	public function getPriceRequests() {
		$escapedName = 'price-requests';
		return $this->$escapedName;
	}

	/**
	 * Requests a price-calculation. Request is built using specified PricingCalculator entity.
	 * Returns an entity which embodies the response.
	 * @param Bf_PricingCalculator $requestEntity 
	 * @return Bf_PriceCalculation $responseEntity
	 */
	public static function calculatePrice(Bf_PricingCalculator $requestEntity) {
		$entity = $requestEntity;
		$serial = $entity->getSerialized();

		$client = $entity
		->getClient();

		$endpointPrefix = static::getResourcePath()
		->getPath();
		$endpointPostfix = "/product-rate-plan";
		$endpoint = $endpointPrefix.$endpointPostfix;

		$response = $client
		->doPost($endpoint, $serial);

		$constructedEntity = Bf_PriceCalculation::makeEntityFromResponseStatic($response, $client);

		return $constructedEntity;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-calculator', 'priceCalculation');
	}
}
Bf_PricingCalculator::initStatics();
