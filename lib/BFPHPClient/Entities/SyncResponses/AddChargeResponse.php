<?php

class Bf_AddChargeResponse extends Bf_BillingEntity {

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('request', Bf_AddChargeRequest::getClassName(), $json);
		$this->unserializeEntity('invoice', Bf_Invoice::getClassName(), $json);
		$this->unserializeArrayEntities('charges', Bf_SubscriptionCharge::getClassName(), $json);
	}

	public static function getByID($id, $options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}
}