<?php

class Bf_ProductRatePlanMigrationAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('ProductRatePlanMigrationAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('mappings', Bf_PricingComponentValueMigrationAmendmentMapping::getClassName(), $json);
	}
}