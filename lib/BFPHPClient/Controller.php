<?php
abstract class Bf_Controller {
	public function getClient() {
		return $this->_client;
	}

	public function setClient(BfClient &$client = NULL) {
		$this->_client = $client;
	}

	protected $_client = NULL;

	public function __construct(BfClient &$client = NULL) {
		$this->setClient($client);
	}

	/**
	 * Asks API to create a real instance of this Controller's entity,
	 * based on provided properties.
	 * @return the created Entity.
	 */
	public function create(array $stateParams = NULL) {
		if ($stateParams == NULL) {
			$stateParams = array();
		}
		$entityClass = static::getEntityClass();

		$client = $this->getClient();
		$entity = new $entityClass($client, $stateParams);
		$createdEntity = $entity
		->create();

		return $createdEntity;
	}
}

