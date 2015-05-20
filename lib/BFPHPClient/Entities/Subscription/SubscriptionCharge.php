<?php

class Bf_SubscriptionCharge extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('invoice', Bf_Invoice::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('', 'subscriptionCharge');
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
}
Bf_SubscriptionCharge::initStatics();