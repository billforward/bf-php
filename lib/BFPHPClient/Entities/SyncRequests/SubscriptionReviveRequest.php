<?php

class Bf_SubscriptionReviveRequest extends Bf_BillingEntity {

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

	/**
	 * Constructs a Bf_SubscriptionReviveRequest model designed for resuming subscriptions.
	 * @param array $stateParams (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 * @return Bf_SubscriptionReviveRequest The created pause request model.
	 */
	public static function construct(
		array $stateParams = array(
			)
		) {
		$model = new self(array_merge(
			static::getFinalArgDefault(__METHOD__),
			$stateParams));

		return $model;
	}
}