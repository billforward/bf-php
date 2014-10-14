<?php

class Bf_PricingComponentValueAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('PricingComponentValueAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}