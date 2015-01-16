<?php

class Bf_Account extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('roles', Bf_Role::getClassName(), $json);
		$this->unserializeArrayEntities('paymentMethods', Bf_PaymentMethod::getClassName(), $json);

		$this->unserializeEntity('profile', Bf_Profile::getClassName(), $json);
	}

	/**
	 * Fetches Bf_Subscriptions for this Bf_Account.
	 * @return Bf_Subscription[]
	 */
	public function getSubscriptions($options = NULL, $customClient = NULL) {
		return Bf_Subscription::getForAccount($this->id, $options, $customClient);
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
			'nominalValue' => $value,
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
