<?php

class Bf_IssueInvoiceAmendment extends Bf_Amendment {
	public function __construct(array $stateParams = NULL, $client = NULL) {
		
		$newStateParams = $this->addTypeParam('IssueInvoiceAmendment', $stateParams);

		parent::__construct($newStateParams, $client);
	}
}