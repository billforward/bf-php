<?php

class Bf_TimeResponse extends Bf_TimeRequest {
	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('subscriptions', Bf_Subscription::getClassName(), $json);
		$this->unserializeArrayEntities('invoices', Bf_Invoice::getClassName(), $json);
	}
}