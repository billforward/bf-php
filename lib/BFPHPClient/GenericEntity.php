<?php

class Bf_GenericEntity extends Bf_MutableEntity {
	public function __construct(array $stateParams = NULL, $client = NULL, Bf_ResourcePath $overrideResourcePath) {
		parent::__construct($stateParams, $client);
		$this->setResourcePath($overrideResourcePath);
	}

	public static function getByID($id, $options = NULL, $customClient = NULL, Bf_ResourcePath $overrideResourcePath = NULL) {
		if (is_null($overrideResourcePath)) {
			trigger_error('Generic entities require a "Bf_ResourcePath $overrideResourcePath" argument, in order to GET from API.',
			 E_USER_ERROR);
		}

		return parent::getByID($id, $options, $customClient, $overrideResourcePath);
	}

	public static function getAll($options = NULL, $customClient = NULL, Bf_ResourcePath $overrideResourcePath = NULL) {
		if (is_null($overrideResourcePath)) {
			trigger_error('Generic entities require a "Bf_ResourcePath $overrideResourcePath" argument, in order to GET from API.',
			 E_USER_ERROR);
		}

		return parent::getAll($options, $customClient, $overrideResourcePath);
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