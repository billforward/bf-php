<?php

class Bf_Subscription extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected $roles = NULL;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('pricingComponentValueChanges', Bf_PricingComponentValueChange::getClassName(), $json);
		$this->unserializeArrayEntities('pricingComponentValues', Bf_PricingComponentValue::getClassName(), $json);

		$this->unserializeEntity('productRatePlan', Bf_ProductRatePlan::getClassName(), $json);
	}

	/**
	 * Gets all versions of Bf_Subscription for a given consistent ID
	 * @return Bf_Subscription[]
	 */
	public static function getAllVersionsForID($id, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		// empty IDs are no good!
		if (!$id) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		if (is_null($options) || !is_array($options)) {
			$options = array();
		}
		$options['include_retired'] = true;

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/$id";
		$fullRoute = $apiRoute.$endpoint;

		$response = $client->doGet($fullRoute, $options);

		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	/**
	 * Gets Bf_Subscription for a given version iD
	 * @return Bf_Subscription
	 */
	public static function getByVersionID($versionID, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		// empty IDs are no good!
		if (!$versionID) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/version/$versionID";
		$fullRoute = $apiRoute.$endpoint;

		$response = $client->doGet($fullRoute, $options);
		$json = $response->json();

		$results = $json['results'];

		$firstMatch = $results[0];

		return new $entityClass($firstMatch, $client);
	}

	/**
	 * Gets Bf_Subscriptions for a given Bf_Account
	 * @return Bf_Subscription[]
	 */
	public static function getForAccount($accountID, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/account/".$accountID;
		$fullRoute = $apiRoute.$endpoint;
		
		$response = $client->doGet($fullRoute, $options);
		
		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	/**
	 * Fetches all versions of Bf_Subscription for this Bf_Subscription.
	 * @return Bf_Subscription[]
	 */
	public function getAllVersions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getAllVersionsForID($this->id, $options = NULL, $customClient = NULL);
	}

	/**
	 * Fetches Bf_Amendments for this Bf_Subscription.
	 * @return Bf_Amendment[]
	 */
	public function getAmendments($options = NULL, $customClient = NULL) {
		return Bf_Amendment::getForSubscription($this->id, $options = NULL, $customClient = NULL);
	}

	/**
	 * Fetches Bf_Invoices for this Bf_Subscription.
	 * @return Bf_Invoice[]
	 */
	public function getInvoices($options = NULL, $customClient = NULL) {
		return Bf_Invoice::getForSubscription($this->id, $options = NULL, $customClient = NULL);
	}

	/**
	 * Gets Bf_PricingComponentValueChanges for this Bf_Subscription.
	 * @return Bf_PricingComponentValueChange[]
	 */
	public function getPricingComponentValueChanges() {
		return $this->pricingComponentValueChanges;
	}

	/**
	 * Gets Bf_PricingComponentValues for this Bf_Subscription.
	 * @return Bf_PricingComponentValue[]
	 */
	public function getPricingComponentValues() {
		return $this->pricingComponentValues;
	}

	/**
	 * Gets Bf_PaymentMethodSubscriptionLinks for this Bf_Subscription.
	 * @return Bf_PaymentMethodSubscriptionLink[]
	 */
	public function getPaymentMethodSubscriptionLinks() {
		return $this->paymentMethodSubscriptionLinks;
	}

	/**
	 * Gets this Bf_Subscription's associated Bf_ProductRatePlan.
	 * @return Bf_ProductRatePlan
	 */
	public function getProductRatePlan() {
		// if this is just a model made on our end, we might not have the product rate plan yet
		if (!$this->productRatePlan) {
			$this->productRatePlan = Bf_ProductRatePlan::getByID($this->productRatePlanID);
		}
		return $this->productRatePlan;
	}

	/**
	 * Attempts to put subscription in 'state: "AwaitingPayment"'
	 * @return Bf_Subscription The updated Bf_Subscription
	 */
	public function activate() {
		$this
		->state = 'AwaitingPayment';
		$response = $this->save();
		return $response;
	}

	/**
	 * Returns (if existent) the Bf_PricingComponentValue corresponding to the
	 * provided BF_PricingComponent
	 * @param Bf_PricingComponent the corresponding Bf_PricingComponent upon which to match
	 * @return Bf_PricingComponentValue The matching Bf_PricingComponentValue (if any)
	 */
	public function getValueOfPricingComponent(Bf_PricingComponent $pricingComponent) {
		return $this->getValueOfPricingComponentWithID($pricingComponent->id);
	}

	/**
	 * Returns (if existent) the Bf_PricingComponentValue corresponding to the
	 * 'BF_PricingComponent (consistent) ID'.
	 * @param string the Bf_PricingComponent ID upon which to match
	 * @return Bf_PricingComponentValue The matching Bf_PricingComponentValue (if any)
	 */
	public function getValueOfPricingComponentWithID($pricingComponentID) {
		$properties = array(
			'pricingComponentID' => $pricingComponentID
			);

		$pricingComponentValues = $this->getPricingComponentValues();

		return Bf_BillingEntity::fromCollectionFindFirstWhoMatchesProperties($pricingComponentValues, $properties);
	}

	/**
	 * Returns (if existent) the Bf_PricingComponentValue whose corresponding
	 * BF_PricingComponent has a name matching the one provided.
	 * @param string the Bf_PricingComponent name upon which to match
	 * @return Bf_PricingComponentValue The matching Bf_PricingComponentValue (if any)
	 */
	public function getValueOfPricingComponentWithName($name) {
		$properties = array(
			'name' => $name
			);

		return $this->getValueOfPricingComponentWithProperties($properties);
	}

	/**
	 * Returns (if existent) the Bf_PricingComponentValue whose corresponding
	 * BF_PricingComponent has properties matching those provided.
	 * @param array the Bf_PricingComponent properties upon which to match
	 * @return Bf_PricingComponentValue The matching Bf_PricingComponentValue (if any)
	 */
	public function getValueOfPricingComponentWithProperties(array $properties) {
		$prp = $this->getProductRatePlan();

		$pricingComponent = $prp->getPricingComponentWithProperties($properties);
		if (is_null($pricingComponent)) {
			// no pricing component matching these properties
			return NULL;
		}

		return $this->getValueOfPricingComponent($pricingComponent);
	}

	/**
	 * Maps this Bf_Subscription's 'pricingComponentValues' to the named pricing components and values.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @return Bf_Subscription ($this)
	 */
	public function setValuesOfPricingComponentsByName(array $namesToValues) {
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

		return $this->setValuesOfPricingComponentsByProperties($propertiesList, $valuesList);
	}

	/**
	 * Maps this Bf_Subscription's 'pricingComponentValues' to the matched (by property map) pricing components and values.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @return Bf_Subscription ($this)
	 */
	public function setValuesOfPricingComponentsByProperties(array $propertiesList, array $valuesList) {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$productRatePlan = $this->getProductRatePlan();

		// ensure that model begins with at least an empty array
		if (!is_array($this->pricingComponentValues)) {
			$this->pricingComponentValues = array();
		}

		// this is the array into which we will be inserting
		$pricingComponentValues = $this->pricingComponentValues;

		foreach ($propertiesList as $key => $value) {
			if (!is_array($value)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$value);
			}
			$pricingComponent = $productRatePlan->getPricingComponentWithProperties($value);

			$pricingComponentValue = new Bf_PricingComponentValue(array(
	            'pricingComponentID' => $pricingComponent->id,
	            'value' => $valuesList[$key],
	            ));

			$overwrote = false;
			foreach ($pricingComponentValues as $key => $value) {
				// find (if exists) matching pricing component value to overwrite
				if ($value->pricingComponentID === $pricingComponentValue->pricingComponentID) {
					// overwrite with the new model
					$pricingComponentValues[$key] = $pricingComponentValue;
					$overwrote = true;
					break;
				}
			}
			if (!$overwrote) {
				// insert this new value in
				array_push($pricingComponentValues, $pricingComponentValue);
			}
		}

		// set our model to use the new list
		$this->pricingComponentValues = $pricingComponentValues;

		return $this;
	}

	/**
	 * Gets all Bf_PaymentMethod[] from an account, and associates to this Bf_Subscription
	 * as Bf_PaymentMethodSubscriptionLink models.
	 * @param Bf_Account the Bf_Account from which to take the payment methods.
	 * @return Bf_Subscription ($this)
	 */
	public function usePaymentMethodsFromAccount(Bf_Account $account) {
		// list of all Payment methods on account that will be linked to this Subscription
		$paymentMethodSubscriptionLinks = $this->paymentMethodSubscriptionLinks;

		// set existing subscription links as deleted
		foreach($paymentMethodSubscriptionLinks as $paymentMethodSubscriptionLink) {
			$paymentMethodSubscriptionLink->deleted = true;
		}

		foreach($account->getPaymentMethods() as $paymentMethod) {
			// create link to Payment Method
			$paymentMethodSubscriptionLink = new Bf_PaymentMethodSubscriptionLink(array(
			  'paymentMethodID' => $paymentMethod->id
			  ));

			// add to list of links
			array_push($paymentMethodSubscriptionLinks, $paymentMethodSubscriptionLink);
		}

		$this->paymentMethodSubscriptionLinks = $paymentMethodSubscriptionLinks;
		return $this;
	}

	//// UPGRADE/DOWNGRADE VIA AMENDMENT

	/**
	 * Upgrades/downgrades subscription to Bf_PricingComponentValue values corresponding to Bf_PricingComponents whose properties match.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the upgrade amendment
	 * @param string ENUM['Immediate', 'Delayed'] (Default: NULL) When to effect the change in pricing component values. <Immediate>: Upon actioning time, pricing components immediately change to the new value. <Delayed>: Wait until end of billing period to change pricing component to new value. <NULL>: Don't override the change mode that is already specified on the pricing component.
	 * @return Bf_PricingComponentValueAmendment The created upgrade amendment.
	 */
	public function changeValueOfPricingComponentByProperties(array $propertiesList, array $valuesList, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', $changeModeOverride = NULL) {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$componentChanges = array();

		foreach ($propertiesList as $key => $propertyMap) {
			if (!is_array($propertyMap)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$propertyMap);
			}

			$newValue = $valuesList[$key];

			$pricingComponentValue = $this->getValueOfPricingComponentWithProperties($propertyMap);
			$componentChange = new Bf_ComponentChange(array(
				'pricingComponentID' => $pricingComponentValue->pricingComponentID,
				'newValue' => $newValue
			));

			if (!is_null($changeModeOverride)) {
				if ($changeModeOverride === 'Immediate') {
					$componentChange->changeMode = 'immediate';
				} else if ($changeModeOverride === 'Delayed') {
					$componentChange->changeMode = 'delayed';
				}
			}

			array_push($componentChanges, $componentChange);
		}
		
		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $this->id,
			'componentChanges' => $componentChanges,
			'invoicingType' => $invoicingType
			));

		$date = NULL; // defaults to Immediate
		if (is_int($actioningTime)) {
			$date = Bf_BillingEntity::makeBillForwardDate($actioningTime);
		} else if ($actioningTime === 'AtPeriodEnd') {
			if (!is_null($this->currentPeriodEnd)) {
				$date = $this->currentPeriodEnd;
			} else {
				throw new \Exception('Cannot set actioning time to period end, because the subscription does not declare a period end.');
			}
		}

		if (!is_null($date)) {
			$amendment->actioningTime = $date;
		}

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Upgrades/downgrades subscription to Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the upgrade amendment
	 * @param string ENUM['Immediate', 'Delayed'] (Default: NULL) When to effect the change in pricing component values. <Immediate>: Upon actioning time, pricing components immediately change to the new value. <Delayed>: Wait until end of billing period to change pricing component to new value. <NULL>: Don't override the change mode that is already specified on the pricing component.
	 * @return Bf_PricingComponentValueAmendment The created upgrade amendment.
	 */
	public function changeValueOfPricingComponentsByName(array $namesToValues, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', $changeModeOverride = NULL) {
		$propertiesList = array();
		$valuesList = array();

		foreach($namesToValues as $key => $value) {
			// from pricing component name, create a dictionary of identifying properties
			$pricingComponentPropertyMap = array(
				'name' => $key
				);
			array_push($propertiesList, $pricingComponentPropertyMap);
			array_push($valuesList, $value);
		}

		return $this->changeValueOfPricingComponentByProperties($propertiesList, $valuesList, $invoicingType, $actioningTime, $changeModeOverride);
	}

	/**
	 * Upgrades/downgrades subscription to Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type. <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the upgrade amendment
	 * @param string ENUM['Immediate', 'Delayed'] (Default: NULL) When to effect the change in pricing component values. <Immediate>: Upon actioning time, pricing components immediately change to the new value. <Delayed>: Wait until end of billing period to change pricing component to new value. <NULL>: Don't override the change mode that is already specified on the pricing component.
	 * @return Bf_PricingComponentValueAmendment The created upgrade amendment.
	 */
	public function upgrade(array $namesToValues, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', $changeModeOverride = NULL) {
		return $this->changeValueOfPricingComponentsByName($namesToValues, $invoicingType, $actioningTime, $changeModeOverride);
	}

	//// MIGRATE PLAN VIA AMENDMENT

	/**
	 * Migrates subscription to new plan, with Bf_PricingComponentValue values corresponding to Bf_PricingComponents whose properties match.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param Bf_ProductRatePlan The plan to migrate to.
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the migration amendment
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function migrateWithValueOfPricingComponentByProperties(array $propertiesList, array $valuesList, Bf_ProductRatePlan $newPlan, $invoicingType = 'Aggregated', $actioningTime = 'Immediate') {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$mappings = array();

		foreach ($propertiesList as $key => $propertyMap) {
			if (!is_array($propertyMap)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$propertyMap);
			}

			$newValue = $valuesList[$key];

			$pricingComponent = $newPlan->getPricingComponentWithProperties($propertyMap);
			$mapping = new Bf_PricingComponentValueMigrationAmendmentMapping(array(
				'pricingComponentID' => $pricingComponent->id,
				'value' => $newValue
			));

			array_push($mappings, $mapping);
		}
		
		$amendment = new Bf_ProductRatePlanMigrationAmendment(array(
			'subscriptionID' => $this->id,
			'productRatePlanID' => $newPlan->id,
			'mappings' => $mappings,
			'invoicingType' => $invoicingType
			));

		$date = NULL; // defaults to Immediate
		if (is_int($actioningTime)) {
			$date = Bf_BillingEntity::makeBillForwardDate($actioningTime);
		} else if ($actioningTime === 'AtPeriodEnd') {
			if (!is_null($this->currentPeriodEnd)) {
				$date = $this->currentPeriodEnd;
			} else {
				throw new \Exception('Cannot set actioning time to period end, because the subscription does not declare a period end.');
			}
		}

		if (!is_null($date)) {
			$amendment->actioningTime = $date;
		}

		$createdAmendment = Bf_ProductRatePlanMigrationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Migrates subscription to new plan, with Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param Bf_ProductRatePlan The plan to migrate to.
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the migration amendment
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function migrateWithValueOfPricingComponentsByName(array $namesToValues, Bf_ProductRatePlan $newPlan, $invoicingType = 'Aggregated', $actioningTime = 'Immediate') {
		$propertiesList = array();
		$valuesList = array();

		foreach($namesToValues as $key => $value) {
			// from pricing component name, create a dictionary of identifying properties
			$pricingComponentPropertyMap = array(
				'name' => $key
				);
			array_push($propertiesList, $pricingComponentPropertyMap);
			array_push($valuesList, $value);
		}

		return $this->migrateWithValueOfPricingComponentByProperties($propertiesList, $valuesList, $newPlan, $invoicingType, $actioningTime);
	}

	/**
	 * Migrates subscription to new plan, with Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string ID of the plan to migrate to.
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the migration amendment
	 * @param Bf_ProductRatePlan (Alternative parameter to avoid extra API request) The plan to migrate to.
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function migratePlan(array $namesToValues, $newPlanID = NULL, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', Bf_ProductRatePlan $newPlan = NULL) {
		if (is_null($newPlan)) {
			// fetch plan for you
			$newPlan = Bf_ProductRatePlan::getByID($newPlanID);
		}

		return $this->migrateWithValueOfPricingComponentsByName($namesToValues, $newPlan, $invoicingType, $actioningTime);
	}

	//// CANCEL VIA AMENDMENT

	/**
	 * Cancels subscription at a specified time.
	 * @param string ENUM['Immediate', 'AtPeriodEnd'] (Default: 'AtPeriodEnd') Specifies whether the service will end immediately on cancellation or if it will continue until the end of the current period.
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the cancellation amendment
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function cancel($serviceEnd = 'AtPeriodEnd', $actioningTime = 'Immediate') {
		// create model of amendment
		$amendment = new Bf_CancellationAmendment(array(
		  'subscriptionID' => $this->id,
		  'serviceEnd' => $serviceEnd
		  ));

		$date = NULL; // defaults to Immediate
		if (is_int($actioningTime)) {
			$date = Bf_BillingEntity::makeBillForwardDate($actioningTime);
		} else if ($actioningTime === 'AtPeriodEnd') {
			if (!is_null($this->currentPeriodEnd)) {
				$date = $this->currentPeriodEnd;
			} else {
				throw new \Exception('Cannot set actioning time to period end, because the subscription does not declare a period end.');
			}
		}

		if (!is_null($date)) {
			$amendment->actioningTime = $date;
		}

		// create amendment using API
		$createdAmendment = Bf_CancellationAmendment::create($amendment);
		return $createdAmendment;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'subscription');
	}
}
Bf_Subscription::initStatics();
