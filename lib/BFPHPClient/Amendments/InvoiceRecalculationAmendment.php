<?php

class Bf_InvoiceRecalculationAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('InvoiceRecalculationAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}