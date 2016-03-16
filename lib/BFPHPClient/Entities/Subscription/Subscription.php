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

		$endpoint = sprintf("%s",
			rawurlencode($id)
			);

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
    		throw new Bf_EmptyArgumentException("Cannot lookup unspecified versionID!");
		}

		$endpoint = sprintf("version/%s",
			rawurlencode($versionID)
			);

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given product ID
	 * @param union[string | Bf_Product] $product The Bf_Product to which the Bf_Coupon should be applied. <string>: ID of the Bf_Product. <Bf_Product>: The Bf_Product.
	 * @return Bf_Subscription[]
	 */
	public static function getByProductID($product, $options = NULL, $customClient = NULL) {
		$product = Bf_Product::getIdentifier($product);

		$endpoint = sprintf("product/%s",
			rawurlencode($product)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given rate plan ID
	 * @param union[string | Bf_ProductRatePlan] $productRatePlan The Bf_ProductRatePlan to which the Bf_Coupon should be applied. <string>: ID of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @return Bf_Subscription[]
	 */
	public static function getByRatePlanID($productRatePlan, $options = NULL, $customClient = NULL) {
		$productRatePlanID = Bf_ProductRatePlan::getIdentifier($productRatePlan);

		$endpoint = sprintf("product-rate-plan/%s",
			rawurlencode($productRatePlanID)
			);

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

		$endpoint = sprintf("state/%s",
			rawurlencode($state)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Subscriptions for a given Bf_Account
	 * @param union[string | Bf_Account] $account The Bf_Account to which the Bf_Coupon should be applied. <string>: ID of the Bf_Account. <Bf_Account>: The Bf_Account.
	 * @return Bf_Subscription[]
	 */
	public static function getForAccount($account, $options = NULL, $customClient = NULL) {
		$accountID = Bf_Account::getIdentifier($account);

		$endpoint = sprintf("account/%s",
			rawurlencode($accountID)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets a list of available payment methods for the specified subscription
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod[] The fetched payment methods.
	 */
	public static function getPaymentMethodsOnSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$encoded = rawurlencode($subscriptionIdentifier);

		$endpoint = sprintf("%s/payment-methods",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_PaymentMethod::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	/**
	 * Gets a list of available payment methods for this subscription
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod[] The fetched payment methods.
	 */
	public function getPaymentMethods($options = NULL, $customClient = NULL) {
		return static::getPaymentMethodsOnSubscription($this, $options, $customClient);
	}

	/**
	 * Remove the given payment method from the specified subscription
	 * @param union[string | Bf_PaymentMethod] $paymentMethod The Bf_PaymentMethod which should be removed. <string>: ID of the Bf_PaymentMethod. <Bf_Subscription>: The Bf_Subscription.
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription from which the Bf_PaymentMethod should be removed. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod The removed payment method.
	 */
	public static function removePaymentMethodFromSubscription($paymentMethod, $subscription) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);
		$paymentMethodID = Bf_PaymentMethod::getIdentifier($paymentMethod);

		$endpoint = sprintf("%s/payment-methods/%s",
			rawurlencode($subscriptionID),
			rawurlencode($paymentMethodID)
			);

		$responseEntity = Bf_PaymentMethod::getClassName();

		return static::retireAndGrabFirst($endpoint, NULL, $customClient, $responseEntity);
	}

	/**
	 * Gets a list of available payment methods for the specified subscription
	 * @see static::removePaymentMethodFromSubscription()
	 * @param union[string | Bf_PaymentMethod] $paymentMethod The Bf_PaymentMethod which should be removed. <string>: ID of the Bf_PaymentMethod. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod The removed payment method.
	 */
	public function removePaymentMethod($paymentMethod) {
		return static::removePaymentMethodFromSubscription($paymentMethod, $this);
	}

	/**
	 * Gets Bf_SubscriptionCharges for this Bf_Subscription
	 * @return Bf_SubscriptionCharge[]
	 */
	public function getCharges($options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$endpoint = sprintf("%s/charges",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	/**
	 * Fetches all versions of Bf_Subscription for this Bf_Subscription.
	 * @return Bf_Subscription[]
	 */
	public function getAllVersions($options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($this);
		return Bf_Subscription::getAllVersionsForID($subscriptionID, $options, $customClient);
	}

	/**
	 * Fetches Bf_Amendments for this Bf_Subscription.
	 * @return Bf_Amendment[]
	 */
	public function getAmendments($options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($this);
		return Bf_Amendment::getForSubscription($subscriptionID, $options, $customClient);
	}

	/**
	 * Fetches Bf_Invoices for this Bf_Subscription.
	 * @return Bf_Invoice[]
	 */
	public function getInvoices($options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($this);
		return Bf_Invoice::getForSubscription($subscriptionID, $options, $customClient);
	}

	/**
	 * Gets Bf_CreditNotes for this Bf_Subscription.
	 * @return Bf_CreditNote[]
	 */
	public function getCreditNotes($options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($this);
		return Bf_CreditNote::getForSubscription($subscriptionID, $options, $customClient);
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
	 * Gets Bf_PricingComponentValues for the given Bf_Subscription.
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PricingComponentValue[]
	 */
	public static function getPricingComponentValuesForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$encoded = rawurlencode($subscriptionIdentifier);

		$endpoint = sprintf("%s/values",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_PricingComponentValue::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	/**
	 * Gets Bf_PricingComponentValues for this Bf_Subscription.
	 * @return Bf_PricingComponentValue[]
	 */
	public function getPricingComponentValues($refresh = false, $options = NULL, $customClient = NULL) {
		if ($refresh || is_null($this->pricingComponentValues)) {
			$this->pricingComponentValues = static::getPricingComponentValuesForSubscription($this, $options, $customClient);
		}
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
	 *	* @param array[string => string_ENUM[NULL, 'Immediate', 'Delayed']] (Default: array()) $..['namesToChangeModeOverrides'] The map of pricing component names to change mode overrides.
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
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)}  $..['actioningTime'] When to action the upgrade amendment
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

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'subscriptionID' => $subscriptionID,
				'componentChanges' => $componentChanges
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($this)));

		$amendment = new Bf_PricingComponentValueAmendment($stateParams);

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Upgrades/downgrades a pricing component value on the subscription
	 * @param union[string $name | Bf_PricingComponent $entity] Reference to pricing component whose value you wish to change. <string>: name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent entity.
	 * @param number Value to which you wish to upgrade/downgrade
	 * @param array $changeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param boolean $..['noCharge'] Whether to charge the user for the upgrade/downgrade
	 *	* @param union[NULL | string_ENUM['immediate', 'delayed']] (Default: NULL) $..['changeMode'] When the change in value of the component should take effect
	 *	*	
	 *	*	NULL
	 *  *	Use the existing change mode specified on the component
	 *	*
	 *	*	<immediate>
	 *	*	Change the value immediately.
	 *	*	Overrides whatever change mode is specified already on the component.
	 *	*
	 *	*	<delayed>
	 *	*	Change the value when the period ends.
	 *	*	Overrides whatever change mode is specified already on the component.
	 *	*
	 * @return Bf_PricingComponentValueResponse The result of changing the value.
	 */
	public function changeValue(
		$pricingComponent,
		$value,
		array $changeOptions = array(
			'changeMode' => NULL,
			'invoicingType' => 'Immediate',
			'noCharge' => false
			)
		) {
		$inputOptions = $changeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$name = $pricingComponent;
		if (static::isEntityOfGivenClass($pricingComponent, Bf_PricingComponent::getClassName())) {
			$name = $pricingComponent->name;
		}

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'value' => $value,
				),
			$inputOptions
			);
		$requestEntity = new Bf_PricingComponentValueRequest($stateParams);

		$endpoint = sprintf("%s/values/%s",
			rawurlencode($subscriptionID),
			rawurlencode($name)
			);

		$responseEntity = Bf_PricingComponentValueResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Upgrades/downgrade multiple pricing component values on the subscription
	 * @param array[string => number] $namesToValues The map of pricing component names (or IDs) to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @see changeValue()
	 * @return Bf_PricingComponentValueResponse[] All value change results created in the process.
	 */
	public function changeValues(
		array $namesToValues,
		array $changeOptions = array(
			'changeMode' => NULL,
			'invoicingType' => 'Immediate',
			'noCharge' => false
			)
		) {
		$inputOptions = $changeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$pricingComponentValueAndIDRequests = array_map(
			function($key, $value) {
				$requestStateParams = array(
					'value' => $value
					);

				$request = new Bf_PricingComponentValueRequest($requestStateParams);

				$wrapperStateParams = array(
					'pricingComponent' => $key,
					'request' => $request
					);

				$wrapper = new Bf_PricingComponentValueAndIDRequest($wrapperStateParams);

				return $wrapper;

			},
			array_keys($namesToValues),
			$namesToValues
		);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'requests' => $pricingComponentValueAndIDRequests
				),
			$inputOptions
			);
		$requestEntity = new Bf_PricingComponentValuesRequest($stateParams);

		$endpoint = sprintf("%s/values-batch",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_PricingComponentValueResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Removes for the specified subscription pending value changes for the given pricing component
	 * @param union[string($id|$name) | Bf_PricingComponent] Reference to pricing component whose pending changes you wish to discard. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription from which the Bf_PricingComponent should be removed. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PricingComponentValue[] The remaining pricing component values in effect for the provided Bf_PricingComponent
	 */
	public static function removePendingValueChangeFromSubscription($pricingComponent, $subscription) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);
		$pricingComponentRef = Bf_PricingComponent::getIdentifier($pricingComponent);

		$endpoint = sprintf("%s/values/%s",
			rawurlencode($subscriptionID),
			rawurlencode($pricingComponentRef)
			);

		$responseEntity = Bf_PricingComponentValue::getClassName();

		return static::retireAndGrabCollection($endpoint, NULL, $customClient, $responseEntity);
	}

	/**
	 * Removes for the specified subscription pending value changes for the given pricing component
	 * @param union[string($id|$name) | Bf_PricingComponent] Reference to pricing component whose pending changes you wish to discard. <string>: ID or name of the Bf_PricingComponent. <Bf_PricingComponent>: The Bf_PricingComponent.
	 * @return Bf_PricingComponentValue[] The remaining pricing component values in effect for the provided Bf_PricingComponent
	 */
	public function removePendingValueChange($pricingComponent) {
		return static::removePendingValueChangeFromSubscription($pricingComponent, $this);
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
	 *	* @param union[NULL | string] (Default: NULL) $..['renameSubscription'] Optionally rename the subscription upon migration. <NULL> Leave the subscription's name unchanged. <string> The name to which you would like to rename the subscription.
	 *	* @param string_ENUM['None', 'Full', 'Difference', 'DifferenceProRated', 'ProRated'] (Default: 'DifferenceProRated') $..['pricingBehaviour'] Strategy for calculating migration charges.
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
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $..['actioningTime'] When to action the migration amendment.
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

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'mappings' => $mappings,
				'subscriptionID' => $subscriptionID,
				'productRatePlanID' => $planID
				),
			$inputOptions
			);
		static::renameKey($stateParams, 'renameSubscription', 'nextSubscriptionName');
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($this)));

		$amendment = new Bf_ProductRatePlanMigrationAmendment($stateParams);

		$createdAmendment = Bf_ProductRatePlanMigrationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously migrates the subscription to the specified plan.
	 * @see scheduleMigratePlan()
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
					'pricingComponent' => $name,
					'value' => $value
				));
			},
			array_keys($namesToValues),
			$namesToValues
			);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'mappings' => $mappings,
				'productRatePlan' => $planID
				),
			$inputOptions
			);
		static::renameKey($stateParams, 'renameSubscription', 'nextSubscriptionName');
		$requestEntity = new Bf_MigrationRequest($stateParams);

		$endpoint = sprintf("%s/migrate",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_MigrationResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// CANCEL

	/**
	 * Cancels subscription at a specified time.
	 * @param array $cancellationOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string_ENUM['Immediate', 'AtPeriodEnd'] (Default: 'AtPeriodEnd') $..['serviceEnd'] Specifies when the service ends after the subscription is cancelled.
	 *	* 	<Immediate>
	 *	* 	Subscription ends service as soon as it is cancelled.
	 *	*
	 *	* 	<AtPeriodEnd> (Default)
	 *	* 	After cancellation, the subscription continues to provide service until its billing period ends.
	 *	*
	 *	* @param string_ENUM['Credit', 'None'] (Default: 'Credit') $..['cancellationCredit'] 
	 *	*
	 *	* 	<Credit> (Default)
	 *	*
	 *	* 	<None>
	 *	*
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $..['actioningTime'] When to action the cancellation amendment
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

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'subscriptionID' => $subscriptionID
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($this)));

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

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
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

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
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

	//// INVOICE OUTSTANDING CHARGES

	/**
	 * Synchronously generates invoices for outstanding charges on the subscription.
	 * @param array $invoicingOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $..['includeAggregated']
	 *	* @param boolean (Default: false) $..['includeInvoicedChargesOnly']
	 *	* @param union[NULL | string_ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] (Default: NULL) $..['invoiceState']]
	 * @return Bf_Invoice[] The generated invoices.
	 */
	public function invoiceOutstandingCharges(
		array $invoicingOptions = array(
			'includeAggregated' => false,
			'includeInvoicedChargesOnly' => false,
			'invoiceState' => NULL
			)
		) {
		$inputOptions = $invoicingOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);

		$requestEntity = new Bf_SubscriptionReviveRequest($stateParams);

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$endpoint = sprintf("%s/invoice-charges",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_Invoice::getClassName();

		$constructedEntities = static::postEntityAndGrabCollection($endpoint, $requestEntity, $responseEntity);
		return $constructedEntities;
	}

	//// FREEZE

	/**
	 * Synchronously freezes the subscription.
	 * @param array $freezeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param {@see self::parseTimeRequestFromTime(mixed)} $..['scheduleResumption'] Schedules the frozen subscription to resume at some time.
	 * @return Bf_Subscription The frozen subscription.
	 */
	public function freeze(
		array $freezeOptions = array(
			'scheduleResumption' => NULL
			)
		) {

		$inputOptions = $freezeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);
		static::renameKey($stateParams, 'scheduleResumption', 'resume');
		
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
	 *	* @param boolean (Default: false) $..['dryRun'] Whether to forego persisting the effected changes.
	 *	* @param {@see self::parseTimeRequestFromTime(mixed)} $..['scheduleResumption'] Schedules the resumption to be actioned at some future time.
	 *	* @param {@see self::parseTimeRequestFromTime(mixed)} $..['newSubscriptionStart'] The start date to which the subscription will be advanced, upon resumption.
	 *	* @param string_ENUM['Trial', 'Provisioned', 'Paid', 'AwaitingPayment', 'Cancelled', 'Failed', 'Expired'] $..['newSubscriptionState'] The state to which the subscription will be moved, upon resumption.
	 * @return Bf_Subscription The frozen subscription.
	 */
	public function resume(
		array $resumptionOptions = array(
			'dryRun' => false,
			'scheduleResumption' => NULL,
			'newSubscriptionStart' => NULL,
			'newSubscriptionState' => NULL
			)
		) {

		$inputOptions = $resumptionOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);
		static::renameKey($stateParams, 'scheduleResumption', 'resume');
		
		$requestEntity = new Bf_ResumeRequest($stateParams);

		$endpoint = sprintf("%s/resume",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// ADVANCE SUBSCRIPTION THROUGH TIME

	/**
	 * Synchronously advances the subscription through time.
	 * @param array $advancementOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $..['dryRun'] Whether to forego persisting the effected changes
	 *	* @param boolean (Default: false) $..['skipIntermediatePeriods'] Whether to raise invoices as time advances over period boundaries
	 *	* @param boolean (Default: true) $..['handleAmendments'] Whether to handle any scheduled amendments as time scrubs forward
	 *	* @param string_ENUM['SingleAttempt', 'FollowDunning', 'None'] (Default: 'SingleAttempt') $..['executionStrategy'] What strategy to use when executing any invoices raised as time advances
	 *	*
	 *	* 	<SingleAttempt> (Default)
	 *	* 	Execute any invoice just once.
	 *	*
	 *	*	<FollowDunning>
	 *	*	Apply the existing dunning strategy when executing invoices.
	 *	*
	 *	* 	<None>
	 *	* 	Do not execute invoices.
	 *	*
	 *	* @param boolean (Default: false) $..['freezeOnCompletion'] Whether to move the subscription to the `Locked` state upon completion of the time advancement
	 *	* @param {@see Bf_BillingEntity::parseTimeRequestFromTime(mixed)} (Default: NULL) $..['from'] From when to advance time
	 *	* @param {@see Bf_BillingEntity::parseTimeRequestToTime(mixed)} (Default: NULL) (Used only if $..['periods'] is NULL) $..['to'] Until when to advance time
	 *	* @param integer (Default: NULL) (Used only if $..['to'] is NULL) $..['periods'] The number of period boundaries up to which the subscription's time should be advanced. A 1-value advances the subscription to the end of its current service period. Higher values advance the subscription to subsequent period boundaries.
	 *	* @param integer (Default: true) $..['advanceInclusively'] When advancing onto an instant in time: should TimeControl action billing events scheduled to run upon our destination time (i.e. if advancing to the end of the period, should we cross the boundary: entering the next period)?
	 * @return Bf_TimeResponse The results of advancing the subscription through time.
	 */
	public function advance(
		array $advancementOptions = array(
			'dryRun' => false,
			'skipIntermediatePeriods' => false,
			'handleAmendments' => true,
			'executionStrategy' => 'SingleAttempt',
			'freezeOnCompletion' => false,
			'from' => NULL,
			'to' => NULL,
			'periods' => NULL,
			'advanceInclusively' => true
			)
		) {

		$inputOptions = $advancementOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array(
				'from' => 'parseTimeRequestFromTime',
				'to' => 'parseTimeRequestToTime'
				),
			array(
				'from' => array($this),
				'to' => array($this)
				));
		$requestEntity = new Bf_TimeRequest($stateParams);

		$endpoint = sprintf("%s/advance",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_TimeResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	//// CHARGE

	/**
	 * Creates a charge on the subscription
	 * @param array $chargeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string (Default: NULL) $..['pricingComponent'] The name or ID of the pricing component (provided the charge pertains to a pricing component)
	 *	* @param string (Default: NULL) $..['pricingComponentValue'] The value of the pricing component (provided the charge pertains to a pricing component)
	 *	* @param float (Default: NULL) $..['amount'] The monetary amount of the charge (provided the charge is an ad-hoc charge rather than regarding some pricing component)
	 *	* @param string (Default: NULL) $..['description'] The reason for creating the charge
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 *	* @param boolean $..['taxAmount'] Whether to apply tax atop the charge (provided the charge is an ad-hoc charge rather than regarding some pricing component)
	 *	* @param string_ENUM['Credit', 'Debit'] (Default: 'Debit') $..['chargeType'] Direction of the charge.
	 *	*
	 *	*	<Credit>
	 *	*	Credit awarded to customer.
	 *	*
	 *	*	<Debit> (Default)
	 *	*	Money debited from customer.
	 *	*
	 * @return Bf_SubscriptionCharge[] All charges created in the process.
	 */
	public function charge(
		array $chargeOptions = array(
			'pricingComponent' => NULL,
			'pricingComponentValue' => NULL,
			'amount' => NULL,
			'description' => NULL,
			'invoicingType' => 'Aggregated',
			'taxAmount' => false,
			'chargeType' => 'Debit'
			)
		) {
		$inputOptions = $chargeOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				),
			$inputOptions
			);
		$requestEntity = new Bf_AddChargeRequest($stateParams);

		$endpoint = sprintf("%s/charge",
			rawurlencode($subscriptionID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		$constructedEntity = static::postEntityAndGrabCollection($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Creates multiple pricing component charges on the subscription
	 * @param array[string => number] $namesToValues The map of pricing component names (or IDs) to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @see charge()
	 * @return Bf_SubscriptionCharge[] All charges created in the process.
	 */
	public function chargeComponents(
		array $namesToValues,
		array $chargeOptions = array(
			'description' => NULL,
			'invoicingType' => 'Aggregated',
			'chargeType' => 'Debit'
			)
		) {
		$_this = $this;
		return array_reduce(array_map(
			function($key, $value) use($_this, $chargeOptions) {
				return $_this->charge(array_merge($chargeOptions,
					array(
						'pricingComponent' => $key,
						'pricingComponentValue' => $value
						)));
			},
			array_keys($namesToValues), $namesToValues
			), 'array_merge', array());
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'subscription');
	}
}
Bf_Subscription::initStatics();
