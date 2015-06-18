<?php

class Bf_Refund extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('refunds', 'refund');
	}
}
Bf_Refund::initStatics();