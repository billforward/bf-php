<?php

class Bf_CancellationAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('CancellationAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}