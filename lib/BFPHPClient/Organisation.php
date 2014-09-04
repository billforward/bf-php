<?php

class Bf_Organisation extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('apiConfigurations', Bf_APIConfiguration::getClassName(), $json);
	}

	/**
	 * Gets ApiConfigurations for this Bf_Organisation.
	 * @return Bf_APIConfiguration[]
	 */
	public function getApiConfigurations() {
		return $this->apiConfigurations;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('organizations', 'organization');
	}

	public static function getMine($options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePathStatic()->getPath();
		$endpoint = "/mine";
		$fullRoute = $apiRoute.$endpoint;
		
		$response = $client->doGet($fullRoute, $options);
		
		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($value, $client);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}
}
Bf_Organisation::initStatics();
