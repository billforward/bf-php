<?php

class Bf_PricingComponentValueAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('PricingComponentValueChangeAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}