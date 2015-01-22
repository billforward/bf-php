<?php
abstract class Bf_InsertableEntity extends Bf_BillingEntity {
	/**
	 * Asks API to create a real instance of specified entity,
	 * based on provided properties.
	 * @param Bf_InsertableEntity the Entity to create.
	 * @return the created Entity.
	 */
	public static function create(Bf_InsertableEntity $entity) {
		$endpoint = static::getResourcePath()->getPath();

		return static::postEntityAndGrabFirst($endpoint, $entity, $client);
	}
}
