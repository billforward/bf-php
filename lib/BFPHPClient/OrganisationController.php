<?php

class Bf_OrganisationController extends Bf_Controller {
	public static function getEntityClass() {
		return Bf_Organisation::getClassName();
	}

	public function getMine() {
		$entityClass = static::getEntityClass();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/mine";
		$fullRoute = $apiRoute.$endpoint;

		$client = $this->getClient();
		$response = $client->doGet($fullRoute);
		
		$json = $response->json();
		$results = $json['results'];

		$entities = array();

		foreach($results as $value) {
			$constructedEntity = new $entityClass($client, $value);
			array_push($entities, $constructedEntity);
		}

		return $entities;
	}
}
