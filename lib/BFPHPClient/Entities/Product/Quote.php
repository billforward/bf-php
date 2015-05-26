<?php

class Bf_Quote extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('quotes', '');
	}

	/**
	 * Retrieves a quote for the price of the specified quantities of pricing components of the product rate plan
	 * @param union[union[string $id | Bf_ProductRatePlan $entity]] Reference to subscription <string>: $id of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan entity.
	 * @param array[string => number] $namesToValues The map of pricing component names to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @param array $quoteOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param array (Default: array()) $..['couponCodes'] List of coupon codes to be applied in the quote
	 *	* @param string_ENUM['InitialPeriod', 'RecurringPeriod', 'Upgrade'] (Default: 'InitialPeriod') $..['quoteFor']
	 *	*
	 *	* 	<InitialPeriod> (Default)
	 *	*
	 *	*	<RecurringPeriod>
	 *	*
	 *	* 	<Upgrade>
	 *	*
	 * @return Bf_Quote The price quote
	 */
	public static function getQuote(
		$ratePlan,
		array $namesToValues,
		array $quoteOptions = array(
			'couponCodes' => array(),
			'quoteFor' => 'InitialPeriod'
			)
		) {
		$inputOptions = $quoteOptions;
		// $ratePlanFetched = Bf_ProductRatePlan::fetchIfNecessary($ratePlan);

		$ratePlanID = Bf_ProductRatePlan::getIdentifier($ratePlan);

		$mappings = array_map(
			function($name, $value) {
				return new Bf_QuoteRequestValue(array(
					'pricingComponent' => $name,
					'quantity' => $value
				));
			},
			array_keys($namesToValues),
			$namesToValues
			);
		
		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'quantities' => $mappings,
				'productRatePlan' => $ratePlanID
				),
			$inputOptions
			);

		$requestEntity = new Bf_QuoteRequest($stateParams);

		$endpoint = '';

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}
}
Bf_Quote::initStatics();
