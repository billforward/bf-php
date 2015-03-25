<?php

class Bf_AmendmentPriceNTime extends Bf_InsertableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		throw new Bf_UnsupportedMethodException('Create support is denied for this entity; '
		 .'this entity is never persisted to a database, and thus cannot be created. '
		 .'It exists solely to model an entity that the API can return in a response.');
	}

	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get by ID support is denied for this entity; '
		 .'this entity is never persisted to a database, and thus cannot be GETted.'
		 .'It exists solely to model an entity that the API can return in a response.');
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get All support is denied for this entity; '
		 .'this entity is never persisted to a database, and thus cannot be GETted.'
		 .'It exists solely to model an entity that the API can return in a response.');
	}

	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);
	}

	/**
	 * Wrapper to allow other classes (for example the Pricing Calculator) to
	 * make instances of this entity from a server response.
	 * @param Bf_RawAPIOutput $response 
	 * @param BillForwardClient $providedClient 
	 * @return Bf_AmendmentPriceNTime The constructed entity
	 */
	public static function callMakeEntityFromResponseStatic(Bf_RawAPIOutput $response, BillForwardClient $providedClient) {
		return static::makeEntityFromResponseStatic($response, $providedClient);
	}
}