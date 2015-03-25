<?php

class Bf_PriceRequest extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		throw new Bf_UnsupportedMethodException('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it. '
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).');
	}
	
	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	public function save() {
		throw new Bf_UnsupportedMethodException('Save support is denied for this entity; '
		 .'at the time of writing, the provided API endpoint is not functioning.'
		 .'The entity can be saved through cascade only (i.e. save a related entity).');
	}

	/**
	 * Returns a Bf_PriceRequest model with 'updatedPricingComponentValues' mapped to the input key-value pairs.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string (option 1) The ID of the product rate plan for which to generate a price request
	 * @param Bf_ProductRatePlan (option 2) The model of the product rate plan for which to generate a price request; provide this to avoid fetching from API
	 * @return Bf_PriceRequest The constructed Bf_PriceRequest
	 */
	public static function forPricingComponentsByName(array $namesToValues, $productRatePlanID=null, Bf_ProductRatePlan $productRatePlanModel=null) {
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

		return static::forPricingComponentsByProperties($propertiesList, $valuesList, $productRatePlanID, $productRatePlanModel);
	}

	/**
	 * Returns a Bf_PriceRequest model with 'updatedPricingComponentValues' mapped to the input key-value pairs.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param string (option 1) The ID of the product rate plan for which to generate a price request
	 * @param Bf_ProductRatePlan (option 2) The model of the product rate plan for which to generate a price request; provide this to avoid fetching from API
	 * @return Bf_PriceRequest The constructed Bf_PriceRequest
	 */
	public static function forPricingComponentsByProperties(array $propertiesList, array $valuesList, $productRatePlanID=null, Bf_ProductRatePlan $productRatePlanModel=null) {
		if (!is_array($propertiesList)) {
			throw new Bf_MalformedInputException('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new Bf_MalformedInputException('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$productRatePlan;
		if (is_null($productRatePlanModel)) {
			if (is_null($productRatePlanID)) {
				throw new Bf_EmptyArgumentException('Received null product rate plan, and null product rate plan ID.');
			}

			// fetch from API
			$productRatePlan = Bf_ProductRatePlan::getByID($productRatePlanID);	
		} else {
			$productRatePlan = $productRatePlanModel;
		}

		$updatedPricingComponentValues = array();

		foreach ($propertiesList as $key => $value) {
			if (!is_array($value)) {
				throw new Bf_MalformedInputException('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$value);
			}
			$pricingComponent = $productRatePlan->getPricingComponentWithProperties($value);

			$updatedPricingComponentValue = new Bf_PricingComponentValue(array(
	            'pricingComponentID' => $pricingComponent->id,
	            'value' => $valuesList[$key],
	            ));
			array_push($updatedPricingComponentValues, $updatedPricingComponentValue);
		}

		$model = new Bf_PriceRequest(array(
            'productRatePlanID' => $productRatePlan->id,
            'productID' => $productRatePlan->productID,
            'updatedPricingComponentValues' => $updatedPricingComponentValues
            ));

		return $model;
	}
}