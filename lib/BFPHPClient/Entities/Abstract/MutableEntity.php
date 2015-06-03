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

		$updatedEntity = static::putAndGrabFirst('', $serial, $client);
		return $updatedEntity;
	}

	/**
	 * Asks API to retire existing instance of this entity.
	 * Many BillForward entities lack 'retire' support.
	 * You could try also setting the 'deleted' boolean on an entity.
	 * @return Bf_MutableEntity the retired Entity.
	 */
	public function retire() {
		$id = static::getIdentifier($this);
		// empty IDs are no good!
		if (!$id) {
			throw new Bf_EmptyArgumentException("Cannot retire entity with empty ID!");
		}

		// $serial = $this->getSerialized();
		$client = $this->getClient();

		$endpoint = sprintf("/%s",
			rawurlencode($id)
			);

		$retiredEntity = static::retireAndGrabFirst($endpoint, NULL, $client);
		return $retiredEntity;
	}
}
