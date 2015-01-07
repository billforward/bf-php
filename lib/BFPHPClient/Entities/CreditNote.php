<?php

class Bf_CreditNote extends Bf_MutableEntity {
	public static function getAll($options = NULL, $customClient = NULL) {
		trigger_error('Get All support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).',
		 E_USER_ERROR);
	}

	public function save() {
		trigger_error('Save support is denied for this entity; '
		 .'at the time of writing, the provided API endpoint is not functioning.'
		 .'The entity can be saved through cascade only (i.e. save a related entity).',
		 E_USER_ERROR);
	}

	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('credit-notes', 'creditNote');
	}

	/**
	 * Gets Bf_CreditNotes for a given Bf_Account
	 * @return Bf_CreditNote[]
	 */
	public static function getForAccount($accountID, $options = NULL, $customClient = NULL) {
		// empty IDs are no good!
		if (!$accountID) {
    		trigger_error("Cannot lookup empty ID!", E_USER_ERROR);
		}

		$endpoint = "/account/$accountID";

		return static::getCollection($endpoint, $options, $customClient);
	}
}
Bf_CreditNote::initStatics();