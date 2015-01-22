<?php
abstract class Bf_InsertableEntity extends Bf_BillingEntity {
	/**
	 * Asks API to create a real instance of specified entity,
	 * based on provided properties.
	 * @param Bf_InsertableEntity the Entity to create.
	 * @param Bf_InsertableEntity (Default: NULL) Name of the class of entity you expect to receive back (defaults to the input class).
	 * @return the created Entity.
	 */
	public static function create(Bf_InsertableEntity $entity, $responseEntity = NULL) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		$endpoint = static::getResourcePath()->getPath();

		$response = $client->doPost($endpoint, $serial);

		$constructedEntity = static::responseToFirstEntity($response, $client, $responseEntity);
		return $constructedEntity;
	}
}
