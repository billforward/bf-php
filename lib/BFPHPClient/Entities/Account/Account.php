<?php

class Bf_Account extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('roles', Bf_Role::getClassName(), $json);
		$this->unserializeArrayEntities('paymentMethods', Bf_PaymentMethod::getClassName(), $json);

		$this->unserializeEntity('profile', Bf_Profile::getClassName(), $json);
		$this->unserializeEntity('metadata', Bf_MetadataJson::getClassName(), $json);
	}

	/**
	 * Fetches Bf_Subscriptions for this Bf_Account.
	 * @return Bf_Subscription[]
	 */
	public function getSubscriptions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getForAccount($this->id, $options, $customClient);
	}

	/**
	 * Fetches Bf_Invoices for this Bf_Account.
	 * @return Bf_Invoice[]
	 */
	public function getInvoices($options = NULL, $customClient = NULL) {
		return Bf_Invoice::getForAccount($this->id, $options, $customClient);
	}

	/**
	 * Gets Bf_Roles for this Bf_Account.
	 * @return Bf_Role[]
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * Gets Bf_PaymentMethods for this Bf_Account.
	 * @return Bf_PaymentMethod[]
	 */
	public function getPaymentMethods() {
		return $this->paymentMethods;
	}

	/**
	 * Gets Bf_Profile for this Bf_Account.
	 * @return Bf_Profile
	 */
	public function getProfile() {
		return $this->profile;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('accounts', 'account');
	}

	/**
	 * Gets Bf_CreditNotes for this Bf_Account.
	 * @return Bf_CreditNote[]
	 */
	public function getCreditNotes($options = NULL, $customClient = NULL) {
		return Bf_CreditNote::getForAccount($this->id, $options, $customClient);
	}

	/**
	 * Issues to the Bf_Account, credit of the specified value and currency.
	 * @param int Nominal value of credit note
	 * @param ISO_4217_Currency_Code The currency code
	 * @return Bf_CreditNote
	 */
	public function issueCredit($value, $currency = 'USD') {
		$creditNote = new Bf_CreditNote(array(
			'value' => $value,
			'currency' => $currency
			));

		return $creditNote->issueToAccount($this->id);
	}

	/**
	 * Gets nominal remaining value of all credit notes on this account, for the specified currency.
	 *
	 * NOTE: As with all API calls, this counts by default only the first 10 credit notes.
	 * Override by passing into $options: array('records' => 100); or however many credit notes you expect is a reasonable upper limit.
	 * @return int
	 */
	public function getRemainingCreditForCurrency($currency = 'USD', $options = NULL, $customClient = NULL) {
		$creditNotes = $this->getCreditNotes($options, $customClient);

		return Bf_CreditNote::getRemainingCreditForCurrency($creditNotes, $currency);
	}

	//// ADVANCE ACCOUNT'S SUBSCRIPTIONS THROUGH TIME

	/**
	 * Synchronously advances the account's subscriptions through time.
	 * @param array $advancementOptions (Default: All keys set to their respective default values) Encapsulates the following optional parameters:
	 *	* @param boolean (Default: false) $..['dryRun'] Whether to forego persisting the effected changes.
	 *	* @param boolean (Default: false) $..['skipIntermediatePeriods']
	 *	* @param boolean (Default: true) $..['handleAmendments']
	 *	* @param string_ENUM['SingleAttempt', 'FollowDunning', 'None'] (Default: 'SingleAttempt') $..['executionStrategy']
	 *	*
	 *	* 	<SingleAttempt> (Default)
	 *	*
	 *	*	<FollowDunning>
	 *	*
	 *	* 	<None>
	 *	*
	 *	* @param boolean (Default: false) $..['freezeOnCompletion']
	 *	* @param {@see Bf_BillingEntity::parseTimeRequestFromTime(mixed)} $..['from'] From when to advance time
	 *	* @param {@see Bf_BillingEntity::parseTimeRequestToTime(mixed)} $..['to'] Until when to advance time
	 *	* @param integer (Default: NULL) (Non-null value of param requires that $..['to'] be NULL instead) $..['periods']
	 * @return Bf_AccountTimeResponse The results of advancing the account's subscription through time.
	 */
	public function advance(
		array $advancementOptions = array(
			'dryRun' => false,
			'skipIntermediatePeriods' => false,
			'handleAmendments' => true,
			'executionStrategy' => 'SingleAttempt',
			'freezeOnCompletion' => false,
			'from' => NULL,
			'to' => 'CurrentPeriodEnd',
			'periods' => NULL
			)
		) {

		$inputOptions = $advancementOptions;

		$accountID = Bf_Account::getIdentifier($this);

		$stateParams = static::mergeUserArgsOverNonNullDefaults(
			__METHOD__,
			array(),
			$inputOptions
			);
		static::mutateKeysByStaticLambdas(
			$stateParams,
			array(
				'from' => 'parseTimeRequestFromTime',
				'to' => 'parseTimeRequestToTime'
				),
			array(
				'from' => array(NULL),
				'to' => array(NULL)
				));
		$requestEntity = new Bf_TimeRequest($stateParams);

		$endpoint = sprintf("%s/advance",
			rawurlencode($accountID)
			);

		$responseEntity = Bf_AccountTimeResponse::getClassName();

		$constructedEntity = static::postEntityAndGrabFirst($endpoint, $requestEntity, $responseEntity);
		return $constructedEntity;
	}

	/**
	 * Creates using the API a new StripeToken.
	 * Adds to this Bf_Account's paymentMethods a model of
	 * a Bf_PaymentMethod for that token.
	 * @param Stripe_Card The 'card' object retrieved from Stripe
	 * @return Bf_Account ($this)
	 */
	public function addNewStripePaymentMethod($card) {
		// create model of relationship to tokenized card
		$stripeToken = new Bf_StripeToken(array(
			'accountID' => $this->id,
			'cardDetailsID' => $card->id,
			'stripeCustomerID' => $card->customer
			));

		// send model to API to be created
		$createdStripeToken = Bf_StripeToken::create($stripeToken);

		// create model of payment method
		$stripePaymentMethod = new Bf_PaymentMethod(array(
			'linkID' => $createdStripeToken->id,
			'name' => $card->last4,
			'description' => "Stripe (" . ($card->type ? $card->type : ($card->brand ? $card->brand : "Unknown")) . "): " . $card->last4,
			'crmID' => $card->id,
			'expiryDate' => $card->exp_year. '/'. str_pad($card->exp_month, 2, "0", STR_PAD_LEFT),
			'gateway' => "stripe"
			));
		
		if (!$this->paymentMethods) {
			// initialize as empty array
			$this->paymentMethods = array();
		}

		// add our modelled payment method
		array_push($this->paymentMethods, $stripePaymentMethod);

		return $this;
	}
}
Bf_Account::initStatics();
