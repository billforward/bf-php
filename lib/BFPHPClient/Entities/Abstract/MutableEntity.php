<?php
abstract class Bf_MutableEntity extends Bf_InsertableEntity {
	/**
	 * Asks API to update existing instance of this entity,
	 * based on provided properties.
	 * @return Bf_MutableEntity the updated Entity.
	 */
	public function save() {
		$serial = $this->getSerialized();
		$client = $this->getClient();

		$endpoint = static::getResourcePath()->getPath();

		$updatedEntity = static::putAndGrabFirst($endpoint, $serial, $client);
		return $updatedEntity;
	}

	/**
	 * Asks API to retire existing instance of this entity.
	 * Many BillForward entities lack 'retire' support.
	 * You could try also setting the 'deleted' boolean on an entity.
	 * @return Bf_MutableEntity the retired Entity.
	 */
	public function retire() {
		$serial = $this->getSerialized();
		$client = $this->getClient();

		$endpoint = static::getResourcePath()->getPath();

		$retiredEntity = static::retireAndGrabFirst($endpoint, $serial, $client);
		return $retiredEntity;
	}
}
