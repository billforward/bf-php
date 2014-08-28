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

	public function getbyID($id, $options = NULL) {
		$entityClass = static::getEntityClass();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/$id";
		$fullRoute = $apiRoute.$endpoint;

		$client = $this->getClient();
		$response = $client->doGet($fullRoute, $options);
		$json = $response->json();

		$results = $json['results'];

		$firstMatch = $results[0];

//		$justAccount = $json->

		return new $entityClass($client, $firstMatch);
	}

	public function getAll($options = NULL) {
		$entityClass = static::getEntityClass();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$fullRoute = $apiRoute;

		$client = $this->getClient();
		$response = $client->doGet($fullRoute, $options);

		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($client, $value);
			array_push($entities, $constructedEntity);
		}

		return $entities;
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

