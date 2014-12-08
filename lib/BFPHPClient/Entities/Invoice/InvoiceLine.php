<?php

class Bf_InvoiceLine extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		trigger_error('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it. '
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).',
		 E_USER_ERROR);
	}
	
	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		trigger_error('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).',
		 E_USER_ERROR);
	}

	public static function getAll($options = NULL, $customClient = NULL) {
		trigger_error('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).',
		 E_USER_ERROR);
	}

	public function save() {
		trigger_error('Save support is denied for this entity; '
		 .'at the time of writing, the provided API endpoint is not functioning.'
		 .'The entity can be saved through cascade only (i.e. save a related entity).',
		 E_USER_ERROR);
	}

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		// apparently not guaranteed to exist. let's not enforce unserialization of unit of measure.
		// TODO: make this conditional rather than just off.
		//$this->unserializeEntity('unitOfMeasure', Bf_UnitOfMeasure::getClassName(), $json);
	}

	/**
	 * Gets Bf_UnitOfMeasure for this Bf_InvoiceLine.
	 * @return Bf_UnitOfMeasure
	 */
	public function getUnitOfMeasure() {
		return $this->unitOfMeasure;
	}
}