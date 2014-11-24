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
	 * Gets Bf_Invoices for a given Bf_Subscription
	 * @return Bf_Invoice[]
	 */
	public static function getForSubscription($subscriptionID, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/subscription/".$subscriptionID;
		$fullRoute = $apiRoute.$endpoint;
		
		$response = $client->doGet($fullRoute, $options);
		
		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();