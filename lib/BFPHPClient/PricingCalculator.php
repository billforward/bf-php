<?php

class Bf_PricingCalculator {
	protected static $_resourcePath;

	/**
	 * Requests a price-calculation. Request is built using this PricingCalculator entity, plus
	 * specified PriceRequest entity.
	 * Returns an entity which embodies the response.
	 * @param Bf_PricingCalculator $requestEntity 
	 * @return Bf_PriceCalculation $responseEntity
	 */
	public static function requestPriceCalculation(Bf_PriceRequest $requestEntity) {
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

		$constructedEntity = Bf_PriceCalculation::callMakeEntityFromResponseStatic($response, $client);

		return $constructedEntity;
	}

	/**
	 * Requests a price-calculation. Request is built using specified PricingCalculator entity, plus
	 * specified AmendmentPriceRequest entity.
	 * Returns an entity which embodies the response.
	 * @param Bf_AmendmentPriceRequest $requestEntity 
	 * @return Bf_PriceCalculation $responseEntity
	 */
	public static function requestAmendmentPriceAndTime(Bf_AmendmentPriceRequest $requestEntity) {
		$entity = $requestEntity;
		$serial = $entity->getSerialized();

		$client = $entity
		->getClient();

		$endpointPrefix = static::getResourcePath()
		->getPath();
		$endpointPostfix = "/coupon-instance/initialisation";
		$endpoint = $endpointPrefix.$endpointPostfix;

		$response = $client
		->doPost($endpoint, $serial);

		$constructedEntity = Bf_PriceCalculation::callMakeEntityFromResponseStatic($response, $client);

		return $constructedEntity;
	}

	public static function getResourcePath() {
		return static::$_resourcePath;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-calculator', 'priceCalculation');
	}
}
Bf_PricingCalculator::initStatics();
