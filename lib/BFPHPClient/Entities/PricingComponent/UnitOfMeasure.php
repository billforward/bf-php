<?php

class Bf_UnitOfMeasure extends Bf_MutableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('units-of-measure', 'unitOfMeasure');
	}
}
Bf_UnitOfMeasure::initStatics();
