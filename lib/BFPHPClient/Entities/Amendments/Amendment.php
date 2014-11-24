<?php

class Bf_Amendment extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('amendments', 'amendment');
	}

	/**
	 * Gets Bf_Amendments for a given Bf_Subscription
	 * @return Bf_Subscriptions[]
	 */
	public static function getForSubscription($subscriptionID, $options = NULL, $customClient = NULL) {
		$client = NULL;
		if (is_null($customClient)) {
			$client = static::getSingletonClient();
		} else {
			$client = $customClient;
		}

		$entityClass = static::getClassName();

		$apiRoute = $entityClass::getResourcePath()->getPath();
		$endpoint = "/subscription/".$subscriptionID;
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

	public function discard() {
		// create model of amendment
		$amendment = new Bf_AmendmentDiscardAmendment(array(
			'amendmentToDiscardID' => $this->id,
			'subscriptionID' => $this->subscriptionID
			));

		$createdAmendment = Bf_AmendmentDiscardAmendment::create($amendment);
		return $createdAmendment;
	}
}
Bf_Amendment::initStatics();