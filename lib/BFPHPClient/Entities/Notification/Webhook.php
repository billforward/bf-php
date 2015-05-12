<?php

class Bf_Webhook extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('webhookSubscriptions', Bf_WebhookSubscription::getClassName(), $json);

		$this->unserializeEntity('organization', Bf_Organisation::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('webhooks', 'webhook');
	}
}
Bf_Webhook::initStatics();