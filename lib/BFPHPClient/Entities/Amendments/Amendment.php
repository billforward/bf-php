<?php

class Bf_Amendment extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('amendments', 'amendment');
	}
}
Bf_Amendment::initStatics();