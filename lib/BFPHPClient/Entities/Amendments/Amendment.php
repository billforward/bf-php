<?php

class Bf_Amendment extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('amendments', 'amendment');
	}

	/**
	 * Gets Bf_Amendments for a given Bf_Subscription
	 * @param union[string | Bf_Subscription] $subscription The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_Subscriptions[]
	 */
	public static function getForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);
		$endpoint = sprintf("subscription/%s",
			rawurlencode($subscriptionID)
			);
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Discard the amendment (now, or at a scheduled time).
	 * @param array $discardOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param {@see self::parseActioningTime(mixed)} $..['actioningTime'] When to action the 'next execution attempt' amendment
	 * @return Bf_AmendmentDiscardAmendment The created 'amendment discarding' amendment.
	 */
	public function scheduleDiscard(
		array $discardOptions = array(
			'actioningTime' => 'Immediate'
			)
		) {

		$inputOptions = $discardOptions;

		$amendmentID = Bf_Amendment::getIdentifier($this);
		$subscriptionID = Bf_Subscription::getIdentifier($this->subscriptionID);

		$stateParams = array_merge(
			static::getFinalArgDefault(__METHOD__),
			array(
				'subscriptionID' => $subscriptionID,
				'amendmentToDiscardID' => $amendmentID
				),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array('actioningTime' => 'parseActioningTime'),
			array('actioningTime' => array($subscriptionID)));

		// create model of amendment
		$amendment = new Bf_AmendmentDiscardAmendment($stateParams);

		$createdAmendment = Bf_AmendmentDiscardAmendment::create($amendment);
		return $createdAmendment;
	}

	/**
	 * Parses into a BillForward timestamp the actioning time for some amendment
	 * @param union[int $timestamp | string_ENUM['Immediate' | 'ServerNow', 'ClientNow', 'AtPeriodEnd']] (Default: 'Immediate') When to action the amendment
	 *
	 *  int
	 *  Schedule the amendment to occur at the specified UNIX timestamp.
	 *  Examples:
	 *  	* time()
	 *  	* 1431704624
	 *  	* Bf_BillingEntity::makeUTCTimeFromBillForwardDate('2015-04-23T17:13:37Z')
	 *
	 *	string (within ENUM)
	 *  <Immediate> (Default) | <ServerNow>
	 *  Perform the amendment now (synchronously where possible). Actioning time is set to BillForward's 'now' at the time it parses the request.
	 *
	 *  <ClientNow>
	 *  Schedule the amendment for 'now' according to this client's clock (which will likely be in the past by the time the request reaches BillForward).
	 *  
	 *  <AtPeriodEnd>
	 *  Schedule the amendment to occur at the end of the subscription's current billing period.
	 *
	 *  string (outside ENUM)
	 *  Schedule the amendment to occur at the specified BillForward-formatted timestamp.
	 *  Examples:
	 *  	* '2015-04-23T17:13:37Z'
	 *  	* Bf_BillingEntity::makeBillForwardDate(time())
	 *  	* Bf_BillingEntity::makeBillForwardDate(1431704624)
	 *
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'AtPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return string The BillForward-formatted time.
	 */
	public static function parseActioningTime($actioningTime, $subscription = NULL) {
		$intSpecified = NULL;

		switch ($actioningTime) {
			case 'ServerNow':
			case 'Immediate':
				return NULL;
			case 'AtPeriodEnd':
				// we need to consult subscription
				if (is_null($subscription)) {
					throw new Bf_EmptyArgumentException('Failed to consult subscription to ascertain AtPeriodEnd time, because a null reference was provided to the subscription.');
				}
				$subscriptionFetched = Bf_Subscription::fetchIfNecessary($subscription);
				return $subscriptionFetched->getCurrentPeriodEnd();
			case 'ClientNow':
				$intSpecified = time();
			default:
				if (is_int($actioningTime)) {
					$intSpecified = $actioningTime;
				}
				if (!is_null($intSpecified)) {
					return Bf_BillingEntity::makeBillForwardDate($intSpecified);
				}
				if (is_string($actioningTime)) {
					return $actioningTime;
				}
		}

		return NULL;
	}

	/**
	 * Mutates actioningTime in the referenced array
	 * @param array $stateParams Map possibly containing `actioningTime` key that desires parsing.
	 * @param union[NULL | union[string $id | Bf_Subscription $entity]] (Default: NULL) (Optional unless 'AtPeriodEnd' actioningTime specified) Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return static The modified array.
	 */
	public static function mutateActioningTime(array &$stateParams, $subscription = NULL) {
		$parsedActioningTime = Bf_Amendment::parseActioningTime(static::popKey($stateParams, 'actioningTime'), $subscription);
		if (!is_null($parsedActioningTime)) {
			$stateParams['actioningTime'] = $parsedActioningTime;
		}
		return $stateParams;
	}
}
Bf_Amendment::initStatics();