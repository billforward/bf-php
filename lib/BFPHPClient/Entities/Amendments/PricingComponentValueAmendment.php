<?php

class Bf_PricingComponentValueAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('PricingComponentValueAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}

	protected function doUnserialize(array $json) {
		// consult parent for further unserialization
		parent::doUnserialize($json);

		$this->unserializeArrayEntities('componentChanges', Bf_ComponentChange::getClassName(), $json);
	}
}