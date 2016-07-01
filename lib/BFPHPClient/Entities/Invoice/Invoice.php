<?php

class Bf_Invoice extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		throw new Bf_UnsupportedMethodException('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be created by the BillForward engines, in response to certain events. ');
	}

	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('invoiceLines', Bf_InvoiceLine::getClassName(), $json);
		$this->unserializeArrayEntities('taxLines', Bf_TaxLine::getClassName(), $json);
		$this->unserializeArrayEntities('invoicePayments', Bf_InvoicePayment::getClassName(), $json);
		$this->unserializeArrayEntities('invoiceRefunds', Bf_Refund::getClassName(), $json);
		$this->unserializeArrayEntities('invoiceCreditNotes', Bf_CreditNote::getClassName(), $json);
		$this->unserializeArrayEntities('charges', Bf_SubscriptionCharge::getClassName(), $json);
	}

	/**
	 * Gets Bf_InvoiceLines for this Bf_Invoice.
	 * @return Bf_InvoiceLine[]
	 */
	public function getInvoiceLines() {
		return $this->invoiceLines;
	}

	/**
	 * Gets Bf_TaxLines for this Bf_Invoice.
	 * @return Bf_TaxLine[]
	 */
	public function getTaxLines() {
		return $this->taxLines;
	}

	/**
	 * Gets Bf_InvoicePayments for this Bf_Invoice.
	 * @return Bf_InvoicePayment[]
	 */
	public function getInvoicePayments() {
		return $this->invoicePayments;
	}

	/**
	 * Gets Bf_CreditNotes for this Bf_Invoice.
	 * @return Bf_CreditNote[]
	 */
	public function getCreditNotes($options = NULL, $customClient = NULL) {
		$invoiceID = Bf_Invoice::getIdentifier($this);
		return Bf_CreditNote::getForInvoice($invoiceID, $options, $customClient);
	}

	/**
	 * Fetches all versions of Bf_Invoice for this Bf_Invoice.
	 * @return Bf_Invoice[]
	 */
	public function getAllVersions($options = NULL, $customClient = NULL) {
		$invoiceID = Bf_Invoice::getIdentifier($this);
		return static::getAllVersionsForID($invoiceID, $options, $customClient);
	}

	/**
	 * Gets Bf_SubscriptionCharges for this Bf_Invoice
	 * @return Bf_SubscriptionCharge[]
	 */
	public function getCharges($options = NULL, $customClient = NULL) {
		$invoiceID = Bf_Invoice::getIdentifier($this);

		$endpoint = sprintf("%s/charges",
			rawurlencode($invoiceID)
			);

		$responseEntity = Bf_SubscriptionCharge::getClassName();

		return static::getCollection($endpoint, $options, $customClient, $responseEntity);
	}

	/**
	 * Issues the invoice (now, or at a scheduled time).
	 * @param array $issuanceOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $issuanceOptions['actioningTime'] When to action the issuance amendment
	 * @return Bf_IssueInvoiceAmendment The created amendment.
	 */
	public function scheduleIssuance(
		array $issuanceOptions = array(
			'actioningTime' => 'Immediate'
			)
		) {

		$inputOptions = $issuanceOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this->subscriptionID);
		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($subscriptionID)));

		$amendment = new Bf_IssueInvoiceAmendment($stateParams);

		$createdAmendment = Bf_IssueInvoiceAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously issues the invoice.
	 * @see issue()
	 * @return Bf_Invoice The invoice, after issuance.
	 */
	public function issue(
		array $issuanceOptions = array(
			)
		) {

		$this->state = 'Unpaid';
		$updatedInvoice = $this->save();
		return $updatedInvoice;
	}

	/**
	 * Synchronously unissues an 'Unpaid' invoice.
	 * @return Bf_Invoice The invoice, after unissuance.
	 */
	public function unissue(
		array $unissuanceOptions = array(
			)
		) {

		$this->state = 'Pending';
		$updatedInvoice = $this->save();
		return $updatedInvoice;
	}

	/**
	 * Recalculates the invoice (now, or at a scheduled time).
	 * @param array $recalculationOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string_ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] (Default: 'Pending') $..['newInvoiceState'] State to which the invoice will be moved following the recalculation.
	 *	* @param string_ENUM['RecalculateAsLatestSubscriptionVersion', 'RecalculateAsCurrentSubscriptionVersion'] (Default: 'RecalculateAsLatestSubscriptionVersion') $..['recalculationBehaviour'] How to recalculate the invoice.
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $..['actioningTime'] When to action the recalculation amendment
	 * @return Bf_InvoiceRecalculationAmendment The created amendment.
	 */
	public function scheduleRecalculation(
		array $recalculationOptions = array(
			'newInvoiceState' => 'Pending',
			'recalculationBehaviour' => 'RecalculateAsLatestSubscriptionVersion',
			'actioningTime' => 'Immediate',
			'includeInvoicedChargesOnly' => true
			)
		) {
		$inputOptions = $recalculationOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this->subscriptionID);
		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($subscriptionID)));

		$amendment = new Bf_InvoiceRecalculationAmendment($stateParams);

		$createdAmendment = Bf_InvoiceRecalculationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously recalculates the invoice.
	 * @see recalculate()
	 * @return Bf_Invoice The invoice, after recalculation.
	 */
	public function recalculate(
		array $recalculationOptions = array(
			'newState' => 'Pending',
			'recalculationBehaviour' => 'RecalculateAsLatestSubscriptionVersion',
			'onlyInvoiceAssociatedCharges' => true,
			'dryRun' => false
			)
		) {

		$inputOptions = $recalculationOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);

		$requestEntity = new Bf_InvoiceRecalculationRequest($stateParams);

		$endpoint = sprintf("%s/recalculate",
			rawurlencode($invoiceID)
			);

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}

	/**
	 * Retries execution of the invoice (now, or at a scheduled time).
	 * @param array $executionOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param bool $..['forcePaid'] (Default: false) Whether to force the invoice into the paid state using an 'offline payment'.
	 *	* @param {@see Bf_Amendment::parseActioningTime(mixed)} $..['actioningTime'] When to action the 'next execution attempt' amendment
	 * @return Bf_InvoiceNextExecutionAttemptAmendment The created 'next execution attempt' amendment.
	 */
	public function scheduleRetryExecution(
		array $executionOptions = array(
			'forcePaid' => false,
			'actioningTime' => 'Immediate'
			)
		) {
		$inputOptions = $executionOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this->subscriptionID);
		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($subscriptionID)));

		$amendment = new Bf_InvoiceNextExecutionAttemptAmendment($stateParams);

		$createdAmendment = Bf_InvoiceNextExecutionAttemptAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Synchronously retries execution of the invoice.
	 * @see retryExecution()
	 * @return Bf_Invoice The invoice, after attempting execution.
	 */
	public function retryExecution(
		array $executionOptions = array(
			'forcePaid' => false
			)
		) {

		$inputOptions = $executionOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);

		$requestEntity = new Bf_InvoiceExecutionRequest($stateParams);

		$endpoint = sprintf("%s/execute",
			rawurlencode($invoiceID)
			);

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}

	//// CHARGE

	/**
	 * Creates a charge on the invoice
	 * @param array $chargeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param string (Default: NULL) $..['pricingComponentName'] The name of the pricing component (provided the charge pertains to a pricing component)
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
	 *	* @param string_ENUM['Credit', 'Debit'] (Default: 'Debit') $..['chargeType']
	 *	*
	 *	*	<Credit>
	 *	*
	 *	*	<Debit> (Default)
	 *	*
	 * @return Bf_SubscriptionCharge[] All charges created in the process.
	 */
	public function charge(
		array $chargeOptions = array(
			'pricingComponentName' => NULL,
			'pricingComponentValue' => NULL,
			'amount' => NULL,
			'description' => NULL,
			'invoicingType' => 'Aggregated',
			'taxAmount' => false,
			'chargeType' => 'Debit'
			)
		) {
		$inputOptions = $chargeOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				),
			$inputOptions
			);
		$requestEntity = new Bf_AddChargeRequest($stateParams);

		$endpoint = sprintf("%s/charges",
			rawurlencode($invoiceID)
			);

		$responseEntity = Bf_AddChargeResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Gets Bf_Invoices for a given Bf_Subscription
	 * @param union[string | Bf_Subscription] $subscription Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return Bf_Invoice[]
	 */
	public static function getForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$endpoint = sprintf("subscription/%s",
			rawurlencode($subscriptionID)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Invoices for a given state
	 * @param string_ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] State upon which to search
	 * @return Bf_Invoice[]
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
	 * Gets Bf_Invoice for a given version ID
	 * @param string version ID of the Bf_Invoice
	 * @return Bf_Invoice
	 */
	public static function getByVersionID($invoiceVersionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$invoiceVersionID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty invoiceVersionID!");
		}

		$endpoint = sprintf("version/%s",
			rawurlencode($invoiceVersionID)
			);

		return static::getFirst($endpoint, $options, $customClient);
	}

	/**
	 * Creates multiple pricing component charges on the invoice
	 * @param array[string => number] $namesToValues The map of pricing component names (or IDs) to quantities
	 * Example:
	 * array(
	 * 	'Bandwidth' => 102,
	 * 	'CPU' => 10
	 * )
	 * @param array $chargeOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param Boolean (Default: NULL) $..['dryRun'] [If null: do not override the `dryRun` value to which the nested requests default.] Whether to forego persisting the effected changes.
	 *	* @param string_ENUM['Immediate', 'Aggregated'] (Default: NULL) $..['invoicingType'] Subscription-charge invoicing type
	 *	*
	 *	*	NULL
	 *  *	Do not override the `invoicingType` value to which the nested requests default.
	 *  *	
	 *	*	<Immediate>
	 *	*	Generate invoice straight away with this charge applied.
	 *	*
	 *	*	<Aggregated> (Default)
	 *	*	Add this charge to next invoice.
	 *	*
	 * @return Bf_AddChargesResponse Response object containing result of adding batch of charges
	 */
	public function chargeComponentsBatch(
		array $namesToValues,
		array $chargeOptions = array(
			'invoicingType' => 'Aggregated',
			'dryRun' => false
			)
		) {
		$inputOptions = $chargeOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$addChargeRequests = array_map(
			function($key, $value) {
				$stateParams = array(
					'pricingComponent' => $key,
					'pricingComponentValue' => $value
					);

				$addChargeRequest = new Bf_AddChargeRequest($stateParams);

				return $addChargeRequest;

			},
			array_keys($namesToValues),
			$namesToValues
		);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(
				'requests' => $addChargeRequests
				),
			$inputOptions
			);
		$requestEntity = new Bf_AddChargesToInvoiceRequest($stateParams);

		$endpoint = sprintf("%s/charges/batch",
			rawurlencode($invoiceID)
			);

		$responseEntity = Bf_AddChargesResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Gets all versions of Bf_Invoice for a given consistent ID
	 * @param string ID of the Bf_Invoice
	 * @return Bf_Invoice[]
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
	 * Gets Bf_Invoices for a given Bf_Subscription version
	 * @param string version ID of the Bf_Subscription
	 * @return Bf_Invoice[]
	 */
	public static function getForSubscriptionVersion($subscriptionVersionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$subscriptionVersionID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = sprintf("subscription/version/%s",
			rawurlencode($subscriptionVersionID)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Invoices for a given Bf_Account
	 * @param union[string | Bf_Account] $account Reference to account <string>: $id of the Bf_Account. <Bf_Account>: The Bf_Account entity.
	 * @return Bf_Invoice[]
	 */
	public static function getForAccount($account, $options = NULL, $customClient = NULL) {
		$accountID = Bf_Account::getIdentifier($account);

		$endpoint = sprintf("account/%s",
			rawurlencode($accountID)
			);
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();