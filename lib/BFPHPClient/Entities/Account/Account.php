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
}
Bf_Account::initStatics();
