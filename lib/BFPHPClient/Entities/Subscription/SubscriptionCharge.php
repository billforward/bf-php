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
}
Bf_SubscriptionCharge::initStatics();