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
	 * @return Bf_AmendmentPriceNTime $responseEntity
	 */
	public static function requestUpgradePrice(Bf_AmendmentPriceRequest $requestEntity) {
		$entity = $requestEntity;
		$serial = $entity->getSerialized();

		$client = $entity
		->getClient();

		$endpointPrefix = static::getResourcePath()
		->getPath();
		$endpointPostfix = "/amendment-cost";
		$endpoint = $endpointPrefix.$endpointPostfix;

		$response = $client
		->doPost($endpoint, $serial);

		$constructedEntity = Bf_AmendmentPriceNTime::callMakeEntityFromResponseStatic($response, $client);

		return $constructedEntity;
	}

	/**
	 * Requests a price-calculation. Request is built using specified PricingCalculator entity, plus
	 * specified AmendmentPriceRequest entity.
	 * Returns an entity which embodies the response.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string (option 1) ID of the subscription to calculate price for
	 * @param Bf_Subscription (option 2) model of the subscription to calculate price for; provide this to avoid fetching from API
	 * @return array Price now and after (array('now' => Bf_PriceCalculation, 'after' => Bf_PriceCalculation, 'priceDiff' => int))
	 */
	public static function requestPriceNowVsAfter(array $namesToValues, $subscriptionID = NULL, Bf_Subscription $subscriptionModel = NULL) {
		$subscription;
		if (is_null($subscriptionModel)) {
			if (is_null($subscriptionID)) {
				throw new \Exception('Received null subscription, and null subscription ID.');
			}

			// fetch from API
			$subscription = Bf_Subscription::getByID($subscriptionID);	
		} else {
			$subscription = $subscriptionModel;
		}

		$productRatePlan = $subscription->getProductRatePlan();

		$nowPriceRequest = new Bf_PriceRequest(array(
            'productRatePlanID' => $productRatePlan->id,
            'productID' => $productRatePlan->productID,
            'updatedPricingComponentValues' => $subscription->pricingComponentValues
            ));

		$afterPriceRequest = Bf_PriceRequest::forPricingComponentsByName($namesToValues, NULL, $productRatePlan);

		$nowPriceRequestCalculated = static::requestPriceCalculation($nowPriceRequest);
		$afterPriceRequestCalculated = static::requestPriceCalculation($afterPriceRequest);

		$priceDiff = $afterPriceRequestCalculated->discountedCost - $nowPriceRequestCalculated->discountedCost;

		return array(
			'now' => $nowPriceRequestCalculated,
			'after' => $afterPriceRequestCalculated,
			'priceDiff' => $priceDiff
			);
	}

	public static function getResourcePath() {
		return static::$_resourcePath;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-calculator', 'priceCalculation');
	}
}
Bf_PricingCalculator::initStatics();
