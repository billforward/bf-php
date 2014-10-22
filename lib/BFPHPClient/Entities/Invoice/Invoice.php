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
	 * Changes the value of whichever Bf_PricingComponentValue corresponds to a Bf_PricingComponent whose properties match
	 * all those provided.
	 * @param array The properties by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentWhosePropertiesMatch(array $pricingComponentProperties, $newValue, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		$subscription = Bf_Subscription::getByID($this->subscriptionID);

		$pricingComponentValue = $subscription->getValueOfPricingComponentWithProperties($pricingComponentProperties);

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
	 * Changes the value of whichever Bf_PricingComponentValue corresponds to a Bf_PricingComponent whose name
	 * matches the one provided.
	 * @param array The name by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentWhoseNameMatches($name, $newValue, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		$properties = array(
			'chargeModel' => 'tiered',
			'name' => $name
			);

		return $this->changeValueOfPricingComponentWhosePropertiesMatch($properties, $newValue, $changeMode, $invoicingType);
	}

	/**
	 * Changes the value of whichever Bf_PricingComponentValue corresponds to a Bf_PricingComponent which recruits
	 * the provided Bf_UnitOfMeasure.
	 * @param Bf_UnitOfMeasure The unit of measure by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription. In cases where multiple pricing components on the Bf_ProductRatePlan recruit the same Bf_UnitOfMeasure, the first Bf_PricingComponent encountered will be picked.
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentWhoseUoMMatches(Bf_UnitOfMeasure $unitOfMeasure, $newValue, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		$properties = array(
			'chargeModel' => 'tiered',
			'unitOfMeasureID' => $unitOfMeasure->id
			);

		return $this->changeValueOfPricingComponentWhosePropertiesMatch($properties, $newValue, $changeMode, $invoicingType);
	}

	/**
	 * Changes the value of whichever Bf_PricingComponentValue corresponds to a Bf_PricingComponent which recruits
	 * the provided Bf_UnitOfMeasure.
	 * @param string The Bf_UnitOfMeasure name by which a Bf_PricingComponent will be found, instrumental to finding its corresponding Bf_PricingComponentValue on the Bf_Subscription. In cases where multiple pricing components on the Bf_ProductRatePlan recruit the same Bf_UnitOfMeasure, the first Bf_PricingComponent encountered will be picked.
	 * @param float the new value to which the Bf_PricingComponentValue will be changed
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function upgrade($unitOfMeasureName, $newValue, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		$uomProperties = array(
			'name' => $unitOfMeasureName
			);
		$unitOfMeasure = Bf_UnitOfMeasure::getAllThenGrabFirstWithProperties($uomProperties);

		return $this->changeValueOfPricingComponentWhoseUoMMatches($unitOfMeasure, $newValue, $changeMode, $invoicingType);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();