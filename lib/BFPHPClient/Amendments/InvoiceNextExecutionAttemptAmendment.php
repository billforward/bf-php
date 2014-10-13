<?php

class Bf_InvoiceNextExecutionAttemptAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('InvoiceNextExecutionAttemptAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}