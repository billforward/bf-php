<?php
abstract class Bf_MutableEntity extends Bf_InsertableEntity {
	/**
	 * Asks API to update existing instance of this entity,
	 * based on provided properties.
	 * @return the updated Entity.
	 */
	public function save() {
		$serial = $this->getSerialized($this);

		$client = $this
		->getClient();

		$endpoint = $this->getResourcePath()
		->getPath();

		$response = $client->doPut($endpoint, $serial);
		$constructedEntity = $this->makeEntityFromResponse($response);

		return $constructedEntity;
	}
}
