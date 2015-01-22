<?php
abstract class Bf_InsertableEntity extends Bf_BillingEntity {
	/**
	 * Asks API to create a real instance of specified entity,
	 * based on provided properties.
	 * @param Bf_InsertableEntity the Entity to create.
	 * @return the created Entity.
	 */
	public static function create(Bf_InsertableEntity $entity) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		$endpoint = static::getResourcePath()->getPath();

		$response = $client->doPost($endpoint, $serial);

		$constructedEntity = static::responseToFirstEntity($response, $client);
		return $constructedEntity;
	}
}
