<?php

class Bf_Amendment extends Bf_InsertableEntity {
	protected static $_resourcePath;

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('amendments', 'amendment');
	}

	protected function addTypeParam($type, $stateParams) {
		if (is_null($stateParams)) {
			$stateParams = array();
		}
		$newStateParams = array(
			'@type' => $type
			);
		foreach($stateParams as $key => $value) {
			if ($key !== '@type') {
				$newStateParams[$key] = $value;	
			}
		}
		return $newStateParams;
	}
}
Bf_Amendment::initStatics();