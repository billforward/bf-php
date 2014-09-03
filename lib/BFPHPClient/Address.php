<?php

class Bf_Address extends Bf_MutableEntity {
	public static function create(Bf_InsertableEntity $entity) {
		trigger_error('Create support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be created through cascade only (i.e. instantiated within another entity).',
		 E_USER_ERROR);
	}
	
	protected static $resourcePath;

	public static function initStatics() {
		self::$resourcePath = new Bf_ResourcePath('addresses', 'address');
	}
}
Bf_Address::initStatics();