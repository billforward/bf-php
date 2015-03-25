<?php

class Bf_Address extends Bf_MutableEntity {
	public static function getbyID($id, $options = NULL, $customClient = NULL) {
		throw new Bf_UnsupportedMethodException('Get by ID support is denied for this entity; '
		 .'at the time of writing, no API endpoint exists to support it.'
		 .'The entity can be GETted through cascade only (i.e. GET a related entity).');
	}

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
		self::$_resourcePath = new Bf_ResourcePath('addresses', 'address');
	}
}
Bf_Address::initStatics();