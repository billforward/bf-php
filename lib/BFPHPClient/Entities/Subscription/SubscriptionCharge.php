<?php

class Bf_SubscriptionCharge extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('invoice', Bf_Invoice::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('charges', 'subscriptionCharge');
	}

	/**
	 * Gets this Bf_SubscriptionCharge's associated Bf_Invoice.
	 * @return Bf_Invoice
	 */
	public function getInvoice() {
		if (!$this->invoice) {
			if (!$this->invoiceID) {
				throw new Bf_PreconditionFailedException("This Bf_SubscriptionCharge has neither an 'invoice' specified, nor a 'invoiceID' by which to obtain said productRatePlan.");
			}
			$this->invoice = Bf_Invoice::getByID($this->invoiceID);
		}
		return $this->invoice;
	}

	/**
	 * Gets Bf_SubscriptionCharges for a given Bf_Account
	 * @param union[string | Bf_Account] $account Reference to account <string>: $id of the Bf_Account. <Bf_Account>: The Bf_Account entity.
	 * @return Bf_SubscriptionCharge[]
	 */
	public static function getForAccount($account, $options = NULL, $customClient = NULL) {
		$accountID = Bf_Account::getIdentifier($account);

		$endpoint = sprintf("account/%s",
			rawurlencode($accountID)
			);
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_SubscriptionCharges for a given state
	 * @param string_ENUM['Voided', 'Pending', 'AwaitingPayment', 'Paid', 'Failed'] State upon which to search
	 * @return Bf_SubscriptionCharge[]
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
	 * Gets Bf_SubscriptionCharge for a given version ID
	 * @param string version ID of the Bf_SubscriptionCharge
	 * @return Bf_SubscriptionCharge
	 */
	public static function getByVersionID($chargeVersionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$chargeVersionID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty chargeVersionID!");
		}

		$endpoint = sprintf("version/%s",
			rawurlencode($chargeVersionID)
			);

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Synchronously recalculates the charge.
	 * @param array $recalculationOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string (Default: NULL) $..['name'] The new name of the charge.
	 *	* @param string (Default: NULL) $..['description'] The new description of the charge.
	 *	* @param string (Default: NULL) $..['pricingComponentValue'] The new value of the pricing component (provided the charge pertains to a pricing component)
	 *	* @param float (Default: NULL) $..['amount'] The new monetary amount of the charge (provided the charge is an ad-hoc charge rather than regarding some pricing component)
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: 'Aggregated') $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 * @return Bf_SubscriptionCharge The charge, after recalculation.
	 */
	public function recalculate(
		array $recalculationOptions = array(
			'name' => NULL,
			'description' => NULL,
			'amount' => NULL,
			'pricingComponentValue' => NULL,
			'invoicingType' => 'Aggregated',
			'dryRun' => false
			)
		) {

		$inputOptions = $recalculationOptions;

		$chargeID = Bf_SubscriptionCharge::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);

		$requestEntity = new Bf_RecalculateChargeRequest($stateParams);

		$endpoint = sprintf("%s/recalculate",
			rawurlencode($chargeID)
			);

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}

	/**
	 * Synchronously voids the charge.
	 * @return Bf_SubscriptionCharge the retired entity.
	 */
	public function void() {
		return $this->retire();
	}

	/**
	 * Synchronously voids the charge.
	 * @param union[string $id | Bf_SubscriptionCharge $entity] Reference to charge <string>: $id of the Bf_SubscriptionCharge. <Bf_SubscriptionCharge>: The Bf_SubscriptionCharge entity.
	 * @return Bf_SubscriptionCharge the retired entity.
	 */
	public static function voidCharge($charge) {
		if (!Bf_SubscriptionCharge::isEntityOfThisClass($charge)) {
			$chargeID = Bf_SubscriptionCharge::getIdentifier($charge);
			// make sham object using string ID
			$charge = new Bf_SubscriptionCharge(array(
				'id' => $chargeID
				));
		}

		return $charge->void();
	}
}
Bf_SubscriptionCharge::initStatics();