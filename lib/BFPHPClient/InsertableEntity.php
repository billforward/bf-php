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

		$client = $entity
		->getClient();

		$resourcePath = $entity->getResourcePath();
		$overrideResourcePath = $resourcePath;

		$endpoint = $resourcePath->getPath();

		$response = $client
		->doPost($endpoint, $serial);

		$constructedEntity = static::makeEntityFromResponseStatic($response, $client, $overrideResourcePath);

		return $constructedEntity;
	}

	protected static function makeEntityFromResponseStatic(Bf_RawAPIOutput $response, BillForwardClient $providedClient, Bf_ResourcePath $overrideResourcePath = NULL) {
		$payload = $response
		->json();
		$results = $payload['results'];

		// For now assume that request succeeds, and also that user only wanted to create one entity.
		$probablyOnlyEntity = $results[0];

		$entityClass = static::getClassName();

		$client = $providedClient;
		
		$constructedEntity = new $entityClass($probablyOnlyEntity, $client, $overrideResourcePath);

		return $constructedEntity;
	}

	protected function makeEntityFromResponse(Bf_RawAPIOutput $response) {
		$client = $this
		->getClient();

		$thisClass = static::getClassName();

		$overrideResourcePath = $this->getResourcePath();

		return $thisClass::makeEntityFromResponseStatic($response, $client, $overrideResourcePath);
	}
}
