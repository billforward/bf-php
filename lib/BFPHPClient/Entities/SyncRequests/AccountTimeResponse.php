<?php

class Bf_AccountTimeResponse {
	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('responses', Bf_TimeResponse::getClassName(), $json);
	}
}