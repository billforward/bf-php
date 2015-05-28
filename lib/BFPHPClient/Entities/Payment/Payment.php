<?php

class Bf_Payment extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('payments', 'payment');
	}
}
Bf_Payment::initStatics();