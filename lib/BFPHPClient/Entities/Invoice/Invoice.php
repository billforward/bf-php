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
	 * @param array List of pricing component properties; array(array('name' => 'Bandwidth usage'), array('name' => 'CPU usage'))
	 * @param array List of values to assign to respective pricing components; array(103, 2)
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentByProperties(array $propertiesList, array $valuesList, array $pricingComponentProperties, $newValue, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		if (!is_array($propertiesList)) {
			throw new \Exception('Expected input to be an array (a list of entity property maps). Instead received: '+$propertiesList);
		}

		if (!is_array($valuesList)) {
			throw new \Exception('Expected input to be an array (a list of integer values). Instead received: '+$valuesList);
		}

		$subscription = Bf_Subscription::getByID($this->subscriptionID);

		$componentCharges = array();

		foreach ($propertiesList as $key => $propertyMap) {
			if (!is_array($propertyMap)) {
				throw new \Exception('Expected each element of input array to be an array (a map of expected properties on entity, to values). Instead received: '+$propertyMap);
			}

			$newValue = $valuesList[$key];

			$pricingComponentValue = $subscription->getValueOfPricingComponentWithProperties($propertyMap);
			$componentCharge = new Bf_ComponentCharge(array(
				'logicalComponentID' => $pricingComponentValue->pricingComponentID,
				'oldValue' => $pricingComponentValue->value,
				'newValue' => $newValue
			));

			array_push($componentCharges, $componentCharge)
		}
		
		$amendment = new Bf_PricingComponentValueAmendment(array(
			'subscriptionID' => $subscription->id,
			'invoiceID' => $this->id,
			'componentCharges' => $componentCharges,
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
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function changeValueOfPricingComponentsByName(array $namesToValues, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		$propertiesList = array();
		$valuesList = array();

		foreach($namesToValues as $key => $value) {
			// from pricing component name, create a dictionary of identifying properties
			$pricingComponentPropertyMap = array(
				'name' => $key
				);
			array_push($propertiesList, $pricingComponentPropertyMap);
		}

		return $this->changeValueOfPricingComponentWhosePropertiesMatch($propertiesList, $valuesList, $changeMode, $invoicingType);
	}

	/**
	 * Changes the value of whichever Bf_PricingComponentValue corresponds to a Bf_PricingComponent which recruits
	 * the provided Bf_UnitOfMeasure.
	 * @param array The map of pricing component names to numerical values ('Bandwidth usage' => 102)
	 * @param string ENUM['immediate', 'delayed'] When the change happens. <immediate>: Immediately, <delayed>: At the start of the next billing period
	 * @param string ENUM['Immediate', 'Aggregated'] Subscription-charge invoicing type <Immediate>: Generate invoice straight away with this charge applied, <Aggregated>: Add this charge to next invoice
	 * @return Bf_PricingComponentValueAmendment
	 */
	public function upgrade(array $namesToValues, $changeMode = 'immediate', $invoicingType = 'Aggregated') {
		return $this->changeValueOfPricingComponentsByName($namesToValues, $changeMode, $invoicingType);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('invoices', 'invoice');
	}
}
Bf_Invoice::initStatics();