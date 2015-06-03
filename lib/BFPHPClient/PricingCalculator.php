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

		$endpoint = sprintf("%s/product-rate-plan"
			static::getResourcePath()->getPath()
			);

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
	/*public static function requestUpgradePrice(Bf_AmendmentPriceRequest $requestEntity) {
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
	}*/

	/**
	 * Requests a calculation of the price required to upgrade the subscription to the specified values.
	 * The price is worked out by applying the higher cost to only the remaining time in the billing period (pro-rata'd).
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string (option 1) ID of the subscription to calculate price for
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. The time at which the upgrade would take place.
	 * @param Bf_Subscription (option 2) model of the subscription to calculate price for; provide this to avoid fetching from API
	 * @return Bf_AmendmentPriceNTime $responseEntity
	 */
	public static function requestUpgradePrice(array $namesToValues, $subscriptionID = NULL, $asOfTime = 'Immediate', Bf_Subscription $subscriptionModel = NULL) {
		$subscription;
		if (is_null($subscriptionModel)) {
			if (is_null($subscriptionID)) {
				throw new Bf_EmptyArgumentException('Received null subscription, and null subscription ID.');
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

		$asOfDate = NULL; // defaults to Immediate
		if (is_int($asOfTime)) {
			$asOfDate = Bf_BillingEntity::makeBillForwardDate($asOfTime);
		} else if ($asOfTime === 'AtPeriodEnd') {
			if (!is_null($subscription->currentPeriodEnd)) {
				$asOfDate = $subscription->currentPeriodEnd;
				$asOfTime = Bf_BillingEntity::makeUTCTimeFromBillForwardDate($subscription->currentPeriodEnd);
			} else {
				throw new Bf_PreconditionFailedException('Cannot set As Of Time to period end, because the subscription does not declare a period end.');
			}
		} else {
			$asOfTime = time();
			$asOfDate = Bf_BillingEntity::makeBillForwardDate($asOfTime);
		}

		$nowPriceRequestCalculated = static::requestPriceCalculation($nowPriceRequest);
		$afterPriceRequestCalculated = static::requestPriceCalculation($afterPriceRequest);

		$priceDiff = $afterPriceRequestCalculated->discountedCost - $nowPriceRequestCalculated->discountedCost;

		$periodSize = Bf_BillingEntity::makeUTCTimeFromBillForwardDate($subscription->currentPeriodEnd) - Bf_BillingEntity::makeUTCTimeFromBillForwardDate($subscription->currentPeriodStart);
		$periodRemaining = Bf_BillingEntity::makeUTCTimeFromBillForwardDate($subscription->currentPeriodEnd) - $asOfTime;

		// var_export($periodSize);
		// var_export($periodRemaining);

		$fractionRemaining = $periodRemaining/$periodSize;

		// var_export($fractionRemaining);

		$proRataPrice = $priceDiff*$fractionRemaining;

		$calculationModel = new Bf_AmendmentPriceNTime(array(
			'@type' => 'amendmentPriceNTime',
			'days' => $periodRemaining.' Seconds',
			'cost' => $proRataPrice,
			'asOfDate' => $asOfDate
			));

		return $calculationModel;

		/*return array(
			'now' => $nowPriceRequestCalculated,
			'after' => $afterPriceRequestCalculated,
			'priceDiff' => $priceDiff
			);*/
	}

	public static function getResourcePath() {
		return static::$_resourcePath;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('pricing-calculator', 'priceCalculation');
	}
}
Bf_PricingCalculator::initStatics();
