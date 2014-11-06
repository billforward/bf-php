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

		$endpoint = static::getResourcePath()
		->getPath();

		$response = $client->doPut($endpoint, $serial);
		$constructedEntity = $this->makeEntityFromResponse($response);

		return $constructedEntity;
	}

	/**
	 * Asks API to retire existing instance of this entity.
	 * Many BillForward entities lack 'retire' support.
	 * You could try also setting the 'deleted' boolean on an entity.
	 * @return the updated Entity.
	 */
	public function retire() {
		$serial = $this->getSerialized($this);

		$client = $this
		->getClient();

		$endpoint = static::getResourcePath()
		->getPath();

		$response = $client->doRetire($endpoint, $serial);
		$retiredEntity = $this->makeEntityFromResponse($response);

		return $retiredEntity;
	}
}
