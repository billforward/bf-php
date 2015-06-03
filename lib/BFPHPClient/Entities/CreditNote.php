<?php

class Bf_CreditNote extends Bf_MutableEntity {
	public static function getAll($options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

	public function save() {
		throw new Bf_UnsupportedMethodException('Save support is denied for this entity; '
		 .'at the time of writing, the provided API endpoint is not functioning.'
		 .'The entity can be saved through cascade only (i.e. save a related entity).');
	}

	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('credit-notes', 'creditNote');
	}

	/**
	 * Gets Bf_CreditNotes for a given Bf_Account
	 * @param union[string | Bf_Account] $account Reference to account <string>: $id of the Bf_Account. <Bf_Account>: The Bf_Account entity.
	 * @return Bf_CreditNote[]
	 */
	public static function getForAccount($account, $options = NULL, $customClient = NULL) {
		$accountID = Bf_Account::getIdentifier($account);

		$endpoint = sprintf("account/%s",
			rawurlencode($accountID)
			);
		
		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_CreditNotes for a given Bf_Subscription
	 * @param union[string | Bf_Subscription] $subscription Reference to subscription <string>: $id of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription entity.
	 * @return Bf_CreditNote[]
	 */
	public static function getForSubscription($subscription, $options = NULL, $customClient = NULL) {
		$subscriptionID = Bf_Subscription::getIdentifier($subscription);

		$endpoint = sprintf("subscription/%s",
			rawurlencode($subscriptionID)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets Bf_CreditNotes for a given Bf_Invoice
	 * @param union[string | Bf_Invoice] $invoice Reference to invoice <string>: $id of the Bf_Invoice. <Bf_Invoice>: The Bf_Invoice entity.
	 * @return Bf_CreditNote[]
	 */
	public static function getForInvoice($invoice, $options = NULL, $customClient = NULL) {
		$invoiceID = Bf_Invoice::getIdentifier($invoice);

		$endpoint = sprintf("invoice/%s",
			rawurlencode($invoiceID)
			);

		return static::getCollection($endpoint, $options, $customClient);
	}

	/**
	 * Gets nominal remaining value of some collection of credit notes, for the specified currency.
	 * @param Bf_CreditNote[] The collection of credit notes
	 * @param ISO_4217_Currency_Code The currency code
	 * @return int
	 */
	public static function getRemainingCreditForCurrency($creditNotes, $currency = 'USD') {
		$remainingNominalValue = 0;

		foreach ($creditNotes as $creditNote) {
			if ($creditNote->currency === $currency) {
				$remainingNominalValue += $creditNote->remainingNominalValue;
			}
		}

		return $remainingNominalValue;
	}

	/**
	 * Issues this credit note to the specified Bf_Account
	 * @param string ID of the account to which credit should be issued
	 * @return Bf_CreditNote The issued credit note returned by the API
	 */
	public function issueToAccount($accountID) {
		$this->accountID = $accountID;

		return Bf_CreditNote::create($this);
	}

	/**
	 * Issues this credit note to the specified Bf_Subscription
	 * @param string ID of the subscription to which credit should be issued
	 * @param string ID of the account to whom the subscription belongs
	 * @return Bf_CreditNote The issued credit note returned by the API
	 */
	public function issueToSubscription($subscriptionID) {
		$this->subscriptionID = $subscriptionID;

		return Bf_CreditNote::create($this);
	}
}
Bf_CreditNote::initStatics();