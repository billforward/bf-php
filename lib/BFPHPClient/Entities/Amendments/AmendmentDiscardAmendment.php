<?php

class Bf_AmendmentDiscardAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('AmendmentDiscardAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}