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
	 * @param string ENUM['Trial', 'Provisioned', 'Paid', 'AwaitingPayment', 'Cancelled', 'Failed', 'Expired'] State upon which to search
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

	//// UPGRADE/DOWNGRADE VIA AMENDMENT

	/**
	 * Upgrades/downgrades subscription to Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array[string => number] The map of pricing component names to quantities ('Bandwidth usage' => 102)
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type. <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the upgrade amendment
	 * @param string ENUM[NULL, 'Immediate', 'Delayed'] (Default: NULL) When to effect the change in pricing component values. <Immediate>: Upon actioning time, pricing components immediately change to the new value. <Delayed>: Wait until end of billing period to change pricing component to new value. <NULL>: Don't override the change mode that is already specified on the pricing component.
	 * @param array[string => string ENUM[NULL, 'Immediate', 'Delayed']] (Default: empty array()) The map of pricing component names to change mode overrides.
	 ***
	 *  <NULL> (omitted components are interpreted with NULL override)
	 *  Don't override the change mode that is already specified on the pricing component.
	 *
	 *  <Immediate>
	 *  Upon actioning the upgrade, this pricing component will immediately change to the new value.
	 *  
	 *  <Delayed>
	 *  Wait until end of billing period to change pricing component to new value.
	 ***
	 * @return Bf_PricingComponentValueAmendment The created upgrade amendment.
	 */
	public function upgrade(array $namesToValues, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', array $namesToChangeModeOverrides = array()) {
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

		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $this->id,
			'componentChanges' => $componentChanges,
			'invoicingType' => $invoicingType
			));
		$amendment->applyActioningTime($actioningTime, $this);

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		return $createdAmendment;
	}

	//// MIGRATE PLAN VIA AMENDMENT

	/**
	 * Migrates subscription to new plan, with Bf_PricingComponentValue values corresponding to named Bf_PricingComponents.
	 * This works only for 'arrears' or 'in advance' pricing components.
	 * @param array[string => number] The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param union[string $id | Bf_ProductRatePlan $entity] The rate plan to which you wish to migrate. <string>: ID of the Bf_ProductRatePlan. <Bf_ProductRatePlan>: The Bf_ProductRatePlan.
	 * @param string ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the migration amendment
	 * @param string ENUM['None', 'Full', 'Difference', 'DifferenceProRated', 'ProRated'] (Default: 'DifferenceProRated') Strategy for calculating migration charges.
	 ***
	 *  <None>
	 *  No migration charge will be issued at all.
	 *
	 *  <Full>
	 *  The migration cost will be the cost of the in advance components of the new Product Rate Plan.
	 *  
	 *  <Difference>
	 *  The migration cost will be the difference between the in advance components  
	 *  of the current Product Rate Plan and the new Product Rate plan.
	 *  
	 *  <DifferenceProRated>
	 *  The migration cost will be the difference between the in advance components  
	 *  of the current Product Rate Plan and new Product Rate plan multiplied by the ratio SecondsRemaining/SecondsInInvoicePeriod.
	 *  
	 *  <ProRated>
	 *  This value has two definitions.
	 *   1. We are migrating to a plan of the same period duration. The migration cost will be the cost of the in advance components of the new Product Rate Plan
	 *   multiplied by the ratio SecondsRemaining/SecondsInInvoicePeriod. 
	 *  
	 *   2. We are migrating to a plan of a different period duration. 
	 *   This means that a Credit Note will be generated with a ProRata value for the remaining duration of the current period.
	 ***
	 * @return Bf_ProductRatePlanMigrationAmendment The created migration amendment.
	 */
	public function migratePlan(array $namesToValues, $newPlan, $invoicingType = 'Aggregated', $actioningTime = 'Immediate', $pricingBehaviour = 'DifferenceProRated') {
		$planID = Bf_ProductRatePlan::getIdentifier($newPlan);

		$mappings = array_map(function($name, $value) {
			return new Bf_PricingComponentValueMigrationAmendmentMapping(array(
				'pricingComponentName' => $name,
				'value' => $value
			));
		}, array_keys($namesToValues), $namesToValues);
		
		$amendment = new Bf_ProductRatePlanMigrationAmendment(array(
			'subscriptionID' => $this->id,
			'productRatePlanID' => $planID,
			'mappings' => $mappings,
			'invoicingType' => $invoicingType,
			'pricingBehaviour' => $pricingBehaviour
			));
		$amendment->applyActioningTime($actioningTime, $this);

		$createdAmendment = Bf_ProductRatePlanMigrationAmendment::create($amendment);
		return $createdAmendment;
	}

	//// CANCEL VIA AMENDMENT

	/**
	 * Cancels subscription at a specified time.
	 * @param string ENUM['Immediate', 'AtPeriodEnd'] (Default: 'AtPeriodEnd') Specifies whether the service will end immediately on cancellation or if it will continue until the end of the current period.
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] Default: 'Immediate'. When to action the cancellation amendment
	 * @return Bf_CancellationAmendment The created cancellation amendment.
	 */
	public function cancel($serviceEnd = 'AtPeriodEnd', $actioningTime = 'Immediate') {
		// create model of amendment
		$amendment = new Bf_CancellationAmendment(array(
		  'subscriptionID' => $this->id,
		  'serviceEnd' => $serviceEnd
		  ));
		$amendment->applyActioningTime($actioningTime, $this);

		// create amendment using API
		$createdAmendment = Bf_CancellationAmendment::create($amendment);
		return $createdAmendment;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('subscriptions', 'subscription');
	}
}
Bf_Subscription::initStatics();
