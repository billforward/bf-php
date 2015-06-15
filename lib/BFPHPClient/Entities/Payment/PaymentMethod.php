<?php

class Bf_PaymentMethod extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('payment-methods', 'paymentMethod');
	}

	/**
	 * Removes a payment method from use by the specified subscription
	 * @param union[string $id | Bf_Subscription $subscription] The Bf_Subscription to which the Bf_Coupon should be applied. <string>: ID of the Bf_Subscription. <Bf_Subscription>: The Bf_Subscription.
	 * @return Bf_PaymentMethod The removed payment method.
	 */
	public function removeFromSubscription($subscription) {
		return Bf_Subscription::removePaymentMethodFromSubscription($this, $subscription);
	}
}
Bf_PaymentMethod::initStatics();