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
	 * Changes the value of the Bf_PricingComponentValue whose corresponding Bf_PricingComponent matches
	 * the provided properties.
	 * @param array The properties by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentWhosePropertiesMatch(array $pricingComponentProperties, $newValue, $changeMode, $invoicingType = 'Aggregated') {
		$subscription = Bf_Subscription::getByID($this->subscriptionID);

		$pricingComponentValue = $subscription->getPCVCorrespondingToPricingComponentWithProperties($pricingComponentProperties);

		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $subscription->id,
			'invoiceID' => $this->id,
			'oldValue' => $pricingComponentValue->value,
			'newValue' => $newValue,
			'mode' => $changeMode,
			'invoicingType' => $invoicingType,
			'logicalComponentID' => $pricingComponentValue->pricingComponentID
			));

		$createdAmendment = Bf_PricingComponentValueAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Changes the value of the Bf_PricingComponentValue whose corresponding Bf_PricingComponent matches
	 * the provided name.
	 * @param array The name by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentWhoseNameMatches($name, $newValue, $changeMode, $invoicingType = 'Aggregated') {
		$properties = array(
			'name' => $name
			);

		return $this->changeValueOfPricingComponentWhosePropertiesMatch($properties, $newValue, $changeMode, $invoicingType);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();