<?php

class Bf_Subscription extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected $roles = NULL;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('pricingComponentValueChanges', Bf_PricingComponentValueChange::getClassName(), $json);
		$this->unserializeArrayEntities('pricingComponentValues', Bf_PricingComponentValue::getClassName(), $json);
		$this->unserializeArrayEntities('paymentMethodSubscriptionLinks', Bf_PaymentMethodSubscriptionLink::getClassName(), $json);

		$this->unserializeEntity('productRatePlan', Bf_ProductRatePlan::getClassName(), $json);
	}

	/**
	 * Gets all versions of Bf_Subscription for a given consistent ID
	 * @param string ID of the Bf_Subscription
	 * @return Bf_Subscription[]
	 */
	public static function getAllVersionsForID($id, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$id) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		if (is_null($options) || !is_array($options)) {
			$options = array();
		}
		$options['include_retired'] = true;

		$endpoint = "/$id";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscription for a given version ID
	 * @param string version ID of the Bf_Subscription
	 * @return Bf_Subscription
	 */
	public static function getByVersionID($versionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$versionID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/version/$versionID";

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given product ID
	 * @param string ID of the Bf_Product upon which to search
	 * @return Bf_Subscription[]
	 */
	public static function getByProductID($productID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$productID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/product/$productID";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given rate plan ID
	 * @param string ID of the Bf_ProductRatePlan upon which to search
	 * @return Bf_Subscription[]
	 */
	public static function getByRatePlanID($productRatePlanID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$productRatePlanID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/product-rate-plan/$productRatePlanID";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given state
	 * @param string_ENUM['Trial', 'Provisioned', 'Paid', 'AwaitingPayment', 'Cancelled', 'Failed', 'Expired'] State upon which to search
	 * @return Bf_Subscription[]
	 */
	public static function getByState($state, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$state) {
    		throw new Bf_EmptyArgumentException("Cannot lookup unspecified state!");
		}

		$endpoint = "/state/$state";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given Bf_Account
	 * @param string version ID of the Bf_Account
	 * @return Bf_Subscription[]
	 */
	public static function getForAccount($accountID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$accountID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/account/$accountID";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets a list of available payment methods for the specified subscription
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod[] The fetched payment methods.
	 */
	public static function getPaymentMethodsOnSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);

		// empty IDs are no good!
		if (!$subscriptionIdentifier) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty subscription ID!");
		}

		$encoded = rawurlencode($subscriptionIdentifier);

		$endpoint = "/$encoded/payment-methods";

		$responseEntity = Bf_PaymentMethod::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	/**
	 * Gets a list of available payment methods for the specified subscription
	 * @param union[string $id | Bf_PaymentMethod $paymentMethod] The Bf_PaymentMethod which should be removed. <string>: ID of the Bf_PaymentMethod. <Bf_Subscription>: The Bf_Subscription.
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription from which the Bf_PaymentMethod should be removed. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod The removed payment method.
	 */
	public static function removePaymentMethodFromSubscription($paymentMethod, $subscription) {
		$subscriptionIdentifier = Bf_Subscription::getIdentifier($subscription);
		$paymentMethodIdentifier = Bf_PaymentMethod::getIdentifier($paymentMethod);

		// empty IDs are no good!
		if (!$subscriptionIdentifier)
    		throw new Bf_EmptyArgumentException("Cannot lookup empty subscription ID!");

		// empty IDs are no good!
		if (!$paymentMethodIdentifier)
    		throw new Bf_EmptyArgumentException("Cannot lookup empty subscription ID!");

		$subEncoded = rawurlencode($subscriptionIdentifier);
		$paymentMethodEncoded = rawurlencode($paymentMethodIdentifier);

		$endpoint = "$subEncoded/payment-methods/$paymentMethodEncoded";

		$responseEntity = Bf_PaymentMethod::getClassName();

		return static::retireAndGrabFirst($endpoint, NULL, $customClient, $responseEntity);
	}

	/**
	 * Fetches all versions of Bf_Subscription for this Bf_Subscription.
	 * @return Bf_Subscription[]
	 */
	public function getAllVersions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getAllVersionsForID($this->id, $options, $customClient);
	}

	/**
	 * Fetches Bf_Amendments for this Bf_Subscription.
	 * @return Bf_Amendment[]
	 */
	public function getAmendments($options = NULL, $customClient = NULL) {
		return Bf_Amendment::getForSubscription($this->id, $options, $customClient);
	}

	/**
	 * Fetches Bf_Invoices for this Bf_Subscription.
	 * @return Bf_Invoice[]
	 */
	public function getInvoices($options = NULL, $customClient = NULL) {
		return Bf_Invoice::getForSubscription($this->id, $options, $customClient);
	}

	/**
	 * Gets Bf_CreditNotes for this Bf_Subscription.
	 * @return Bf_CreditNote[]
	 */
	public function getCreditNotes($options = NULL, $customClient = NULL) {
		return Bf_CreditNote::getForSubscription($this->id, $options, $customClient);
	}

	/**
	 * Gets Bf_Coupons applied to this Bf_Subscription.
	 * @return Bf_Coupon[]
	 */
	public function getCoupons($options = NULL, $customClient = NULL) {
		return Bf_Coupon::getForSubscription($this, $options, $customClient);
	}

	/**
	 * Gets Bf_Coupons which can be applied to this Bf_Subscription.
	 * Gets coupons by 'base code' only.
	 * @return Bf_Coupon[]
	 */
	public function getApplicableCoupons($options = NULL, $customClient = NULL) {
		return Bf_Coupon::getApplicableToSubscription($this, $options, $customClient);
	}

	/**
	 * Gets Bf_PaymentMethods which are available to this Bf_Subscription.
	 * @return Bf_PaymentMethod[]
	 */
	public function getPaymentMethods($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getPaymentMethodsOnSubscription($this, $options, $customClient);
	}

	public function getCurrentPeriodEnd() {
		if (!is_null($this->currentPeriodEnd)) {
			return $this->currentPeriodEnd;
		} else {
			throw new Bf_PreconditionFailedException('Cannot set actioning time to period end, because the subscription does not declare a period end. This could mean the subscription has not yet been instantiated by the BillForward engines. You could try again in a few seconds, or in future invoke this functionality after a WebHook confirms the subscription has reached the necessary state.');
		}
	}

	/**
	 * Issues against the Bf_Subscription, credit of the specified value and currency.
	 * @param int Nominal value of credit note
	 * @param ISO_4217_Currency_Code The currency code
	 * @return Bf_CreditNote
	 */
	public function issueCredit($value, $currency = 'USD') {
		$creditNote = new Bf_CreditNote(array(
			'nominalValue' => $value,
			'currency' => $currency
			));

		return $creditNote->issueToSubscription($this->id);
	}

	/**
	 * Gets nominal remaining value of all credit notes on this account, for the specified currency.
	 *
	 * NOTE: As with all API calls, this counts by default only the first 10 credit notes.
	 * Override by passing into $options: array('records' => 100); or however many credit notes you expect is a reasonable upper limit.
	 * @return int
	 */
	public function getRemainingCreditForCurrency($currency = 'USD', $options = NULL, $customClient = NULL) {
		$creditNotes = $this->getCreditNotes($options, $customClient);

		return Bf_CreditNote::getRemainingCreditForCurrency($creditNotes, $currency);
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
	 * Gets this Bf_Subscription's associated Bf_ProductRatePlan.
	 * @return Bf_ProductRatePlan
	 */
	public function getProductRatePlan() {
		// if this is just a model made on our end, we might not have the serialized rate plan yet
		// alternatively: newer BillForward may omit serialized rate plan and necessitate a fetch
		if (!$this->productRatePlan) {
			if (!$this->productRatePlanID) {
				throw new Bf_PreconditionFailedException("This Bf_Subscription has neither a 'productRatePlan' specified, nor a 'productRatePlanID' by which to obtain said productRatePlan.");
			}
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
			throw new Bf_MalformedInputException('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new Bf_MalformedInputException('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
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
				throw new Bf_MalformedInputException('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$value);
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
	 * Applies Bf_Coupon to this Bf_Subscription
	 * @param Bf_Coupon The coupon to apply to this subscription
	 * @return Bf_Coupon The applied coupon.
	 */
	public function applyCoupon(Bf_Coupon $coupon) {
		return $coupon->applyToSubscription($this);
	}

	/**
	 * Applies Bf_Coupon to this Bf_Subscription
	 * @param string The Coupon code to apply to this subscription
	 * @return Bf_Coupon The applied coupon.
	 */
	public function applyCouponCode($couponCode) {
		return Bf_Coupon::applyCouponCodeToSubscription($couponCode, $this);
	}

	//// UPGRADE/DOWNGRADE

	/**
	 * Upgrades/downgrades subscription to Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array[string => number] $namesToValues The map of pricing component names to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @param array $upgradeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param array[string => string_ENUM[NULL, 'Immediate', 'Delayed']] (Default: array()) $upgradeOptions['namesToChangeModeOverrides'] The map of pricing component names to change mode overrides.
	 *	*
	 *	*	Each key in the this array maps to a value of the following ENUM:
	 *	*  *	<NULL> (Behaviour for omitted keys)
	 *	*  *	Don't override the change mode that is already specified on the pricing component.
	 *	*  *	
	 *	*  *	<Immediate>
	 *	*  *	Upon actioning the upgrade, this pricing component will immediately change to the new value.
	 *	*  *	
	 *	*  *	<Delayed>
	 *	*  *	Wait until end of billing period to change pricing component to new value.
	 *	* Example:
	 *	* array(
	 *	* 	'CPU' => 'Immediate',
	 *	* 	'RAM' => 'Delayed'
	 *	* )
	 *	*
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $upgradeOptions['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)}  $upgradeOptions['actioningTime'] When to action the upgrade amendment
	 * @return Bf_PricingComponentValueAmendment The created upgrade amendment.
	 */
	public function scheduleUpgrade(
		array $namesToValues,
		array $upgradeOptions = array(
			'namesToChangeModeOverrides' => array(),
			'invoicingType' => 'Aggregated',
			'actioningTime' => 'Immediate'
			)
		) {

		$inputOptions = $upgradeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$componentChanges = array_map(function($key, $value) use($namesToChangeModeOverrides) {
			$change = new Bf_ComponentChange(array(
				'pricingComponentName' => $key,
				'newValue' => $value
			));
			if (array_key_exists($key, $namesToChangeModeOverrides)) {
				if (!is_null($namesToChangeModeOverrides[$key])) {
					$componentChange->changeMode = strtolower($namesToChangeModeOverrides[$key]);
				}	
			}
			return $change;
		}, array_keys($namesToValues), $namesToValues);

		static::popKey($inputOptions, 'namesToChangeModeOverrides');
		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'componentChanges' => $componentChanges,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);
		$amendment = new Bf_PricingComponentValueAmendment($stateParams);

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		return $createdAmendment;
	}

	//// MIGRATE PLAN

	/**
	 * Migrates subscription to new plan, with Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array[string => number] $namesToValues The map of pricing component names to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @param union[string $id | Bf_ProductRatePlan $entity] $newPlan The rate plan to which you wish to migrate. <string>: ID of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @param array $migrationOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param union[NULL | string] (Default: NULL) $migrationOptions['renameSubscription'] Optionally rename the subscription upon migration. <NULL> Leave the subscription's name unchanged. <string> The name to which you would like to rename the subscription.
	 *	* @param string_ENUM['None', 'Full', 'Difference', 'DifferenceProRated', 'ProRated'] (Default: 'DifferenceProRated') $migrationOptions['pricingBehaviour'] Strategy for calculating migration charges.
	 *	*
	 *	*  <None>
	 *	*  No migration charge will be issued at all.
	 *	*
	 *	*  <Full>
	 *	*  The migration cost will be the cost of the in advance components of the new Product Rate Plan.
	 *	*  
	 *	*  <Difference>
	 *	*  The migration cost will be the difference between the in advance components  
	 *	*  of the current Product Rate Plan and the new Product Rate plan.
	 *	*  
	 *	*  <DifferenceProRated> (Default)
	 *	*  The migration cost will be the difference between the in advance components  
	 *	*  of the current Product Rate Plan and new Product Rate plan multiplied by the ratio SecondsRemaining/SecondsInInvoicePeriod.
	 *	*  
	 *	*  <ProRated>
	 *	*  This value has two definitions.
	 *	*   1. We are migrating to a plan of the same period duration. The migration cost will be the cost of the in advance components of the new Product Rate Plan
	 *	*   multiplied by the ratio SecondsRemaining/SecondsInInvoicePeriod. 
	 *	*  
	 *	*   2. We are migrating to a plan of a different period duration. 
	 *	*   This means that a Credit Note will be generated with a ProRata value for the remaining duration of the current period.
	 *	*
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $migrationOptions['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $migrationOptions['actioningTime'] When to action the migration amendment.
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function scheduleMigratePlan(
		array $namesToValues,
		$newPlan,
		array $migrationOptions = array(
			'renameSubscription' => NULL,
			'pricingBehaviour' => 'DifferenceProRated',
			'invoicingType' => 'Aggregated',
			'actioningTime' => 'Immediate'
			)
		) {
		$inputOptions = $migrationOptions;

		$planID = Bf_ProductRatePlan::getIdentifier($newPlan);
		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$mappings = array_map(
			function($name, $value) {
				return new Bf_PricingComponentValueMigrationAmendmentMapping(array(
					'pricingComponentName' => $name,
					'value' => $value
				));
			},
			array_keys($namesToValues),
			$namesToValues
			);

		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'mappings' => $mappings,
				'subscriptionID' => $subscriptionID,
				'productRatePlanID' => $planID,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);
		static::renameKey($stateParams, 'renameSubscription', 'nextSubscriptionName');
		$amendment = new Bf_ProductRatePlanMigrationAmendment($stateParams);

		$createdAmendment = Bf_ProductRatePlanMigrationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously migrates the subscription to the specified plan.
	 * @see migratePlan()
	 * @return Bf_MigrationResponse The migration result.
	 */
	public function migratePlan(
		array $namesToValues,
		$newPlan,
		array $migrationOptions = array(
			'renameSubscription' => NULL,
			'pricingBehaviour' => 'DifferenceProRated',
			'invoicingType' => 'Aggregated',
			'dryRun' => false
			)
		) {
		$inputOptions = $migrationOptions;

		$planID = Bf_ProductRatePlan::getIdentifier($newPlan);
		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$mappings = array_map(
			function($name, $value) {
				return new Bf_PricingComponentMigrationValue(array(
					'pricingComponentName' => $name,
					'value' => $value
				));
			},
			array_keys($namesToValues),
			$namesToValues
			);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'mappings' => $mappings
				),
			$inputOptions
			);
		static::renameKey($stateParams, 'renameSubscription', 'nextSubscriptionName');
		$requestEntity = new Bf_MigrationRequest($stateParams);

		$endpoint = sprintf("%s/migrate/%s",
			rawurlencode($subscriptionID),
			rawurlencode($planID)
			);

		$responseEntity = Bf_MigrationResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// CANCEL

	/**
	 * Cancels subscription at a specified time.
	 * @param array $cancellationOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string_ENUM['Immediate', 'AtPeriodEnd'] (Default: 'AtPeriodEnd') $cancellationOptions['serviceEnd'] Specifies when the service ends after the subscription is cancelled.
	 *	* 	<Immediate>
	 *	* 	Subscription ends service as soon as it is cancelled.
	 *	*
	 *	* 	<AtPeriodEnd> (Default)
	 *	* 	After cancellation, the subscription continues to provide service until its billing period ends.
	 *	*
	 *	* @param string_ENUM['Credit', 'None'] (Default: 'Credit') $cancellationOptions['cancellationCredit'] 
	 *	*
	 *	* 	<Credit> (Default)
	 *	*
	 *	* 	<None>
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $cancellationOptions['actioningTime'] When to action the cancellation amendment
	 * @return Bf_CancellationAmendment The created cancellation amendment.
	 */
	public function scheduleCancellation(
		array $cancellationOptions = array(
			'serviceEnd' => 'AtPeriodEnd',
			'cancellationCredit' => 'Credit',
			'actioningTime' => 'Immediate'
			)
		) {
		$inputOptions = $cancellationOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);

		// create model of amendment
		$amendment = new Bf_CancellationAmendment($stateParams);

		// create amendment using API
		$createdAmendment = Bf_CancellationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously cancels the subscription.
	 * @see cancel()
	 * @return Bf_SubscriptionCancellation The cancellation result.
	 */
	public function cancel(
		array $cancellationOptions = array(
			'serviceEnd' => 'AtPeriodEnd',
			'cancellationCredit' => 'Credit'
			)
		) {

		$inputOptions = $cancellationOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);

		$requestEntity = new Bf_SubscriptionCancellation($stateParams);

		$endpoint = sprintf("%s/cancel",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCancellation::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// REVIVE CANCELLED SUBSCRIPTION

	/**
	 * Synchronously revives the subscription.
	 * @return Bf_Subscription The revived subscription.
	 */
	public function revive(
		array $revivalOptions = array(
			)
		) {
		$inputOptions = $revivalOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);

		$requestEntity = new Bf_SubscriptionReviveRequest($stateParams);

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$endpoint = sprintf("%s/revive",
			rawurlencode($subscriptionID)
			);

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}

	//// FREEZE

	/**
	 * Synchronously freezes the subscription.
	 * @param array $freezeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $freezeOptions['dryRun'] Whether to forego persisting the effected changes.
	 * @return Bf_Subscription The frozen subscription.
	 */
	public function freeze(
		array $freezeOptions = array(
			'dryRun' => false
			)
		) {

		$inputOptions = $freezeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);
		
		$requestEntity = new Bf_PauseRequest($stateParams);

		$endpoint = sprintf("%s/freeze",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// RESUME FROZEN SUBSCRIPTION

	/**
	 * Synchronously resumes the subscription.
	 * @param array $resumptionOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $resumptionOptions['dryRun'] Whether to forego persisting the effected changes.
	 * @return Bf_Subscription The frozen subscription.
	 */
	public function resume(
		array $resumptionOptions = array(
			'dryRun' => false
			)
		) {

		$inputOptions = $resumptionOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);
		
		$requestEntity = new Bf_PauseRequest($stateParams);

		$endpoint = sprintf("%s/resume",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// ADVANCE SUBSCRIPTION THROUGH TIME

	/**
	 * Synchronously resumes the subscription.
	 * @param array $advancementOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $advancementOptions['dryRun'] Whether to forego persisting the effected changes.
	 *	* @param boolean (Default: false) $advancementOptions['skipIntermediatePeriods']
	 *	* @param boolean (Default: true) $advancementOptions['handleAmendments']
	 *	* @param string_ENUM['SingleAttempt', 'FollowDunning', 'None'] (Default: 'SingleAttempt') $advancementOptions['executionStrategy']
	 *	*
	 *	* 	<SingleAttempt> (Default)
	 *	*
	 *	*	<FollowDunning> (Default)
	 *	*
	 *	* 	<None>
	 *	*
	 *	* @param boolean (Default: false) $advancementOptions['freezeOnCompletion']
	 * @return Bf_Subscription The frozen subscription.
	 */
	public function advance(
		array $advancementOptions = array(
			'dryRun' => false,
			'skipIntermediatePeriods' => false,
			'handleAmendments' => true,
			'executionStrategy' => 'SingleAttempt',
			'freezeOnCompletion' => false,
			'from' => NULL,
			'to' => 'PeriodEnd'
			)
		) {

		$inputOptions = $advancementOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);

		$requestEntity = new Bf_TimeRequest($stateParams);

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$endpoint = sprintf("%s/resume",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Parses into a BillForward timestamp the Bf_TimeRequest 'From' time
	 * @param union[int $timestamp | string_ENUM['Now', 'CurrentPeriodEnd']] (Default: 'Immediate') When to action the amendment
	 *
	 *  int
	 *  'From' the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  'From' the time at which the request reaches the server
	 *
	 *  <ClientNow>
	 *  'From' the current time by this client's clock.
	 *  
	 *  <CurrentPeriodEnd>
	 *  'From' the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the amendment to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 *
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'CurrentPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	public static function parseTimeRequestFromTime($fromTime, $subscription = NULL) {
		$intSpecified = NULL;

		switch ($fromTime) {
			case 'ServerNow':
			case 'Immediate':
				return NULL;
			case 'CurrentPeriodEnd':
				// we need to consult subscription
				if (is_null($subscription)) {
					throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain CurrentPeriodEnd time, because a null reference was provided to the subscription.');
				}
				$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
				return $subscriptionFetched->getCurrentPeriodEnd();
			case 'ClientNow':
				$intSpecified = time();
			default:
				if (is_int($fromTime)) {
					$intSpecified = $fromTime;
				}
				if (!is_null($intSpecified)) {
					return Bf_BillingEntity::makeBillForwardDate($intSpecified);
				}
				if (is_string($fromTime)) {
					return $fromTime;
				}
		}

		return NULL;
	}

	/**
	 * Parses into a BillForward timestamp the Bf_TimeRequest 'From' time
	 * @param union[int $timestamp | string_ENUM['Now', 'CurrentPeriodEnd']] (Default: 'Immediate') When to action the amendment
	 *
	 *  int
	 *  'From' the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  'To' the time at which the request reaches the server
	 *
	 *  <ClientNow>
	 *  'To' the current time by this client's clock.
	 *  
	 *  <CurrentPeriodEnd>
	 *  'To' the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the amendment to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 *
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'CurrentPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	public static function parseTimeRequestToTime($fromTime, $subscription = NULL) {
		$intSpecified = NULL;

		switch ($fromTime) {
			case 'ServerNow':
			case 'Immediate':
				return NULL;
			case 'CurrentPeriodEnd':
				// we need to consult subscription
				if (is_null($subscription)) {
					throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain CurrentPeriodEnd time, because a null reference was provided to the subscription.');
				}
				$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
				return $subscriptionFetched->getCurrentPeriodEnd();
			case 'ClientNow':
				$intSpecified = time();
			default:
				if (is_int($fromTime)) {
					$intSpecified = $fromTime;
				}
				if (!is_null($intSpecified)) {
					return Bf_BillingEntity::makeBillForwardDate($intSpecified);
				}
				if (is_string($fromTime)) {
					return $fromTime;
				}
		}

		return NULL;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'subscription');
	}
}
Bf_Subscription::initStatics();
