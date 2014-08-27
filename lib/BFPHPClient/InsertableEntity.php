<?php
abstract class Bf_InsertableEntity extends Bf_BillingEntity {
	/**
	 * Asks API to create a real instance of this entity,
	 * based on provided properties.
	 * @return the created Entity.
	 */
	public function create() {
		$serial = $this->getSerialized($this);

		$client = $this
		->getClient();

		$endpoint = static::getResourcePath()
		->getPath();

		$response = $client->doPost($endpoint, $serial);
		$constructedEntity = $this->makeEntityFromResponse($response);

		return $constructedEntity;
	}

	protected function makeEntityFromResponse(Bf_RawAPIOutput $response) {
		$payload = $response
		->json();
		$results = $payload['results'];

		// For now assume that request succeeds, and also that user only wanted to create one entity.
		$probablyOnlyEntity = $results[0];

		$entityClass = static::getClassName();

		$client = $this
		->getClient();
		
		$constructedEntity = new $entityClass($client, $probablyOnlyEntity);

		return $constructedEntity;
	}
}
