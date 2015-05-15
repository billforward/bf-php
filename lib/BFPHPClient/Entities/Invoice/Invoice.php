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
	 * @param union[int $timestamp | string_ENUM['Immediate', 'AtPeriodEnd']] (Default: 'Immediate') When to action the issuance amendment
	 ***
	 *  int
	 *  Schedule the issuance to occur at the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  Perform the issuance now (synchronously where possible).
	 *  
	 *  <AtPeriodEnd>
	 *  Schedule the issuance to occur at the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the issuance to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 ***
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
	 * @param string_ENUM['Paid', 'Unpaid', 'Pending', 'Voided'] (Default: 'Pending') State to which the invoice will be moved following the recalculation.
	 * @param string_ENUM['RecalculateAsLatestSubscriptionVersion', 'RecalculateAsCurrentSubscriptionVersion'] (Default: 'RecalculateAsLatestSubscriptionVersion') How to recalculate the invoice.
	 * @param union[int $timestamp | string_ENUM['Immediate', 'AtPeriodEnd']] (Default: 'Immediate') When to action the recalculation amendment
	 ***
	 *  int
	 *  Schedule the recalculation to occur at the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  Perform the recalculation now (synchronously where possible).
	 *  
	 *  <AtPeriodEnd>
	 *  Schedule the recalculation to occur at the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the recalculation to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 ***
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
	 * @param union[int $timestamp | string_ENUM['Immediate', 'AtPeriodEnd']] (Default: 'Immediate') When to action the 'next execution attempt' amendment
	 ***
	 *  int
	 *  Schedule the 'next execution attempt' to occur at the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default)
	 *  Perform the 'next execution attempt' now (synchronously where possible).
	 *  
	 *  <AtPeriodEnd>
	 *  Schedule the 'next execution attempt' to occur at the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the 'next execution attempt' to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 ***
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