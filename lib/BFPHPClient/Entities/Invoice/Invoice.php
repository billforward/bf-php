<?php

class Bf_Invoice extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		trigger_error('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be created by the BillForward engines, in response to certain events. ',
		 E_USER_ERROR);
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
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the issuance amendment.
	 * @return Bf_IssueInvoiceAmendment The created amendment.
	 */
	public function issue($actioningTime = 'Immediate') {
		$amendment = new Bf_IssueInvoiceAmendment(array(
			'subscriptionID' => $this->subscriptionID,
			'invoiceID' => $this->id
			));

		$amendment->applyActioningTime($actioningTime, $this->subscriptionID);

		$createdAmendment = Bf_IssueInvoiceAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Recalculates the invoice (now, or at a scheduled time).
	 * @param string ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] (Default: 'Pending') State to which the invoice will be moved following the recalculation.
	 * @param string ENUM['RecalculateAsLatestSubscriptionVersion', 'RecalculateAsCurrentSubscriptionVersion'] (Default: 'RecalculateAsLatestSubscriptionVersion') How to recalculate the invoice.
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the recalculation amendment.
	 * @return Bf_InvoiceRecalculationAmendment The created amendment.
	 */
	public function recalculate($newInvoiceState = 'Pending', $recalculationBehaviour = 'RecalculateAsLatestSubscriptionVersion', $actioningTime = 'Immediate') {
		$amendment = new Bf_InvoiceRecalculationAmendment(array(
			'subscriptionID' => $this->subscriptionID,
			'invoiceID' => $this->id
			));

		$amendment->applyActioningTime($actioningTime, $this->subscriptionID);

		$amendment->recalculationBehaviour = $recalculationBehaviour;

		if (!is_null($newInvoiceState)) {
			$amendment->newInvoiceState = $newInvoiceState;
		}

		$createdAmendment = Bf_InvoiceRecalculationAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Retries execution of the invoice (now, or at a scheduled time).
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the 'next execution attempt' amendment.
	 * @return Bf_InvoiceNextExecutionAttemptAmendment The created amendment.
	 */
	public function attemptRetry($actioningTime = 'Immediate') {
		$amendment = new Bf_InvoiceNextExecutionAttemptAmendment(array(
			'subscriptionID' => $this->subscriptionID,
			'invoiceID' => $this->id
			));

		$amendment->applyActioningTime($actioningTime, $this->subscriptionID);

		$createdAmendment = Bf_InvoiceNextExecutionAttemptAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Gets Bf_Invoices for a given Bf_Subscription
	 * @param string ID of the Bf_Subscription
	 * @return Bf_Invoice[]
	 */
	public static function getForSubscription($subscriptionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$subscriptionID) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$endpoint = "/subscription/$subscriptionID";

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_Invoices for a given state
	 * @param string ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] State upon which to search
	 * @return Bf_Invoice[]
	 */
	public static function getByState($state, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$state) {
    		trigger_error("Cannot lookup unspecified state!", E_USER_ERROR);
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
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
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
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
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
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
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
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$endpoint = "/account/$accountID";
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();