<?php

class Bf_Amendment extends Bf_MutableEntity {
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
			throw new Bf_EmptyArgumentException("Cannot lookup empty ID!");
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

	/**
	 * Parses into a BillForward timestamp the actioning time for some amendment
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the issuance amendment.
	 * @param mixed[NULL, string $subscriptionID, Bf_Subscription $subscription] (Default: NULL) Reference to subscription (required only for 'AtPeriodEnd' time).
	 * @return string The BillForward-formatted time.
	 */
	public static function parseActioningTime($actioningTime, $subscription = NULL) {
		$date = NULL; // defaults to Immediate
		if (is_int($actioningTime)) {
			$date = Bf_BillingEntity::makeBillForwardDate($actioningTime);
		} else if ($actioningTime === 'AtPeriodEnd') {
			// we need to consult subscription
			if (is_null($subscription)) {
				throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain AtPeriodEnd time, because a null reference was provided to the subscription.');
			}
			$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
			if (!is_null($subscriptionFetched->currentPeriodEnd)) {
				$date = $subscriptionFetched->currentPeriodEnd;
			} else {
				throw new Bf_PreconditionFailedException('Cannot set actioning time to period end, because the subscription does not declare a period end. This could mean the subscription has not yet been instantiated by the BillForward engines. You could try again in a few seconds, or in future invoke this functionality after a WebHook confirms the subscription has reached the necessary state.');
			}
		}
		return $date;
	}

	/**
	 * Assigns to this amendment the specified actioning time.
	 * @param mixed[int $timestamp, 'Immediate', 'AtPeriodEnd'] (Default: 'Immediate') When to action the issuance amendment.
	 * @param mixed[NULL, string $subscriptionID, Bf_Subscription $subscription] (Default: NULL) Reference to subscription (required only for 'AtPeriodEnd' time).
	 * @return static The modified Bf_Amendment model.
	 */
	public function applyActioningTime($actioningTime, $subscription = NULL) {
		$parsedActioningTime = static::parseActioningTime($actioningTime, $subscription);
		// if null, defaults to 'Immediate'
		if (!is_null($parsedActioningTime)) {
			$this->actioningTime = $parsedActioningTime;
		}
		return $this;
	}
}
Bf_Amendment::initStatics();