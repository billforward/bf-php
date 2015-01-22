<?php
abstract class Bf_InsertableEntity extends Bf_BillingEntity {
	/**
	 * Asks API to create a real instance of specified entity,
	 * based on provided properties.
	 * @param the Entity to create.
	 * @return the created Entity.
	 */
	public static function create(Bf_InsertableEntity $entity) {
		$serial = $entity->getSerialized();
		$client = $entity->getClient();

		$endpoint = static::getResourcePath()->getPath();

		$response = $client->doPost($endpoint, $serial);

		$constructedEntity = static::makeEntityFromResponseStatic($response, $client);

		return $constructedEntity;
	}

	protected static function makeEntityFromResponseStatic(Bf_RawAPIOutput $response, BillForwardClient $client) {
		// For now assume that request succeeds, and also that user only wanted to create one entity.
		$probablyOnlyEntity = $payload->getFirstResult();

		$entityClass = static::getClassName();
		
		$constructedEntity = new $entityClass($probablyOnlyEntity, $client);

		return $constructedEntity;
	}

	protected function makeEntityFromResponse(Bf_RawAPIOutput $response) {
		$client = $this->getClient();

		$thisClass = static::getClassName();

		return $thisClass::makeEntityFromResponseStatic($response, $client);
	}
}
