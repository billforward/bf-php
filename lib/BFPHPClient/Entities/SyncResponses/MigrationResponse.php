<?php

class Bf_MigrationResponse extends Bf_MigrationRequest {
	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeEntity('subscription', Bf_Subscription::getClassName(), $json);
	}
}