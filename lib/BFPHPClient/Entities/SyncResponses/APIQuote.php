<?php

class Bf_APIQuote extends Bf_InsertableEntity {
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

	public static function create(Bf_InsertableEntity $entity) {
		throw new Bf_UnsupportedMethodException('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.');
	}

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('quantities', Bf_QuoteResponseValue::getClassName(), $json);
		$this->unserializeArrayEntities('discounts', Bf_CouponWrapperResponse::getClassName(), $json);
	}
}