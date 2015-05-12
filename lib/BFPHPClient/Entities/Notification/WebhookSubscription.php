<?php

class Bf_WebhookSubscription extends Bf_MutableEntity {
	protected static $_resourcePath;

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('webhook', Bf_Webhook::getClassName(), $json);
	}

	public static function initStatics() {
		self::$_resourcePath = new Bf_ResourcePath('webhooks', 'webhookSubscription');
	}
}
Bf_WebhookSubscription::initStatics();