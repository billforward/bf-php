<?php

class Bf_AmendmentPriceRequest extends Bf_MutableEntity {
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

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('subscription', Bf_Subscription::getClassName(), $json);
		$this->unserializeEntity('codeType', Bf_AmendmentPriceRequestCodeType::getClassName(), $json);
		$this->unserializeArrayEntities('pricingComponentValues', Bf_PricingComponentValue::getClassName(), $json);
	}

	/**
	 * Gets Bf_Subscription for this Bf_AmendmentPriceRequest.
	 * @return Bf_Subscription
	 */
	public function getSubscription() {
		return $this->subscription;
	}

	/**
	 * Gets Bf_AmendmentPriceRequestCodeType for this Bf_AmendmentPriceRequest.
	 * @return Bf_AmendmentPriceRequestCodeType
	 */
	public function getCodeType() {
		return $this->codeType;
	}

	/**
	 * Gets Bf_PricingComponentValues for this Bf_AmendmentPriceRequest.
	 * @return Bf_PricingComponentValue[]
	 */
	public function getPricingComponentValues() {
		return $this->pricingComponentValues;
	}

	/**
	 * Returns a Bf_AmendmentPriceRequest model with 'componentValues' mapped to the input key-value pairs.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string (option 1) The ID of the subscription for which to generate a price request
	 * @param Bf_Subscription (option 2) The model of the subscription for which to generate an upgrade price request; provide this to avoid fetching from API
	 * @return Bf_AmendmentPriceRequest The constructed Bf_AmendmentPriceRequest
	 */
	public static function forPricingComponentsByName(array $namesToValues, $subscriptionID=null, Bf_Subscription $subscriptionModel=null) {
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

		return static::forPricingComponentsByProperties($propertiesList, $valuesList, $subscriptionID, $subscriptionModel);
	}

	/**
	 * Returns a Bf_AmendmentPriceRequest model with 'componentValues' mapped to the input key-value pairs.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param string (option 1) The ID of the subscription for which to generate a price request
	 * @param Bf_Subscription (option 2) The model of the subscription for which to generate an upgrade price request; provide this to avoid fetching from API
	 * @return Bf_AmendmentPriceRequest The constructed Bf_AmendmentPriceRequest
	 */
	public static function forPricingComponentsByProperties(array $propertiesList, array $valuesList, $subscriptionID=null, Bf_Subscription $subscriptionModel=null) {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

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

		$componentValues = array();

		$productRatePlan = $subscription->getProductRatePlan();

		foreach ($propertiesList as $key => $value) {
			if (!is_array($value)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$value);
			}
			$pricingComponent = $productRatePlan->getPricingComponentWithProperties($value);

			$updatedPricingComponentValue = new Bf_PricingComponentValue(array(
	            'pricingComponentID' => $pricingComponent->id,
	            'value' => $valuesList[$key],
	            ));
			array_push($componentValues, $updatedPricingComponentValue);
		}

		$model = new Bf_AmendmentPriceRequest(array(
            'subscription' => $subscription,
            'componentValues' => $componentValues
            ));

		return $model;
	}
}