<?php

class Bf_Organisation extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('apiConfigurations', Bf_APIConfiguration::getClassName(), $json);
	}

	/**
	 * Gets ApiConfigurations for this Bf_Organisation.
	 * @return Bf_APIConfiguration[]
	 */
	public function getApiConfigurations() {
		return $this->apiConfigurations;
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('organizations', 'organization');
	}
}
Bf_Organisation::initStatics();
