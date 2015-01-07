<?php

class Bf_Amendment extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('amendments', 'amendment');
	}

	/**
	 * Gets Bf_Amendments for a given Bf_Subscription
	 * @param string ID of the Bf_Subscription
	 * @return Bf_Subscriptions[]
	 */
	public static function getForSubscription($subscriptionID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$subscriptionID) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$endpoint = "/subscription/$subscriptionID";
		
		return static::getCollection($endpoint, $options, $customClient);
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