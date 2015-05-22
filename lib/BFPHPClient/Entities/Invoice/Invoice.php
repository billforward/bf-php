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
		return Bf_CreditNote::getForInvoice($this->id, $options, $customClient);
	}

	/**
	 * Fetches all versions of Bf_Invoice for this Bf_Invoice.
	 * @return Bf_Invoice[]
	 */
	public function getAllVersions($options = NULL, $customClient = NULL) {
		return static::getAllVersionsForID($this->id, $options, $customClient);
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

		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $subscriptionID);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);

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
			'actioningTime' => 'Immediate'
			)
		) {
		$inputOptions = $recalculationOptions;

		$subscriptionID = Bf_Subscription::getIdentifier($this->subscriptionID);
		$invoiceID = Bf_Invoice::getIdentifier($this);

		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $subscriptionID);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);

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
			'newInvoiceState' => 'Pending',
			'recalculationBehaviour' => 'RecalculateAsLatestSubscriptionVersion'
			)
		) {

		$inputOptions = $recalculationOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);

		$requestEntity = new Bf_InvoiceRecalculationRequest($stateParams);

		$endpoint = sprintf("%s/execute",
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

		$actioningTime = Bf_Amendment::parseActioningTime(static::popKey($inputOptions, 'actioningTime'), $subscriptionID);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'invoiceID' => $invoiceID,
				'actioningTime' => $actioningTime
				),
			$inputOptions
			);

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

		$inputOptions = $recalculationOptions;

		$invoiceID = Bf_Invoice::getIdentifier($this);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				),
			$inputOptions
			);

		$requestEntity = new Bf_InvoiceExecutionRequest($stateParams);

		$endpoint = sprintf("%s/execute",
			rawurlencode($invoiceID)
			);

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity);
		return $constructedEntity;
	}

	/**
	 * Gets Bf_Invoices for a given Bf_Subscription
	 * @param string ID of the Bf_Subscription
	 * @return Bf_Invoice[]
	 */
	public static function getForSubscription($subscriptionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$subscriptionID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/subscription/$subscriptionID";

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

		$endpoint = "/state/$state";

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
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/version/$invoiceVersionID";

		return static::getFirst($endpoint, $options, $customClient);
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

		$endpoint = "/$id";

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

		$endpoint = "/subscription/version/$subscriptionVersionID";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Invoices for a given Bf_Account
	 * @param string ID of the Bf_Account
	 * @return Bf_Invoice[]
	 */
	public static function getForAccount($accountID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$accountID) {
    		throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
		}

		$endpoint = "/account/$accountID";
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();