<?php

class Bf_GenericEntity extends Bf_MutableEntity {
	public function __construct(array $stateParams = NULL, $client = NULL, Bf_ResourcePath $overrideResourcePath) {
		parent::__construct($stateParams, $client);
		$this->setResourcePath($overrideResourcePath);
	}

	public function getByIDGeneric($id, $options = NULL, $customClient = NULL) {
		return static::getByID($id, $options, $customClient, $this->getResourcePath());
	}

	public function getAllGeneric($options = NULL, $customClient = NULL) {
		return static::getAll($options, $customClient, $this->getResourcePath());
	}

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);
	}

	public function getResourcePath() {
		return $this->_overrideResourcePath;
	}

	protected function setResourcePath(Bf_ResourcePath $newPath) {
		$this->_overrideResourcePath = $newPath;
	}
}