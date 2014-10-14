<?php

class Bf_TaxationLink extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('taxation-links', 'TaxationLink');
	}
}
Bf_TaxationLink::initStatics();
