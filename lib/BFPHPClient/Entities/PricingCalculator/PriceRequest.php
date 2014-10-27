<?php

class Bf_PriceRequest extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		trigger_error('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it. '
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).',
		 E_USER_ERROR);
	}
	
	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		trigger_error('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).',
		 E_USER_ERROR);
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		trigger_error('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).',
		 E_USER_ERROR);
	}

	public function save() {
		trigger_error('Save support is denied for this entity; '
		 .'at the time of writing, the provided API endpoint is not functioning.'
		 .'The entity can be saved through cascade only (i.e. save a related entity).',
		 E_USER_ERROR);
	}

	/**
	 * Returns a Bf_PriceRequest model with 'updatedPricingComponentValues' mapped to the input key-value pairs.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string The ID of the product rate plan for which to generate a price request
	 * @return Bf_PriceRequest The constructed Bf_PriceRequest
	 */
	public static function forPricingComponentsByName(array $namesToValues, $productRatePlanID) {
		$propertiesList = array();
		$valuesList = array();

		// convert namesToValues to a more generic 'entity property map' to values
		foreach ($namesToValues as $key => $value) {
			$propToValue = array(
				'name' => $key
				);
			array_push($propertiesList, $propToValue);
			array_push($valuesList, $value);
		}

		return static::forPricingComponentsByProperties($propertiesList, $valuesList, $productRatePlanID);
	}

	/**
	 * Returns a Bf_PriceRequest model with 'updatedPricingComponentValues' mapped to the input key-value pairs.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param string The ID of the product rate plan for which to generate a price request
	 * @return Bf_PriceRequest The constructed Bf_PriceRequest
	 */
	public static function forPricingComponentsByProperties(array $propertiesList, array $valuesList, $productRatePlanID) {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$productRatePlan = Bf_ProductRatePlan::getByID($productRatePlanID);

		$updatedPricingComponentValues = array();

		foreach ($propertiesList as $key => $value) {
			if (!is_array($value)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$value);
			}
			$pricingComponent = $productRatePlan->getPricingComponentWithProperties($value);

			$updatedPricingComponentValue = new Bf_PricingComponentValue(array(
	            'pricingComponentID' => $pricingComponent->id,
	            'value' => $valuesList[$key],
	            ));
			array_push($updatedPricingComponentValues, $updatedPricingComponentValue);
		}

		$model = new Bf_PriceRequest(array(
            'productRatePlanID' => $productRatePlanID,
            'productID' => $productRatePlan->productID,
            'updatedPricingComponentValues' => $updatedPricingComponentValues
            ));

		return $model;
	}
}