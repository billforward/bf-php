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